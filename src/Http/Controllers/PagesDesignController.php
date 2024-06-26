<?php

namespace ManoCode\Page\Http\Controllers;

use ManoCode\Page\Services\PageDesignService;
use ManoCode\Page\Services\PageGroupService;
use Slowlyo\OwlAdmin\Controllers\AdminController;
use Slowlyo\OwlAdmin\Renderers\Page;
use Slowlyo\OwlAdmin\Renderers\TreeControl;
use Slowlyo\OwlAdmin\Renderers\Wrapper;

class PagesDesignController extends AdminController
{
    protected string $serviceName = PageDesignService::class;

    public function list()
    {
        return Page::make()->css(
            [
                '.cxd-Page-aside' => [
                    "min-width" => '250px !important;',
                    "border-right" => '0!important;'
                ]
            ])->aside(
            Wrapper::make()
                ->className('cxd-Crud')
                ->body(
                    TreeControl::make()
                        ->options(PageGroupService::deptTree())
                        ->onlyLeaf(true)
                        ->name('department_tree')
                        ->inputClassName('no-border no-padder mt-1')
                        ->inputOnly(true)
                        ->selectFirst()
                        ->submitOnChange()->onEvent([
                            'change' => [
                                'weight' => '0',
                                'actions' => [
                                    [
                                        'componentId' => 'u:f9a819bafd1b',
                                        'ignoreError' => '',
                                        'actionType' => 'reload',
                                        'data' => [
                                            'group_id' => '${event.data.value}',
                                        ],
                                    ],
                                ],
                            ],
                        ])
                ))->body($this->baseCRUD()
            ->api($this->getListGetDataPath() . "&group_id=\${group_id}")
            ->filterTogglable(false)
            ->id('u:f9a819bafd1b')
            ->headerToolbar([
                $this->createButton(true, 'lg'),
                ...$this->baseHeaderToolBar()
            ])
            ->columns([
                amis()->TableColumn('id', 'ID')->sortable(),
                amis()->TableColumn('name', '页面名称')->searchable(),
                amis()->TableColumn('title', '页面标题')->searchable(),
                amis()->SelectControl('state_title', '状态')->type('tag'),
                amis()->SelectControl('group_title', '分组')->type('tag'),
                amis()->TableColumn('sign', admin_trans('admin.pages.sign'))->searchable(),
                amis()->TableColumn('access_num', '展示次数'),
                amis()->TableColumn('updated_at', admin_trans('admin.created_at'))->type('datetime')->sortable(true),
                $this->rowActions(true, 'lg')
            ]))->asideClassName('mr-3.5 border-r-none');
    }


    /**
     * 操作列
     *
     * @param bool|array|string $dialog     是否弹窗, 弹窗: true|dialog, 抽屉: drawer
     * @param string            $dialogSize 弹窗大小, 默认: md, 可选值: xs | sm | md | lg | xl | full
     *
     * @return \Slowlyo\OwlAdmin\Renderers\Operation
     */
    protected function rowActions(bool|array|string $dialog = false, string $dialogSize = 'md')
    {
        if (is_array($dialog)) {
            return amis()->Operation()->label(admin_trans('admin.actions'))->buttons($dialog);
        }

        return amis()->Operation()->label(admin_trans('admin.actions'))->buttons([
            $this->rowShowButton($dialog, $dialogSize),
            $this->rowShowLogButton($dialog, $dialogSize),
            amis()->LinkAction()
                ->link(request()->getSchemeAndHttpHost() . '/pages/${sign}.html')
                ->label('预览')
                ->icon('')
                ->level('link'),
            $this->rowEditButton($dialog, $dialogSize),
            $this->rowDeleteButton(),
        ]);
    }

    /**
     * 行详情按钮
     *
     * @param bool|string $dialog     是否弹窗, 弹窗: true|dialog, 抽屉: drawer
     * @param string      $dialogSize 弹窗大小, 默认: md, 可选值: xs | sm | md | lg | xl | full
     * @param string      $title      弹窗标题 & 按钮文字, 默认: 详情
     *
     * @return \Slowlyo\OwlAdmin\Renderers\DialogAction|\Slowlyo\OwlAdmin\Renderers\LinkAction
     */
    protected function rowShowLogButton(bool|string $dialog = false, string $dialogSize = 'md', string $title = '')
    {
        $button = amis()->LinkAction()->link($this->getShowPath());

        if ($dialog) {
            if ($dialog === 'drawer') {
                $button = amis()->DrawerAction()->drawer(
                    amis()->Drawer()->title('访问日志')->body($this->detail('$id'))->size($dialogSize)
                );
            } else {
                $button = amis()->DialogAction()->dialog(
                    amis()->Dialog()->title('访问日志')->body((new PageAccesController())->list('page_access?_action=getData&page_id=${id}'))->size($dialogSize)
                );
            }
        }

        return $button->label('访问日志')->icon('fa-regular fa-eye')->level('link');
    }
    /**
     * 行详情按钮
     *
     * @param bool|string $dialog     是否弹窗, 弹窗: true|dialog, 抽屉: drawer
     * @param string      $dialogSize 弹窗大小, 默认: md, 可选值: xs | sm | md | lg | xl | full
     * @param string      $title      弹窗标题 & 按钮文字, 默认: 详情
     *
     * @return \Slowlyo\OwlAdmin\Renderers\DialogAction|\Slowlyo\OwlAdmin\Renderers\LinkAction
     */
    protected function rowShowButton(bool|string $dialog = false, string $dialogSize = 'md', string $title = '')
    {
        $title  = $title ?: admin_trans('admin.show');
        $button = amis()->LinkAction()->link($this->getShowPath());

        if ($dialog) {
            if ($dialog === 'drawer') {
                $button = amis()->DrawerAction()->drawer(
                    amis()->Drawer()->title($title)->body($this->detail('$id'))->size($dialogSize)
                );
            } else {
                $button = amis()->DialogAction()->dialog(
                    amis()->Dialog()->title($title)->body(amis()->IFrame()->src('${window.location.origin}/pages/${sign}.html'))->size($dialogSize)
                );
            }
        }

        return $button->label($title)->icon('fa-regular fa-eye')->level('link');
    }

    public function form()
    {
        return $this->baseForm()->body([
            amis()->TextControl('name', '页面名称')->required(),
            amis()->TextControl('title', '页面标题')->required(),
            amis()->TextControl('sign', '页面标识')->required(),
            amis()->TreeSelectControl('group_id', admin_trans('上级分组'))->onlyLeaf(true)->source('/page/get-tree-group')->required(),
            amis()->SubFormControl('page', '页面')->form(
                amis()->Form()->className('h-full')->set('size', 'full')->title('')->body(
                    amis('custom-amis-editor')
                        ->name('schema')
                        ->label('')
                        ->mode('normal')
                        ->className('h-full')
                )
            )->required(),
            amis()->SelectControl('state', '状态')->options([
                [
                    'label' => '启用',
                    'value' => 'enable'
                ],
                [
                    'label' => '禁用',
                    'value' => 'disable'
                ],
            ])->required(),
        ]);
    }


    public function detail($id)
    {
        return $this->baseDetail()->body([]);
    }
}
