<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Website;
use App\Traits\CommonTraits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class WebsiteController extends Controller
{
    use CommonTraits;
    public function fetch(Request $request)
    {
        $search = $request->search;
        $lists = Website::query();
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny(['name', 'domain', 'format'], "like", "%$search%");
            });
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function count()
    {
        $count = Website::count();
        return $this->sendSuccess('Websites count!', $count);
    }
    public function store(Request $request)
    {
        $validators = Validator($request->all(), [
            'name' => 'required',
            'domain' => 'required',
            'format' => 'required|in:' . implode(',', Website::FORMATS),
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        Website::create($request->all());
        return $this->sendSuccess('Website successfully added !');
    }
    public function update(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:websites,id',
            'name' => 'required',
            'domain' => 'required',
            'format' => 'required|in:' . implode(',', Website::FORMATS),
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        Website::find($request->id)->update($request->all());
        return $this->sendSuccess('Website successfully updated !');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:websites,id',
        ]);
        Website::whereIn('id', $request->ids)->delete();
        return $this->sendSuccess('Websites successfully deleted!');
    }
    public function parseCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
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
            'file' => 'required|file|mimes:csv,txt',
            'website' => 'required|string',
            'domain' => 'required|string',
            'format' => 'required|string',
        ]);

        $csv = Reader::createFromPath($request->file('file')->getRealPath(), 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        $batchSize = 1000;
        $batch = [];

        DB::beginTransaction();

        try {
            foreach ($records as $row) {
                if (!empty($row[$request->website])) {
                    $format = trim($row[$request->format]);
                    $batch[] = [
                        'domain' => trim($row[$request->domain]),
                        'name' => trim($row[$request->website]),
                        'format' => in_array($format, Website::FORMATS) ? $format : 'f.last',
                    ];
                }
                if (count($batch) === $batchSize) {
                    Website::insert($batch);
                    $batch = [];
                }
            }
            if (!empty($batch)) {
                Website::insert($batch);
            }
            DB::commit();
            return $this->sendSuccess('Websites & Domains Successfully Imported!');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }
    //
}
