<?php

namespace App\DataTables\Visa;

use App\Models\Visa\Membership;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MembershipDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', fn (Membership $membership) => $membership->created_at->format('M Y, d'))
            ->editColumn('start_date', fn (Membership $membership) => optional($membership->start_date)->format('Y-m-d'))
            ->editColumn('end_date', fn (Membership $membership) => optional($membership->end_date)->format('Y-m-d'))
            ->editColumn('plan_type', fn (Membership $membership) => Str::headline($membership->plan_type))
            ->editColumn('status', fn (Membership $membership) => Str::headline($membership->status))
            ->addColumn('client_name', fn (Membership $membership) => $membership->client?->name ?? '—')
            ->addColumn('action', 'dashboard.visa.memberships.action')
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(Membership $model): QueryBuilder
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
            Column::make('plan_type'),
            Column::make('discount_percent'),
            Column::make('points_balance'),
            Column::make('status'),
            Column::make('start_date'),
            Column::make('end_date'),
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
        return 'Membership_' . date('YmdHis');
    }
}
