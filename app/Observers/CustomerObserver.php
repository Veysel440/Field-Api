<?php

namespace App\Observers;

use App\Models\Customer;
use App\Support\CacheVersion;

class CustomerObserver {
    public function created(Customer $m){ CacheVersion::bump('customers'); }
    public function updated(Customer $m){ CacheVersion::bump('customers'); }
    public function deleted(Customer $m){ CacheVersion::bump('customers'); }
}
