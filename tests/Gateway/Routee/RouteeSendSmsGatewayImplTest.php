<?php
declare(strict_types=1);

namespace App\Tests\Gateway\Routee;

use App\Gateway\Routee\RouteeAdapterException;
use App\Gateway\Routee\RouteeAuthTokenType;
use App\Gateway\Routee\RouteeSendSmsGateway;
use App\Gateway\Routee\RouteeSendSmsGatewayImpl;
use App\Tests\Gateway\HttpClientTestCase;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;

class RouteeSendSmsGatewayImplTest extends HttpClientTestCase
{
    protected const SUCCESS_RESPONSE_FILE_PATH = __DIR__ . '/fixture/routee-post-sms-success-response.json';

    protected $sut;

    /** @var RouteeAuthTokenType */
    protected $authToken;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->sut = new RouteeSendSmsGatewayImpl($this->client);

        $this->authToken = new RouteeAuthTokenType('someToken');
    }

    public function testInstanceOff()
    {
        self::assertInstanceOf(RouteeSendSmsGateway::class, $this->sut);
    }
    
    public function testSmsOriginalResponseSuccess()
    {
        // ARRANGE
        $this->mockPostSmsWillReturn(
            200,
            file_get_contents(self::SUCCESS_RESPONSE_FILE_PATH)
        );

        // ACT
        $this->sut->send($this->authToken, 'some msg', "+381644646697");
    }

    public function testThrowExceptionOnUnrecognisedBody()
    {
        // ARRANGE
        $this->mockPostSmsWillReturn(
            200,
            ['someContent' => 123]
        );

        // EXPECTS
        self::expectException(RouteeAdapterException::class);
        self::expectExceptionMessage('Unrecognised body: "{"someContent":123}"');

        // ACT
        $this->sut->send($this->authToken, 'some msg', "+381644646697");
    }

    public function testThrowException()
    {
        // ARRANGE
        $this->mockPostSms()->willThrowException(
            new \Exception('Some error')
        );

        // EXPECTS
        self::expectException(RouteeAdapterException::class);
        self::expectExceptionMessage('Some error');

        // ACT
        $this->sut->send($this->authToken, 'some msg', "+381644646697");
    }

    public function testThrowExceptionWhenStatusNot200()
    {
        // ARRANGE
        $this->mockPostSmsWillReturn(
            400,
            file_get_contents(self::SUCCESS_RESPONSE_FILE_PATH)
        );

        // EXPECTS
        self::expectException(RouteeAdapterException::class);
        self::expectExceptionMessage('Wrong response');

        // ACT
        $this->sut->send($this->authToken, 'some msg', "+381644646697");
    }

    protected function mockPostSmsWillReturn(int $statusCode, $content): void
    {
            $this->mockPostSms()
                ->willReturn(
                    $this->mockRequestSendWillReturn(
                        $this->makeResponse(
                            $statusCode,
                            $content
                        )
                    )
                );
    }

    protected function mockPostSms(): InvocationMocker
    {
        return $this->client
            ->expects(self::once())
            ->method('post')
            ->with(
                'https://connect.routee.net/sms',
                [
                    'authorization' => 'Bearer someToken',
                    'content-type' => 'application/json'
                ],
                json_encode([
                    'body' => 'some msg',
                    'to' => '+381644646697',
                    'from' => 'zoran',
                ])
            );
    }
}
