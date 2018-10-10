<?php
namespace Tec\User\Base\Ui;

use Gap\Http\RedirectResponse;

abstract class UiBase extends \Gap\Base\Ui\UiBase
{
    protected function gotoRoute(
        string $route,
        array $params = [],
        array $args = []
    ): RedirectResponse {
        $url = $this->getRouteUrlBuilder()
            ->routeGet($route, $params, $args);
        return new RedirectResponse($url);
    }
}
