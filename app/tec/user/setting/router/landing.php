<?php
$collection = new \Gap\Routing\RouteCollection();
/*
$collection
    ->site('default')
    ->access('public')

    ->get('/get/pattern', 'routeName', 'Tec\User\Landing\Ui\Entity@show')
    ->post('/post/patter', 'routeName', 'Tec\User\Landing\Ui\Entity@post')
    ->getRest('/get-rest/patter', 'routeName', 'Tec\User\Landing\Rest\Entity@getRest')
    ->postRest('/post-rest/patter', 'routeName', 'Tec\User\Landing\Rest\Entity@postRest')
    ->getOpen('/get-open/patter', 'routeName', 'Tec\User\Landing\Open\Entity@getOpen')
    ->postOpen('/post-open/patter', 'routeName', 'Tec\User\Landing\Open\Entity@postOpen');
*/

$collection
    ->site('default')
    ->access('public')

    ->get('/logout', 'logout', 'Tec\User\Landing\Ui\UserUi@logout')
    ->get('/login', 'login', 'Tec\User\Landing\Ui\UserUi@login')
    ->post('/login', 'login', 'Tec\User\Landing\Ui\UserUi@loginPost')
    ->get('/reg', 'reg', 'Tec\User\Landing\Ui\UserUi@reg')
    ->post('/reg', 'reg', 'Tec\User\Landing\Ui\UserUi@regPost')
    ->get('/', 'home', 'Tec\User\Landing\Ui\HomeUi@front');

return $collection;
