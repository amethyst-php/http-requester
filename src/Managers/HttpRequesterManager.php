<?php

namespace Amethyst\Managers;

use Amethyst\Common\ConfigurableManager;
use Amethyst\Jobs\HttpRequester\SendHttpRequest;
use Amethyst\Models\DataBuilder;
use Amethyst\Models\HttpRequester;
use Illuminate\Support\Collection;
use Railken\Bag;
use Railken\Lem\Manager;
use Railken\Lem\Result;
use Railken\Template\Generators\TextGenerator;

/**
 * @method \Amethyst\Models\HttpRequester                 newEntity()
 * @method \Amethyst\Schemas\HttpRequesterSchema          getSchema()
 * @method \Amethyst\Repositories\HttpRequesterRepository getRepository()
 * @method \Amethyst\Serializers\HttpRequesterSerializer  getSerializer()
 * @method \Amethyst\Validators\HttpRequesterValidator    getValidator()
 * @method \Amethyst\Authorizers\HttpRequesterAuthorizer  getAuthorizer()
 */
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
     * @param HttpRequester|id $httpRequester
     * @param array         $data
     *
     * @return \Railken\Lem\Contracts\ResultContract
     */
    public function execute($httpRequester, array $data = [])
    {
        $httpRequester = is_int($httpRequester) ? $this->getRepository()->findOneById($httpRequester) : $httpRequester;

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
