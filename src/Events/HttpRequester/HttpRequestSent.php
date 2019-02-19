<?php

namespace Railken\Amethyst\Events\HttpRequester;

use Illuminate\Queue\SerializesModels;
use Railken\Amethyst\Models\HttpRequester;
use Railken\Lem\Contracts\AgentContract;

class HttpRequestSent
{
    use SerializesModels;

    public $httpRequester;
    public $agent;

    /**
     * Create a new event instance.
     *
     * @param \Railken\Amethyst\Models\HttpRequester $httpRequester
     * @param \Railken\Lem\Contracts\AgentContract $agent
     */
    public function __construct(HttpRequester $httpRequester, AgentContract $agent = null)
    {
        $this->email = $httpRequester;
        $this->agent = $agent;
    }
}
