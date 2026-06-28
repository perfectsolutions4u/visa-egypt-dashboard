<?php

namespace App\DataTables\Visa;

use App\Models\Visa\Offer;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class OfferDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', fn (Offer $offer) => $offer->created_at->format('M Y, d'))
            ->editColumn('active_from', fn (Offer $offer) => optional($offer->active_from)->format('Y-m-d H:i'))
            ->editColumn('active_to', fn (Offer $offer) => optional($offer->active_to)->format('Y-m-d H:i'))
            ->editColumn('service_target', fn (Offer $offer) => Str::headline($offer->service_target?->value ?? $offer->service_target))
            ->editColumn('is_active', fn (Offer $offer) => $offer->is_active ? 'Yes' : 'No')
            ->addColumn('action', 'dashboard.visa.offers.action')
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(Offer $model): QueryBuilder
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
            Column::make('service_target'),
            Column::make('discount_percent'),
            Column::make('is_active'),
            Column::make('active_from'),
            Column::make('active_to'),
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
        return 'Offer_' . date('YmdHis');
    }
}
