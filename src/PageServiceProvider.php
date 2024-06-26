<?php

namespace ManoCode\Page;

use Illuminate\Support\Facades\Cache;
use ManoCode\Page\Models\PageDesign;
use Slowlyo\OwlAdmin\Renderers\TextControl;
use Slowlyo\OwlAdmin\Extend\ServiceProvider;
use Illuminate\Support\Facades\Route;

class PageServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
    protected $menu = [
        [
            'parent' => 0,
            'title' => '页面管理',
            'url' => '/page',
            'url_type' => '1',
            'icon' => 'ph:user-gear',
        ],
        [
            'parent' => '页面管理',
            'title' => '页面分组',
            'url' => '/page/group',
            'url_type' => '1',
            'icon' => 'ph:user-gear',
        ],
        [
            'parent' => '页面管理',
            'title' => '页面设计',
            'url' => '/page/design',
            'url_type' => '1',
            'icon' => 'ph:user-gear',
        ]
    ];

    public function register()
    {
        /**
         * 加载页面
         */
        Route::any('/pages/{id}.html', function () {
            $id = request()->route('id');
            echo Cache::remember("page:/pages/{$id}.html", 3600, function () use ($id) {
                if (!($page = PageDesign::query()->where('state', 'enable')->where(function ($where) use ($id) {
                    $where->where('sign', $id)->orWhere('id', $id);
                })->first())) {
                    // TODO 返回404
//                    echo "<h1>404</h1>";exit();
                    throw new \Exception('页面不存在');
                }
                $fileBody = file_get_contents(__DIR__ . '/Tpl/Amins.html');
                // 替换页面内容
                $fileBody = str_replace('__PAGE_JSON_BODY__', json_encode($page->getAttribute('schema')), $fileBody);
                $fileBody = str_replace('__PAGE_TITLE__', $page->getAttribute('title'), $fileBody);
                return $fileBody;
            });
        });
        Route::get('/page-design/{id}.json', function () {
            $id = request()->route('id');
            echo Cache::remember("page:/pages/{$id}.html", 3600, function () use ($id) {
                if (!($page = PageDesign::query()->where('state', 'enable')->where(function ($where) use ($id) {
                    $where->where('sign', $id)->orWhere('id', $id);
                })->first())) {
                    return [
                        'status' => 404,
                        'msg' => '页面不存在',
                        'data' => new \ArrayObject()
                    ];
                }
                return [
                    'status' => 200,
                    'data' => [
                        'schema' => $page->getAttribute('schema'),
                        'title' => $page->getAttribute('title')
                    ]
                ];
            });
        });
    }


    public function settingForm()
    {
        return $this->baseSettingForm()->body([
//            TextControl::make()->name('value')->label('Value')->required(true),
        ]);
    }
}
