<?php

namespace Amethyst\Authorizers;

use Railken\Lem\Authorizer;
use Railken\Lem\Tokens;

class HttpRequesterAuthorizer extends Authorizer
{
    /**
     * List of all permissions.
     *
     * @var array
     */
    protected $permissions = [
        Tokens::PERMISSION_CREATE => 'http-requester.create',
        Tokens::PERMISSION_UPDATE => 'http-requester.update',
        Tokens::PERMISSION_SHOW   => 'http-requester.show',
        Tokens::PERMISSION_REMOVE => 'http-requester.remove',
    ];
}
