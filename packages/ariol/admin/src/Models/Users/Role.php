<?php namespace Ariol\Models\Users;

use Ariol\Models\Model;

/**
 * Модель ролей пользователей.
 * @package Ariol\Models\User
 */
class Role extends Model
{
    /**
     * Используемая таблица.
     *
     * @var string
     */
    protected $table = 'roles';

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
     * Костыль.
     *
     * @var int
     */
    protected static $count_update = 0;

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
            'permission_id' => translate('admin.modules.packageItems.users.packageItems.permissions')
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
                    'unique' => true,
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

    /**
     * Получение всех существующих прав.
     *
     * @return array
     */
    public function getPermissions()
    {
        $permissions = [];

        $allPermissions = Permission::orderBy('name', 'asc')->get();
        foreach ($allPermissions as $permission) {
            $permissions[$permission->id] = $permission->name;
        }

        return $permissions;
    }
}
