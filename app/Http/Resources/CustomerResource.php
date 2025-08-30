<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource {
    public function toArray($req){ return ['id'=>$this->id,'name'=>$this->name,'phone'=>$this->phone,'createdAt'=>$this->created_at]; }
}
