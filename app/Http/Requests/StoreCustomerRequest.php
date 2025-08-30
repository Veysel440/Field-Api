<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest {
    public function authorize(){ return $this->user()?->hasAnyRole(['admin','tech']) ?? false; }
    public function rules(){ return ['name'=>'required|string','phone'=>'nullable|string']; }
}
