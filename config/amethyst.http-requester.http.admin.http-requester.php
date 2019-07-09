<?php

return [
    'enabled'    => true,
    'controller' => Amethyst\Http\Controllers\Admin\HttpRequestersController::class,
    'router'     => [
        'as'     => 'http-requester.',
        'prefix' => '/http-requesters',
    ],
];
