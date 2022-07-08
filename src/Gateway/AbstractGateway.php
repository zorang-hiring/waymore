<?php
declare(strict_types=1);

namespace App\Gateway;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\Response;

abstract class AbstractGateway
{
    /** @var ClientInterface */
    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }


    protected function checkResponseGeneral(Response $result): GatewaySuccessResponseDto
    {
        $body = $result->getBody(true);
        $decodedBody = @json_decode($body, true);

        if ($result->getStatusCode() !== 200) {
            throw new GatewayException('Wrong response');
        }

        $result = new GatewaySuccessResponseDto();
        $result->raw = $body;
        $result->decoded = $decodedBody;
        return $result;
    }
}