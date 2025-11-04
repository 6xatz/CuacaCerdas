<?php

function weather_coords(){
	return [
		'Jakarta'   => ['lat'=>-6.2,   'lon'=>106.8166],
		'Bogor'     => ['lat'=>-6.5971,'lon'=>106.8060],
		'Depok'     => ['lat'=>-6.4025,'lon'=>106.7942],
		'Tangerang' => ['lat'=>-6.1783,'lon'=>106.6319],
		'Bekasi'    => ['lat'=>-6.2349,'lon'=>106.9896],
		'Bandung'   => ['lat'=>-6.9175,'lon'=>107.6191],
		'Yogyakarta'=> ['lat'=>-7.7956,'lon'=>110.3695],
		'Semarang'  => ['lat'=>-6.9667,'lon'=>110.4167],
		'Surabaya'  => ['lat'=>-7.2504,'lon'=>112.7688],
		'Denpasar'  => ['lat'=>-8.65,  'lon'=>115.2167],
	];
}

function http_get_json_robust($url){
	if (function_exists('curl_init')){
		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CONNECTTIMEOUT => 6,
			CURLOPT_TIMEOUT => 8,
			CURLOPT_USERAGENT => 'fuzzy-minimal/1.0',
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2,
		]);
		$body = curl_exec($ch);
		curl_close($ch);
		if ($body === false || $body === '') return null;
		$j = json_decode($body, true);
		return is_array($j) ? $j : null;
	}
	$ctx = stream_context_create(['http'=>['timeout'=>8,'header'=>"User-Agent: fuzzy-minimal/1.0\r\n"]]);
	$body = @file_get_contents($url, false, $ctx);
	if ($body === false || $body === '') return null;
	$j = json_decode($body, true);
	return is_array($j) ? $j : null;
}

function get_weather_for_city($city){
	$coords = weather_coords();
	if (!isset($coords[$city])) return ['error'=>'unknown_city'];
	$lat = $coords[$city]['lat'];
	$lon = $coords[$city]['lon'];

	$url1 = 'https://api.open-meteo.com/v1/forecast?latitude=' . rawurlencode($lat)
		. '&longitude=' . rawurlencode($lon)
		. '&current=temperature_2m,relative_humidity_2m,wind_speed_10m';
	$j1 = http_get_json_robust($url1);
	if (is_array($j1) && isset($j1['current'])){
		$curr = $j1['current'];
		return [
			'city' => $city,
			'temperature' => isset($curr['temperature_2m']) ? (float)$curr['temperature_2m'] : null,
			'humidity'    => isset($curr['relative_humidity_2m']) ? (float)$curr['relative_humidity_2m'] : null,
			'wind'        => isset($curr['wind_speed_10m']) ? (float)$curr['wind_speed_10m'] : null,
		];
	}

	$url2 = 'https://api.open-meteo.com/v1/forecast?latitude=' . rawurlencode($lat)
		. '&longitude=' . rawurlencode($lon)
		. '&current_weather=true';
	$j2 = http_get_json_robust($url2);
	if (is_array($j2) && isset($j2['current_weather'])){
		$cw = $j2['current_weather'];
		return [
			'city' => $city,
			'temperature' => isset($cw['temperature']) ? (float)$cw['temperature'] : null,
			'humidity'    => null,
			'wind'        => isset($cw['windspeed']) ? (float)$cw['windspeed'] : null,
		];
	}

	return ['error'=>'upstream_failed'];
}
