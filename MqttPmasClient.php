<?php

require __DIR__ . '/vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\MqttClientException;



class MqttPmasClient {

  protected MqttClient $mqtt;
  protected ConnectionSettings $connectionSettings;

  function __construct($server = 'mqtt.gatial.com', $port = 8883, $client_id = 'mqtt-client') {

    try {
      $mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $client_id);
      //echo("mqtt = $mqtt");

      /*
      pcntl_signal(SIGINT, function (int $signal, $info) use ($mqtt) {
        printf("MQTT Interrupted\n");
        $mqtt->interrupt();
      });
      */

      $this->connectionSettings = (new ConnectionSettings)
        ->setUsername(null)
        ->setPassword(null)
        ->setConnectTimeout(3)
        ->setUseTls(true)
        ->setTlsSelfSignedAllowed(true);

      $this->mqtt = $mqtt;

    } catch (MqttClientException $e) {
      printf('MQTT Client Error: %s\n', $e);
    }


  }


  public function __destruct() {
    try {

      if (isset($this->mqtt))
        if ($this->mqtt->isConnected())
          $this->mqtt->disconnect();
        else {
          printf("Disconnecting: The connection was already interrupted.");
        }

    } catch (MqttClientException $e) {
      printf('MQTT Disconnect Error: %s\n', $e);
    }
  }


  function connect() {
    try {
      $this->mqtt->connect($this->connectionSettings, true);
    } catch (MqttClientException $e) {
      printf('MQTT Connect Error: %s\n', $e);
    }
  }

}

