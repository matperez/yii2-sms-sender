<?php
namespace matperez\yii2smssender\interfaces;

interface IMessageComposer
{
    /**
     * @param string $view
     * @param array $data
     * @return IMessage
     */
    public function compose($view, array $data = []);
}
