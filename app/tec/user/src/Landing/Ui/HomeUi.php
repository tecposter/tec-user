<?php
namespace Tec\User\Landing\Ui;

use Gap\Http\Response;

class HomeUi extends UiBase
{
    public function front(): Response
    {
        return new Response('home');
    }
}
