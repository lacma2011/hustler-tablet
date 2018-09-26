<?php
if( !@$_SERVER["HTTP_X_REQUESTED_WITH"] ){
	//echo "SORRY";exit;
        header('HTTP/1.1 403 Forbidden');
        exit;
}
?>
