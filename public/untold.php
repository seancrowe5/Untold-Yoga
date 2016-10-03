<?php 
require_once('vendor/twilio/sdk/Services/Twilio.php');
$client = new Services_Twilio("ACb96a18da857931b850c16568a0275715", "6470bf06ec1a71f57431fbaa74a964c9");

//APPLY BUTTON PRESSED (phone number and name passed in post $data)
    //get the  phone number
    //get event number
    //insert new apply row with phone and event number
    //change UI to say "APPLIED"
    //save cookie for user and event num they applied for
    //twilio text message that says: "You've applied for the 8/4 class"

//SIGN UP BUTTON PRESSED
    //gets info




//signup Button Pressed
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
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
    
    //validation
    if ( empty($fullName) OR empty($phoneNumber) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Set a 400 (bad request) response code and exit.
        http_response_code(400);
        echo "Oops! There was a problem with your submission. Please complete the form and try again.";
        exit;
      }
    
    //check if users number exists in our DB
    if(!checkExistingUser($phoneNumber)){
        //user does not exist! create new user in User table
        createNewUser($firstName, $lastName, $email, $phoneNumber);
       
    }else{
        //user exists...lets' update their information
        updateUserInfo($firstName, $lastName, $email, $phoneNumber);
    }
    
    //add user applied table entry
    userApplied($phoneNumber, 2);
      
   
    
    //show alert to user: "You're applied! We'll send you a text a week before the event and let you know if you got accepted"         
}else {
      // Not a POST request, set a 403 (forbidden) response code.
      http_response_code(403);
      echo "There was a problem with your submission, please try again.";
    }

/////////////////////
///Helper Methods////
/////////////////////

function checkExistingUser($phone) {

    //connect to MYSQL DB
    $conn = dbConnection();
    
    //Query for all users with the entered phone
    $sql = "SELECT * FROM user WHERE phone = '$phone'";
    $result = $conn->query($sql);
    
    //set flag to track if this user exists
    $isUser = false;
    
    //Check if user exists in our db...if there is a result
    if ($result->num_rows > 0) {
        $isUser = true;
    } 
    
    //close SQL
    $conn->close();

    return $isUser;
}
function createNewUser($firstName, $lastName, $email, $phone){
    $conn = dbConnection();
    
    $sql = "INSERT INTO user (firstname, lastname, email, phone)
                VALUES ('$firstName', '$lastName', '$email', '$phone')";
    
    if ($conn->query($sql) === TRUE) {
        sendEmailToMe($firstName, $phone, $email);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    //close SQL
    $conn->close();
}
function updateUserInfo($firstName, $lastName, $email, $phoneNumber){
    $conn = dbConnection();
    
    $sql = "UPDATE user
            SET firstName='$firstName', 
                lastName='$lastName',
                email='$email',
                phone='$phoneNumber'
            WHERE phone='$phoneNumber'";
    
    if ($conn->query($sql) === TRUE) {
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    //close SQL
    $conn->close();
}
function  userApplied($phoneNumber, $eventNum){
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
      $subject = "$name signed up for the next class!";

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




    

    