<?php
	require_once("./include/membersite_config.php");
	$fgmembersite->DBLogin();
    $fgmembersite->RemoveUser();
	// $fgmembersite->RemoveCurrentUser();
	if($fgmembersite->ReorderPositions())
	{
		$fgmembersite->RedirectFirstPerson();
	}
?>
