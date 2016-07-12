<?php

ini_set('display_errors',1);

use Illuminate\Database\Capsule\Manager as DB;

require 'vendor/autoload.php';

$settings =  [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new Slim\App($settings);

$app->get('/v1/patient.get', function ($request, $response, $args) {

	$patients = DB::select('select * from patients where 1');
	if (count($patients)>0)
    echo json_encode($patients,true); else {
    	$resp = $response->withStatus(204);
    	return $resp;
    }

});

$app->post('/v1/patient.add', function ($request, $response, $args) {
	$post = $request->getParsedBody();
	$resp = $status = 0;

		
	if (!isset($post['fullname']) || strlen($post['fullname']) < 6 || preg_match("/[a-zA-Z0-9]+/i", $post['fullname']) )
		$status = 401;

	if (!intval($post['phone']) || preg_match("/\D+/i", $post['phone']) || strlen(intval($post['phone'])) <> 11 ) $status = 402;

	if (!isset($post['diagnoz']) || !$post['diagnoz'] || strlen($post['diagnoz'])<2) $status = 403;

	if (!isset($post['price']) || preg_match("/\D+/i", $post['price']) || strlen($post['price'])>6 || intval($post['price'])<0) $status = 404;

	if ( isset($post['prepay']) && preg_match("/\D+/i", $post['prepay']) || strlen($post['prepay'])>6 || intval($post['prepay'])<0) $status = 405;

	if (!isset($post['date1']) || strlen($post['date1'])<10) $status = 406;

	if (isset($post['date2']) && strlen($post['date2'])<10) $status = 407;
	if (isset($post['date3']) && strlen($post['date3'])<10) $status = 408;
	if (isset($post['date4']) && strlen($post['date4'])<10) $status = 409;

	if ($status!=0) {
		$resp = $response->withStatus($status);
		return $resp;
	}
	
	$fullname = $post['fullname'];
	$phone = $post['phone'];
	$diagnoz = $post['diagnoz'];
	$price = $post['price'];
	$prepay = isset($post['prepay']) ? intval($post['prepay']) : 0;
	$date1 = strtotime(str_replace(' ', '', $post['date1']));
	$date2 = isset($post['date2']) ? strtotime(str_replace(' ', '', $post['date2'])) : 0;
	$date3 = isset($post['date3']) ? strtotime(str_replace(' ', '', $post['date3'])) : 0;
	$date4 = isset($post['date4']) ? strtotime(str_replace(' ', '', $post['date4'])) : 0;

	$patients = DB::insert('INSERT INTO patients (fullname,phone,diagnoz,price,prepay,date1,date2,date3,date4) VALUE(?,?,?,?,?,?,?,?,?)', [$fullname,$phone,$diagnoz,$price,$prepay,$date1,$date2,$date3,$date4]);

	$resp = $response->withStatus(201);

	return $resp;
    
});

$app->run();