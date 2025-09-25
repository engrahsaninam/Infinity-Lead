<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;


use App\Models\ApiKey;
use App\Models\GeneratedEmail;
use App\Models\Infinity;
use App\Models\LinkedinExport;
use App\Models\LinkedinExportData;
use App\Models\Website;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use League\Csv\Statement;
use App\Services\HunterService;
use App\Services\GenerateEmailCombinations;
use NeverBounce\Account;
use Normalizer;

class LeadFinderController extends Controller
{
    //
    use CommonTraits;
    public function records(Request $request)
    {
        $records = LinkedinExportData::with('emails')->where('linkedin_export_id', $request->id)->paginate($request->records);


        $allItems = LinkedinExportData::with('emails')
            ->where('linkedin_export_id', $request->id)
            ->get();


        $validCount = $allItems->flatMap(fn($item) => $item->emails)->where('status', 'valid')->count();
        $invalidCount = $allItems->flatMap(fn($item) => $item->emails)->where('status', 'invalid')->count();

        $analytics = [
            [
                'label' => 'Skipped Leads',
                'key' => 'skipped_emails',
                'value' => $allItems->filter(fn($item) => $item->emails->isEmpty())->count(),
            ],
            [
                'label' => 'Invalid Leads',
                'key' => 'invalid_emails',
                'value' => $invalidCount,
            ],
            [
                'label' => 'Valid Leads',
                'key' => 'valid_emails',
                'value' => $validCount,
            ],
            [
                'label' => 'Total Leads',
                'key' => 'total_leads',
                'value' => $allItems->count(),
            ],

            [
                'col' => 'md-12',
                'label' => 'Synced Unique Companies',
                'key' => 'synced_companies',
                'value' => $allItems->pluck('website')->filter()->unique()->count(),
            ],
            [
                'label' => 'Unique Companies',
                'key' => 'unique_companies',
                'value' => $allItems->pluck('company')->filter()->unique()->count(),
            ],



            [
                'label' => 'Total Emails Generated',
                'key' => 'total_emails_generated',
                'value' => $allItems->flatMap(fn($item) => $item->emails)->count(),
            ],
            [
                'label' => 'Total Credits Used',
                'key' => 'credits_used',
                'value' => $validCount + $invalidCount,
            ],


        ];



        $data = [
            'records' => $records,
            'analytics' => $analytics,
        ];
        return $this->sendSuccess('Records fetched successfully!', $data);
    }
    public function fetch(Request $request)
    {
        $exports = LinkedinExport::with('records')->where('user_id', Auth::id())->get();
        return $this->sendSuccess('Exports fetched successfully!', $exports);
    }

    public function uploadMultiple(Request $request)
    {
        if ($request->has('combined') && $request->input('combined') === 'true') return $this->handleCombinedUpload($request);
        $validators = Validator::make($request->all(), [
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:csv,txt,xlsx|max:10240', // 10MB max per file
        ]);
        if ($validators->fails()) return $this->sendError($validators->messages(), 422);

        $uploadedFiles = $request->file('files');
        $successCount = $totalRecords = 0;
        $errorFiles = [];
        DB::beginTransaction();
        try {
            foreach ($uploadedFiles as $file) {
                $originalName = $file->getClientOriginalName();
                
                $path = Infinity::upload($file, Infinity::_PUBLIC_LINKEDIN_EXPORT);
                if (!$path) {
                    $errorFiles[] = "Failed to upload: {$originalName}";
                    continue;
                }

                // Convert CSV file to UTF-8 to prevent malformed UTF-8 errors
                $absolutePath = storage_path('app/public/' . str_replace('/storage/', '', $path));
                $this->convertFileToUtf8($absolutePath);

                // Create export record with filename as name
                $exportName = pathinfo($originalName, PATHINFO_FILENAME);
                $export = LinkedinExport::create([
                    'user_id' => Auth::id(),
                    'name' => $exportName,
                    'file' => $path,
                ]);

                // $result = $this->processCSVFile($path, $export->id);

                $extension = $file->getClientOriginalExtension();
if (in_array($extension, ['csv', 'txt'])) {
    $result = $this->processCSVFile($path, $export->id);
} elseif ($extension === 'xlsx') {
    $result = $this->processXLSXFile($path, $export->id);
} else {
    $errorFiles[] = "{$originalName}: Unsupported file format";
    $export->delete();
    continue;
}
                if ($result['success']) {
                    $successCount++;
                    $totalRecords += $result['recordCount'];
                } else {
                    $errorFiles[] = "{$originalName}: {$result['message']}";
                    $export->delete();

                    $streamPath = str_replace('/storage/', '', $path);
                    Storage::disk('public')->delete($streamPath);
                }
            }

            if ($successCount === 0) {
                DB::rollBack();
                return $this->sendError('All files failed to process: ' . implode(', ', $errorFiles), 421);
            }
            DB::commit();

            $message = "{$successCount} file(s) uploaded successfully with {$totalRecords} total records.";
            if (!empty($errorFiles)) $message .= " Some files failed: " . implode(', ', $errorFiles);

            return $this->sendSuccess($message, [
                'uploaded_count' => $successCount,
                'total_files' => count($uploadedFiles),
                'total_records' => $totalRecords,
                'errors' => $errorFiles
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Upload failed: ' . $e->getMessage(), 421);
        }
    }

    private function handleCombinedUpload(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'files' => 'required|array|min:2', // at least 2 to combine
            'files.*' => 'required|file|mimes:csv,txt,xlsx|max:10240',
            'combined_name' => 'required|string|max:255',
        ]);
        if ($validators->fails()) return $this->sendError($validators->messages(), 422);

        $uploadedFiles = $request->file('files');
        $combinedName = pathinfo($request->combined_name, PATHINFO_FILENAME) . "_" . uniqid() . '.csv'; // force CSV for output

        try {
            $tempPath = storage_path("app/public/temp_" . uniqid() . ".csv");
            $outputHandle = fopen($tempPath, 'w');

            $headerWritten = false;
            foreach ($uploadedFiles as $file) {
                $extension = $file->getClientOriginalExtension();

                if (in_array($extension, ['csv', 'txt'])) {
                    $rows = array_map('str_getcsv', file($file->getRealPath()));
                // } elseif ($extension === 'xlsx') {
                //     $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                //     $rows = $spreadsheet->getActiveSheet()->toArray();
                } else {
                    continue;
                }

                if (!$headerWritten) {
                    fputcsv($outputHandle, $rows[0]);
                    $headerWritten = true;
                }

                foreach (array_slice($rows, 1) as $row) {
                    fputcsv($outputHandle, $row);
                }
            }
            fclose($outputHandle);
            $this->convertFileToUtf8($tempPath);

            $path = "linkedin_exports/" . $combinedName;
            Storage::disk('public')->put($path, file_get_contents($tempPath));
            unlink($tempPath);

            $export = LinkedinExport::create([
                'user_id' => Auth::id(),
                'name' => pathinfo($request->combined_name, PATHINFO_FILENAME),
                'file' => "/storage/" . $path,
            ]);

            $result = $this->processCSVFile("/storage/" . $path, $export->id);
            if (!$result['success']) {
                $export->delete();
                return $this->sendError("Combined upload failed: " . $result['message'], 421);
            }

            return $this->sendSuccess(
                "Combined file uploaded successfully with {$result['recordCount']} records.",
                [
                    'uploaded_count' => 1,
                    'total_files' => count($uploadedFiles),
                    'total_records' => $result['recordCount'],
                    'errors' => [],
                ]
            );
        } catch (\Exception $e) {
            return $this->sendError('Combined upload failed: ' . $e->getMessage(), 421);
        }
    }

    private function convertFileToUtf8($filePath)
    {
        $content = file_get_contents($filePath);
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }
        $encoding = mb_detect_encoding($content, [
            'UTF-8',
            'ISO-8859-1',
            'Windows-1252'
        ], true);

        if ($encoding === false) {
            $encoding = 'Windows-1252';
        }
        $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        $content = utf8_decode($content);
        $content = utf8_encode($content);

        if (class_exists('Normalizer')) {
            $content = Normalizer::normalize($content, Normalizer::FORM_C);
        }
        file_put_contents($filePath, $content);
    }

    private function processCSVFile($filePath, $exportId)
    {
        try {
            $streamPath = str_replace('/storage/', '', $filePath);
            if (!Storage::disk('public')->exists($streamPath)) return ['success' => false, 'message' => 'File does not exist on disk'];

            $stream = Storage::disk('public')->readStream($streamPath);
            if (!$stream) return ['success' => false, 'message' => 'Unable to read file stream'];

            $csv = Reader::createFromStream($stream);
            $csv->setHeaderOffset(0);
            $records = Statement::create()->process($csv);

            $batchSize = 1000;
            $batch = [];
            $recordCount = 0;
            foreach ($records as $record) {
                $batch[] = [
                    'linkedin_export_id' => $exportId,
                    'name' => $record['name'] ?? '',
                    'first_name' => $record['first_name'] ?? '',
                    'last_name' => $record['last_name'] ?? '',
                    'title' => $record['title'] ?? '',
                    'company' => $record['company'] ?? null,
                    'profile' => $record['profile'] ?? null,
                    'url' => $record['url'] ?? null,
                    'region' => $record['region'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $recordCount++;

                if (count($batch) === $batchSize) {
                    LinkedinExportData::insert($batch);
                    $batch = [];
                }
            }
            if (!empty($batch)) LinkedinExportData::insert($batch);
            fclose($stream);
            
            return ['success' => true, 'recordCount' => $recordCount];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete(Request $request)
    {
        LinkedinExport::find($request->id)->delete();
        return $this->sendSuccess('LinkedIn data deleted successfully!');
    }

    public function syncCompanies(Request $request, HunterService $hunter)
    {
        $lead = LinkedinExport::findOrFail($request->id);
        if ($lead->company_sync > 0) {
            return $this->sendSuccess('Companies already synced');
        }
        foreach ($lead->records->whereNull('website') as $record) {
            $companyName = $record->company;
            $existingRecord = LinkedinExportData::where('company', $companyName)->whereNotNull('website')->first();
            if ($existingRecord) {
                $domain = $existingRecord->website;
                $record->website = $domain;
                $record->save();
            } else {
                $domain = $hunter->getDomainFromCompany($companyName);
                if ($domain) {
                    $record->website = $domain;
                    $record->save();
                }
            }
        }

         $lead->company_sync = 1;
        $lead->save();
        return $this->sendSuccess('LinkedIn companies fetched successfully!');
    }
    public function syncCompaniesDatabase(Request $request, HunterService $hunter)
    {
        $lead = LinkedinExport::findOrFail($request->id);
        if ($lead->company_sync > 0) {
            return $this->sendError('Companies already synced', 421);
        }
        foreach ($lead->records->whereNull('website') as $record) {
            $companyName = $record->company;
            $existingRecord = LinkedinExportData::where('company', $companyName)->whereNotNull('website')->first();
            if ($existingRecord) {
                $domain = $existingRecord->website;
                $record->website = $domain;
                $record->save();
            } else {
                $domain = Website::where('name', $companyName)->first();
                if ($domain) {
                    $record->website = $domain->domain;
                    $record->save();
                }
            }
        }
        if ($lead->records->whereNull('website')->count() === $lead->records->count()) {
            return $this->sendError('No company found!', 421);
        }
        $lead->company_sync = 1;
        $lead->save();
        return $this->sendSuccess('LinkedIn companies fetched successfully!');
    }
    public function verifyEmails(Request $request)
    {

        $key = ApiKey::where('type', ApiKey::TYPE_VT)
            ->where('user_id', Auth::id())
            ->first();
        if (!$key) {
            return $this->sendError('Email verify API Key missing!', 421);
        }
        $apiKey = $key->key;
        $lead = LinkedinExport::findOrFail($request->id);
        if ($lead->verify_email_sync > 0) {
            return $this->sendError('Emails already verified', 421);
        }
        foreach ($lead->records->whereNull('email') as $record) {
            foreach ($record->emails as $email_record) {
                $isValid = false;
                $email = $email_record->email;
                if ($key->tool == ApiKey::TOOL_REOON) {
                    $isValid = $this->verifyWithReoon($email, $apiKey);
                }
                if ($key->tool == ApiKey::TOOL_EMAILABLE) {
                    $isValid = $this->verifyWithEmailable($email, $apiKey);
                }
                if ($key->tool == ApiKey::TOOL_ZERO_BOUNCE) {
                    $isValid = $this->verifyWithZeroBounce($email, $apiKey);
                }
                if ($key->tool == ApiKey::TOOL_NEVER_BOUNCE) {
                    $isValid = $this->verifyWithNeverBounce($email, $apiKey);
                }
                if ($key->tool == ApiKey::TOOL_MILLION_VERIFIER) {
                    $isValid = $this->verifyWithMillionVerifier($email, $apiKey);
                }
                if ($isValid) {
                    $email_record->status = 'valid';
                    $email_record->save();
                    break;
                } else {
                    $email_record->status = 'invalid';
                    $email_record->save();
                }
            }
        }
        $lead->verify_email_sync = 1;
        $lead->save();
        return $this->sendSuccess('Email verification processed successfully!');


    }
    public function generateEmails(Request $request, GenerateEmailCombinations $emailGenerator)
    {
        $lead = LinkedinExport::findOrFail($request->id);
        if ($lead->generate_email_sync > 0) return $this->sendError('Emails already generated', 421);

        foreach ($lead->records as $record) {
            $domain = parse_url($record->website, PHP_URL_HOST) ?? $record->website;
          
            $combinations = $emailGenerator->generateEmailCombinations(
                $record->first_name,
                $record->last_name,
                $domain,
                $record->company
            );

            foreach ($combinations as $email) {
                GeneratedEmail::updateOrCreate(
                    ['linked_in_export_data_id' => $record->id, 'email' => $email],
                    ['status' => 'guessed']
                );
            }
        }
        $lead->generate_email_sync = 1;
        $lead->save();
        return $this->sendSuccess('Emails generated successfully!');
    }
    protected function verifyWithReoon($email, $apiKey)
    {
        $url = "https://emailverifier.reoon.com/api/v1/verify?email={$email}&key={$apiKey}";
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);
            if ($data['status'] === 'valid') {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            dd($e->getMessage());

        }
    }
    protected function verifyWithEmailable($email, $apiKey)
    {
        $url = "https://api.emailable.com/v1/verify?email={$email}&api_key={$apiKey}";
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);
            if ($data['state'] === 'deliverable') {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            dd($e->getMessage());

        }
    }
    protected function verifyWithZeroBounce($email, $apiKey)
    {
        $url = "https://api.zerobounce.net/v2/validate?email={$email}&api_key={$apiKey}";
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);
            if ($data['status'] === 'valid') {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            dd($e->getMessage());

        }
    }
    protected function verifyWithMillionVerifier($email, $apiKey)
    {
        $url = "https://api.millionverifier.com/api/v3/?api={$apiKey}&email={$email}&timeout=10";
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);
            if ($data['result'] === 'ok') {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    protected function verifyWithNeverBounce($email, $apiKey)
    {
        try {
            \NeverBounce\Auth::setApiKey($apiKey);
            $response = Account::info($email);
            if ($response->status === 'success') {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }


    public function export($id, $type)
    {
        $lead = LinkedinExport::find($id);
        if (!in_array($type, ['valid', 'invalid', 'all', 'skipped'])) {
            abort(404);
        }
        return view('exports.linkedin_export', compact('lead', 'type'));
    }
}