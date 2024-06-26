<?php

namespace ManoCode\Page\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Slowlyo\OwlAdmin\Models\BaseModel;

class PageDesign extends BaseModel
{
    use HasTimestamps;
    protected $table='page_design';

    protected $casts = [
        'schema' => 'json',
    ];
}
