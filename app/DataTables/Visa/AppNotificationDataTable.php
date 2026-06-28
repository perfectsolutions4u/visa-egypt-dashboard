<?php

namespace App\DataTables\Visa;

use App\Models\Visa\AppNotification;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AppNotificationDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', fn (AppNotification $notification) => $notification->created_at->format('M Y, d'))
            ->editColumn('read_at', fn (AppNotification $notification) => optional($notification->read_at)->format('Y-m-d H:i') ?? 'Unread')
            ->addColumn('client_name', fn (AppNotification $notification) => $notification->client?->name ?? 'All Clients')
            ->addColumn('action', 'dashboard.visa.app-notifications.action')
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(AppNotification $model): QueryBuilder
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
            Column::make('title'),
            Column::make('client_name'),
            Column::make('type'),
            Column::make('read_at'),
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
        return 'AppNotification_' . date('YmdHis');
    }
}
