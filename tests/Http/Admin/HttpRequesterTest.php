<?php

namespace Railken\Amethyst\Tests\Http\Admin;

use Railken\Amethyst\Api\Support\Testing\TestableBaseTrait;
use Railken\Amethyst\Fakers\HttpRequesterFaker;
use Railken\Amethyst\Managers\HttpRequesterManager;
use Railken\Amethyst\Tests\BaseTest;

class HttpRequesterTest extends BaseTest
{
    use TestableBaseTrait;

    /**
     * Faker class.
     *
     * @var string
     */
    protected $faker = HttpRequesterFaker::class;

    /**
     * Router group resource.
     *
     * @var string
     */
    protected $group = 'admin';

    /**
     * Route name.
     *
     * @var string
     */
    protected $route = 'admin.http-requester';

    public function testSend()
    {
        $manager = new HttpRequesterManager();
        $result = $manager->create(HttpRequesterFaker::make()->parameters());
        $this->assertEquals(1, $result->ok());
        $resource = $result->getResource();
        $response = $this->callAndTest('POST', route('admin.http-requester.send', ['id' => $resource->id]), ['data' => ['name' => $resource->name]], 200);
    }

    public function testRender()
    {
        $manager = new HttpRequesterManager();
        $result = $manager->create(HttpRequesterFaker::make()->parameters());
        $this->assertEquals(1, $result->ok());
        $resource = $result->getResource();
        $response = $this->callAndTest('post', route('admin.http-requester.render'), [
            'data_builder_id' => $resource->data_builder->id,
            'body'            => '{{ name }}',
            'data'            => ['name' => 'ban'],
        ], 200);
        $this->assertEquals('ban', base64_decode(json_decode($response->getContent())->resource->body, true));
    }
}
