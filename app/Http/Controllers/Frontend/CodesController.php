<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCodeRequest;
use App\Http\Requests\StoreCodeRequest;
use App\Http\Requests\UpdateCodeRequest;
use App\Models\Code;
use App\Models\Coupon;
use App\Models\User;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CodesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('code_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $codes = Code::all();

        return view('frontend.codes.index', compact('codes'));
    }

    public function create()
    {
        abort_if(Gate::denies('code_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $coupons = Coupon::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $reserved_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $purchased_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('frontend.codes.create', compact('coupons', 'reserved_bies', 'purchased_bies'));
    }

    public function store(StoreCodeRequest $request)
    {
        $code = Code::create($request->all());

        return redirect()->route('frontend.codes.index');
    }

    public function edit(Code $code)
    {
        abort_if(Gate::denies('code_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $coupons = Coupon::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $reserved_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $purchased_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $code->load('coupon', 'reserved_by', 'purchased_by');

        return view('frontend.codes.edit', compact('coupons', 'reserved_bies', 'purchased_bies', 'code'));
    }

    public function update(UpdateCodeRequest $request, Code $code)
    {
        $code->update($request->all());

        return redirect()->route('frontend.codes.index');
    }

    public function show(Code $code)
    {
        abort_if(Gate::denies('code_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $code->load('coupon', 'reserved_by', 'purchased_by');

        return view('frontend.codes.show', compact('code'));
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

    public function purchase(Request $request, Code $code)
    {
        $user               = $request->user();
        $isReservationValid = $code->reserved_at ? Carbon::parse($code->reserved_at)->addMinutes(10)->isFuture() : false;

        if ($code->reserved_by_id != $user->id || !$isReservationValid) {
            return redirect()
                ->route('frontend.coupons.index')
                ->withErrors(['Sorry, you didn\'t purchase on time']);
        }

        $paymentMethod = $request->input('payment_method');
        $code->load('coupon');

        try {
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($paymentMethod);
            $user->charge($code->coupon->price * 100, $paymentMethod);

            $code->purchase()->create([
                'user_id' => $user->id,
                'price'   => $code->coupon->price,
            ]);

            $code->update([
                'reserved_at'     => null,
                'reserved_by'     => null,
                'purchased_at'    => now(),
                'purchased_by_id' => $user->id,
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }

        return redirect()
            ->route('frontend.coupons.index')
            ->with('message', 'The coupon has been purchased successfully. Code of the coupon is: ' . $code->code);
    }
}
