<?php 

require_once('vendor/twilio/sdk/Services/Twilio.php');
$client = new Services_Twilio("ACb96a18da857931b850c16568a0275715", "6470bf06ec1a71f57431fbaa74a964c9");

//$yogis = array(
//    "+18109232876" => "Kelly",
//    "+13127210099" => "Brian",
//    "+19168493420" => "Hannah",
//    "+16183351117" => "Lauren"
//
//);


$fromNumber = "+13126464724";

foreach ($yogis as $toNumber => $name) {
    echo "message sent to: '$toNumber'" ;
    $message = "Thanks for signing up for Untold Yoga :) Unfortunately, our class for this Sunday filled up quick. Look out for a text from us next week when we release the dates for the next couple classes! Just make sure to apply fast so you get on the list. Have a wonderful weekend!";
    
    $client->account->messages->sendMessage($fromNumber, $toNumber, $message); 
}

?>
