<?php
$_REQUEST['nats']? $nats = $_REQUEST['nats'] : $nats = 'MC4wLjkuOS4wLjAuMC4wLjA';
//echo 'http://secure.hustler.com/signup/signup.php?nats=' . $nats . '&step=2';exit;
header('Location: http://secure.hustler.com/signup/signup.php?nats=' . $nats . '&step=2');
?>