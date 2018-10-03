<?php
namespace Tec\User\Landing\Ui;

use Gap\Http\Response;
use Gap\Http\ResponseInterface;
use Gap\Http\RedirectResponse;

use Tec\User\Landing\Dto\RegDto;
use Tec\User\Landing\Service\UserService;

class UserUi extends UiBase
{
    private $userService;

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

    public function regPost(): ResponseInterface
    {
        $post = $this->request->request;
        $regDto = new RegDto([
            'email' => $post->get('email'),
            'phone' => $post->get('phone'),
            'zcode' => $post->get('zcode'),
            'fullname' => $post->get('fullname'),
            'password' => $post->get('password'),
        ]);

        $this->getUserService()->reg($regDto);

        $loginUrl = $this->getRouteUrlBuilder()->routeGet('login');
        return new RedirectResponse($loginUrl);
    }

    private function getUserService(): UserService
    {
        if ($this->userService) {
            return $this->userService;
        }

        $this->userService = new UserService($this->app);
        return $this->userService;
    }
}
