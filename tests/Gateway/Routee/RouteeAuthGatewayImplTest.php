<?php
declare(strict_types=1);

namespace App\Tests\Gateway\Routee;

use App\Gateway\Routee\RouteeAdapterException;
use App\Gateway\Routee\RouteeAuthGateway;
use App\Gateway\Routee\RouteeAuthGatewayImpl;
use App\Gateway\Routee\RouteeAuthTokenType;
use App\Tests\Gateway\HttpClientTestCase;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;

/**
 * @see https://docs.routee.net/docs
 */
class RouteeAuthGatewayImplTest extends HttpClientTestCase
{
    protected const API_APP_ID = 'someId';
    protected const API_APP_SECRET = 'someSecret';
    protected const SUCCESS_RESPONSE_FILE_PATH = __DIR__ . '/fixture/routee-auth-success-response.json';

    protected $sut;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->sut = new RouteeAuthGatewayImpl(
            $this->client,
            self::API_APP_ID,
            self::API_APP_SECRET
        );
    }
    
    public function testAuthOriginalResponseSuccess()
    {
        // ARRANGE
        $this->mockPostAuthWillReturn(
            200,
            file_get_contents(self::SUCCESS_RESPONSE_FILE_PATH)
        );
        
        // ACT, ASSERT
        self::assertEquals(
            new RouteeAuthTokenType('d855ba59-696a-4dfa-a988-af464cde5c64'),
            $this->sut->auth()
        );
    }

    public function testInstanceOff()
    {
        self::assertInstanceOf(RouteeAuthGateway::class, $this->sut);
    }

    public function testThrowExceptionOnUnrecognisedBody()
    {
        // ARRANGE
        $this->mockPostAuthWillReturn(
            200,
            ['someContent' => 123]
        );

        // EXPECTS
        self::expectException(RouteeAdapterException::class);
        self::expectExceptionMessage('Unrecognised body: "{"someContent":123}"');

        // ACT
        $this->sut->auth();
    }

    public function testThrowException()
    {
        // ARRANGE
        $this->mockPostAuth()->willThrowException(
            new \Exception('Some error')
        );

        // EXPECTS
        self::expectException(RouteeAdapterException::class);
        self::expectExceptionMessage('Some error');

        // ACT
        $this->sut->auth();
    }

    public function testThrowExceptionWhenStatusNot200()
    {
        // ARRANGE
        $this->mockPostAuthWillReturn(
            400,
            file_get_contents(self::SUCCESS_RESPONSE_FILE_PATH)
        );

        // EXPECTS
        self::expectException(RouteeAdapterException::class);
        self::expectExceptionMessage('Wrong response');

        // ACT
        $this->sut->auth();
    }

    protected function mockPostAuthWillReturn(int $statusCode, $content): void
    {
        $this->mockPostAuth()
            ->willReturn(
                $this->mockRequestSendWillReturn(
                    $this->makeResponse(
                        $statusCode,
                        $content
                    )
                )
            );
    }

    protected function mockPostAuth(): InvocationMocker
    {
        return $this->client
            ->expects(self::once())
            ->method('post')
            ->with(
                'https://auth.routee.net/oauth/token',
                [
                    'authorization' => 'Basic ' . base64_encode(
                        self::API_APP_ID . ':' . self::API_APP_SECRET
                        ),
                    'content-type' => 'application/x-www-form-urlencoded'
                ],
                ['grant_type' => 'client_credentials']
            );
    }
}