<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/yiisoft/yii2/Yii.php';

new \yii\console\Application([
    'id' => 'test-application',
    'basePath' => __DIR__,
]);