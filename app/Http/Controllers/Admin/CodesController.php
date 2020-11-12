<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCodeRequest;
use App\Http\Requests\StoreCodeRequest;
use App\Http\Requests\UpdateCodeRequest;
use App\Models\Code;
use App\Models\Coupon;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CodesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('code_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $codes = Code::all();

        return view('admin.codes.index', compact('codes'));
    }

    public function create()
    {
        abort_if(Gate::denies('code_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $coupons = Coupon::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $reserved_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $purchased_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.codes.create', compact('coupons', 'reserved_bies', 'purchased_bies'));
    }

    public function store(StoreCodeRequest $request)
    {
        $code = Code::create($request->all());

        return redirect()->route('admin.codes.index');
    }

    public function edit(Code $code)
    {
        abort_if(Gate::denies('code_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $coupons = Coupon::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $reserved_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $purchased_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $code->load('coupon', 'reserved_by', 'purchased_by');

        return view('admin.codes.edit', compact('coupons', 'reserved_bies', 'purchased_bies', 'code'));
    }

    public function update(UpdateCodeRequest $request, Code $code)
    {
        $code->update($request->all());

        return redirect()->route('admin.codes.index');
    }

    public function show(Code $code)
    {
        abort_if(Gate::denies('code_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $code->load('coupon', 'reserved_by', 'purchased_by');

        return view('admin.codes.show', compact('code'));
    }

    public function destroy(Code $code)
    {
        abort_if(Gate::denies('code_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $code->delete();

        return back();
    }

    public function massDestroy(MassDestroyCodeRequest $request)
    {
        Code::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
