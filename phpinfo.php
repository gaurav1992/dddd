<?php
echo phpinfo();exit;
echo "testing ";
session_id("driverid");
session_start();
$arr= array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
echo $_SESSION['allocated_driver'];
echo "<hr>";

$_SESSION['allocated_driver'] = array(8, 9, 10, 11, 12);
$_SESSION['allocated_driver'][count($_SESSION['allocated_driver'])]=67;


	
	

echo "last print";
print_r($_SESSION);
function generateRandomString($length = 10) {
$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$charactersLength = strlen($characters);
$randomString = '';
for ($i = 0; $i < $length; $i++) {
$randomString .= $characters[rand(0, $charactersLength - 1)];
}
return $randomString;
}
?>


