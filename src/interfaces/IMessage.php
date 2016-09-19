<?php
namespace matperez\yii2smssender\interfaces;

interface IMessage
{
    /**
     * @return string
     */
    public function getFrom();

    /**
     * @param string $from
     */
    public function setFrom($from);

    /**
     * @return string
     */
    public function getTo();

    /**
     * @param string $to
     */
    public function setTo($to);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $message
     */
    public function setMessage($message);

    /**
     * @param ISender $sender
     * @return bool
     */
    public function send(ISender $sender = null);

    /**
     * @return ISender
     */
    public function getSender();

    /**
     * @param ISender $sender
     */
    public function setSender($sender);
}