<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function getLastXMonthsFirstDate($xMonth){
	$date	=	new DateTime('first day of this month');
	for ($i = 0; $i < $xMonth; $i++) {
		$date->modify('-1 month');
	}
	
	return $date->format('Y-m-d');
}