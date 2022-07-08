<?php
declare(strict_types=1);

namespace App\Gateway\Routee;

use App\Gateway\AbstractGateway;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\Response;

class RouteeAuthGatewayImpl extends AbstractGateway implements RouteeAuthGateway
{
    /** @var string */
    protected $appId;

    /** @var string */
    protected $appSecret;

    public function __construct(
        ClientInterface $client,
        string $appId,
        string $appSecret
    ) {
        parent::__construct($client);

        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /**
     * @return RouteeAuthTokenType
     * @throws RouteeAdapterException
     */
    public function auth(): RouteeAuthTokenType
    {
        try {
            $decodedBody = $this->checkResponse(
                $this->makeRequest()
            );
        } catch (\Throwable $t) {
            throw new RouteeAdapterException($t->getMessage(), 0, $t);
        }

        return new RouteeAuthTokenType($decodedBody['access_token']);
    }

    protected function makeRequest(): Response
    {
        return $this->client->post(
            'https://auth.routee.net/oauth/token',
            [
                'authorization' => $this->getAuthHeaderValue(),
                'content-type' => 'application/x-www-form-urlencoded'
            ],
            ['grant_type' => 'client_credentials']
        )->send();
    }

    protected function getAuthHeaderValue(): string
    {
        return 'Basic ' . base64_encode($this->appId . ':' . $this->appSecret);
    }

    protected function checkResponse(Response $result): array
    {
        $result = parent::checkResponseGeneral($result);

        if (
            !is_array($result->decoded)
            || empty($result->decoded['access_token'])
        ) {
            throw new RouteeAdapterException(
                sprintf('Unrecognised body: "%s"', $result->raw)
            );
        }

        return $result->decoded;
    }
}