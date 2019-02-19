<?php

return [
    'table'      => 'amethyst_http_requesters',
    'comment'    => 'HttpRequester',
    'model'      => Railken\Amethyst\Models\HttpRequester::class,
    'schema'     => Railken\Amethyst\Schemas\HttpRequesterSchema::class,
    'repository' => Railken\Amethyst\Repositories\HttpRequesterRepository::class,
    'serializer' => Railken\Amethyst\Serializers\HttpRequesterSerializer::class,
    'validator'  => Railken\Amethyst\Validators\HttpRequesterValidator::class,
    'authorizer' => Railken\Amethyst\Authorizers\HttpRequesterAuthorizer::class,
    'faker'      => Railken\Amethyst\Fakers\HttpRequesterFaker::class,
    'manager'    => Railken\Amethyst\Managers\HttpRequesterManager::class,
];
