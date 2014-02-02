<?
	require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

	$headers = "From: " . $_POST['from'] . "\r\n";
	
	if(mail($_POST['email'], $_POST['title'], $_POST['content'], $headers))
	{	
		update_user_meta($_POST['user'], $_POST['id'], 'post_shared');
	
		return true;
	}
	else
		return false;
?>