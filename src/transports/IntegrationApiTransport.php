<?php
namespace matperez\yii2smssender\transports;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use matperez\yii2smssender\exceptions\TransportException;
use matperez\yii2smssender\interfaces\ITransport;
use yii\base\Component;
use yii\base\InvalidConfigException;

class IntegrationApiTransport extends Component implements ITransport
{
    /**
     * @var string
     */
    public $login;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $baseUrl = 'https://integrationapi.net/rest';

    /**
     * @var int maximum message validity in seconds
     */
    public $validity = 2800;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $sessionId;

    /**
     * InternationalApiTransport constructor.
     * @param ClientInterface $client
     * @param array $config
     */
    public function __construct(ClientInterface $client, array $config = [])
    {
        $this->client = $client;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     * @throws \matperez\yii2smssender\exceptions\TransportException
     * @throws \yii\base\InvalidConfigException
     */
    public function send($from, $to, $message)
    {
        if (!$this->isAuthenticated()) {
            $this->authenticate();
        }
        $params = [
            'sessionId' => $this->sessionId,
            'sourceAddress' => $from,
            'destinationAddress' => $to,
            'data' => $message,
            'validity' => $this->validity,
            'sendDate' => null,
        ];
        try {
            $this->client->request('POST', $this->baseUrl.'/Sms/Send', ['body' => $params]);
        } catch (GuzzleException $e) {
            throw new TransportException('Http client error: '.$e->getMessage(), $e->getCode(), $e);
        } catch (\InvalidArgumentException $e) {
            throw new TransportException('Unable to decode server response: '.$e->getMessage(), $e->getCode(), $e);
        }
        return true;
    }

    /**
     * @return bool
     * @throws \matperez\yii2smssender\exceptions\TransportException
     * @throws \yii\base\InvalidConfigException
     */
    protected function authenticate()
    {
        if (!$this->login || !$this->password) {
            throw new InvalidConfigException('Login and password should be set.');
        }
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/user/sessionid', [
                'query' => [
                    'login' => $this->login,
                    'password' => $this->password,
                ],
            ]);
            $this->sessionId = \GuzzleHttp\json_decode((string) $response->getBody());
        } catch (GuzzleException $e) {
            throw new TransportException('Http client error: '.$e->getMessage(), $e->getCode(), $e);
        } catch (\InvalidArgumentException $e) {
            throw new TransportException('Unable to decode server response: '.$e->getMessage(), $e->getCode(), $e);
        }
        return true;
    }

    /**
     * @return string
     */
    public function isAuthenticated()
    {
        return (bool) $this->sessionId;
    }

    /**
     * @param string $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return string
     * @throws \matperez\yii2smssender\exceptions\TransportException
     */
    public function getSessionId()
    {
        if (!$this->sessionId) {
            $this->authenticate();
        }
        return $this->sessionId;
    }
}
