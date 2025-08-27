<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentStoreRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'entity'=>['required','in:workOrder,asset,customer'],
            'id'=>['required','integer'],
            'file'=>['required','file','max:5120','mimetypes:image/png,image/jpeg,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        ];
    }
}
