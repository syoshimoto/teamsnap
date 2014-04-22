<?php

$teamId = 123;
$rosterId = 456;
$token = 'XXXXXXXXXX';

function postIt($data) {
	global $teamId, $rosterId, $token;
	
	$curl = curl_init(); 
	$destination = "https://api.teamsnap.com/v2/teams/$teamId/as_roster/$rosterId/practices";
	curl_setopt($curl, CURLOPT_URL, $destination); 
	curl_setopt($curl, CURLOPT_POST, true); 
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false); 
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	// curl_setopt($curl, CURLOPT_VERBOSE, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt($curl,CURLOPT_HTTPHEADER,array (
	        "Content-Type: application/json",
			"X-Teamsnap-Token: $token"
	    ));

	$response = curl_exec($curl);

	curl_close($curl);
}

$rawfile = fopen("php://stdin", "r");
$events = array();
while (($line = fgetcsv($rawfile, 1000, ',')) !== FALSE) {
    if ($line[0] != 'Start Date') {
            $events[] = array(
			'date' => $line[0], 
			'time' => $line[1],
			'location' => $line[2],
			'shortname' => $line[3],
			'event' => $line[4]
		);
    }
}
fclose($rawfile);

date_default_timezone_set('America/Los_Angeles');

foreach ($events as $event) {
	print_r($event);
	$body = array();
	$payload = array();
	$payload['type'] = "Practice";
	$payload['team_id'] = $teamId;
	$payload['event_date_start'] = date('c', strtotime($event['date'] . " " . $event['time']));
	$payload['eventname'] = $event['event'];
	$payload['tracks_availability'] = true;
	$payload['can_set_availability'] = true;
	$payload['minutes_early'] = 10;
	$payload['duration_hours'] = 2;
	$payload['duration_minutes'] = 0;
	$payload['shortlabel'] = $event['shortname'] . ": " . $event['location'];
	if ($event['location'] == "LGCS Foothill Upper") {
		$payload['location_id'] = 2290011;
	} elseif ($event['location'] == "LGCS MVRC Field A") {
		$payload['location_id'] = 2290066;
	} elseif ($event['location'] == "LGCS Foothill Main") {
		$payload['location_id'] = 2820328;
	}
	$body['practice'] = $payload;
	print_r($body);
	// print_r(postIt(json_encode($body)));
}