<?php

namespace Amethyst\Events\HttpRequester;

use Exception;
use Illuminate\Queue\SerializesModels;
use Amethyst\Models\HttpRequester;
use Railken\Lem\Contracts\AgentContract;

class HttpRequestFailed
{
    use SerializesModels;

    public $httpRequester;
    public $error;
    public $agent;

    /**
     * Create a new event instance.
     *
     * @param \Amethyst\Models\HttpRequester $httpRequester
     * @param \Exception                             $exception
     * @param \Railken\Lem\Contracts\AgentContract   $agent
     */
    public function __construct(HttpRequester $httpRequester, Exception $exception, AgentContract $agent = null)
    {
        $this->httpRequester = $httpRequester;
        $this->error = (object) [
            'class'   => get_class($exception),
            'message' => $exception->getMessage(),
        ];

        $this->agent = $agent;
    }
}
