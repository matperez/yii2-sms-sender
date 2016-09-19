<?php
namespace matperez\yii2smssender\tests\transports;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use matperez\yii2smssender\interfaces\ITransport;
use matperez\yii2smssender\transports\IntegrationApiTransport;
use matperez\yii2smssender\tests\TestCase;
use Psr\Http\Message\RequestInterface;

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

    public function testItWontSendMessageIfUnableToAuthenticate()
    {
        /** @var IntegrationApiTransport|\Mockery\Mock $transport */
        $transport = \Mockery::mock($this->transport)->shouldAllowMockingProtectedMethods();
        $transport->setSessionId(null);
        $transport->shouldReceive('authenticate')->andReturn(false);
        self::assertFalse($transport->send('from', 'to', 'message'));
    }

    public function testItWontSendMessageOnGuzzleError()
    {
        $request = \Mockery::mock(RequestInterface::class);
        $exception = new RequestException('', $request);
        $this->client->shouldReceive('request')->andThrow($exception);
        self::assertFalse($this->transport->send('from', 'to', 'message'));
    }

    public function testItWontSendMessageOnOtherErrors()
    {
        $this->client->shouldReceive('request')->andThrow(new \Exception());
        self::assertFalse($this->transport->send('from', 'to', 'message'));
    }

    public function testItWontAuthenticateWithNoLoginOrPassword()
    {
        $this->transport->setSessionId(null);
        self::assertNull($this->transport->getSessionId());
    }

    public function testItWontAuthenticateOnGuzzleException()
    {
        $this->transport->setSessionId(null);
        $this->transport->login = 'login';
        $this->transport->password = 'password';
        $request = \Mockery::mock(RequestInterface::class);
        $exception = new RequestException('', $request);
        $this->client->shouldReceive('request')->andThrow($exception);
        self::assertNull($this->transport->getSessionId());
    }

    public function testItWontAuthenticateOnOtherExceptions()
    {
        $this->transport->setSessionId(null);
        $this->transport->login = 'login';
        $this->transport->password = 'password';
        $this->client->shouldReceive('request')->andThrow(new \Exception());
        self::assertNull($this->transport->getSessionId());
    }

    public function testItAuthenticates()
    {
        $this->transport->setSessionId(null);
        $this->transport->login = 'login';
        $this->transport->password = 'password';
        $this->client->shouldReceive('request')->andReturn(new Response(200, [], json_encode('sessionid')));
        self::assertEquals('sessionid', $this->transport->getSessionId());
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
