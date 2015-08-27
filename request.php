<?PHP

$ctx = stream_context_create(array(
    'http' => array(
        'timeout' => 0.2
        )
    )
);
file_get_contents("http://scied-web.pppl.gov/pressure_a/museum_display/0/40/0/0", 0, $ctx);

// $xml = file_get_contents("http://scied-web.pppl.gov/pressure_a/museum_display/0/40/0/0");
// $lines = file('http://scied-web.pppl.gov/pressure_a/museum_display/0/40/0/0');
// $response = http_get("http://scied-web.pppl.gov/pressure_a/museum_display/0/40/0/0", array("timeout"=>1), $info);
// $response = http_get("http://scied-web.pppl.gov/pressure_a/museum_display/0/40/0/0");
// $r = new HttpRequest('http://scied-web.pppl.gov/pressure_a/museum_display/0/40/0/0', HttpRequest::METH_GET);
?>