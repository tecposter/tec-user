<?php
namespace Tec\User\Landing\Ui;

use Gap\Http\Response;
use Gap\Http\ResponseInterface;
use Gap\Http\RedirectResponse;

use Tec\User\Landing\Dto\RegDto;
use Tec\User\Landing\Service\UserService;
use Tec\User\Landing\Service\OpenIdService;

use Symfony\Component\HttpFoundation\Cookie;

class UserUi extends UiBase
{
    private $userService;
    private $openIdService;

    public function logout(): RedirectResponse
    {
        $homeUrl = $this->getRouteUrlBuilder()->routeGet('home');
        $response = new RedirectResponse($homeUrl);
        // public function clearCookie($name, $path = '/', $domain = null, $secure = false, $httpOnly = true)
        $response->headers->clearCookie('idToken', '/', '.' . $this->config->str('baseHost'), true, true);
        return $response;
    }

    public function login(): Response
    {
        return $this->view('page/landing/login');
    }

    public function loginPost(): ResponseInterface
    {
        $post = $this->request->request;
        $email = $post->get('email');
        $password = $post->get('password');

        $userDto = $this->getUserService()->loginByEmail($email, $password);
        if (is_null($userDto)) {
            return new Response('login failed');
        }

        // https://symfony.com/blog/new-in-symfony-3-3-cookie-improvements

        $idToken = $this->getOpenIdService()->createIdTokenByUser($userDto);
        $cookie = new Cookie('idToken', (string) $idToken);

        $homeUrl = $this->getRouteUrlBuilder()->routeGet('home');
        $response = new RedirectResponse($homeUrl);

        // public function __construct(
        // $name, $value = null, $expire = 0, $path = '/', $domain = null,
        // $secure = false, $httpOnly = true, $raw = false, $sameSite = null)
        $response->headers->setCookie(new Cookie(
            'idToken',
            $idToken,
            0,
            '/', // path
            '.' . $this->config->str('baseHost'), // domain
            true,   // secure
            true    // httpOnly
        ));
        return $response;
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

    private function getOpenIdService(): OpenIdService
    {
        if ($this->openIdService) {
            return $this->openIdService;
        }

        $this->openIdService = new OpenIdService($this->app);
        return $this->openIdService;
    }
}
