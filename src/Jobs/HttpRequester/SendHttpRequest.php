<?php

namespace Railken\Amethyst\Jobs\HttpRequester;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Railken\Amethyst\Events\HttpRequester\HttpRequestFailed;
use Railken\Amethyst\Events\HttpRequester\HttpRequestSent;
use Railken\Amethyst\Managers\DataBuilderManager;
use Railken\Amethyst\Managers\HttpRequesterManager;
use Railken\Amethyst\Models\HttpRequester;
use Railken\Bag;
use Railken\Lem\Contracts\AgentContract;
use GuzzleHttp\Psr7\Request;

class SendHttpRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $httpRequester;
    protected $data;
    protected $agent;

    /**
     * Create a new job instance.
     *
     * @param HttpRequester                          $httpRequester
     * @param array                                $data
     * @param \Railken\Lem\Contracts\AgentContract $agent
     */
    public function __construct(HttpRequester $httpRequester, array $data = [], AgentContract $agent = null)
    {
        $this->email = $httpRequester;
        $this->data = $data;
        $this->agent = $agent;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $data = $this->data;
        $httpRequester = $this->email;

        $esm = new HttpRequesterManager();
        $dbm = new DataBuilderManager();

        $result = $dbm->build($httpRequester->data_builder, $data);

        if (!$result->ok()) {
            return event(new HttpRequestFailed($httpRequester, $result->getErrors()[0], $this->agent));
        }

        $data = $result->getResource();
        $result = $esm->render($httpRequester->data_builder, [
            'body'        => $httpRequester->url,
            'method'     => $httpRequester->method,
            'headers'      => $httpRequester->headers,
            'body'  => $httpRequester->recipients
        ], $data);

        if (!$result->ok()) {
            return event(new HttpRequestFailed($httpRequester, $result->getErrors()[0], $this->agent));
        }

        $bag = new Bag($result->getResource());

        $request = new Request($bag->get('method'), $bag->get('url'), [
            'headers' => Yaml::parse($bag->get('headers')),
            'body' => Yaml::parse($bag->get('body'))
        ]);
        $response = $client->send($request, [
            'http_errors' => false
        ]);



        event(new HttpRequestSent($httpRequester, $this->agent));
    }
}
