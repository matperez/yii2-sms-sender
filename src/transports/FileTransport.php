<?php
namespace matperez\yii2smssender\transports;

use matperez\yii2smssender\interfaces\ITransport;
use yii\base\Component;
use yii\helpers\FileHelper;

class FileTransport extends Component implements ITransport
{
    /**
     * @var string
     */
    public $path = '@runtime/sms';

    /**
     * @var callable
     */
    public $filenameCallback;

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidParamException
     * @throws \yii\base\Exception
     */
    public function send($from, $to, $message)
    {
        $filename = $this->getFileName($from, $to, $message);
        $directory = \Yii::getAlias($this->path);
        FileHelper::createDirectory($directory);
        $path = $directory.DIRECTORY_SEPARATOR.$filename;
        $content = $this->getFileContent($from, $to, $message);
        file_put_contents($path, $content);
        return true;
    }

    /**
     * @param string $from
     * @param string $to
     * @param string $message
     * @return string
     */
    protected function getFileContent($from, $to, $message)
    {
        return sprintf("from: %s\nto: %s\nmessage: %s", $from, $to, $message);
    }

    /**
     * @param string $from
     * @param string $to
     * @param string $message
     * @return string
     */
    protected function getFileName($from, $to, $message)
    {
        if (is_callable($this->filenameCallback)) {
            return call_user_func($this->filenameCallback, $from, $to, $message);
        }
        $time = microtime(true);
        return date('Ymd-His-', $time) . sprintf('%04d', (int) (($time - (int) $time) * 10000)) . '-' . sprintf('%04d', mt_rand(0, 10000)) . '.txt';
    }
}
