<?PHP
    session_start();
    require_once("./include/membersite_config.php");
    // $fgmembersite->VerifyAdmin();

    if(isset($_POST['firstname']))
    {
       // echo "Firstname is set! (".$_POST['firstname'].")";
       // echo "Submitted = (".$_POST['submitted'].")";
       $fgmembersite->GuestLogin();
       $fgmembersite->ReorderPositions();
    }
?>