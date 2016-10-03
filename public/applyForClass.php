<?php
require('vendor/twilio/sdk/Services/Twilio.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form fields and remove whitespace.

    //name
    $fullName = strip_tags(trim($_POST["name"]));
    $parts = explode(" ", $fullName);
    $lastName = array_pop($parts);
    $firstName = implode(" ", $parts);

    //phone
    $phoneNumber = trim($_POST["phone"]); //todo: scrub this to make data consistents
    $phoneNumber = preg_replace("/[^0-9.]+/", "", $phoneNumber);
                                                  
    //email
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    
    //event
    $eventNum = trim($_POST["event"]);

    
    //check if user has applied to classs
    if(!hasUserApplied($phoneNumber, intval($eventNum))){
        //apply user to class
        applyUser($phoneNumber, intval($eventNum));
        sendEmailToMe($fullName, $phoneNumber, $email);
        sendText($fullName, $phoneNumber);
    }
    
} else {
    // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }


/////////////////////
///HELPER METHODS////
/////////////////////

function applyUser($phoneNumber, $eventNum){
    $conn = dbConnection();
    
    $sql = "INSERT INTO apply (phone, eventNum)
                VALUES ('$phoneNumber', '$eventNum')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Congrats! You've applied to this class. We'll text you in a few days to let you know if you got selected.";
        
        http_response_code(200);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    
    
    
    //close SQL
    $conn->close();
}

function sendEmailToMe($name, $phone, $email){
    
     // Set the recipient email address.
      $recipient = "sean@untoldyoga.com";

      // Set the email subject.
      $subject = "$name applied up for the next class!";

      // Build the email content.
      $email_content = "Name: $name\n";
      $email_content .= "Email: $email\n";
      $email_content .= "Phone: $phone\n";

      // Build the email headers.
      $email_headers = "From: $name <$email>";

      // Send the email.
      if (mail($recipient, $subject, $email_content, $email_headers)) {
        // Set a 200 (okay) response code.
      } else {
        // Set a 500 (internal server error) response code.
      }
}

function sendText($fullName, $phoneNumber){
    
echo "<script type='text/javascript'>alert('$phoneNumber');</script>";
 
$client = new Services_Twilio("ACb96a18da857931b850c16568a0275715", "6470bf06ec1a71f57431fbaa74a964c9");

$toNumber = "+1";
$toNumber.= $phoneNumber;
$fromNumber = "+13126464724";
$message = "Hey there! Thanks for applying for an Untold Yoga class. We'll text you a week before the event to let you know if you got selected to attend :)";
$client->account->messages->sendMessage($fromNumber, $phoneNumber, $message); 

    
}

function hasUserApplied($phoneNumber, $eventNum){
    
     //connect to MYSQL DB
    $conn = dbConnection();
    
    //Query for all users with the entered phone
    $sql = "SELECT * FROM apply WHERE phone = '$phoneNumber' AND eventNum = '$eventNum'";
    $result = $conn->query($sql);
    
    //set flag to track if this user exists
    $hasApplied = false;
    
    //Check if user exists in our db...if there is a result
    if ($result->num_rows > 0) {
        $hasApplied = true;
    } 
    
    //close SQL
    $conn->close();

    return $hasApplied;
}

/////////////////////
///DB CONNECTION/////
/////////////////////
function dbConnection(){
    //connect to MYSQL DB
    $servername = "localhost";
    $username = "untoldyo_sean";
    $password = "Zdm-zHJ-e6Y-GQP";
    $dbname = "untoldyo_prod";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}


?>
