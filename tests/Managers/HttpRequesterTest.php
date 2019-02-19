<?php

namespace Railken\Amethyst\Tests\Managers;

use Railken\Amethyst\Fakers\HttpRequesterFaker;
use Railken\Amethyst\Managers\HttpRequesterManager;
use Railken\Amethyst\Tests\BaseTest;
use Railken\Lem\Support\Testing\TestableBaseTrait;

class HttpRequesterTest extends BaseTest
{
    use TestableBaseTrait;

    /**
     * Manager class.
     *
     * @var string
     */
    protected $manager = HttpRequesterManager::class;

    /**
     * Faker class.
     *
     * @var string
     */
    protected $faker = HttpRequesterFaker::class;
}
