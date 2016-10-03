<?php 

//signup Button Pressed
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //name
    $fullName = strip_tags(trim($_POST["name"]));
    
    $name = explode(" ", $fullName);
    $firstName = $name[0]; // George 
    $lastName =  $name[1]; // Washington

    
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
        //yay
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




    

    