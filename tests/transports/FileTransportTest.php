<?php
namespace matperez\yii2smssender\tests\transports;

use matperez\yii2smssender\exceptions\TransportException;
use matperez\yii2smssender\tests\TestCase;
use matperez\yii2smssender\transports\FileTransport;

class FileTransportTest extends TestCase
{
    /**
     * @var FileTransport
     */
    private $transport;

    public function testItExists()
    {
        self::assertInstanceOf(FileTransport::class, $this->transport);
    }

    public function testItStoresMessagesToFile()
    {
        $this->transport->filenameCallback = function() {
            return 'stored-message.txt';
        };
        $this->transport->path = '@runtime';
        $path = \Yii::getAlias('@runtime/stored-message.txt');
        @unlink($path);

        self::assertFileNotExists($path);

        $message = 'message';
        $from = 'from';
        $to = 'to';
        self::assertTrue($this->transport->send($from, $to, $message));
        self::assertFileExists($path);
    }

    public function testItWillThrowExceptionOnError()
    {
        $this->transport->path = '@not-exists';
        $this->expectException(TransportException::class);
        $this->transport->send('from', 'to', 'message');
    }

    public function testInCantFetchBalance()
    {
        self::assertFalse($this->transport->canFetchBalance());
        $this->expectException(TransportException::class);
        $this->transport->getBalance();
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->transport = new FileTransport();
    }
}
