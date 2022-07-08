<?php
declare(strict_types=1);

namespace App\Gateway\OpenWeatherMap;

use App\CurrentTemperatureGetter;
use App\Exception\GetCurrentTemperatureException;
use App\Gateway\AbstractGateway;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\Response;

/**
 * @see https://openweathermap.org/current#name
 */
class OWMCurrentTemperatureGetterGateway
    extends AbstractGateway
    implements CurrentTemperatureGetter
{
    /** @var string */
    protected $apiKey;

    public function __construct(ClientInterface $client, string $apiKey)
    {
        parent::__construct($client);

        $this->apiKey = $apiKey;
    }

    public function getCurrentTemperature(string $location): int
    {
        try {
            $decodedBody = $this->checkResponse(
                $this->makeRequest($location)
            );
        } catch (\Throwable $t) {
            throw new GetCurrentTemperatureException($t->getMessage(), 0, $t);
        }

        return (int) round((float) $decodedBody['main']['temp']);
    }

    protected function makeRequest(string $location): Response
    {
        return $this->client->get(
            'https://api.openweathermap.org/data/2.5/weather',
            null,
            [
                'query' => [
                    'q' => $location,
                    'units' => 'metric',
                    'appid' => $this->apiKey
                ]
            ]
        )->send();
    }

    protected function checkResponse(Response $result): array
    {
        $result = parent::checkResponseGeneral($result);

        if (!is_array($result->decoded) || empty($result->decoded['main']['temp'])) {
            throw new GetCurrentTemperatureException(
                sprintf('Unrecognised body: "%s"', $result->raw)
            );
        }

        return $result->decoded;
    }
}