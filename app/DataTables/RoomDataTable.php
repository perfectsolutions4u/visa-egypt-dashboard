<?php

namespace App\DataTables;

use App\Models\Room;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class RoomDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', fn(Room $room) => $room->created_at->format('M Y, d'))
            ->editColumn('hotel', fn(Room $room) => $room->hotel?->name)
            ->addColumn('action', 'dashboard.rooms.action')
            ->editColumn('name', fn(Room $room) => $room->name)
            ->editColumn('extra_bed_available', function(Room $room) {
                return $room->extra_bed_available ? 
                    '<span class="badge bg-success">Available</span>' : 
                    '<span class="badge bg-secondary">Not Available</span>';
            })
            ->editColumn('extra_bed_price', function(Room $room) {
                return $room->extra_bed_available ? '$' . number_format($room->extra_bed_price, 2) : '-';
            })
            ->editColumn('max_extra_beds', function(Room $room) {
                return $room->extra_bed_available ? $room->max_extra_beds : '-';
            })
            ->editColumn('total_capacity', function(Room $room) {
                return $room->max_capacity + ($room->extra_bed_available ? $room->max_extra_beds : 0);
            })
            ->filterColumn('hotel', function ($query, $keyword) {
                $query->whereHas('hotel', function ($query) use ($keyword) {
                    $query->whereTranslationLike('name', "%{$keyword}%");
                });
            })
            ->filterTranslatedColumn('name')
            ->orderColumn('name', fn($query, $dir) => $query->orderByTranslation('name', $dir))
            ->setRowId('id')
            ->rawColumns(['action', 'extra_bed_available']);
    }

    public function query(Room $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('data-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Blfrtip')
            //->dom('Bfrtip')
            ->orderBy(0)
            ->selectStyleSingle()
            ->buttons(array_reverse([
                Button::make('excel')->className('btn btn-sm float-right ms-1 p-1 text-light btn-success'),
                Button::make('csv')->className('btn btn-sm float-right ms-1 p-1 text-light btn-primary'),
                Button::make('print')->className('btn btn-sm float-right ms-1 p-1 text-light btn-secondary'),
                Button::make('reload')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info')
            ]));
    }

    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('name'),
            Column::make('hotel'),
            Column::make('slug'),
            Column::make('enabled'),
            Column::make('bed_count')->title('Beds'),
            Column::make('room_type'),
            Column::make('max_capacity')->title('Base Capacity'),
            Column::make('total_capacity')->title('Total Capacity'),
            Column::make('night_price')->title('Base Price'),
            Column::make('extra_bed_available')->title('Extra Beds'),
            Column::make('extra_bed_price')->title('Extra Bed Price'),
            Column::make('max_extra_beds')->title('Max Extra Beds'),
            Column::make('created_at'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Room_' . date('YmdHis');
    }
}
