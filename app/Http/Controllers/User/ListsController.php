<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessCsvUpload;
use App\Models\Csv;
use App\Models\GoogleSheet;
use App\Models\Infinity;
use App\Models\Lists;
use App\Models\Subscriber;
use App\Traits\CommonTraits;
use Auth;
use Exception;
use Google\Client;
use Illuminate\Http\Request;
use League\Csv\Reader;
use Storage;

class ListsController extends Controller
{
    //
    use CommonTraits;
    public function fetch(Request $request)
    {
        $search = $request->search;
        $lists = Lists::with('csv')->where('user_id', Auth::id());
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny(['name'], "like", "%$search%");
            });
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function delete(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        Lists::find($request->id)->delete();
        return $this->sendSuccess('Deleted successfully!');
    }
    public function show(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $list = Lists::with(['csv'])->find($request->id);
        return $this->sendSuccess('Fetched successfully!', $list);
    }

    public function store(Request $request)
    {
        $validators = Validator($request->all(), [
            'name' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        if (!$request->id) {
            $request->merge(['user_id' => Auth::id()]);
            Lists::create($request->all());
            return $this->sendSuccess('List created successfully!');
        } else {
            Lists::find($request->id)->update($request->all());
            return $this->sendSuccess('List updated successfully!');
        }
    }
    public function count()
    {
        $count = Lists::where('user_id', Auth::id())->count();
        return $this->sendSuccess('List count!', $count);
    }


    public function pagination(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:lists,id',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $rows = Subscriber::where('list_id', $request->id)
            ->paginate($request->records);
        return $this->sendSuccess('Lists fetched successfully!', $rows);
    }

    public function removeCsv(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        Lists::find($request->id);
        Csv::where('list_id', $request->id)->delete();
        Subscriber::where('list_id', $request->id)->delete();
        return $this->sendSuccess('CSV removed successfully!');
    }
    public function csvHeaders(Request $request)
    {
        $validators = Validator($request->all(), [
            'csv' => 'required|mimes:csv,txt',
        ]);

        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $file = $request->file('csv');
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0);
        $headers = $csv->getHeader();
        return $this->sendSuccess('Headers fetched successfully!', $headers);
    }
    public function readSheet(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
            'sheet' => 'required',
            'spreadsheet' => 'required',
            'mapping' => 'required',
            'mappings' => 'required',
        ], [
            'mapping.required' => 'Email mapping column required',
            'mappings.required' => 'CSV columns not found!'
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $sheetId = $request->input('sheet');
        $spreadsheetId = $request->spreadsheet['id'];
        $user = Auth::user();
        $googleSheet = GoogleSheet::where('user_id', $user->id)->first();
        $googleSheetController = new GoogleSheetController();
        $googleSheetController->authentication($googleSheet);

        try {
            $accessToken = $googleSheet->google_access_token;
            $httpClient = new \GuzzleHttp\Client();
            $csvUrl = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=csv&gid={$sheetId}";
            $response = $httpClient->get($csvUrl, [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Accept' => 'text/csv',
                ],
            ]);
            $content = $response->getBody()->getContents();

            $fileName = time() . '-' . $sheetId . '.csv';
            Storage::disk('public')->put("csv/" . $fileName, $content);
            $filePath = '/storage/csv/' . $fileName;


            $fileStoragePath = storage_path('app/public/csv/' . $fileName);
            $csv = Reader::createFromPath($fileStoragePath, 'r');
            $csv->setHeaderOffset(0);
            $headers = $csv->getHeader();
            $records = iterator_to_array($csv->getRecords());
            $count = count($records);
            $list = Lists::find($request->id);
            $headerJson = json_encode(array_combine(range(1, count($headers)), $headers));


            $csvRecord = Csv::create([
                'list_id' => $list->id,
                'file' => $filePath,
                'type' => CSV::TYPE_GOOGLE,
                'status' => CSV::STATUS_DRAFT,
                'headers' => $headerJson,
                'mapping' => $request->mapping,
                'rows' => $count,
                'uploaded' => 0,
                'spreadsheet_id' => $spreadsheetId,
                'sheet_id' => $sheetId,
            ]);
            $chunkSize = 1000;
            $chunks = array_chunk($records, $chunkSize);
            foreach ($chunks as $chunk) {
                $recordsToInsert = [];
                foreach ($chunk as $row) {
                    $leadData = ['list_id' => $list->id, 'csv_id' => $csvRecord->id];
                    foreach (array_values($row) as $index => $value) {
                        if ($index < Subscriber::COLUMNS) {
                            $cleanedValue = trim(mb_convert_encoding($value, 'UTF-8', 'UTF-8'));
                            $columnKey = 'column_' . ($index + 1);
                            $leadData[$columnKey] = $cleanedValue;
                        }
                        $now = now();
                        $leadData['created_at'] = $now;
                        $leadData['updated_at'] = $now;
                    }
                    $recordsToInsert[] = $leadData;
                }
                Subscriber::insert($recordsToInsert);
            }
            $csvRecord->update([
                'uploaded' => $count,
                'status' => Csv::STATUS_COMPLETED,
            ]);

            return $this->sendSuccess('Google sheet processed successfully!');
        } catch
        (Exception $e) {
            return $this->sendError('Something went wrong!', 421);
        }
    }
    public function Csv(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
            'mapping' => 'required',
            'mappings' => 'required',
            'csv' => 'required|mimes:csv,txt',
        ], [
            'mapping.required' => 'Email mapping column required',
            'mappings.required' => 'CSV columns not found!'
        ]);

        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $file = $request->file('csv');
        $list = Lists::findOrFail($request->id);
        $filePath = Infinity::upload($file, Infinity::_PUBLIC_CSV);
        $fileStoragePath = storage_path('app/public' . str_replace('storage', '', $filePath));
        $csv = Reader::createFromPath($fileStoragePath, 'r');
        $csv->setHeaderOffset(0);
        $headers = $csv->getHeader();
        $headerJson = json_encode(array_combine(range(1, count($headers)), $headers));

        $records = iterator_to_array($csv->getRecords());
        $count = count($records);


        $csvRecord = Csv::create([
            'list_id' => $list->id,
            'file' => $filePath,
            'type' => CSV::TYPE_CSV,
            'status' => CSV::STATUS_DRAFT,
            'headers' => $headerJson,
            'mapping' => $request->mapping,
            'rows' => $count,
            'uploaded' => 0,
        ]);

        $chunkSize = 1000;
        $chunks = array_chunk($records, $chunkSize);
        foreach ($chunks as $chunk) {
            $recordsToInsert = [];
            foreach ($chunk as $row) {
                $leadData = ['list_id' => $list->id, 'csv_id' => $csvRecord->id];
                foreach (array_values($row) as $index => $value) {
                    if ($index < Subscriber::COLUMNS) {
                        $cleanedValue = trim(mb_convert_encoding($value, 'UTF-8', 'UTF-8'));
                        $columnKey = 'column_' . ($index + 1);
                        $leadData[$columnKey] = $cleanedValue;
                    }
                    $now = now();
                    $leadData['created_at'] = $now;
                    $leadData['updated_at'] = $now;
                }
                $recordsToInsert[] = $leadData;
            }
            Subscriber::insert($recordsToInsert);
        }
        $csvRecord->update([
            'uploaded' => $count,
            'status' => Csv::STATUS_COMPLETED,
        ]);
        return $this->sendSuccess('CSV uploaded and processed!');
    }
    public function googleHeaders(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
            'sheet' => 'required',
            'spreadsheet' => 'required',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $sheetId = $request->input('sheet');
        $spreadsheetId = $request->spreadsheet['id'];
        $user = Auth::user();
        $googleSheet = GoogleSheet::where('user_id', $user->id)->first();
        $googleSheetController = new GoogleSheetController();
        $googleSheetController->authentication($googleSheet);
        $googleSheet = GoogleSheet::where('user_id', $user->id)->first();

        try {
            $accessToken = $googleSheet->google_access_token;
            $httpClient = new \GuzzleHttp\Client();
            $csvUrl = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=csv&gid={$sheetId}";
            $response = $httpClient->get($csvUrl, [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Accept' => 'text/csv',
                ],
            ]);
            $content = $response->getBody()->getContents();
            $csv = Reader::createFromString($content);
            $csv->setHeaderOffset(0);
            $headers = $csv->getHeader();
            return $this->sendSuccess('Headers fetched successfully!', $headers);
        } catch
        (Exception $e) {
            return $this->sendError('Something went wrong! .'. $e->getMessage(), 421);
        }
    }

    public function progress(Request $request)
    {
        $request->validate(['id' => 'required|exists:csvs,id']);
        $csv = Csv::find($request->id);
        return response()->json([
            'uploaded' => $csv->uploaded,
            'rows' => $csv->rows,
            'status' => $csv->status,
            'progress' => $csv->rows > 0 ? round(($csv->uploaded / $csv->rows) * 100) : 0
        ]);
    }
}
