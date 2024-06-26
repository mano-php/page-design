<?php

namespace ManoCode\Page\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Slowlyo\OwlAdmin\Models\BaseModel;

class PageGroup extends BaseModel
{
    use HasTimestamps;

    protected $table = 'page_group';
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
