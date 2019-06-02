<?php

namespace Railken\Amethyst\Managers;

use Illuminate\Support\Collection;
use Railken\Amethyst\Common\ConfigurableManager;
use Railken\Amethyst\Jobs\HttpRequester\SendHttpRequest;
use Railken\Amethyst\Models\DataBuilder;
use Railken\Amethyst\Models\HttpRequester;
use Railken\Bag;
use Railken\Lem\Manager;
use Railken\Lem\Result;
use Railken\Template\Generators\TextGenerator;

class HttpRequesterManager extends Manager
{
    use ConfigurableManager;

    /**
     * @var string
     */
    protected $config = 'amethyst.http-requester.data.http-requester';

    /**
     * Send an http request..
     *
     * @param HttpRequester $httpRequester
     * @param array         $data
     *
     * @return \Railken\Lem\Contracts\ResultContract
     */
    public function execute(HttpRequester $httpRequester, array $data = [])
    {
        $result = (new DataBuilderManager())->validateRaw($httpRequester->data_builder, $data);

        dispatch(new SendHttpRequest($httpRequester, $data, $this->getAgent()));

        return $result;
    }

    /**
     * Render an email.
     *
     * @param DataBuilder $data_builder
     * @param array       $parameters
     * @param array       $data
     *
     * @return \Railken\Lem\Contracts\ResultContract
     */
    public function render(DataBuilder $data_builder, $parameters, array $data = [])
    {
        $parameters = $this->castParameters($parameters);

        $generator = new TextGenerator();

        $result = new Result();

        try {
            $bag = new Bag($parameters);

            $bag->set('url', $generator->generateAndRender(strval($bag->get('url')), $data));
            $bag->set('method', $generator->generateAndRender(strval($bag->get('method')), $data));
            $bag->set('headers', $generator->generateAndRender(strval($bag->get('headers')), $data));
            $bag->set('body', $generator->generateAndRender(strval($bag->get('body')), $data));

            $result->setResources(new Collection([$bag->toArray()]));
        } catch (\Twig_Error $e) {
            $e = new \Exception($e->getRawMessage().' on line '.$e->getTemplateLine());

            $result->addErrors(new Collection([$e]));
        }

        return $result;
    }

    /**
     * Describe extra actions.
     *
     * @return array
     */
    public function getDescriptor()
    {
        return [
            'actions' => [
                'executor',
            ],
        ];
    }
}
