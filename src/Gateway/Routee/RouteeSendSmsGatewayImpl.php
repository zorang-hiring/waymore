<?php
declare(strict_types=1);

namespace App\Gateway\Routee;

use App\Gateway\AbstractGateway;
use Guzzle\Http\Message\Response;

class RouteeSendSmsGatewayImpl extends AbstractGateway implements RouteeSendSmsGateway
{
    protected const SENDER = 'zoran';

    public function send(RouteeAuthTokenType $token, string $message, string $toPhone): void
    {
        try {
            $this->checkResponse(
                $this->makeRequest($token, $message, $toPhone)
            );
        } catch (\Throwable $t) {
            throw new RouteeAdapterException($t->getMessage(), 0, $t);
        }
    }

    protected function makeRequest(RouteeAuthTokenType $token, string $message, string $toPhone): Response
    {
        return $this->client->post(
            'https://connect.routee.net/sms',
            [
                'authorization' => 'Bearer ' . $token,
                'content-type' => 'application/json'
            ],
            json_encode([
                'body' => $message,
                'to' => $toPhone,
                'from' => self::SENDER,
            ])
        )->send();
    }

    protected function checkResponse(Response $result): array
    {
        $result = parent::checkResponseGeneral($result);

        if (
            !is_array($result->decoded)
            || empty($result->decoded['status'])
            || $result->decoded['status'] !== 'Queued'
        ) {
            throw new RouteeAdapterException(
                sprintf('Unrecognised body: "%s"', $result->raw)
            );
        }

        return $result->decoded;
    }
}