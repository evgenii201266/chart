<?php namespace Ariol\Admin\Controllers;

use Auth;
use Illuminate\Http\Request;

/**
 * Класс авторизации в админке.
 * @package Ariol\Auth\Controller
 */
class AuthController extends Controller
{
    /**
     * Форма авторизации.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getLogin()
    {
        return view('ariol::login');
    }

    /**
     * Авторизация пользователя.
     *
     * @param Request $request
     * @return array
     */
    public function postLogin(Request $request)
    {
        if (Auth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ], $request->has('remember'))) {
            if (Auth::user()->role_id == 0) {
                $this->getLogout();

                return ['message' => 'У Вас нет прав для входа в административную панель.'];
            }

            return ['status' => true];
        }

        return ['message' => 'Проверьте введённые данные.'];
    }

    /**
     * Выход из аккаунта.
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function getLogout()
    {
        Auth::logout();

        return redirect(config('ariol.admin-path') . '/login');
    }
}
