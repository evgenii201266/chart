<?php namespace config;

use App\User;
use Ariol\Models\Page;
use Ariol\Models\Users\Role;
use Ariol\Models\Users\Permission;
use Ariol\Models\Menu as AdminMenu;
use Illuminate\Database\Eloquent\Model;

/**
 * Класс меню админки.
 * @package config
 */
class Menu extends Model
{
    /**
     * Левое меню админки.
     *
     * @return array
     */
    public static function getMenu()
    {
        $menu = [
            [
                'icon' => '<i class="icon-cog4 position-left"></i>',
                'name' => translate('ariol::menu.items.system_settings.title'),
                'sub' => [
                    [
                        'icon' => '<i class="icon-magic-wand position-left"></i>',
                        'name' => translate('ariol::menu.items.system_settings.items.cache_clear'),
                        'url' => 'system/cache'
                    ],
                    [
                        'icon' => '<i class="icon-archive position-left"></i>',
                        'name' => translate('ariol::menu.items.system_settings.items.localization'),
                        'url' => 'system/localization'
                    ]
                ]
            ],
            [
                'icon' => '<i class="icon-puzzle3 position-left"></i>',
                'name' => translate('ariol::menu.items.menu'),
                'url' => 'menu',
                'count' => AdminMenu::count()
            ],
            [
                'icon' => '<i class="icon-people position-left"></i>',
                'name' => translate('ariol::menu.items.users.title'),
                'sub' => [
                    [
                        'icon' => '<i class="icon-people position-left"></i>',
                        'name' => translate('ariol::menu.items.users.items.users'),
                        'url' => 'users',
                        'count' => User::count()
                    ],
                    [
                        'icon' => '<i class="icon-chess-king position-left"></i>',
                        'name' => translate('ariol::menu.items.users.items.roles'),
                        'url' => 'roles',
                        'count' => Role::count()
                    ],
                    [
                        'icon' => '<i class="icon-certificate position-left"></i>',
                        'name' => translate('ariol::menu.items.users.items.permissions'),
                        'url' => 'permissions',
                        'count' => Permission::count()
                    ]
                ]
            ],
            [
                'icon' => '<i class="icon-files-empty2 position-left"></i>',
                'name' => translate('ariol::menu.items.pages'),
                'url' => 'pages',
                'count' => Page::count()
            ]
        ];

        return $menu;
    }
}
