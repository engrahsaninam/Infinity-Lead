<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Column;
use App\Models\Lead;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ColumnController extends Controller
{
    use CommonTraits;
    public function fetch(Request $request)
    {
        $userId = Auth::id();
        $search = $request->search;
        $filter = $request->filter;
        Column::firstOrCreate(
            ['user_id' => $userId, 'primary' => 1],
            ['name' => 'Leads']
        );
        $columns = Column::with([
            'leads.chat',
            'leads' => function ($query) use ($search, $filter) {
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->whereAny(['first_name', 'last_name', 'phone', 'email'], 'like', "%$search%");
                    });
                }
                match ($filter) {
                    'contacted' => $query->where('status', Lead::STATUS_CONTACTED),
                    'not-contacted' => $query->where('status', Lead::STATUS_NOT_CONTACTED),
                    'responded' => $query->whereExists(function ($sub) {
                            $sub->select(DB::raw(1))
                            ->from('chats')
                            ->whereColumn('chats.lead_id', 'leads.id')
                            ->whereColumn('chats.from', 'leads.email');
                        }),
                    'not-responded' => $query->whereNotExists(function ($sub) {
                            $sub->select(DB::raw(1))
                            ->from('chats')
                            ->whereColumn('chats.lead_id', 'leads.id')
                            ->whereColumn('chats.from', 'leads.email');
                        }),
                    default => null
                };
            }
        ])
            ->where('user_id', $userId)
            ->get();

        return $this->sendSuccess('Columns fetched successfully!', $columns);
    }



    public function create(Request $request)
    {
        $validators = Validator($request->all(), [
            'name' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        Column::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
        ]);
        return $this->sendSuccess('Column created successfully!');
    }
    public function update(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'color' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        Column::find($request->id)->update($request->all());
        return $this->sendSuccess('Column updated successfully!');
    }

    public function delete(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $column = Column::find($request->id);
        if ($column->primary === 0) {
            $column->delete();
            return $this->sendSuccess('Column deleted successfully!');
        }
        return $this->sendError('Primary column can not be deleted!', 421);
    }
}
