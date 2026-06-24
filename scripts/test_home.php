<?php
$request = Illuminate\Http\Request::create('/home', 'GET');
$response = app()->handle($request);
echo $response->getStatusCode() . "\n";
echo substr($response->getContent(), 0, 500);
