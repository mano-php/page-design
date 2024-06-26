<?php

namespace ManoCode\Page\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use ManoCode\Page\Models\PageAcces;
use ManoCode\Page\Models\PageDesign;
use ManoCode\Page\Models\PageGroup;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * @method PageDesign getModel()
 * @method PageDesign|Builder query()
 */
class PageDesignService extends AdminService
{
    protected string $modelName = PageDesign::class;

    public string $cacheKeyPrefix = 'page:';

    public function list()
    {
        $query = $this->listQuery();
        if (intval($this->request->input('group_id')) >= 1) {
            $query->where('group_id', intval($this->request->input('group_id')));
        }
        $list = (clone $query)->paginate(request()->input('perPage', 20));
        $items = $list->items();
        $total = $list->total();
        foreach ($items as $key => $item) {
            $items[$key]['state_title'] = ['enable' => '启用', 'disable' => '禁用'][$item['state']];
            $items[$key]['group_title'] = PageGroup::query()->where('id', $item['group_id'])->value('name');
            $items[$key]['access_num'] = PageAcces::query()->where('page_id', $item->id)->count();
        }
        return compact('items', 'total');
    }

    public function saving(&$data, $primaryKey = '')
    {
        $data['schema'] = data_get($data, 'page.schema');
        admin_abort_if(blank($data['schema']), admin_trans('admin.pages.schema_cannot_be_empty'));
        unset($data['page']);

        $exists = $this->query()
            ->where('sign', $data['sign'])
            ->when($primaryKey, fn($q) => $q->where('id', '<>', $primaryKey))
            ->exists();
        Cache::forget('page:/pages/' . $data['sign'] . '.html');
        admin_abort_if($exists, admin_trans('admin.pages.sign_exists'));
    }

    public function saved($model, $isEdit = false)
    {
        if ($isEdit) {
            cache()->delete($this->cacheKeyPrefix . $model->sign);
        }
    }

    public function delete(string $ids)
    {
        $this->query()->whereIn('id', explode(',', $ids))->get()->map(function ($item) {
            cache()->delete($this->cacheKeyPrefix . $item->sign);
        });


        return parent::delete($ids);
    }

    public function getEditData($id)
    {
        $data = parent::getEditData($id);

        $data->setAttribute('page', ['schema' => $data->schema]);
        $data->setAttribute('schema', '');

        return $data;
    }

    /**
     * 获取页面结构
     *
     * @param $sign
     *
     * @return mixed
     */
    public function get($sign)
    {
        return cache()->rememberForever($this->cacheKeyPrefix . $sign, function () use ($sign) {
            return $this->query()->where('sign', $sign)->value('schema');
        });
    }

    public function options()
    {
        return $this->query()->get(['sign as value', 'title as label']);
    }
}
