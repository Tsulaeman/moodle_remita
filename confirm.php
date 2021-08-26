<?php

ini_set("error_reporting", E_ALL);

use enrol_remita\remita;

require_once "classes/remita.php";

$remita = new remita(
    "2547916",
    "1946",
    "4430731",
    "https://remitademo.net"
);

$res = $remita->verify(110008239453);

var_dump($res);