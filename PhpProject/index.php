<?php
$xml = simplexml_load_file('d:\\Programmierung\\Projekte\\Dokumentationen\\FAQ-Daten\\xml\\general\\php-doku-generator\\faq.xml');

var_dump( isset($xml->Question) );