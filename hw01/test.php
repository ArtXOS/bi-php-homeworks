<?php

function getPrice($item) {

    if( preg_match("/((\d)(\.|,)*)+(\s)*(CZK|Kč|-)/", $item, $matches) > 0 ) {} 
    else if (  preg_match("/(CZK|Kč){1}(\s)*((\d)(\.|,)*)+/", $item, $matches) > 0 ) {}
    else { return NULL; }

    $final  = 0;
    $thousands = 0;
    $hundreds = 0;
    $leftover = 0;

    if ( preg_match("/((\d)*\.)*/", $matches[0], $tmp) > 0 ) {
        $thousands = floatval(preg_replace("/\./", '', $tmp[0]))*1000;
    }

    if( preg_match("/(\d)+(,|(\d)|(\w)){1}/", $matches[0], $tmp) > 0 ) {
        $hundreds = floatval(preg_replace("/,/", '',$tmp[0],));
    }

    if( preg_match("/(,)+(\d)+/", $matches[0], $tmp) > 0 ) {
        $leftover = floatval( preg_replace("/,/",'', $tmp[0]));
    }

    $final = $thousands + $hundreds + $leftover;

    return $final;
}


function sortList($list) {

    usort($list, function($a, $b) {
        return getPrice($a) > getPrice($b) ? 1 : -1;
    });

    return $list;
}

function sumList($list) {
	$sum = 0;
	
	foreach($list as $value) {
		$sum = $sum + getPrice($value);
	}

	return $sum;
}

if (count($argv) !== 2) {
	echo "Usage: php shopping.php <input>\n";
	exit(1);
}

$input = file_get_contents(end($argv));
$list = explode("\n", $input);
$list = sortList($list);
print_r($list);
print_r(sumList($list));