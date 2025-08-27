<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChecklistTemplateStoreRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return ['title'=>['required','string','max:120'], 'items'=>['required','array','min:1'], 'items.*'=>['string','max:200']];
    }
}
