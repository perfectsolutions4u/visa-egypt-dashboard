<?php

namespace App\DataTables\Visa;

use App\Models\Visa\Program;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProgramDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', fn (Program $program) => $program->created_at->format('M Y, d'))
            ->editColumn('is_active', fn (Program $program) => $program->is_active ? 'Yes' : 'No')
            ->editColumn('is_best_seller', fn (Program $program) => $program->is_best_seller ? 'Yes' : 'No')
            ->editColumn('starting_price', fn (Program $program) => number_format($program->starting_price, 2))
            ->addColumn('action', 'dashboard.visa.programs.action')
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(Program $model): QueryBuilder
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
            ->orderBy(0)
            ->selectStyleSingle()
            ->buttons(array_reverse([
                Button::make('excel')->className('btn btn-sm float-right ms-1 p-1 text-light btn-success'),
                Button::make('csv')->className('btn btn-sm float-right ms-1 p-1 text-light btn-primary'),
                Button::make('print')->className('btn btn-sm float-right ms-1 p-1 text-light btn-secondary'),
                Button::make('reload')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info'),
            ]));
    }

    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('name'),
            Column::make('slug'),
            Column::make('duration'),
            Column::make('starting_price'),
            Column::make('is_active'),
            Column::make('is_best_seller'),
            Column::make('sort_order'),
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
        return 'Program_' . date('YmdHis');
    }
}
