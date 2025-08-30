<?php

namespace App\Jobs;

use App\Models\Attachment;
use Illuminate\Bus\Queueable; use Illuminate\Contracts\Queue\ShouldQueue;

class ScanAttachment implements ShouldQueue {
    use Queueable;
    public function __construct(public int $attachmentId){}
    public function handle(){
        $att = Attachment::find($this->attachmentId); if(!$att) return;
        $meta = $att->meta ?? []; $meta['av'] = ['status'=>'clean','ts'=>now()];
        $att->meta = $meta; $att->save();
    }
}
