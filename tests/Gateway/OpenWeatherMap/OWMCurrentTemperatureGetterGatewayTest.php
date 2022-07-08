<?php
declare(strict_types=1);

namespace App\Tests\Gateway\OpenWeatherMap;

use App\Exception\GetCurrentTemperatureException;
use App\Gateway\OpenWeatherMap\OWMCurrentTemperatureGetterGateway;
use App\Tests\Gateway\HttpClientTestCase;
use Guzzle\Http\ClientInterface;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\MockObject;

class OWMCurrentTemperatureGetterGatewayTest extends HttpClientTestCase
{
    protected const SUCCESS_RESPONSE_FILE_PATH = __DIR__ . '/fixture/weather-success-response.json';
    protected $sut;

    /** @var MockObject|ClientInterface */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new OWMCurrentTemperatureGetterGateway($this->client, 'someApiId');
    }

    public function testGetTemperatureOriginalResponseSuccess()
    {
        // ARRANGE
        $this->mockGetWeatherWillReturn(
            200,
            file_get_contents(self::SUCCESS_RESPONSE_FILE_PATH)
        );

        // ACT
        $temperature = $this->sut->getCurrentTemperature('someCity');

        // ASSERT
        self::assertSame(21, $temperature);

    }

    /**
     * @testWith ["21.03", 21]
     *           ["21.49", 21]
     *           ["21.50", 22]
     *           ["21.60", 22]
     */
    public function testGetTemperatureSuccessRoundTemperature($originalTemp, $expected)
    {
        // ARRANGE
        $this->mockGetWeatherWillReturn(
            200,
            ['main' => ['temp' => $originalTemp]]
        );

        // ACT
        $temperature = $this->sut->getCurrentTemperature('someCity');

        // ASSERT
        self::assertSame($expected, $temperature);
    }

    public function testThrowExceptionOnUnrecognisedBody()
    {
        // ARRANGE
        $this->mockGetWeatherWillReturn(
            200,
            ['someContent' => 123]
        );

        // EXPECTS
        self::expectException(GetCurrentTemperatureException::class);
        self::expectExceptionMessage('Unrecognised body: "{"someContent":123}"');

        // ACT
        $this->sut->getCurrentTemperature('someCity');
    }

    public function testThrowException()
    {
        // ARRANGE
        $this->mockGetWeather()->willThrowException(
            new \Exception('Some error')
        );

        // EXPECTS
        self::expectException(GetCurrentTemperatureException::class);
        self::expectExceptionMessage('Some error');

        // ACT
        $this->sut->getCurrentTemperature('someCity');
    }

    public function testThrowExceptionWhenStatusNot200()
    {
        // ARRANGE
        $this->mockGetWeatherWillReturn(
            400,
            ['main' => ['temp' => 22.02]]
        );

        // EXPECTS
        self::expectException(GetCurrentTemperatureException::class);
        self::expectExceptionMessage('Wrong response');

        // ACT
        $this->sut->getCurrentTemperature('someCity');
    }

    protected function mockGetWeather(): InvocationMocker
    {
        return $this->client
            ->expects(self::once())
            ->method('get')
            ->with(
                'https://api.openweathermap.org/data/2.5/weather',
                null,
                [
                    'query' => [
                        'q' => 'someCity',
                        'units' => 'metric',
                        'appid' => 'someApiId'
                    ]
                ]
            );
    }

    protected function mockGetWeatherWillReturn(int $statusCode, $content): void
    {
        $this->mockGetWeather()
            ->willReturn(
                $this->mockRequestSendWillReturn(
                    $this->makeResponse(
                        $statusCode,
                        $content
                    )
                )
            );
    }
}
