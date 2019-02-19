<?php

return [
    'enabled'    => true,
    'controller' => Railken\Amethyst\Http\Controllers\Admin\HttpRequestersController::class,
    'router'     => [
        'as'     => 'http-requester.',
        'prefix' => '/http-requesters',
    ],
];
