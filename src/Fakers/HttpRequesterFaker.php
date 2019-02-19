<?php

namespace Railken\Amethyst\Fakers;

use Faker\Factory;
use Railken\Bag;
use Railken\Lem\Faker;
use Symfony\Component\Yaml\Yaml;

class HttpRequesterFaker extends Faker
{
    /**
     * @return \Railken\Bag
     */
    public function parameters()
    {
        $faker = Factory::create();

        $bag = new Bag();
        $bag->set('name', $faker->name);
        $bag->set('description', $faker->text);
        $bag->set('data_builder', DataBuilderFaker::make()->parameters()->toArray());
        $bag->set('url', 'https://github.com/php');
        $bag->set('method', 'GET');
        $bag->set('headers', Yaml::dump(['foo' => 'bar']));
        $bag->set('body', Yaml::dump(['foo' => 'bar']));

        return $bag;
    }
}
