# yii2-sms-sender

[![Build Status](https://travis-ci.org/matperez/yii2-sms-sender.svg?branch=master)](https://travis-ci.org/matperez/yii2-sms-sender)

Yii2 SMS Sender

## Setup

### Dev environment
```
'components' = [
  'sms' => [
    'class' => \matperez\yii2smssender\components\Sender::class,
    'transportConfig' => [
      'class' => \matperez\yii2smssender\transports\FileTransport::class, 
    ],
  ],
],
```

### Production environment

Using https://integrationapi.net

```
'components' = [
  'sms' => [
    'transportConfig' => [
      'class' => \matperez\yii2smssender\transports\IntegrationApiTransport::class,
      'login' => 'login',
      'password' => 'password',
    ],
  ],
],
```

Container config

```
\Yii::$container->set(\GuzzleHttp\ClientInterface::class, function() {
  return new \GuzzleHttp\Client();
});
```

### Usage 

```
$message = Yii::$app->sms->compose('some-view', $params);
$message->setTo('1234345456');
$message->setFrom('sender');
$message->send();
```
