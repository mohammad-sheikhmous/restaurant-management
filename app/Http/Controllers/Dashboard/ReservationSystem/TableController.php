<?php

namespace App\Http\Controllers\Dashboard\ReservationSystem;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationSystem\TableRequest;
use App\Http\Resources\Resource\TableResource;
use App\Models\Table;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::with(['type'])->withExists(['reservations as active_reservation_exists' => function ($query) {
            return $query->where('status', 'active');
        }])->latest()->paginate(\request()->limit ?? 10);

        $page = numberToOrdinalWord(request()->page ?? 1);

        return dataJson('tables', (TableResource::collection($tables))->response()->getData(true),
            "All tables for {$page} page.");
    }

    public function show($id)
    {
        if (request()->for == 'showing')
            $table = Table::whereId($id)->with(['type'])
                ->select('tables.*',
                    DB::raw("(
                    select concat(res_date, ' ',
                        time_format(res_time, '%H:%i'),
                        '-',
                        time_format(addtime(res_time, res_duration), '%H:%i')
                    )
                    from reservations r join reservation_table rt
                    on r.id = rt.reservation_id
                    where rt.table_id = tables.id and r.status = 'accepted'
                    order by res_date, res_time
                    limit 1
                ) next_reservation_date"),
                    DB::raw("(
                    select concat(res_date, ' ',
                        time_format(res_time, '%H:%i'),
                        '-',
                        time_format(addtime(res_time, res_duration), '%H:%i')
                    )
                    from reservations r join reservation_table rt
                    on r.id = rt.reservation_id
                    where rt.table_id = tables.id and r.status = 'accepted'
                    order by res_date desc, res_time desc
                    limit 1
                ) last_reservation_date"))
                ->withCount(['reservations as curr_reservations_count' => function ($query) {
                    return $query->where('status', 'accepted');
                }, 'reservations as prev_reservations_count' => function ($query) {
                    return $query->where('status', 'completed');
                }])
                ->withExists(['reservations as active_reservation_exists' => function ($query) {
                    return $query->where('status', 'active');
                }])
                ->first();
        else
            $table = Table::with('type:id,name')->find($id);

        if (!isset($table))
            return messageJson('Table not found.!', false, 404);

        return dataJson('table', TableResource::make($table), "Table with id: $id returned.");
    }

    public function store(TableRequest $request)
    {
        Table::create($request->all());

        return messageJson('New table created', true, 201);
    }

    public function update(TableRequest $request, $id)
    {
        $table = Table::find($id);
        if (!$table)
            return messageJson('Table not found.!', false, 404);

        $table->update($request->all());

        return messageJson('The table updated successfully.');
    }

    public function destroy($id)
    {
        $table = Table::find($id);
        if (!$table)
            return messageJson('Table not found.!', false, 404);

        $table->delete();

        return messageJson('The table deleted successfully.');
    }

    public function changeStatus($id)
    {
        $table = Table::find($id);
        if (!$table)
            return messageJson('Table not found.!', false, 404);

        $table->update(['activation' => $table->activation == 'active' ? 'inactive' : 'active']);

        return messageJson('The table status changed successfully.');
    }
}
