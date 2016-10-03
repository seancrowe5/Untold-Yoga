<?php
require_once('vendor/twilio/sdk/Services/Twilio.php');
$client = new Services_Twilio("ACb96a18da857931b850c16568a0275715", "6470bf06ec1a71f57431fbaa74a964c9");


$conn = dbConnection();

//Query for all users with the entered phone
$sql = "SELECT * FROM apply WHERE eventNum = '2'";
$result = $conn->query($sql);
$phones  = [];

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        
        array_push($phones,$row["phone"]);
        
    }
    
    getUsers($phones);
    

    sendMessage($phones);
    
    $conn->close();
  
    
} else {
    echo "0 results";
}
//close SQL


function getUsers($phones){
    $conn = dbConnection();
    $sql = "SELECT * FROM user WHERE phone IN (".implode(',',$phones).")";
    $results = $conn->query($sql);

    while ($row = mysqli_fetch_assoc($results)) {
        echo $row['phone']. "<br>";
        
    }
    
$conn->close();

}

function sendMessage($phones){
    
    $client = new Services_Twilio("ACb96a18da857931b850c16568a0275715", "6470bf06ec1a71f57431fbaa74a964c9");
    
//    $yogis = array("3306714458",
//"7083084396",
//"8476566152",
//"7166982144",
//"4193077436",
//"8473548816",
//"5024723618",
//"9544714160",
//"6308533446",
//"7024253215",
//"8083834023",
//"6302409012",
//"3035967021",
//"6164853580",
//"3145502015",
//"9379032350",
//"2245451235",
//"6304492395",
//"8478141320",
//"8472261310",
//"3307039667",
//"3125057035"
//                  );
    
    $message = "Good morning! It's going to be a HOT HOT HOT yoga class today. We'll have a case of water to share, but bring your own too! We're super excited to have Jen as our instructor this morning, she'll lead us through a great class. Arrival time is 10:45 at 2756 N. Pine Grove in Lincoln Park and will be donation based. Shoot us a text back if you need anything!";
        
    $fromNumber = "+13126464724";

    foreach ($yogis as $toNumber) {
        
        $toNumber = "+1" .$toNumber;
        echo "message sent to: '$toNumber' <br>" ;
        
        //$client->account->messages->sendMessage($fromNumber, $toNumber, $message); 
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