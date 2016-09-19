<?php
namespace matperez\yii2smssender\transports;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use matperez\yii2smssender\interfaces\ITransport;
use yii\base\Component;

class InternationalApiTransport extends Component implements ITransport
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
     */
    public function send($from, $to, $message)
    {
        if (!$this->isAuthenticated() && !$this->authenticate()) {
            return false;
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
            return false;
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function authenticate()
    {
        if (!$this->login || !$this->password) {
            return false;
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
            return false;
        } catch (\Exception $e) {
            return false;
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
     */
    public function getSessionId()
    {
        if (!$this->sessionId) {
            $this->authenticate();
        }
        return $this->sessionId;
    }
}
