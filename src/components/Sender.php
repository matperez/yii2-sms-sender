<?php
namespace matperez\yii2smssender\components;

use matperez\yii2smssender\exceptions\TransportException;
use matperez\yii2smssender\interfaces\IMessage;
use matperez\yii2smssender\interfaces\IMessageComposer;
use matperez\yii2smssender\interfaces\ISender;
use matperez\yii2smssender\interfaces\ITransport;
use matperez\yii2smssender\models\Message;
use matperez\yii2smssender\transports\FileTransport;
use yii\base\Component;

class Sender extends Component implements ISender, IMessageComposer
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
     */
    public function compose($view, array $data = [])
    {
        $content = \Yii::$app->getView()
            ->render($this->viewPath.'/'.$view, $data);
        /** @var IMessage $message */
        $message = \Yii::createObject(Message::class);
        $message->setMessage($content);
        $message->setSender($this);
        return $message;
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
