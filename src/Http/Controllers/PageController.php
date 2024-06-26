<?php

namespace ManoCode\Page\Http\Controllers;

use Slowlyo\OwlAdmin\Controllers\AdminController;

class PageController extends AdminController
{
    public function index()
    {
        $page = $this->basePage()->body('Page Extension.');

        return $this->response()->success($page);
    }
}
