<?php

require_once 'GoogleShopping.php';

$ean = '8806085553941';

$crawler = new GoogleShopping;
$prices = $crawler->getPrices($ean);

echo "<PRE>", json_encode($prices, JSON_PRETTY_PRINT), "</PRE>";

?>
