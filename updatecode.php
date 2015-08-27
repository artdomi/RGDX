<?PHP
    session_start();
    require_once("./include/membersite_config.php");
    $fgmembersite->DBLogin();

    $goodcode = ( isset( $_GET['playcode'] ) && is_numeric( $_GET['playcode'] ) ) ? intval( $_GET['playcode'] ) : 0;

    if ($goodcode) {
        if ($fgmembersite->UpdateForCode())
            {
             // $codelegit = 1;    //SUCCESSFUL CHANGE OF IP & SID
            }
        else
            {
             // $codelegit = 2;    //CHANGE OF IP & SID IS NOT SUCCESSFUL
            }
            // $_SESSION['gotcode'] = '0';
    }
?>