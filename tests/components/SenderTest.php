<?php
namespace matperez\yii2smssender\tests\components;

use matperez\yii2smssender\components\Sender;
use matperez\yii2smssender\interfaces\IMessage;
use matperez\yii2smssender\interfaces\IMessageComposer;
use matperez\yii2smssender\interfaces\ISender;
use matperez\yii2smssender\interfaces\ITransport;
use matperez\yii2smssender\models\Message;
use matperez\yii2smssender\tests\TestCase;
use yii\helpers\FileHelper;

class SenderTest extends TestCase
{
    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var ITransport|\Mockery\Mock
     */
    private $transport;

    public function testItShouldSendMessages()
    {
        self::assertInstanceOf(ISender::class, $this->sender);

        $message = new Message();
        $message->setFrom('from');
        $message->setTo('to');
        $message->setMessage('message');

        $this->transport->shouldReceive('send')->andReturn(true);

        self::assertTrue($this->sender->send($message));

        $this->transport->shouldHaveReceived('send')->with('from', 'to', 'message');
    }

    public function testItShouldComposeMessages()
    {
        self::assertInstanceOf(IMessageComposer::class, $this->sender);

        $this->sender->viewPath = '@runtime';
        $runtime = \Yii::getAlias('@runtime');
        FileHelper::createDirectory($runtime);
        file_put_contents($runtime.'/empty.php', 'template content');

        $message = $this->sender->compose('empty');
        self::assertInstanceOf(IMessage::class, $message);
        self::assertEquals('template content', $message->getMessage());
        self::assertEquals($this->sender, $message->getSender());
    }

    protected function setUp()
    {
        parent::setUp();
        $this->transport = \Mockery::mock(ITransport::class);
        \Yii::$container->set(ITransport::class, function() {
            return $this->transport;
        });
        $this->sender = new Sender();
        $this->sender->transportConfig = [
            'class' => ITransport::class,
        ];
    }
}
