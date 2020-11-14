<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPurchaseRequest;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Code;
use App\Models\Purchase;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PurchasesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('purchase_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $purchases = Purchase::availableForUser()->get();

        return view('frontend.purchases.index', compact('purchases'));
    }

    public function show($purchase)
    {
        abort_if(Gate::denies('purchase_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $item = Purchase::availableForUser()
            ->with('user', 'code')
            ->findOrFail($purchase);

        return view('frontend.purchases.show', ['purchase' => $item]);
    }
}
