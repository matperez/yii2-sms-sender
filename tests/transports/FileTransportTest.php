<?php
namespace matperez\yii2smssender\tests\transports;

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

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->transport = new FileTransport();
    }
}
