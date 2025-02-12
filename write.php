<?php
set_time_limit(0);

$handle = fopen('php://input','r');
$jsonInput = fgets($handle);

$dec = json_decode($jsonInput,true);

$decoded = $dec['data'];

foreach($decoded as $n => $s) {
    $d = '/opt/p/' . $n;

    mkdir($d);

    foreach($decoded[$n]['nd'] as $i => $v)
	file_put_contents($d . '/' . 'avaliacao_' . $dec['code'] . '_' . $i . '_nd.v', $v);

    foreach($decoded[$n]['sem'] as $i => $v)
	file_put_contents($d . '/' . 'avaliacao_' . $dec['code'] . '_' . $i . '_sem.v', $v);


    //echo('/opt/p/' . $n);
}

//file_put_contents('/opt

//die(json_encode($decoded)); 
?>
