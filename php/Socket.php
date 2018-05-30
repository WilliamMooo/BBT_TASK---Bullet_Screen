<?php

require_once(__DIR__.'/SensitiveWord.php');

$sensitive = new SensitiveWord(__DIR__.'/chinese_dictionary.txt');

$ws = new swoole_websocket_server("0.0.0.0", 5555);

$ws->set(
   array(
      'log_file'  => '/tmp/log/swoole.log',
      'daemonize' => true
   )
);

$ws->on('open', function ($ws, $request){
   // do nothing
});

$ws->on('message', function($ws, $frame){
   global $sensitive;
   $data = json_decode($frame->data, true);
   $data['type'] = "message";
   $data['msg'] = $sensitive->filter($data['str']);
   $data['username'] = $data['username'];
   foreach($ws->connections as $fd){
      $ws->push($fd, json_encode($data));
   }
});

$ws->on('close', function($ws, $fd){
   //do nothing
});

$ws->start();

