<?php

namespace Amethyst\Tests\Managers;

use Amethyst\Fakers\HttpRequesterFaker;
use Amethyst\Managers\DataBuilderManager;
use Amethyst\Managers\HttpRequesterManager;
use Amethyst\Tests\BaseTest;
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

    public function testSend()
    {
        /** @var \Amethyst\Managers\HttpRequesterManager */
        $manager = $this->getManager();
        $result = $manager->create(HttpRequesterFaker::make()->parameters());
        $this->assertEquals(1, $result->ok());
        $resource = $result->getResource();
        $result = $manager->execute($resource, [
            'name' => 'a',
        ]);
        $this->assertEquals(true, $result->ok());
    }

    public function testRender()
    {
        /** @var \Amethyst\Managers\HttpRequesterManager */
        $manager = $this->getManager();
        $result = $manager->create(HttpRequesterFaker::make()->parameters());
        $this->assertEquals(1, $result->ok());
        $resource = $result->getResource();
        $result = $manager->render($resource->data_builder, [
            'body' => '{{ name }}',
        ], (new DataBuilderManager())->build($resource->data_builder, ['name' => 'ban'])->getResource());
        $this->assertEquals(true, $result->ok());
        $this->assertEquals('ban', $result->getResource()['body']);
    }
}
