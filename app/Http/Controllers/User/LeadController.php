<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Column;
use App\Models\Lead;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use League\Csv\Reader;

class LeadController extends Controller
{
    use CommonTraits;
    public function create(Request $request)
    {
        $validators = Validator($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $request->merge(['user_id' => auth()->id()]);
        $column = Column::firstOrCreate(
            ['user_id' => Auth::id(), 'primary' => 1],
            ['name' => 'Leads']
        );
        $request->merge(['column_id' => $column->id]);
        Lead::create($request->all());
        return $this->sendSuccess('Lead created successfully!');
    }
    public function update(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:leads,id',
            'email' => 'required|email',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        Lead::find($request->id)->update($request->all());
        return $this->sendSuccess('Lead updated successfully!');
    }

    public function clearNotification(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:leads,id',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $lead = Lead::find($request->id);
        foreach ($lead->notifications as $notification) {
            $notification->delete();
        }
        return $this->sendSuccess('Notifications removed successfully!');
    }


    public function fetch(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:leads,id',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $lead = Lead::with('chat.account', 'sender')->find($request->id);
        return $this->sendSuccess('Lead fetched successfully!', $lead);
    }
    public function index(Request $request)
    {
        $lead = Lead::find($request->id);
        $lead->update(
            ['column_id' => $request['column']['id'] ?? $lead->column_id]
        );
        return $this->sendSuccess('Index updated successfully!', $lead);

    }


    public function parseCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120'
        ]);

        $csv = Reader::createFromString(file_get_contents($request->file('file')));
        $csv->setHeaderOffset(0);
        $headers = $csv->getHeader();

        $originalColumns = [
            'email' => 'Email',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'job_title' => 'Job Title',
            'phone' => 'Phone',
            'city' => 'City',
            'company' => 'Company',
            'website' => 'Website Url',
            'linkedin' => 'LinkedIn Url',
        ];
        $suggestions = [];
        foreach ($originalColumns as $key => $label) {
            foreach ($headers as $header) {
                if (Str::lower($header) === Str::lower($label)) {
                    $suggestions[$key] = $header;
                    break;
                }
            }
        }

        return response()->json([
            'headers' => $headers,
            'columns' => $originalColumns,
            'suggestions' => $suggestions,
        ]);
    }



    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
            'mapping' => 'required',
            'column_id' => 'required',
        ]);

        $mapping = $request->input('mapping');
        $mapping = json_decode($mapping, true);
        if (empty($mapping['email'])) {
            return $this->sendError('Email column must be mapped.', 421);
        }
        $csv = Reader::createFromString(file_get_contents($request->file('file')));
        $csv->setHeaderOffset(0);
        $records = iterator_to_array($csv->getRecords());
        $inserted = 0;
        foreach ($records as $row) {
            $leadData = [];
            $emailValue = $row[$mapping['email']] ?? null;
            if (!$emailValue || !filter_var($emailValue, FILTER_VALIDATE_EMAIL)) {
                Log::error("{$emailValue} not valid for import leads " . Auth::user()->name);
                continue;
            }
            $leadData['email'] = $emailValue;
            $leadData['user_id'] = auth()->id();
            $optionalFields = [
                'first_name',
                'middle_name',
                'last_name',
                'job_title',
                'phone',
                'city',
                'company',
                'website',
                'linkedin'
            ];
            foreach ($optionalFields as $field) {
                if (!empty($mapping[$field]) && isset($row[$mapping[$field]])) {
                    $value = $row[$mapping[$field]];
                    $leadData[$field] = $this->cleanString($value);
                }
            }
            $leadData['column_id'] = $request->column_id;
            $leadData['status'] = Lead::STATUS_NOT_CONTACTED;
            Lead::create($leadData);
            $inserted++;
        }
        return $this->sendSuccess("Import completed. $inserted leads added.");
    }
    function cleanString($value)
    {
        $value = preg_replace('/\x{00A0}/u', ' ', $value); // replace non-breaking space with regular space
        $value = preg_replace('/[^\x20-\x7E\x0A\x0D]/u', '', $value); // remove other non-printables
        return trim($value);
    }
    public function delete(Request $request)
    {
        foreach ($request->id as $id) {
            $campaign = Lead::find($id);
            $campaign->delete();
        }
        return $this->sendSuccess('Leads deleted successfully!');
    }
}
