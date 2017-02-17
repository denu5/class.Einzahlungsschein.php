<?php

require_once('class.einzahlungsschein.php');

$amount= "150.20";
$ref="5000001195";

$ezs = new Einzahlungsschein();
$ezs->setBankData("Berner Kantonalbank AG", "3001 Bern", "01-200000-7");
$ezs->setRecipientData("My Company Ltd.", "Exampleway 61", "3001 Bern", "123456");
$ezs->setPayerData("Heinz Müller", "Beispielweg 23", "3072 Musterlingen");
$ezs->setPaymentData($amount, $ref);

print_r($ezs);
echo $ezs->createCompleteReferenceNumber();
echo "\n";
echo $ezs->createBottomLineString();