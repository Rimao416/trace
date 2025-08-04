<?php
require 'vendor/autoload.php';
include 'Configs.php';


use Parse\ParseUser;

// Open login.php in case current user is logged out
$currUser = ParseUser::getCurrentUser();
// if ($currUser && $currUser->get("role") === 'admin') {
if ($currUser) {
    header('Refresh:0; url=dashboard/panel.php');
} else {
    header('Refresh:0; url=auth/login.php');
}

