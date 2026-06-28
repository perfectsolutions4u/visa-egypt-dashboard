<?php

namespace App\DataTables\Visa;

use App\Models\Visa\MembershipTier;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MembershipPlanDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', fn (MembershipTier $plan) => $plan->created_at->format('M Y, d'))
            ->editColumn('price_usd', fn (MembershipTier $plan) => number_format($plan->price_usd, 2))
            ->editColumn('discount_percent', fn (MembershipTier $plan) => rtrim(rtrim(number_format($plan->discount_percent, 2), '0'), '.').'%')
            ->editColumn('is_active', fn (MembershipTier $plan) => $plan->is_active ? 'Yes' : 'No')
            ->editColumn('is_featured', fn (MembershipTier $plan) => $plan->is_featured ? 'Yes' : 'No')
            ->addColumn('special_offer', fn (MembershipTier $plan) => $plan->special_offer_text ?: '—')
            ->addColumn('action', 'dashboard.visa.membership-plans.action')
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(MembershipTier $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('sort_order')->orderBy('id');
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
            Column::make('slug'),
            Column::make('name'),
            Column::make('tagline'),
            Column::make('price_usd'),
            Column::make('sort_order'),
            Column::make('special_offer'),
            Column::make('is_featured'),
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
        return 'MembershipPlan_'.date('YmdHis');
    }
}
