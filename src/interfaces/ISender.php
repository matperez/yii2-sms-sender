<?php
namespace matperez\yii2smssender\interfaces;

use matperez\yii2smssender\models\Message;

interface ISender
{
    /**
     * @param Message $message
     * @return boolean
     */
    public function send(Message $message);
}
