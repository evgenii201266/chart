<?php namespace Ariol\Admin\Controllers;

/**
 * Класс перенаправления в админку.
 * @package Ariol\Auth\Controller
 */
class IndexController extends BaseController
{
    /**
     * Перенаправление на страницу пользователей, если пользователь авторизирован и имеет соответствующие права.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index()
    {
        return view('ariol::index');
    }
}
