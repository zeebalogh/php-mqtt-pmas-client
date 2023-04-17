<?php

require __DIR__ . '/vendor/autoload.php';

use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

pcntl_async_signals(true);

$server   = 'mqtt.gatial.com';
$port     = 8883;
#$port     = 8084;
$clientId = 'mqtt-php';
$bids	    = [];

try {
  $mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);

  pcntl_signal(SIGINT, function (int $signal, $info) use ($mqtt) {
    printf("Interrupted\n");
    $mqtt->interrupt();
  });

  /*
  $connectionSettings = (new ConnectionSettings)
    ->setUsername('soon')
    ->setPassword('SooN2020');
  */
  $connectionSettings = (new ConnectionSettings)
    ->setConnectTimeout(3)
    ->setUseTls(true)
    ->setTlsSelfSignedAllowed(true);

  $mqtt->connect($connectionSettings, true);
  $mqtt->subscribe('pmas', function ($topic, $message) use ($mqtt) {
    printf("Received message on topic [%s]: %s\n", $topic, $message);
    if ($message == "quit") {
      printf("Quit\n");
      $mqtt->interrupt();
    }
    else if($message == "new") {
      $mqtt->publish(
        'broker/master',
        '{\
          "action" : "new", \ 
          "owner" : "pmas", \
          "topic" : "matobl/Extruders", \
          "value" : 0.0, \
          "subject" : { \
            "name" : "Big Bags", \
            "THICKNESS" : 0.2, \
            "WEIGHT":100.0, \
            "WIDTH":0.4, \
            "COLOR":"White", \
            "MIN_COMPLETION_TIME":"2021-12-25", \
            "POLL_END":"2021-12-01" \
          } \
        }', 0);
    }
    else if($message == "assign") {
      // read last line
      $lines    = file('bids');
      $lastLine = array_pop($lines);
      printf("Last JSON: %s\n", $lastLine);

      printf("Winner: %s\n", $lastLine);
      $json = json_decode($lastLine, true);
      $json['action'] = 'assign';

      $mqtt->publish('broker/master', json_encode($json), 0);
    }
    else {
      $json = json_decode($message, true);
      if($json["action"] == "bid") {
        $file = fopen('bids', 'a');
        fwrite($file, $message . "\n");
        printf("Bid Auction: %s, from: %s, value: %s\n", $json["auction_id"], $json["agent"], $json["value"]);
      }
    }
  }, 0);

// Another way how to handle incoming MQTT messages
//    $handler = function (MqttClient $mqtt, string $topic, string $message, int $qualityOfService, bool $retained) {
//		printf("Received message [Topic: %s][ QoS: %s]: %s\n", $topic, $qualityOfService, $message);
//    };

//  $mqtt->registerMessageReceivedEventHandler($handler);


  $mqtt->loop(true);
  $mqtt->disconnect();
} catch (MqttClientException $e) {
  printf('An exception occurred: %s\n', $e);
}

