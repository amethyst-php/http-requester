<?php

namespace Railken\Amethyst\Jobs\HttpRequester;

use GuzzleHttp\HandlerStack;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Namshi\Cuzzle\Middleware\CurlFormatterMiddleware;
use Railken\Amethyst\Events\HttpRequester\HttpRequestFailed;
use Railken\Amethyst\Events\HttpRequester\HttpRequestSent;
use Railken\Amethyst\Managers\DataBuilderManager;
use Railken\Amethyst\Managers\HttpLogManager;
use Railken\Amethyst\Managers\HttpRequesterManager;
use Railken\Amethyst\Models\HttpRequester;
use Railken\Bag;
use Railken\Lem\Contracts\AgentContract;
use Symfony\Component\Yaml\Yaml;

class SendHttpRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $httpRequester;
    protected $data;
    protected $agent;

    /**
     * Create a new job instance.
     *
     * @param HttpRequester                        $httpRequester
     * @param array                                $data
     * @param \Railken\Lem\Contracts\AgentContract $agent
     */
    public function __construct(HttpRequester $httpRequester, array $data = [], AgentContract $agent = null)
    {
        $this->httpRequester = $httpRequester;
        $this->data = $data;
        $this->agent = $agent;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $data = $this->data;
        $httpRequester = $this->httpRequester;

        $esm = new HttpRequesterManager();
        $dbm = new DataBuilderManager();
        $lm = new HttpLogManager();

        $result = $dbm->build($httpRequester->data_builder, $data);

        if (!$result->ok()) {
            return event(new HttpRequestFailed($httpRequester, $result->getErrors()[0], $this->agent));
        }

        $data = $result->getResource();
        $result = $esm->render($httpRequester->data_builder, [
            'url'     => $httpRequester->url,
            'method'  => $httpRequester->method,
            'headers' => $httpRequester->headers,
            'body'    => $httpRequester->body,
        ], $data);

        if (!$result->ok()) {
            return event(new HttpRequestFailed($httpRequester, $result->getErrors()[0], $this->agent));
        }

        $bag = new Bag($result->getResource());

        $time = microtime(true);

        $testHandler = new TestHandler();

        $logger = new Logger('guzzle.to.curl');
        $logger->pushHandler($testHandler);

        $handler = HandlerStack::create();
        $handler->after('cookies', new CurlFormatterMiddleware($logger));

        $client = new \GuzzleHttp\Client([
            'http_errors' => false,
            'handler'     => $handler,
        ]);

        $response = $client->request($bag->get('method'), $bag->get('url'), [
            'headers' => Yaml::parse($bag->get('headers')),
            'body'    => $bag->get('body'),
        ]);

        $lm = new HttpLogManager();
        $lm->createOrFail([
            'method'   => $bag->get('method'),
            'url'      => $bag->get('url'),
            'ip'       => '127.0.0.1',
            'status'   => $response->getStatusCode(),
            'time'     => microtime(true) - $time,
            'request'  => ['headers' => Yaml::parse($bag->get('headers')), 'body' => $bag->get('body')],
            'testable'  => $testHandler->getRecords()[0]['message'],
            'response' => ['headers' => $response->getHeaders(), 'body' => $response->getBody()],
        ]);

        event(new HttpRequestSent($httpRequester, $this->agent));
    }
}
