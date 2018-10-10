<?php
namespace Tec\User\Landing\Ui;

use Gap\Http\Response;

class HomeUi extends UiBase
{
    public function front(): Response
    {
        $cookies = $this->request->cookies;

        if ($cookies->get('idToken')) {
            return new Response($cookies->get('idToken'));
        }

        return new Response('not-login');
    }
}
