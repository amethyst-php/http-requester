<?php

namespace Amethyst\Events\HttpRequester;

use Amethyst\Models\HttpRequester;
use Illuminate\Queue\SerializesModels;
use Railken\Lem\Contracts\AgentContract;

class HttpRequestSent
{
    use SerializesModels;

    public $httpRequester;
    public $agent;

    /**
     * Create a new event instance.
     *
     * @param \Amethyst\Models\HttpRequester       $httpRequester
     * @param \Railken\Lem\Contracts\AgentContract $agent
     */
    public function __construct(HttpRequester $httpRequester, AgentContract $agent = null)
    {
        $this->httpRequester = $httpRequester;
        $this->agent = $agent;
    }
}
