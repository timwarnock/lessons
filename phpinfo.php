<?php


$taipei = new DateTime("now", new DateTimeZone('Asia/Taipei'));
echo "In Taipei it's " . $taipei->format('Y-m-d H:i:sP') . "<br />\n";
$newyork = new DateTime("now", new DateTimeZone('America/New_York'));
echo "In New York it's " . $newyork->format('Y-m-d H:i:sP') . "<br />\n";

#print_r(timezone_abbreviations_list());

#phpinfo();

?>
