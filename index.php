<?php


//this line makes PHP behave in a more strict way
declare(strict_types=1);
//we are going to use session variables so we need to enable sessions
session_start();


//======================== Step 3: Switch between drinks and food ========================

//checks if the "food" is set in URL by both methods - $_GET can be used only for links
if (isset($_GET["food"]) || isset($_POST["food"])) {
    //checks if the value is set by $_GET otherwise sets it with $_POST ->always gets the value at the end
    $foodValue=isset($_GET["food"]) ? $_GET["food"]: $_POST["food"];
    //set up the food offer as default, shows offer of food and drinks on click on the link
    if ($foodValue == 0) {
        //food=0
        $products = [
            (object) ['name' => 'Cola', 'price' => 2],
            (object) ['name' => 'Fanta', 'price' => 2],
            (object) ['name' => 'Sprite', 'price' => 2],
            (object) ['name' => 'Ice-tea', 'price' => 3],
        ];
    } else {
        //food=1
        $products = [
            (object)  ['name' => 'Club Ham', 'price' => 3.20],
            (object) ['name' => 'Club Cheese', 'price' => 3],
            (object) ['name' => 'Club Cheese & Ham', 'price' => 4],
            (object)  ['name' => 'Club Chicken', 'price' => 4],
            (object)  ['name' => 'Club Salmon', 'price' => 5]
        ];
    }
} else {
    $products = [
        (object) ['name' => 'Club Ham', 'price' => 3.20],
        (object)  ['name' => 'Club Cheese', 'price' => 3],
        (object) ['name' => 'Club Cheese & Ham', 'price' => 4],
        (object) ['name' => 'Club Chicken', 'price' => 4],
        (object)  ['name' => 'Club Salmon', 'price' => 5]
    ];
    $foodValue="1";
}

//functions

//======================== Step 1: Validation ========================
//email validation
function test_email($data)
{
    return filter_var($data, FILTER_VALIDATE_EMAIL);
}
//security for $data
function test_input($data)
{
    $data = htmlspecialchars($data);
    return $data;
}
function whatIsHappening()
{
    echo '<h2>$_GET</h2>';
    var_dump($_GET);
    echo '<h2>$_POST</h2>';
    var_dump($_POST);
    echo '<h2>$_COOKIE</h2>';
    var_dump($_COOKIE);
    echo '<h2>$_SESSION</h2>';
    var_dump($_SESSION);
}


$totalValue = 0;


//========================  Step 5: Total revenue counter ========================

$newCookieValue = "";
if (isset($_POST["products"])) {    //gets an array with the ordered products
    foreach ($_POST["products"] as $key => $value) {    //loops per each chosen item
        if(is_numeric($value)){
            $totalValue += $products[$key]->price*$value;//object way
        }
    }
    if (isset($_POST["express_delivery"])) {    //checks if ex.del. check box is set
        $totalValue += (float)$_POST["express_delivery"]; //cast value into number (for math) and saves in var
    }
    $newCookieValue = (float)$_COOKIE["overviewCookie"] + $totalValue;  //cast COOKIe into string (total value number)
}
//sets the cookie value
if (!isset($_COOKIE["overviewCookie"])) {   //check if the C.are set
    setcookie("overviewCookie", "0", time() + (3600), "/");     //if not, set the value for 0
    $currentCookieValue = "0";
} else {
    if(!empty($newCookieValue)){    //if not empty ->has already data from previous order
        setcookie("overviewCookie", strval($newCookieValue), time() + (3600), "/");     //set C.with a new value
        $currentCookieValue = $newCookieValue;  //new becomes the current value
    }else{
        $currentCookieValue = $_COOKIE["overviewCookie"];   //saves current value inC.
    }
}



//======================== Step 2: Make sure the address is saved========================

//gets the data in the session - stays if refreshed ,deletes once the browser is closed
$street = (isset($_SESSION["street"])) ? $_SESSION["street"] : "";
$streetnumber = (isset($_SESSION["streetnumber"])) ? $_SESSION["streetnumber"] : "";
$city = (isset($_SESSION["city"])) ? $_SESSION["city"] : "";
$zipcode = (isset($_SESSION["zipcode"])) ? $_SESSION["zipcode"] : "";



//======================== Step 1: Validation ========================

$streetErr = $streetnumberErr = $cityErr = $zipcodeErr = $emailErr = "";
$sendEmail = false;
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sendEmail = true;

    //email will be filtered (correct) or false one (not valid email address)
    if (empty($_POST["email"])) {
        $sendEmail = false;
        $emailErr = "Please enter your email";
    } else {
        $email = test_email($_POST["email"]);
    }

    //street requirement + validation
    if (empty($_POST["street"])) {
        $streetErr = "Street is required";
        $sendEmail = false;
    } else {
        $street = test_input($_POST["street"]);
    }
    //  $streetErr= (!empty($_POST["street"])) ?  'test_input($_POST["street"])' : "Street is required";

    //streetNumber requirement - must be a number + validation
    $streetnumber = test_input($_POST["streetnumber"]);
    if (!empty($_POST["streetnumber"])) {
        if (is_numeric($streetnumber)) {
            $streetnumber = test_input($_POST["streetnumber"]);
        } else {
            $streetnumberErr = "A number required";
            $sendEmail = false;
        }
    } else {
        $streetnumberErr = "Street number is required";
        $sendEmail = false;
    }

    //city requirement + validation
    if (empty($_POST["city"])) {
        $cityErr = "City is required";
        $sendEmail = false;
    } else {
        $city = test_input($_POST["city"]);
    }

    //zipcode requirement - must be a number + validation
    $zipcode = test_input($_POST["zipcode"]);
    if (!empty($_POST["zipcode"])) {
        if (is_numeric($zipcode)) {
            $zipcode = test_input($_POST["zipcode"]);
        } else {
            $zipcodeErr = "A number required";
            $sendEmail = false;
        }
    } else {
        $zipcodeErr = "Zipcode is required";
        $sendEmail = false;
    }
}



//======================== Step 2: Make sure the address is saved ========================

//saves the data into session var
$_SESSION["street"] = $street;
$_SESSION["streetnumber"] = $streetnumber;
$_SESSION["city"] = $city;
$_SESSION["zipcode"] = $zipcode;



//======================== Step 4: Calculate the delivery time ========================

$date = date_create();
if (isset($_POST["express_delivery"])) {
    $expressDeliveryTime = date_modify($date, "+45 minutes");
    $deliveryTimeDisplay = date_format($date, "H:i") . "<br>";
} else {
    $deliveryTime = date_modify($date, "+2 hours");
    $deliveryTimeDisplay = date_format($date, "H:i") . "<br>";
}

//======================== Step 6: Send the e-mail ========================
//html email
/*
$from = "chcirasy@gmail.com";
$to = "chcirasy@gmail.com";
$subject = "Confirmation of order";
$message = "<div style=\"width:600px; margin:auto;\"><p><strong>Dear User</strong>,<br>Your order has been sent!<br> ETA <?php echo $deliveryTimeDisplay?><p><div>";

//header info
$header = "From: " . strip_tags($from) . "\r\n";
$header .= "Reply-To: " . strip_tags($from) . "\r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

//$mail = mail("chcirasy@gmail.com", "test", "Does it work?");

if ($mail == true) {
    echo("Your message has been sent.");
} else {
    echo("Failed to sent.");
}

*/
require 'form-view.php';









