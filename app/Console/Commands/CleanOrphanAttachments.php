<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class CleanOrphanAttachments extends Command {
    protected $signature='cleanup:attachments';
    protected $description='Delete missing-file or orphan attachment records';
    public function handle(){
        $cnt=0;
        Attachment::chunk(500, function($rows) use (&$cnt){
            foreach($rows as $a){
                if(!Storage::disk('public')->exists($a->path)){
                    $a->delete(); $cnt++;
                }
            }
        });
        $this->info("Cleaned {$cnt} records");
    }
}
