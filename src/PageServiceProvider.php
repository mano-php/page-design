<?php

namespace ManoCode\Page;

use Illuminate\Support\Facades\Cache;
use ManoCode\Page\Models\PageAcces;
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
    public function writeAccessLog($pageId): void
    {
        $request = request(); // 捕获当前请求
        $ip = $request->ip(); // 获取客户端的 IP 地址
        $userAgent = $request->header('User-Agent'); // 获取客户端的用户代理信息
        $origin = $request->header('Origin'); // 获取请求的来源信息
        $log = new PageAcces();
        $log->setAttribute('page_id',$pageId);
        $log->setAttribute('ip',strval($ip));
        $log->setAttribute('user_agent',strval($userAgent));
        $log->setAttribute('origin',strval($origin));
        $log->setAttribute('created_at',date('Y-m-d H:i:s'));
        $log->setAttribute('updated_at',date('Y-m-d H:i:s'));
        $log->save();
    }

    public function register()
    {
        /**
         * 加载页面
         */
        Route::any('/pages/{id}.html', function () {
            $id = request()->route('id');
            $page = Cache::remember("page:/pages/{$id}.html", 3600, function () use ($id) {
                if (!($page = PageDesign::query()->where('state', 'enable')->where(function ($where) use ($id) {
                    $where->where('sign', $id)->orWhere('id', $id);
                })->first())) {
                    // TODO 返回404
//                    echo "<h1>404</h1>";exit();
                    throw new \Exception('页面不存在');
                }
                return $page;
            });
            $this->writeAccessLog($page->getAttribute('id'));
            $fileBody = file_get_contents(__DIR__ . '/Tpl/Amins.html');
            // 替换页面内容
            $fileBody = str_replace('__PAGE_JSON_BODY__', json_encode($page->getAttribute('schema')), $fileBody);
            $fileBody = str_replace('__PAGE_TITLE__', $page->getAttribute('title'), $fileBody);
            echo $fileBody;
        });
        /**
         * API渲染
         */
        Route::get('/page-design/{id}.json', function () {
            $id = request()->route('id');
            $page = Cache::remember("page:/pages/{$id}.html", 3600, function () use ($id) {
                if (!($page = PageDesign::query()->where('state', 'enable')->where(function ($where) use ($id) {
                    $where->where('sign', $id)->orWhere('id', $id);
                })->first())) {
                    return [
                        'status' => 404,
                        'msg' => '页面不存在',
                        'data' => new \ArrayObject()
                    ];
                }
                return $page;
            });
            $this->writeAccessLog($page->getAttribute('id'));
            return [
                'status' => 200,
                'data' => [
                    'schema' => $page->getAttribute('schema'),
                    'title' => $page->getAttribute('title')
                ]
            ];
        });
    }


    public function settingForm()
    {
        return $this->baseSettingForm()->body([
//            TextControl::make()->name('value')->label('Value')->required(true),
        ]);
    }
}
