<?php

namespace App\DataTables\Visa;

use App\Models\Visa\VisaEligibleNationality;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class VisaEligibleNationalityDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('is_active', fn (VisaEligibleNationality $row) => $row->is_active ? 'Yes' : 'No')
            ->editColumn('created_at', fn (VisaEligibleNationality $row) => optional($row->created_at)->format('M d, Y'))
            ->addColumn('action', 'dashboard.visa.visa-nationalities.action')
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(VisaEligibleNationality $model): QueryBuilder
    {
        return $model->newQuery()->ordered();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('data-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Blfrtip')
            ->orderBy(4)
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
            Column::make('code'),
            Column::make('aliases'),
            Column::make('sort_order'),
            Column::make('is_active'),
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
        return 'VisaEligibleNationalities_' . date('YmdHis');
    }
}
