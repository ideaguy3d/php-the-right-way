<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 4/24/2018
 * Time: 12:48 AM
 */

//some settings
$random_images = array(
    'http://icons.iconarchive.com/icons/zairaam/bumpy-planets/256/07-jupiter-icon.png',
    'http://www.princeton.edu/~willman/planetary_systems/Sol/Saturn/Saturn.gif',
    'http://www.solstation.com/stars/venus.gif'
);

// the following img(s) are taking wayyyy to long to load & causing execution time outs
//'http://quest.nasa.gov/mars/background/images/mars.gif'


$cover_image = 'http://www.lovethispic.com/uploaded_images/20521-Rocky-Beach-Sunset.jpg';

//------------------
// - php code here -
//------------------
$randImg = $random_images[array_rand($random_images, 1)];

$cookie_name = "julius";

$q = isset($_GET["q"]) ? $_GET["q"] : "blank";

$cookie_value = "";

if($q === "blank") {
    $cookie_value = "randomImage=$randImg | div1=99 | div2=75 | div3=50 | div4=90";
    setcookie($cookie_name, $cookie_value, time() + (86400 * 60), "/", "julius3d.com");
    echo $cookie_value;
}
else if ($q === "init") {
    if(isset($_COOKIE[$cookie_name])) {
        $cValue = (string)$_COOKIE[$cookie_name];
        echo "$cValue";
    } else {
        $cookie_value = "randomImage=$randImg | div1=25 | div2=75 | div3=99 | div4=90";
        setcookie($cookie_name, $cookie_value, time() + (86400 * 60), "/", "julius3d.com");
        echo "$_COOKIE[$cookie_name]";
    }
}
else {
    $q = explode("|", $q);
    $cookie_value = "randomImage=$randImg | div1=$q[0] | div2=$q[1] | div3=$q[2] | div4=$q[3]";
    setcookie($cookie_name, $cookie_value, time() + (86400 * 60), "/", "julius3d.com");
    echo $_COOKIE[$cookie_name];
}