<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetStoreRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return ['customer_id'=>['required','exists:customers,id'],
            'code'=>['required','string','max:64','unique:assets,code'],
            'name'=>['required','string','max:120'],
            'lat'=>['nullable','numeric','between:-90,90'],
            'lng'=>['nullable','numeric','between:-180,180']];
    }
}
