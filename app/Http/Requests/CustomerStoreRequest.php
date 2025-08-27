<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return ['name'=>['required','string','max:120'],'phone'=>['nullable','string','max:32'],
            'email'=>['nullable','email','max:120'],'address'=>['nullable','string','max:255']];
    }
}
