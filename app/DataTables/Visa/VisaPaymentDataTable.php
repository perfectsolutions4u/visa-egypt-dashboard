<?php

namespace App\DataTables\Visa;

use App\Models\Visa\VisaPayment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class VisaPaymentDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', fn (VisaPayment $payment) => $payment->created_at->format('M Y, d'))
            ->editColumn('amount', fn (VisaPayment $payment) => number_format($payment->amount, 2) . ' ' . $payment->currency)
            ->editColumn('method', fn (VisaPayment $payment) => Str::headline($payment->method?->value ?? $payment->method))
            ->editColumn('status', fn (VisaPayment $payment) => Str::headline($payment->status?->value ?? $payment->status))
            ->addColumn('client_name', fn (VisaPayment $payment) => $payment->client?->name ?? '—')
            ->addColumn('action', 'dashboard.visa.visa-payments.action')
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(VisaPayment $model): QueryBuilder
    {
        return $model->newQuery()->with('client');
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
            Column::make('client_name'),
            Column::make('amount'),
            Column::make('method'),
            Column::make('status'),
            Column::make('gateway_reference'),
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
        return 'VisaPayment_' . date('YmdHis');
    }
}
