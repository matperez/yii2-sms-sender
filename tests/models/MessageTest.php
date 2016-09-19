<?php
namespace matperez\yii2smssender\tests\models;

use matperez\yii2smssender\interfaces\ISender;
use matperez\yii2smssender\models\Message;
use matperez\yii2smssender\tests\TestCase;

class MessageTest extends TestCase
{
    /**
     * @var Message
     */
    private $message;

    public function testItExists()
    {
        self::assertInstanceOf(Message::class, $this->message);
    }

    public function testItStoresFrom()
    {
        self::assertEmpty($this->message->getFrom());
        $this->message->setFrom('from');
        self::assertEquals('from', $this->message->getFrom());
    }

    public function testItStoresTo()
    {
        self::assertEmpty($this->message->getTo());
        $this->message->setTo('to');
        self::assertEquals('to', $this->message->getTo());
    }

    public function testItStoresMessage()
    {
        self::assertEmpty($this->message->getMessage());
        $this->message->setMessage('message');
        self::assertEquals('message', $this->message->getMessage());
    }

    public function testItShouldSendItSelf()
    {
        self::assertFalse($this->message->send());

        $this->message->setFrom('from');
        $this->message->setTo('to');
        $this->message->setMessage('message');

        /** @var \Mockery\Mock|ISender $sender */
        $sender = \Mockery::mock(ISender::class);
        $sender->shouldReceive('send')->andReturn(true);

        self::assertTrue($this->message->send($sender));
    }

    public function testItRequiresSender()
    {
        $this->message->setFrom('from');
        $this->message->setTo('to');
        $this->message->setMessage('message');

        self::expectException(\yii\base\InvalidConfigException::class);
        $this->message->send();
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->message = new Message();
    }
}
