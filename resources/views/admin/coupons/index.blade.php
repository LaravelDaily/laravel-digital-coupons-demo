@extends('layouts.admin')
@section('content')
@can('coupon_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.coupons.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.coupon.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.coupon.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Coupon">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.coupon.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.coupon.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.coupon.fields.price') }}
                        </th>
                        <th>
                            {{ trans('cruds.coupon.fields.photo') }}
                        </th>
                        <th>
                            {{ trans('cruds.coupon.fields.total_codes') }}
                        </th>
                        <th>
                            {{ trans('cruds.coupon.fields.purchased_codes') }}
                        </th>
                        @can('coupon_create')
                            <th>
                                Add codes
                            </th>
                        @endcan
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coupons as $key => $coupon)
                        <tr data-entry-id="{{ $coupon->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $coupon->id ?? '' }}
                            </td>
                            <td>
                                {{ $coupon->name ?? '' }}
                            </td>
                            <td>
                                {{ $coupon->price ?? '' }}
                            </td>
                            <td>
                                @if($coupon->photo)
                                    <a href="{{ $coupon->photo->getUrl() }}" target="_blank" style="display: inline-block">
                                        <img src="{{ $coupon->photo->getUrl('thumb') }}">
                                    </a>
                                @endif
                            </td>
                            <td>
                                {{ $coupon->codes_count ?? '' }}
                            </td>
                            <td>
                                {{ $coupon->purchased_codes_count ?? '' }}
                            </td>
                            @can('coupon_create')
                                <td>
                                    <form action="{{ route('admin.coupons.generateCodes', $coupon->id) }}" method="POST" class="form-inline">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control" name="amount" placeholder="Amount" required>
                                        </div>
                                        <input type="submit" class="btn btn-xs btn-success ml-2" value="Generate">
                                    </form>
                                </td>
                            @endcan
                            <td>
                                @can('coupon_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.coupons.show', $coupon->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('coupon_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.coupons.edit', $coupon->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('coupon_delete')
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('coupon_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.coupons.massDestroy') }}",
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
  let table = $('.datatable-Coupon:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection