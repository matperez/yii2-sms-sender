<?php
namespace matperez\yii2smssender\models;

use matperez\yii2smssender\interfaces\IMessage;
use matperez\yii2smssender\interfaces\ISender;
use yii\base\InvalidConfigException;
use yii\base\Model;

class Message extends Model implements IMessage
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $message;

    /**
     * @var ISender
     */
    private $sender;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['from'], 'required'],
            [['to'], 'required'],
            [['message'], 'string'],
            [['message'], 'required'],
        ];
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @param ISender $sender
     * @return bool
     * @throws \yii\base\InvalidParamException
     * @throws \yii\base\InvalidConfigException
     */
    public function send(ISender $sender = null)
    {
        if (!$this->validate()) {
            return false;
        }
        $sender = $sender ?: $this->sender;
        if (!$sender) {
            throw new InvalidConfigException('Sender should be defined.');
        }
        return $sender->send($this);
    }

    /**
     * @return ISender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param ISender $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }
}
