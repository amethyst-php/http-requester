<?php

return [
    'table'      => 'amethyst_http_requesters',
    'comment'    => 'HttpRequester',
    'model'      => Amethyst\Models\HttpRequester::class,
    'schema'     => Amethyst\Schemas\HttpRequesterSchema::class,
    'repository' => Amethyst\Repositories\HttpRequesterRepository::class,
    'serializer' => Amethyst\Serializers\HttpRequesterSerializer::class,
    'validator'  => Amethyst\Validators\HttpRequesterValidator::class,
    'authorizer' => Amethyst\Authorizers\HttpRequesterAuthorizer::class,
    'faker'      => Amethyst\Fakers\HttpRequesterFaker::class,
    'manager'    => Amethyst\Managers\HttpRequesterManager::class,
];
