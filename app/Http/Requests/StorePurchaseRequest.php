<?php

namespace App\Http\Requests;

use App\Models\Purchase;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StorePurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('purchase_create');
    }

    public function rules()
    {
        return [
            'user_id' => [
                'required',
                'integer',
            ],
            'code_id' => [
                'required',
                'integer',
            ],
            'price'   => [
                'required',
            ],
        ];
    }
}
