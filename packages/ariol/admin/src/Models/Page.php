<?php namespace Ariol\Models;

/**
 * Модель страниц в админке.
 *
 * @package Ariol\Models
 */
class Page extends Model
{
    /**
     * Используемая таблица.
     *
     * @var string
     */
    protected $table = 'pages';

    /**
     * Разрешаем все поля для автозаполнения.
     *
     * @var array
     */
    protected $guarded = [];

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
        'name' => [
            'type' => 'editable'
        ],
        'url' => [
            'type' => 'editable'
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
            'name' => 'Наименование',
            'active' => 'Вкл/выкл отображение',
            'url' => 'Ссылка',
            'content' => 'Контент',
            'main_image' => 'Изображение в шапке',
            'images' => 'Изображения в контенте',
            'scripts' => 'Дополнительные скрипты',
            'seo' => 'Поисковая оптимизация',
            's_title' => 'SEO заголовок',
            's_description' => 'SEO описание',
            's_keywords' => 'SEO ключевые слова',
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
            'url' => [
                'type' => 'string',
                'column' => 6,
                'placeholder' => translate('admin.common.packageItems.alias-description'),
                'params' => [
                    'slug' => 'name'
                ]
            ],
            'active' => [
                'type' => 'boolean'
            ],
            'main_image' => [
                'type' => 'file'
            ],
            'images' => [
                'type' => 'files'
            ],
            'content' => [
                'type' => 'editor',
                'variant' => 'tinymce'
            ],
            'scripts' => [
                'type' => 'text'
            ],
            'seo' => [
                'type' => 'divider'
            ],
            's_title' => [
                'type' => 'text',
                'column' => 4
            ],
            's_keywords' => [
                'type' => 'text',
                'column' => 4
            ],
            's_description' => [
                'type' => 'text',
                'column' => 4
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
        return ['name', 'url'];
    }

    /**
     * Поля, по которым будет осуществляться поиск в таблице.
     *
     * @return array
     */
    public function searchableFields()
    {
        return ['name', 'url'];
    }

    /**
     * Данные страницы по url.
     *
     * @param $url
     * @return mixed
     */
    public function getByUrl($url)
    {
        $page = $this->where('url', $url)->withLang()->where('active', 1)->first();

        if ($page) {
            $images = json_decode($page->images);
            if ($images) {
                $page->content = preg_replace_callback('%(\[image_.*\])%isU', function($match) use ($page, $images) {
                    if ($match[1]) {
                        try {
                            list ($href, $alt, $width) = explode('|', $match[1]);
                        } catch (\Exception $e) {
                            try {
                                $width = 100;
                                list ($href, $alt) = explode('|', $match[1]);
                            } catch (\Exception $e) {
                                $href = $match[1];
                                $alt = '';
                                $width = 100;
                            }
                        }

                        $href = str_replace(['[', ']'], '', $href);
                        $alt = str_replace(['[', ']'], '', $alt);
                        $width = str_replace(['[', ']'], '', $width);

                        if (!$width) {
                            $width = 100;
                        }

                        $width = intval($width);

                        $imageIndex = intval(str_replace('image_', '', $href)) - 1;

                        if ($imageIndex >= 0 && !empty($images[$imageIndex])) {
                            return '<img style="width:' . $width . '%" src="' . $images[$imageIndex] . '" alt="' . $alt . '" />';
                        }
                    }

                    return $match[1];

                }, $page->content);
            }
        }

        return $page;
    }
}
