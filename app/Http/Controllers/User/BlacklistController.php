<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Blacklist;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class BlacklistController extends Controller
{
    //
    use CommonTraits;
    public function fetch(Request $request)
    {
        $search = $request->search;
        $lists = Blacklist::where('user_id', Auth::id());
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny(['email'], "like", "%$search%");
            });
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function count()
    {
        $count = Blacklist::where('user_id', Auth::id())->count();
        return $this->sendSuccess('Blacklist count!', $count);
    }
    public function store(Request $request)
    {
        $validators = Validator($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $request->merge(['user_id' => Auth::id()]);
        Blacklist::create($request->all());
        return $this->sendSuccess('Blacklist Email added successfully!');
    }
    public function delete(Request $request)
    {
        if ($request->all) {
            DB::table('blacklists')->truncate();
            return $this->sendSuccess('All Blacklist emails deleted successfully.');
        }
        $validators = Validator($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:blacklists,id',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        Blacklist::whereIn('id', $request->ids)->delete();
        return $this->sendSuccess('Blacklist emails deleted successfully.');
    }
    public function parseCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120'
        ]);

        $csv = Reader::createFromString(file_get_contents($request->file('file')));
        $csv->setHeaderOffset(0);
        $headers = $csv->getHeader();

        return response()->json([
            'headers' => $headers
        ]);
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
            'email_column' => 'required|string',
        ]);
        $csv = Reader::createFromString(file_get_contents($request->file('file')));
        $csv->setHeaderOffset(0);
        $records = iterator_to_array($csv->getRecords());
        $emails = [];
        $reasons = [];
        foreach ($records as $row) {
            if (!empty($row[$request->email_column])) {
                $emails[] = trim($row[$request->email_column]);
                if ($request->has('reason_column') && isset($row[$request->reason_column])) {
                    $reasons[] = trim($row[$request->reason_column]);
                } else {
                    $reasons[] = null;
                }
            }
        }
        $emails = array_unique($emails);
        foreach ($emails as $index => $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Blacklist::create([
                    'email' => $email,
                    'user_id' => Auth::id(),
                    'description' => $reasons[$index] ?? null,
                ]);
            }
        }
        return $this->sendSuccess('Blacklist Email added successfully!');
    }
}
