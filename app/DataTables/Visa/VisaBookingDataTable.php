<?php

namespace App\DataTables\Visa;

use App\Models\Visa\VisaBooking;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class VisaBookingDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('service_type', fn (VisaBooking $b) => $b->service_type?->label() ?? Str::headline($b->service_type))
            ->editColumn('status', function (VisaBooking $b) {
                $status = $b->status;
                $class = $status?->badgeClass() ?? 'badge-secondary';

                return "<span class='badge {$class}'>".($status?->label() ?? $b->status).'</span>';
            })
            ->editColumn('client_name', fn (VisaBooking $b) => $b->client?->name ?? '—')
            ->editColumn('travel_date', fn (VisaBooking $b) => optional($b->travel_date)->format('Y-m-d') ?? '—')
            ->editColumn('total_amount', fn (VisaBooking $b) => $b->total_amount ? '$'.number_format($b->total_amount, 2) : '—')
            ->editColumn('created_at', fn (VisaBooking $b) => $b->created_at->format('M d, Y'))
            ->filterColumn('client_name', function ($query, $keyword) {
                $query->whereHas('client', fn ($q) => $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%"));
            })
            ->addColumn('action', 'dashboard.visa.visa-bookings.action')
            ->setRowId('id')
            ->rawColumns(['action', 'status']);
    }

    public function query(VisaBooking $model): QueryBuilder
    {
        $query = $model->newQuery()->with('client');

        if ($service = request('service_type')) {
            $query->where('service_type', $service);
        }
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        if ($from = request('date_from')) {
            $query->whereDate('travel_date', '>=', $from);
        }
        if ($to = request('date_to')) {
            $query->whereDate('travel_date', '<=', $to);
        }

        return $query->latest();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('data-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Blfrtip')
            ->orderBy(0, 'desc')
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
            Column::make('booking_ref'),
            Column::make('client_name')->orderable(false),
            Column::make('service_type'),
            Column::make('travel_date'),
            Column::make('status'),
            Column::make('total_amount'),
            Column::make('created_at'),
            Column::computed('action')->exportable(false)->printable(false)->width(80),
        ];
    }
}
