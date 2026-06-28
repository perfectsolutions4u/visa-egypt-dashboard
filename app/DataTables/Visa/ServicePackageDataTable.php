<?php

namespace App\DataTables\Visa;

use App\Models\Visa\ServicePackage;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ServicePackageDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', fn (ServicePackage $package) => $package->created_at->format('M Y, d'))
            ->editColumn('service_type', fn (ServicePackage $package) => $package->service_type?->label() ?? $package->service_type)
            ->editColumn('price', fn (ServicePackage $package) => number_format($package->price, 2))
            ->editColumn('is_popular', fn (ServicePackage $package) => $package->is_popular ? 'Yes' : 'No')
            ->editColumn('is_active', fn (ServicePackage $package) => $package->is_active ? 'Yes' : 'No')
            ->addColumn('action', 'dashboard.visa.service-packages.action')
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(ServicePackage $model): QueryBuilder
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
            Column::make('service_type'),
            Column::make('tier'),
            Column::make('name'),
            Column::make('price'),
            Column::make('is_popular'),
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
        return 'ServicePackage_' . date('YmdHis');
    }
}
