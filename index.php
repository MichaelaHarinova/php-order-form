<?php


//this line makes PHP behave in a more strict way
declare(strict_types=1);
//we are going to use session variables so we need to enable sessions
session_start();


//set up the food offer as default, shows offer of food and drinks on click on the link
if (isset($_GET["food"])) {

    if ($_GET["food"] == 0) {
        $products = [
            ['name' => 'Cola', 'price' => 2],
            ['name' => 'Fanta', 'price' => 2],
            ['name' => 'Sprite', 'price' => 2],
            ['name' => 'Ice-tea', 'price' => 3],
        ];
    } else {
        $products = [
            ['name' => 'Club Ham', 'price' => 3.20],
            ['name' => 'Club Cheese', 'price' => 3],
            ['name' => 'Club Cheese & Ham', 'price' => 4],
            ['name' => 'Club Chicken', 'price' => 4],
            ['name' => 'Club Salmon', 'price' => 5]
        ];
    }

} else {

    $products = [
        ['name' => 'Club Ham', 'price' => 3.20],
        ['name' => 'Club Cheese', 'price' => 3],
        ['name' => 'Club Cheese & Ham', 'price' => 4],
        ['name' => 'Club Chicken', 'price' => 4],
        ['name' => 'Club Salmon', 'price' => 5]
    ];
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


$newCookieValue = "";
if (isset($_POST["products"])) {
    foreach ($_POST["products"] as $index) {
        $totalValue += $products[$index]["price"];
    }
    if (isset($_POST["express_delivery"])) {
        $totalValue += (int)$_POST["express_delivery"];
    }
    $newCookieValue = (float)$_COOKIE["overviewCookie"] + $totalValue;
}
if (!isset($_COOKIE["overviewCookie"])) {
    setcookie("overviewCookie", "0", time() + (86400 * 30), "/"); //one day
} else if(!empty($newCookieValue)){
    setcookie("overviewCookie", strval($newCookieValue), time() + (86400 * 30), "/"); //one day
}

require 'form-view.php';