<?php

namespace App\DataTables\Visa;

use App\Models\Visa\AdditionalService;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AdditionalServiceDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', fn (AdditionalService $service) => $service->created_at->format('M Y, d'))
            ->editColumn('price', fn (AdditionalService $service) => ($service->price_from ? 'From ' : '')
                . number_format($service->price, 2) . ' ' . $service->currency)
            ->editColumn('accent_color', fn (AdditionalService $service) => '<span class="badge" style="background-color: '
                . e($service->accent_color) . '">' . e($service->accent_color) . '</span>')
            ->editColumn('is_active', fn (AdditionalService $service) => $service->is_active ? 'Yes' : 'No')
            ->addColumn('action', 'dashboard.visa.additional-services.action')
            ->setRowId('id')
            ->rawColumns(['accent_color', 'action']);
    }

    public function query(AdditionalService $model): QueryBuilder
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
            Column::make('title'),
            Column::make('price'),
            Column::make('icon'),
            Column::make('accent_color'),
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
        return 'AdditionalService_' . date('YmdHis');
    }
}
