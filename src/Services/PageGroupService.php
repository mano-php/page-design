<?php

namespace ManoCode\Page\Services;

use Illuminate\Database\Eloquent\Builder;
use ManoCode\Corp\Models\Department;
use ManoCode\Page\Models\PageGroup;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * @method PageGroup getModel()
 * @method PageGroup|Builder query()
 */
class PageGroupService extends AdminService
{
    protected string $modelName = PageGroup::class;

    public function list()
    {
        $query = $this->listQuery();
        $list = (clone $query)->where('parent_id', 0)->paginate(request()->input('perPage', 20));
        $items = $list->items();
        $total = $list->total();
        foreach ($items as $key => $item) {
            $children = $this->getChildren((clone $query), $item['id']);
            $items[$key]['children'] = $children;
            $items[$key]['state_title'] = ['enable' => '启用', 'disable' => '禁用'][$item['state']];
        }
        return compact('items', 'total');
    }

    protected function getChildren($query, $parent_id)
    {
        $children = $query->where('parent_id', $parent_id)->get();
        $children = $children ? $children->toArray() : [];
        foreach ($children as $key => $child) {
            $children[$key]['state_title'] = $items[$key]['state_title'] = ['enable' => '启用', 'disable' => '禁用'][$child['state']];;
            $childChildren = $this->getChildren((clone $query), $child['id']);
            if (count($childChildren) >= 1) {
                $children[$key] = $childChildren;
            }
        }
        return $children;
    }

    /**
     * 分组树形筛选
     * @param $rootName
     * @param $id
     * @return array|array[]
     */
    public static function deptTree($rootName = '全部分组', $id = null): array
    {
        $query = PageGroup::with('children');
        $optison = [
            [
                'label' => $rootName,
                'value' => 0,
                'children' => $query->where('parent_id', 0)->get()->map(function ($dept) use ($id) {
                    // 构建每个顶级项的基础结构
                    $item = [
                        'label' => $dept->name, // 假设有一个name字段代表页面或部门名称
                        'value' => $dept->id,   // 使用id作为value
                    ];
                    // 如果有子部门，处理子部门
                    if ($dept->children->isNotEmpty()) {
                        $item['children'] = self::buildTree($dept->children, $id);
                    }
                    return $item;
                })->toArray()
            ]
        ];
        return $id !== null && $id >= 1 ? self::removeNode($optison, $id) : $optison;
    }

    /**
     * 递归构建树形结构
     * @param $children
     * @param $id
     * @return array
     */
    private static function buildTree($children, $id): array
    {
        return $children->map(function ($child) use ($id) {
            // 基础结构
            $childItem = [
                'label' => $child->name,
                'value' => $child->id,
            ];
            // 递归处理更深层次的子部门
            if ($child->children->isNotEmpty()) {
                $childItem['children'] = self::buildTree($child->children, $id);
            }
            return $childItem;
        })->toArray();
    }

    /**
     * 递归删除指定节点及其子节点
     * @param array $tree
     * @param int $id
     * @return array
     */
    private static function removeNode(array $tree, int $id): array
    {
        foreach ($tree as $key => &$node) {
            if ($node['value'] == $id) {
                unset($tree[$key]);
            } elseif (isset($node['children'])) {
                $node['children'] = self::removeNode($node['children'], $id);
                if (empty($node['children'])) {
                    unset($node['children']);
                }
            }
        }
        return array_values($tree);
    }
}
