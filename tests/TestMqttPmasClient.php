<?php

namespace test;

use PHPUnit\Framework\TestCase;

require "../MqttSender.php";


class TestMqttPmasClient extends TestCase {

  public function testMqttClient() {

    //pcntl_async_signals(true);

    $client = new \MqttSender();
    $client->connect();

    for ($i = 0; $i <= 10; $i++)
      $result = $client->send("{ \"test\": \"$i\" }");

    $this->assertEquals(0, $result);

  }


}
