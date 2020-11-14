@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ trans('cruds.purchase.title_singular') }} {{ trans('global.list') }}
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Purchase">
                            <thead>
                                <tr>
                                    <th>
                                        {{ trans('cruds.purchase.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.purchase.fields.user') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.purchase.fields.code') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.purchase.fields.price') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchases as $key => $purchase)
                                    <tr data-entry-id="{{ $purchase->id }}">
                                        <td>
                                            {{ $purchase->id ?? '' }}
                                        </td>
                                        <td>
                                            {{ $purchase->user->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $purchase->code->code ?? '' }}
                                        </td>
                                        <td>
                                            {{ $purchase->price ?? '' }}
                                        </td>
                                        <td>
                                            @can('purchase_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('frontend.purchases.show', $purchase->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-Purchase:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection