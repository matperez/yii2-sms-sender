<?php
namespace matperez\yii2smssender\components;

use matperez\yii2smssender\exceptions\TransportException;
use matperez\yii2smssender\interfaces\IMessage;
use matperez\yii2smssender\interfaces\ISmsSender;
use matperez\yii2smssender\interfaces\ITransport;
use matperez\yii2smssender\models\Message;
use matperez\yii2smssender\transports\FileTransport;
use yii\base\Component;
use yii\base\ViewContextInterface;

class Sender extends Component implements ISmsSender, ViewContextInterface
{
    /**
     * @var array
     */
    public $transportConfig = [
        'class' => FileTransport::class,
    ];

    /**
     * @var string
     */
    public $viewPath = '@app/views/sms';

    /**
     * @var ITransport
     */
    private $transport;

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidParamException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidCallException
     * @throws \yii\base\ViewNotFoundException
     */
    public function compose($view, array $data = [])
    {
        $content = \Yii::$app->getView()
            ->render($view, $data, $this);
        /** @var IMessage $message */
        $message = \Yii::createObject(Message::class);
        $message->setMessage($content);
        $message->setSender($this);
        return $message;
    }

    /**
     * @return string
     */
    public function getViewPath()
    {
        return $this->viewPath;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function send(Message $message)
    {
        try {
            return $this->getTransport()
                ->send($message->getFrom(), $message->getTo(), $message->getMessage());
        } catch (TransportException $e) {
            \Yii::error('Transport exception: '.$e->getMessage(), 'sms');
            return false;
        }
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function getBalance()
    {
        $transport = $this->getTransport();
        if (!$transport->canFetchBalance()) {
            return false;
        }
        try {
            return $this->getTransport()->getBalance();
        } catch (TransportException $e) {
            return false;
        }
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function canFetchBalance()
    {
        return $this->getTransport()->canFetchBalance();
    }

    /**
     * @return ITransport
     * @throws \yii\base\InvalidConfigException
     */
    protected function getTransport()
    {
        if (!$this->transport) {
            $this->transport = \Yii::createObject($this->transportConfig);
        }
        return $this->transport;
    }
}
