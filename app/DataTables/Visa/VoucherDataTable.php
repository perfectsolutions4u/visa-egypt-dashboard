<?php

namespace App\DataTables\Visa;

use App\Models\Visa\Voucher;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class VoucherDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', fn (Voucher $voucher) => $voucher->created_at->format('M Y, d'))
            ->editColumn('valid_from', fn (Voucher $voucher) => optional($voucher->valid_from)->format('Y-m-d'))
            ->editColumn('valid_to', fn (Voucher $voucher) => optional($voucher->valid_to)->format('Y-m-d'))
            ->editColumn('discount_type', fn (Voucher $voucher) => Str::headline($voucher->discount_type?->value ?? $voucher->discount_type))
            ->editColumn('discount_value', function (Voucher $voucher) {
                if ($voucher->discount_type?->value === 'percentage') {
                    return rtrim(rtrim(number_format($voucher->discount_value, 2), '0'), '.') . '%';
                }

                return '$' . number_format($voucher->discount_value, 2);
            })
            ->editColumn('service_target', fn (Voucher $voucher) => $voucher->service_target
                ? Str::headline($voucher->service_target)
                : 'All Services')
            ->editColumn('client.name', fn (Voucher $voucher) => $voucher->client?->name ?? 'Any Client')
            ->editColumn('is_active', fn (Voucher $voucher) => $voucher->is_active ? 'Yes' : 'No')
            ->addColumn('action', 'dashboard.visa.vouchers.action')
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(Voucher $model): QueryBuilder
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
            Column::make('code'),
            Column::make('title'),
            Column::make('discount_type'),
            Column::make('discount_value'),
            Column::make('service_target'),
            Column::make('client.name'),
            Column::make('used_count'),
            Column::make('max_uses'),
            Column::make('is_active'),
            Column::make('valid_from'),
            Column::make('valid_to'),
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
        return 'Voucher_' . date('YmdHis');
    }
}
