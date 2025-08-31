<?php

namespace App\Jobs;

use App\Models\Attachment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ScanAttachment implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public int $attachmentId) {}

    public function handle(): void
    {
        $att = Attachment::find($this->attachmentId);
        if (!$att) return;
        $meta = $att->meta ?? [];
        $meta['scanned'] = true;
        $att->meta = $meta;
        $att->save();
    }
}
