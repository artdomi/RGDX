<?PHP
    session_start();
    require_once("./include/membersite_config.php");
    // $fgmembersite->VerifyAdmin();
?>
<html>
<body>
<?PHP
    $fgmembersite->DBLogin();
    $fgmembersite->DisplayAdminQueue();
?>
</body>
</div>
</html>