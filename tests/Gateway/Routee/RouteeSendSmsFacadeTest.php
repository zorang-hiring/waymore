<?php
declare(strict_types=1);

namespace App\Tests\Gateway\Routee;

use App\Gateway\Routee\RouteeAuthGateway;
use App\Gateway\Routee\RouteeAuthTokenType;
use App\Gateway\Routee\RouteeSendSmsFacade;
use App\Gateway\Routee\RouteeSendSmsGateway;
use App\Messenger;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RouteeSendSmsFacadeTest extends TestCase
{
    /** @var RouteeSendSmsFacade */
    protected $sut;

    /** @var MockObject|RouteeAuthGateway */
    protected $authAdapter;

    /** @var MockObject|RouteeSendSmsGateway */
    protected $sendSmsAdapter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authAdapter = $this->mockAuthGateway();
        $this->sendSmsAdapter = $this->mockSendSmsGateway();
        
        $this->sut = new RouteeSendSmsFacade(
            $this->authAdapter,
            $this->sendSmsAdapter
        );
    }

    public function testInstanceOff()
    {
        self::assertInstanceOf(Messenger::class, $this->sut);
    }

    public function testBasic()
    {
        // ARRANGE
        $this->authAdapter->expects(self::once())
            ->method('auth')
            ->willReturn(new RouteeAuthTokenType('someToken'));

        $this->sendSmsAdapter->expects(self::once())
            ->method('send')
            ->with(
                new RouteeAuthTokenType('someToken'),
                'some msg',
                '+30 6911111112'
            );

        // ACT
        $this->sut->send('some msg', '+30 6911111112');
    }

    protected function mockAuthGateway()
    {
        return self::getMockBuilder(RouteeAuthGateway::class)
            ->getMockForAbstractClass();
    }

    protected function mockSendSmsGateway()
    {
        return self::getMockBuilder(RouteeSendSmsGateway::class)
            ->getMockForAbstractClass();
    }
}
