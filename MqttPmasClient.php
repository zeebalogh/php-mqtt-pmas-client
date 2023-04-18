<?php

require __DIR__ . '/vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\MqttClientException;



class MqttPmasClient {

  protected MqttClient $mqtt;
  protected ConnectionSettings $connectionSettings;

  function __construct($server = 'mqtt.gatial.com', $port = 8883, $client_id = 'mqtt-client') {

    pcntl_async_signals(true);

    try {
      $mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $client_id);
      //echo("mqtt = $mqtt");

      pcntl_signal(SIGINT, function (int $signal, $info) use ($mqtt) {
        printf("Interrupted\n");
        $mqtt->interrupt();
      });

      $this->connectionSettings = (new ConnectionSettings)
        ->setUsername(null)
        ->setPassword(null)
        ->setConnectTimeout(3)
        ->setUseTls(true)
        ->setTlsSelfSignedAllowed(true);

      $mqtt->connect($this->connectionSettings, true);

      $this->mqtt = $mqtt;

    } catch (MqttClientException $e) {
      printf('An exception occurred: %s\n', $e);
    }


  }


  public function __destruct() {
    try {

      if (isset($this->mqtt))
        $this->mqtt->disconnect();

    } catch (MqttClientException $e) {
      printf('An exception occurred: %s\n', $e);
    }
  }

}

