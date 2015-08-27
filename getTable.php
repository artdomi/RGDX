<html>
<body>
<?PHP
    session_start();
    require_once("./include/membersite_config.php");
    $fgmembersite->DBLogin();

    $pos = $fgmembersite->GetUserPosition();
    $total = $fgmembersite->GetOpenPosition()-1;

    if ($fgmembersite->UserInQueue())
    {
    ?>
        <script>
        document.getElementById('controls').setAttribute('data-refresh', 1);
        $( "#PosInfo" ).html("<p>Position: <span class='posspanSmall'><?PHP echo $pos;?></span> of <span class='posspanSmall'><?PHP echo $total;?></span></p>");
        </script>
        <?PHP
        $fgmembersite->DisplayQueue();
    }
    else
    {
        $players = ($total == 1 ? 'human' : 'humans');
        if ($total > 0) {$fgmembersite->DisplayQueue(); $queueVisibility='show';}
                                                  else {$queueVisibility='hide';}
        $WaitingTime = $fgmembersite->MaxWaitTime();
            if ($WaitingTime >= 3600) {
                        $timeHMS =  gmdate("H:i", $WaitingTime);   // HH:MM:SS
                        $units = " hours";
                    } else {
                        $timeHMS =  gmdate("i:s", $WaitingTime);   // MM:SS
                        $units = " min";
                    }
        ?>
        <script>
        $('.QueueHolder').<?PHP echo $queueVisibility;?>();
        $( "#PosInfo" ).html("<p><i class='fa fa-users fa-fw' style='font-size:30px;'></i><span class='posspanSmall'> <?PHP echo $total;?></span> <?PHP echo $players;?> in the lab</p>");
        $( "#PosInfo2" ).html("<p><i class='fa fa-coffee fa-fw' style='font-size:30px;'></i><span class='posspanSmall3'><?PHP echo $timeHMS;?></span> <?PHP echo $units;?> to wait.</p>");
        </script>
        <?PHP

    }
?>
</body>
</html>