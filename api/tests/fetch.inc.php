<?php 
function fetch($url, $method, $data = null) {
	$ch = curl_init(); 

	// options
	$options = array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_CUSTOMREQUEST => $method,
		CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
		CURLOPT_POSTFIELDS => json_encode($data)
	);

	curl_setopt_array($ch, $options);
	$output = json_decode(curl_exec($ch), true);
	curl_close($ch); 

	return $output;
}

function print_data($response) {
	echo '<pre>';
	print_r($response['data']);
	echo '</pre>';
}