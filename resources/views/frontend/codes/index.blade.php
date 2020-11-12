@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @can('code_create')
                <div style="margin-bottom: 10px;" class="row">
                    <div class="col-lg-12">
                        <a class="btn btn-success" href="{{ route('frontend.codes.create') }}">
                            {{ trans('global.add') }} {{ trans('cruds.code.title_singular') }}
                        </a>
                    </div>
                </div>
            @endcan
            <div class="card">
                <div class="card-header">
                    {{ trans('cruds.code.title_singular') }} {{ trans('global.list') }}
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Code">
                            <thead>
                                <tr>
                                    <th>
                                        {{ trans('cruds.code.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.code.fields.coupon') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.code.fields.code') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.code.fields.reserved_at') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.code.fields.reserved_by') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.code.fields.purchased_at') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.code.fields.purchased_by') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($codes as $key => $code)
                                    <tr data-entry-id="{{ $code->id }}">
                                        <td>
                                            {{ $code->id ?? '' }}
                                        </td>
                                        <td>
                                            {{ $code->coupon->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $code->code ?? '' }}
                                        </td>
                                        <td>
                                            {{ $code->reserved_at ?? '' }}
                                        </td>
                                        <td>
                                            {{ $code->reserved_by->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $code->purchased_at ?? '' }}
                                        </td>
                                        <td>
                                            {{ $code->purchased_by->name ?? '' }}
                                        </td>
                                        <td>
                                            @can('code_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('frontend.codes.show', $code->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('code_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('frontend.codes.edit', $code->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('code_delete')
                                                <form action="{{ route('frontend.codes.destroy', $code->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                                </form>
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
@can('code_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('frontend.codes.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-Code:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection