<?php

namespace ManoCode\Page\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 访问日志
 */
class PageAcces extends Model
{
	use SoftDeletes;

	protected $table = 'page_access';
}