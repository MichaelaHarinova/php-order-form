<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" type="text/css"
          rel="stylesheet"/>
    <title>Order food & drinks</title>
</head>
<body>

<?php
//gets the data in the session - stays if refreshed ,deletes once the browser is closed
$email = "";
$street = (isset($_SESSION["street"])) ? $_SESSION["street"] : "";
$streetnumber = (isset($_SESSION["streetnumber"])) ? $_SESSION["streetnumber"] : "";
$city = (isset($_SESSION["city"])) ? $_SESSION["city"] : "";
$zipcode = (isset($_SESSION["zipcode"])) ? $_SESSION["zipcode"] : "";


$streetErr = $streetnumberErr = $cityErr = $zipcodeErr = $emailErr = "";
$sendEmail = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sendEmail = true;

    //email will be filtered (correct) or false one (not valid email address)
    if (empty($_POST["email"])) {
        $sendEmail = false;
        $emailErr = "Please enter your email";
    } else {
        $email = test_email($_POST["email"]);
    }

    //street requirement
    if (empty($_POST["street"])) {
        $streetErr = "Street is required";
        $sendEmail = false;
    } else {
        $street = test_input($_POST["street"]);
    }
    //  $streetErr= (!empty($_POST["street"])) ?  'test_input($_POST["street"])' : "Street is required";

//streetNumber requirement - must be a number
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

//city requirement
    if (empty($_POST["city"])) {
        $cityErr = "City is required";
        $sendEmail = false;
    } else {
        $city = test_input($_POST["city"]);
    }

//zipcode requirement - must be a number
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
//saves the data into session var
$_SESSION["street"] = $street;
$_SESSION["streetnumber"] = $streetnumber;
$_SESSION["city"] = $city;
$_SESSION["zipcode"] = $zipcode;


function test_email($data)
{
    return filter_var($data, FILTER_VALIDATE_EMAIL);
}

function test_input($data)
{
    $data = htmlspecialchars($data);
    return $data;
}

//delivery time
$date = date_create();
if (isset($_POST["express_delivery"])) {
    $expressDeliveryTime = date_modify($date, "+45 minutes");
    $deliveryTimeDisplay = date_format($date, "H:i") . "<br>";
} else {
    $deliveryTime = date_modify($date, "+2 hours");
    $deliveryTimeDisplay = date_format($date, "H:i") . "<br>";
}


?>


<div class="container">
    <?php if ($sendEmail): ?>
        <h3 style="color:#005e00" class="alert alert-success" role="alert">Your order has been sent! ETA <?php echo $deliveryTimeDisplay?></h3>
    <?php endif ?>
    <h1>Order food in restaurant "the Personal Ham Processors"</h1>
    <nav>
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link active" href="?food=1">Order food</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?food=0">Order drinks</a>
            </li>
        </ul>
    </nav>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
        <div class="form-row">
            <div class="form-group col-md-6">

                <label for="email">E-mail:</label>
                <input type="text" id="email" name="email" class="form-control" value="<?php echo $email ?>"/>
                <span style="color:red"><?php echo $emailErr ?></span>
            </div>
            <div></div>
        </div>

        <fieldset>
            <legend>Address</legend>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="street">Street:</label>
                    <input type="text" name="street" id="street" class="form-control" value="<?php echo $street ?>">
                    <span style="color:red"><?php echo $streetErr ?></span>
                </div>
                <div class="form-group col-md-6">
                    <label for="streetnumber">Street number:</label>
                    <input type="text" id="streetnumber" name="streetnumber" class="form-control"
                           value="<?php echo $streetnumber ?>">
                    <span style="color:red"><?php echo $streetnumberErr ?></span>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" class="form-control" value="<?php echo $city ?>">
                    <span style="color:red"><?php echo $cityErr ?></span>
                </div>
                <div class="form-group col-md-6">
                    <label for="zipcode">Zipcode</label>
                    <input type="text" id="zipcode" name="zipcode" class="form-control" value="<?php echo $zipcode ?>">
                    <span style="color:red"><?php echo $zipcodeErr ?></span>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Products</legend>
            <?php foreach ($products as $i => $product): ?>
                <label>
                    <input type="checkbox" value="<?php echo $i ?>" name="products[<?php echo $i ?>]"/> <?php echo $product['name'] ?>
                    -
                    &euro; <?php echo number_format($product['price'], 2) ?></label><br/>
            <?php endforeach; ?>
        </fieldset>

        <label>
            <input type="checkbox" name="express_delivery" value="5"/>
            Express delivery (+ 5 EUR)
        </label>

        <button type="submit" name="sub" class="btn btn-primary">Order!</button>
    </form>

    <footer>You already ordered <strong>&euro; <?php echo $totalValue ?></strong> in food and drinks.</footer>
</div>

<style>
    footer {
        text-align: center;
    }
</style>

</body>
</html>
