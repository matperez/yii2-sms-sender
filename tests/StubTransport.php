<?php
namespace matperez\yii2smssender\tests;

use matperez\yii2smssender\interfaces\ITransport;

class StubTransport implements ITransport
{
    public function send($from, $to, $message)
    {
        return true;
    }

    public function canFetchBalance()
    {
    }

    public function getBalance()
    {
        return false;
    }
}
