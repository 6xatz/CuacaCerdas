<?php

function triangular($x, $a, $b, $c) {
	if ($x <= $a || $x >= $c) return 0.0;
	if ($x == $b) return 1.0;
	if ($x > $a && $x < $b) return ($x - $a) / ($b - $a);
	return ($c - $x) / ($c - $b);
}

function temp_memberships($t){
	return [
		'dingin' => triangular($t, 10, 18, 24),
		'sejuk'  => triangular($t, 20, 25, 30),
		'panas'  => triangular($t, 28, 32, 37),
	];
}

function humidity_memberships($h){
	return [
		'kering' => triangular($h, 20, 35, 50),
		'normal' => triangular($h, 45, 60, 75),
		'lembab' => triangular($h, 70, 85, 95),
	];
}

function wind_memberships($w){ // km/h
	return [
		'pelan'  => triangular($w, 0, 5, 12),
		'sedang' => triangular($w, 8, 15, 25),
		'kencang'=> triangular($w, 20, 30, 45),
	];
}

// Simple rule base: weight
function fuzzy_recommendation($t, $h, $w){
	$temp = temp_memberships($t);
	$hum  = humidity_memberships($h);
	$wind = wind_memberships($w);

	$rules = [

		['if'=>['sejuk','normal','pelan'],  'act'=>'Olahraga luar', 'weight'=>1.0],
		['if'=>['sejuk','normal','sedang'],  'act'=>'Jalan santai',  'weight'=>0.9],
		['if'=>['sejuk','lembab','pelan'],   'act'=>'Jalan santai teduh', 'weight'=>0.95],
		['if'=>['sejuk','lembab','sedang'],  'act'=>'Indoor ringan', 'weight'=>0.8],

		['if'=>['panas','lembab','pelan'],  'act'=>'Ngopi/Indoor AC', 'weight'=>0.9],
		['if'=>['panas','normal','sedang'],  'act'=>'Indoor ringan', 'weight'=>0.8],
		['if'=>['panas','lembab','sedang'],  'act'=>'Indoor saja',   'weight'=>0.95],

		['if'=>['dingin','normal','pelan'],  'act'=>'Olahraga ringan', 'weight'=>0.7],
		['if'=>['dingin','lembab','pelan'],  'act'=>'Di rumah hangat', 'weight'=>1.0],
		['if'=>['sejuk','normal','kencang'],'act'=>'Di rumah',        'weight'=>0.9],
		['if'=>['panas','lembab','kencang'],'act'=>'Di rumah',        'weight'=>1.0],
		['if'=>['sejuk','lembab','kencang'],'act'=>'Di rumah',        'weight'=>0.95],
	];

	$scores = [];
	foreach ($rules as $r){
		list($tK,$hK,$wK) = $r['if'];
		$alpha = min($temp[$tK] ?? 0, $hum[$hK] ?? 0, $wind[$wK] ?? 0) * ($r['weight'] ?? 1);
		if ($alpha <= 0) continue;
		$scores[$r['act']] = max($scores[$r['act']] ?? 0, $alpha);
	}

	if (!$scores){
		return ['action'=>'Data kurang tegas, pilih aktivitas fleksibel', 'confidence'=>0.0,
			'memberships'=>['temp'=>$temp,'humidity'=>$hum,'wind'=>$wind]];
	}

	arsort($scores);
	$bestAction = array_key_first($scores);
	$confidence = $scores[$bestAction];
	return ['action'=>$bestAction, 'confidence'=>$confidence,
		'memberships'=>['temp'=>$temp,'humidity'=>$hum,'wind'=>$wind]];
}
