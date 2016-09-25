<?php
namespace matperez\yii2smssender\tests\transports;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use matperez\yii2smssender\exceptions\TransportException;
use matperez\yii2smssender\interfaces\ITransport;
use matperez\yii2smssender\transports\IntegrationApiTransport;
use matperez\yii2smssender\tests\TestCase;
use Psr\Http\Message\RequestInterface;
use yii\base\InvalidConfigException;

class IntegrationApiTransportTest extends TestCase
{
    /**
     * @var IntegrationApiTransport
     */
    private $transport;

    /**
     * @var ClientInterface|\Mockery\Mock
     */
    private $client;

    public function testItExists()
    {
        self::assertInstanceOf(ITransport::class, $this->transport);
    }

    public function testItSendMessages()
    {
        $this->client->shouldReceive('request')->andReturn(json_encode(''));
        self::assertTrue($this->transport->send('from', 'to', 'message'));
    }

    public function testItRequiresLoginAndPassword()
    {
        $this->expectException(InvalidConfigException::class);
        $this->transport->setSessionId(null);
        $this->transport->getSessionId();
    }

    public function testItWillThrowTransportExceptionOnOnGuzzleError()
    {
        $request = \Mockery::mock(RequestInterface::class);
        $exception = new RequestException('', $request);
        $this->client->shouldReceive('request')->andThrow($exception);
        self::expectException(TransportException::class);
        $this->transport->send('from', 'to', 'message');
    }

    public function testItWillThrowTransportExceptionOnJsonErrors()
    {
        $this->client->shouldReceive('request')->andThrow(new \InvalidArgumentException());
        self::expectException(TransportException::class);
        $this->transport->send('from', 'to', 'message');
    }

    public function testItWillThrowTransportExceptionOnGuzzleExceptionWhenItsAuthenticating()
    {
        $this->transport->setSessionId(null);
        $this->transport->login = 'login';
        $this->transport->password = 'password';
        $request = \Mockery::mock(RequestInterface::class);
        $exception = new RequestException('', $request);
        $this->client->shouldReceive('request')->andThrow($exception);
        $this->expectException(TransportException::class);
        $this->transport->getSessionId();
    }

    public function testItWillThrowTransportExceptionOnJsonExceptionWhenItsAuthenticating()
    {
        $this->transport->setSessionId(null);
        $this->transport->login = 'login';
        $this->transport->password = 'password';
        $this->client->shouldReceive('request')->andThrow(\InvalidArgumentException::class);
        $this->expectException(TransportException::class);
        $this->transport->getSessionId();
    }

    public function testItAuthenticates()
    {
        $this->transport->setSessionId(null);
        $this->transport->login = 'login';
        $this->transport->password = 'password';
        $this->client->shouldReceive('request')->andReturn(new Response(200, [], json_encode('sessionid')));
        self::assertEquals('sessionid', $this->transport->getSessionId());
    }

    public function testItCanFetchBalance()
    {
        self::assertTrue($this->transport->canFetchBalance());
    }

    public function testItThrowsTransportExceptionOnGuzzleErrorWhileFetchingBalance()
    {
        $this->transport->setSessionId('sessionid');
        $request = \Mockery::mock(RequestInterface::class);
        $exception = new RequestException('', $request);
        $this->client->shouldReceive('request')->andThrow($exception);
        $this->expectException(TransportException::class);
        $this->transport->getBalance();
    }

    public function testItThrowsTransportExceptionOnJsonErrorWhileFetchingBalance()
    {
        $this->transport->setSessionId('sessionid');
        $this->client->shouldReceive('request')->andThrow(\InvalidArgumentException::class);
        $this->expectException(TransportException::class);
        $this->transport->getBalance();
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->client = \Mockery::mock(ClientInterface::class);
        $this->transport = new IntegrationApiTransport($this->client);
        $this->transport->setSessionId('sessionid');
    }
}
