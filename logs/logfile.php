<?php
include('../app/customLib/twilio/Services/Twilio.php');
include('../app/customLib/twilio/Services/Twilio/Twiml.php');
$con = mysqli_connect("localhost", "dezinowuser", "eqztCHFd7DDHn8pp", "dezinowlivedb");

$res = print_r( $_REQUEST, true );
$fromNo = $_REQUEST['fromNo'];
$toNo = $_REQUEST['toNo'];
$CallerId = $_REQUEST['Caller'];
$call_sid = $_REQUEST['CallSid'];
$callStatus = $_REQUEST['DialCallStatus'];
$callduration = $_REQUEST['DialCallDuration'];
if (mysqli_connect_errno())
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else{
    $response = new Services_Twilio_Twiml();
    $inserted = mysqli_query($con, "insert into `dn_call_logs` (`from_number`, `to_number`, `caller_id`, `call_status`,`dialCallDuration`,`call_sid`) values('$fromNo','$toNo','$CallerId','$callStatus','$callduration','$call_sid')");
}





//$inserted = mysqli_query($con, "insert into dn_call_logs (`response`) values('$response')");

//print $response;
//return $response;

?>