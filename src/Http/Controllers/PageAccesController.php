<?php

namespace ManoCode\Page\Http\Controllers;

use ManoCode\Page\Services\PageAccesService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 访问日志
 *
 * @property PageAccesService $service
 */
class PageAccesController extends AdminController
{
    protected string $serviceName = PageAccesService::class;

    public function list($api = null)
    {
        $crud = $this->baseCRUD()
            ->filterTogglable(false)
            ->headerToolbar([
//				$this->createButton('dialog'),
//				...$this->baseHeaderToolBar()
            ])
            ->columns([
                amis()->TableColumn('id', 'ID')->sortable(),
//				amis()->TableColumn('page_id', '页面'),
                amis()->TableColumn('ip', '访问IP'),
                amis()->TableColumn('user_agent', '用户代理'),
                amis()->TableColumn('origin', '来源'),
                amis()->TableColumn('created_at', admin_trans('admin.created_at'))->type('datetime')->sortable(),
                amis()->TableColumn('updated_at', admin_trans('admin.updated_at'))->type('datetime')->sortable(),
//				$this->rowActions('dialog')
            ]);
        if ($api != null) {
            $crud->api($api);
        }
        return $this->baseList($crud);
    }

    public function form($isEdit = false)
    {
        return $this->baseForm()->body([
        ]);
    }

    public function detail()
    {
        return $this->baseDetail()->body([
        ]);
    }
}
