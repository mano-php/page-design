<?php

namespace ManoCode\Page\Http\Controllers;

use ManoCode\Page\Services\PageDesignService;
use ManoCode\Page\Services\PageGroupService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 *
 */
class PagesGroupController extends AdminController
{
    protected string $serviceName = PageGroupService::class;

    public function list()
    {
        $crud = $this->baseCRUD()
            ->filterTogglable(false)
            ->headerToolbar([
                $this->createButton(true),
                ...$this->baseHeaderToolBar(),
            ])
            ->columns([
                amis()->TableColumn('id', 'ID'),
                amis()->TableColumn('name', '分组名称'),
                amis()->SelectControl('state_title', '状态')->type('tag'),
                amis()->TableColumn('updated_at', admin_trans('admin.created_at'))->type('datetime')->sortable(true),
                $this->rowActions([
                    $this->rowEditButton(true),
                    $this->rowDeleteButton(),
                ]),
            ]);

        return $this->baseList($crud);
    }

    public function form()
    {
        return $this->baseForm()->body([
            amis()->HiddenControl('id')->static(),
            amis()->TreeSelectControl('parent_id', admin_trans('上级分组'))->source('/page/get-tree-group?id=${id}')->required(),
            amis()->SelectControl('state', admin_trans('状态'))->options([
                [
                    'label' => '启用',
                    'value' => 'enable'
                ],
                [
                    'label' => '禁用',
                    'value' => 'disable'
                ],
            ])->required(),
            amis()->TextControl('name', admin_trans('名称'))->required(),
        ]);
    }


    public function detail($id)
    {
        return $this->baseDetail()->body([]);
    }

    public function getTreeGroup()
    {
        return $this->response()->success(['options' => PageGroupService::deptTree('顶级分组',request()->input('id'))]);
    }
}
