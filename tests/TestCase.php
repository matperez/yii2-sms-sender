<?php
namespace matperez\yii2smssender\tests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function tearDown()
    {
        \Mockery::close();
        parent::tearDown();
    }
}
