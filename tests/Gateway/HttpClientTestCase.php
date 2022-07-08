<?php
declare(strict_types=1);

namespace App\Tests\Gateway;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class HttpClientTestCase extends TestCase
{
    /** @var MockObject|ClientInterface */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->getHttpClientMock();
    }

    /**
     * @return MockObject|ClientInterface
     */
    private function getHttpClientMock(): MockObject
    {
        return self::getMockBuilder(ClientInterface::class)
            ->getMockForAbstractClass();
    }

    protected function makeResponse(int $statusCode, $content): Response {
        return new Response(
            $statusCode,
            null,
            is_string($content) ? $content : json_encode($content)
        );
    }

    protected function mockRequestSendWillReturn(Response $sendResult): MockObject
    {
        $request = TestCase::getMockBuilder(RequestInterface::class)
            ->getMockForAbstractClass();
        $request
            ->expects(TestCase::once())
            ->method('send')
            ->willReturn($sendResult);
        return $request;
    }
}