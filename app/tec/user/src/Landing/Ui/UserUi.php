<?php
namespace Tec\User\Landing\Ui;

use Gap\Http\Response;

class UserUi extends UiBase
{
    public function login(): Response
    {
        return $this->view('page/landing/login');
    }

    public function loginPost(): Response
    {
        return new Response('login post');
    }

    public function reg(): Response
    {
        return $this->view('page/landing/reg');
    }
}
