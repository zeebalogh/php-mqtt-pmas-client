<?php

require "MqttPmasClient.php";

use PhpMqtt\Client\Exceptions\MqttClientException;

class MqttSender extends \MqttPmasClient {

  function __construct($server = 'mqtt.gatial.com', $port = 8883, $client_id = 'mqtt-client') {

    //$config = parse_ini_file("/etc/emerpoll/mqtt-pmas-client.ini");

    parent::__construct($server, $port, $client_id);
  }


  function connect() {
    $this->mqtt->connect($this->connectionSettings, true);
  }


  function send($message, $topic = "silvanus/fire"): int {

    try {

      print_r($this->mqtt);

      $this->mqtt->publish($topic, $message, 0);
      return 0;

    } catch (MqttClientException $e) {
      printf('MQTT Sender Error: %s\n', $e);
    }

    return -1;

  }

}


if ((isset($_POST['key']) && $_POST['key'] !== 'silvanus_2023_key')) {
  header('HTTP/1.1 401 Unauthorized');
  die('Authentication failed');
}

// Process the request and return a result
if (isset($_POST['msg'])) {
  $client = new MqttSender();

  $client->send($_POST['msg']);
}

/*
$result = ['status' => 'success', 'data' => $_POST];
header('Content-Type: application/json');
echo json_encode($result);
*/
