<?php
require_once 'fb-login/vendor/autoload.php';
$fb = new Facebook\Facebook([
    'app_id' => '3394390013907608',
    'app_secret' => '99ad1e0e624bf10b03467ec7f45989bc',
    'default_graph_version' => 'v4.0'
]);