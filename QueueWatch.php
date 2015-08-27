<?php
    require_once("./include/membersite_config.php");
    $fgmembersite->DBLogin();
    $fgmembersite->CheckTimer();
    $fgmembersite->CheckPulse();
    // $fgmembersite->ReorderPositions();
    if (($fgmembersite->GetOpenPosition()-1) < 1) {

    	$ctx = stream_context_create(array(
		    'http' => array(
		        'timeout' => 0.2
		        )
		    )
		);
        $fgmembersite->ChangeAccessKey();
        $AccessKey = $fgmembersite->GetAccessKey();
        $url = "http://scied-web.pppl.gov:8080/RGDX_Service/RGDX_control/0/0/40/1/0.5/0.5/" . $AccessKey;
        // url=("http://scied-web.pppl.gov:8080/rgdx_service/rgdx_control/"+Electrode.value+"/"+Electromagnet.value+"/"+Pressure.value+"/"+lights.value+"/"+MoveElectrode.value+"/"+MoveHC.value+"/"+AccessKey.value);

        // $url = "http://scied-web.pppl.gov:8080/RGDX_Service/RGDX_control/0/0/40/1/0.5/0";
		file_get_contents($url, 0, $ctx);
    }
    // $fgmembersite->RemoveCurrentUser();
    // $fgmembersite->TestAddEmail();
?>