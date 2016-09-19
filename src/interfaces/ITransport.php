<?php
namespace matperez\yii2smssender\interfaces;

interface ITransport
{
    /**
     * @param string $from
     * @param string $to
     * @param string $message
     * @return boolean
     */
    public function send($from, $to, $message);
}
