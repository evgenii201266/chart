<?php namespace Ariol\Models\Users;

use Ariol\Models\Model;

/**
 * Модель прав пользователей.
 * @package Ariol\Models\Users
 */
class Permission extends Model
{
    /**
     * Используемая таблица.
     *
     * @var string
     */
    protected $table = 'permissions';

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
     * Данные полей, выводимых на странице.
     *
     * @var array
     */
    public $columns = ['alias', 'name'];

    /**
     * Наименование полей.
     *
     * @return array
     */
    public function labels()
    {
        return [
            'name' => translate('admin.common.packageItems.name'),
            'alias' => translate('admin.common.packageItems.alias'),
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
            'alias' => [
                'type' => 'string',
                'column' => 6,
                'params' => [
                    'require' => true
                ]
            ],
            'name' => [
                'type' => 'string',
                'column' => 6,
                'params' => [
                    'require' => true
                ]
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
        return ['alias', 'name'];
    }

    /**
     * Поля, по которым будет осуществляться поиск в таблице.
     *
     * @return array
     */
    public function searchableFields()
    {
        return ['alias', 'name'];
    }
}
