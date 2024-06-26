<?php

namespace ManoCode\Page\Services;

use ManoCode\Page\Models\PageAcces;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 访问日志
 *
 * @method PageAcces getModel()
 * @method PageAcces|\Illuminate\Database\Query\Builder query()
 */
class PageAccesService extends AdminService
{
	protected string $modelName = PageAcces::class;
}