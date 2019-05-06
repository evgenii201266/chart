<?php namespace Ariol\Models;

/**
 * Модель пунктов меню в админке.
 *
 * @package Ariol\Models
 */
class Menu extends Model
{
    /**
     * Используемая таблица.
     *
     * @var string
     */
    protected $table = 'menus';

    /**
     * Разрешаем все поля для автозаполнения.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * В таблице не используются timestamp'ы.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Используется ли мультиязычность.
     *
     * @var bool
     */
    public $languagable = true;

    /**
     * Данные полей, выводимых на странице.
     *
     * @var array
     */
    public $columns = [
        'position' => [
            'width' => 100,
            'type' => 'editable'
        ],
        'name' => [
            'type' => 'editable'
        ],
        'parent' => [
            'width' => 200,
            'type' => 'template',
            'method' => 'catName'
        ],
        'active' => [
            'width' => 150,
            'type' => 'activity'
        ]
    ];

    /**
     * Наименование полей.
     *
     * @return array
     */
    public function labels()
    {
        return [
            'position' => translate('admin.common.packageItems.position'),
            'name' => translate('admin.common.packageItems.name'),
            'parent' => translate('admin.modules.packageItems.menu.packageItems.parent'),
            'active' => translate('admin.common.packageItems.display'),
            'link' => translate('admin.common.packageItems.url')
        ];
    }

    /**
     * Описание типов полей.
     *
     * @return array
     */
    public function form()
    {
        return [
            'name' => [
                'type' => 'string',
                'column' => 6,
                'params' => [
                    'require' => true
                ]
            ],
            'link' => [
                'type' => 'string',
                'column' => 6,
                'params' => [
                    'slug' => 'name'
                ]
            ],
            'parent' => [
                'type' => 'select',
                'column' => 6,
                'values' => $this->outputCategories()
            ],
            'position' => [
                'type' => 'number',
                'column' => 6,
            ]
        ];
    }

    /**
     * Поля, по которым можно сортировать данные в таблице.
     *
     * @return array
     */
    public function sortableFields()
    {
        return ['position', 'name'];
    }

    /**
     * Поля, по которым будет осуществляться поиск в таблице.
     *
     * @return array
     */
    public function searchableFields()
    {
        return ['name', 'link'];
    }

    /**
     * Получение данных активной категории.
     *
     * @return mixed
     */
    public function cat()
    {
        return $this->hasOne('Ariol\Models\Menu', 'id', 'parent')->where('active', 1);
    }

    /**
     * Вывод категорий.
     *
     * @return array
     */
    public function outputCategories()
    {
        $data = $this::with('cat')->get();

        $items = [];
        $items[0] = translate('admin.modules.packageItems.menu.packageItems.main_category');

        foreach ($data as $item) {
            if ((isset($this->id) && $this->id != $item->id && $this->id != $item->parent) || !isset($this->id)) {
                $cat = $item->cat ? $item->cat->name. ' / ' : null;
                $cats = $cat ? ($item->cat->cat ? $item->cat->cat->name . ' / ' : '') : null;

                $items[$item->id] = $cats.$cat.$item->name;
            }
        }

        return $items;
    }

    /**
     * Наименование категории.
     *
     * @return string
     */
    public function catName()
    {
        return $this->cat ? $this->cat->name : translate('admin.modules.packageItems.menu.packageItems.main_category');
    }

    /**
     * Меню админки.
     *
     * @return array
     */
    public function getMenu()
    {
        $parents = Menu::where('active', '1')->where('parent', '0')
            ->withLang()->orderBy('position', 'asc')->get();

        $menu = [];

        foreach ($parents as $keyP => $parentsItem) {
            $menu[$keyP] = [
                'parentId' => $parentsItem->id,
                'parentName' => $parentsItem->name,
                'parentLink' => $parentsItem->link,
                'parentPosition' => $parentsItem->position,
                'child' => [],
            ];

            $child = Menu::where('active', '1')->where('parent', '=', $parentsItem->id)
                ->withLang()->orderBy('position', 'asc')->get();

            foreach ($child as $keyC => $childItem) {
                $children[$keyC] = [
                    'childID' => $childItem->id,
                    'childParent' => $childItem->parents,
                    'childName' => $childItem->name,
                    'childLink' => $childItem->link,
                    'childPosition' => $childItem->position,
                ];

                $menu[$keyP]['child'][] = $children[$keyC];
            }
        }

        return $menu;
    }
}
