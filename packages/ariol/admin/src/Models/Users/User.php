<?php

namespace Ariol\Models\Users;

use Auth;
use Ariol\Models\Model;

/**
 * Модель пользователей в админке.
 *
 * @package Ariol\Models\Users
 */
class User extends Model
{
    /**
     * Используемая таблица.
     *
     * @var string
     */
    protected $table = 'users';

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
    public $timestamps = true;

    /**
     * Данные полей, выводимых на странице.
     */
    public $columns = [
        'name', 'email',
        'role_id' => [
            'type' => 'template',
            'method' => 'getRole'
        ]
    ];

    /**
     * Наименование полей.
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function labels()
    {
        return [
            'email' => translate('admin.common.packageItems.e-mail'),
            'name' => translate('admin.modules.packageItems.users.packageItems.name'),
            'role_id' => translate('admin.modules.packageItems.users.packageItems.role_id'),
            'password' => translate('admin.modules.packageItems.users.packageItems.password')
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
                'column' => 4,
                'type' => 'string',
                'params' => [
                    'require' => true
                ]
            ],
            'email' => [
                'column' => 4,
                'type' => 'email',
                'params' => [
                    'require' => true,
                    'unique' => true,
                ]
            ],
            'role_id' => [
                'column' => 4,
                'type' => 'select',
                'params' => [
                    'require' => true
                ],
                'values' => $this->getRoles()
            ],
            'password' => [
                'type' => 'password'
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
        return ['id', 'name'];
    }

    /**
     * Получение роли пользователя.
     *
     * @return string
     */
    public function getRole()
    {
        $role = Role::where('id', $this->role_id)->first();

        return isset($role) ? $role->name : null;
    }

    /**
     * Получение всех ролей пользователей.
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = [0 => 'Пользователь'];

        foreach (Role::get() as $role) {
            $roles[$role->id] = $role->name;
        }

        return $roles;
    }

    /**
     * Получение роли пользователя.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role()
    {
        return $this->hasOne('Ariol\Models\Users\Role', 'id', 'role_id');
    }

    /**
     * Проверка на существование роли у пользователя.
     *
     * @param $alias
     * @return bool
     */
    public function hasRole($alias)
    {
        $role = Role::where('alias', $alias)->first();
        if (! isset($role)) {
            return false;
        }

        if (Auth::user()->role_id != $role->id) {
            return false;
        }

        return true;
    }
}
