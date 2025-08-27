<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkOrderStoreRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'customer_id'=>['required','exists:customers,id'],
            'asset_id'=>['nullable','exists:assets,id'],
            'code'=>['required','string','max:64','unique:work_orders,code'],
            'title'=>['required','string','max:200'],
            'status'=>['in:open,in_progress,done'],
            'lat'=>['nullable','numeric','between:-90,90'],
            'lng'=>['nullable','numeric','between:-180,180'],
        ];
    }
}
