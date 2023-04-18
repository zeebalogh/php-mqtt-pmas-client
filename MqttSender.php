<?php

require "MqttPmasClient.php";

use PhpMqtt\Client\Exceptions\MqttClientException;


class MqttSender extends MqttPmasClient {

  function __construct($server = 'mqtt.gatial.com', $port = 8883, $client_id = 'pmas-mqtt-sender') {

    //$config = parse_ini_file("/etc/emerpoll/mqtt-pmas-client.ini");

    // Call the parent constructor
    parent::__construct($server, $port, $client_id);
  }


  function send($message, $topic = "silvanus/test"): int {

    try {

      //print_r($this->mqtt);

      $this->mqtt->publish($topic, $message, 0);
      return 0;

    } catch (MqttClientException $e) {
      printf('MQTT Sender Error: %s\n', $e);
    }

    return -1;
  }

}


if ((isset($_REQUEST['key']) && $_REQUEST['key'] == 'Silvanus_2023_KEY')) {

  // Process the request and return a result
  if (isset($_REQUEST['m'])) {

    //print_r($_REQUEST);
    $client = new MqttSender();
    $client->connect();

    // Sending the message
    if ($client->send($_REQUEST['m']) === 0)
      header('HTTP/1.1 200 OK');
  }
} else {
  header('HTTP/1.1 401 Unauthorized');
  die('Authentication failed');
}


/*
$result = ['status' => 'success', 'data' => $_POST];
header('Content-Type: application/json');
echo json_encode($result);
*/
