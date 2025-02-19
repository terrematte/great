<?php
set_time_limit(0);

$handle = fopen('php://input','r');
$jsonInput = fgets($handle);

$dec = json_decode($jsonInput,true);

$decoded = $dec['data'];

$course = $dec['course'];

//$dirfiles = '/opt/proofweb/files/'.$course ;
$dirfiles = '/opt/proofweb/tasks/'.$course ;

$fail = 0;

foreach($decoded as $n => $s) {
//    $d = $dirfiles .'/'. $n;
    $d = $dirfiles .'-'. $n;

    mkdir($d);

    foreach($decoded[$n]['nd'] as $i => $v){
		$file = $d. '/' .  $dec['code'] . '_' . ($i+1) . '_DN.v';
	        file_put_contents($file, $v);
	        if(! file_exists($file)){ $fail=$fail+1; }
		//exec("chown 1018:www-data -R $file");
	}

    foreach($decoded[$n]['sem'] as $i => $v){
		$file = $d . '/' . $dec['code'] . '_' . ($i+1) . '_SM.v';
	        file_put_contents($file, $v);
	        if(! file_exists($file)){ $fail=$fail+1; }
	       	//exec("chown 1018:www-data -R $file");
	}
    foreach($decoded[$n]['vld'] as $i => $v){
			$file = $d . '/.' . $dec['code'] . '_' . ($i+1) . "-$v". '.v';
			file_put_contents($file, "$v");
		if(! file_exists($file)){ $fail=$fail+1; }
                //exec("chown 1018:www-data -R $file");
        }


}

if($fail > 0){
	die('Ops, Saving Error!');
}

?>
