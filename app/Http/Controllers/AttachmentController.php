<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function index(Request $r) {
        $r->validate(['entity'=>['required'],'id'=>['required','integer']]);
        return ['data'=> Attachment::where('entity_type',$r->string('entity'))
            ->where('entity_id',$r->integer('id'))->orderByDesc('id')->get()
            ->map(fn($a)=>[
                'id'=>$a->id,'name'=>$a->name,'size'=>$a->size,'url'=>Storage::disk('public')->url($a->path),
                'createdAt'=>$a->created_at
            ])];
    }

    public function store(\App\Http\Requests\AttachmentStoreRequest $r) {
        $file = $r->file('file');
        $path = $file->store('uploads/'.date('Y/m'), 'public');
        $att = Attachment::create([
            'entity_type'=>$r->string('entity'),
            'entity_id'=>$r->integer('id'),
            'name'=>$file->getClientOriginalName(),
            'path'=>$path,
            'size'=>$file->getSize(),
            'mime'=>$file->getMimeType(),
            'file'=>'required|file|max:5120|mimetypes:image/png,image/jpeg,application/pdf,application/zip',
            'entity'=>'required|in:workOrder,asset,customer',
            'id'=>'required|integer',
            'note'=>'nullable|string',
        ]);
        dispatch(new \App\Jobs\ScanAttachment($att->id));
        return response()->json(['id'=>$att->id,'name'=>$att->name,'size'=>$att->size,'url'=>Storage::url($att->path)],201);
    }
}
