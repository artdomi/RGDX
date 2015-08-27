<?PHP
    session_start();
    require_once("./include/membersite_config.php");
    $fgmembersite->DBLogin();
    // $fgmembersite->ReorderPositions();  //MOVE QUEUE FORWARD IF MIN(POS)>1, SET TIME_ELAPSED TO NOW() FOR POS=1
    $fgmembersite->CheckTimer();    //CHECK REMAINING TIME OF THE 1'ST POS - FORCE KICKOUT AUTHORIZED
    $fgmembersite->CheckPulse();    //CHECK PULSE OF THE 1'ST USER - FORCE KICKOUT AUTHORIZED
    $total = $fgmembersite->GetOpenPosition()-1; //NUMBER OF USERS

    if ($fgmembersite->UserInQueue()) {

        $firstname = $fgmembersite->GetCurrentUser();

        if ($fgmembersite->CheckValidUser()) {  //IF USER IS IN THE FIRST POS.
                $time = $fgmembersite->GetRemainingTime();
                $fgmembersite->Pulse();
                $timerTitle = "Remaining time:";
                $timerStyle = "posspanPlay";
                //----------------CHECK IF THE CONTROLS ARE LOADED--------------
                ?>
                <script>
                    var attr = $('#controls').data('refresh');
                    if (attr < 2){ //USER DOESN'T HAVE CONTROLS LOADED YET
                        $('#controls').data('refresh', '2'); //CONTROLS NOW LOADED
                        // $( ".devlog" ).append( "<li>data-refresh = " + $('#controls').data('refresh') + "(precaution)</li>" );
                        $('#MessageHolder').fadeOut(500);
                        $('#Status').fadeIn(500);   //SHOW QUIT BTN TO EXIT QUEUE OR RGDX
                        $("#controls").fadeIn(300);
                        $('#controls').animate({height:'400px'},"slow");
                        $("#controls").load("./controls.php");
                    }
                </script>
                <?PHP
                //--------------------------------------------------------------
        }
        else //USER WAITING IN QUEUE
        {
                $time = $fgmembersite->EstimateWaitTime();
                $timerTitle = "Wait time:";
                $timerStyle = "posspanWait";
                //------------CHECK IF 'PLEASE WAIT' PAGE IS LOADED-------------
                ?>
                <script>
                    var attr = $('#controls').data('refresh');
                    if (attr != 1){ //USER DOESN'T HAVE THE 'PLEASE WAIT' MESSAGE IN CONTROLS
                        $('#controls').data('refresh', '1'); //CONTROLS NOW LOADED
                        $("#Status").fadeIn(500);   //SHOW QUIT BTN TO EXIT QUEUE OR RGDX
                        $('#MessageHolder').fadeOut(500);   //SHOW QUIT BTN TO EXIT QUEUE OR RGDX
                        $("#controls").fadeIn(500);
                        $("#controls").load("./queuepage.php");
                    }
                </script>
                <?PHP
                //--------------------------------------------------------------
        }
        if ($time >= 3600) {    //IS REMAINING/WAITING TIME GREATER THAN 1H?
                    $timeHMS =  gmdate("H:i", $time);     // HH:MM:SS
                    $units = " hours";
                } else {
                    $timeHMS =  gmdate("i:s", $time);     // MM:SS
                    $units = " min";
                }
        ?>
        <a class="Firstname"><i class="fa fa-user fa-3x"></i> <?php echo $firstname; ?></a>
        <p class='fineNotice'><?php echo $timerTitle; ?></p>
        <p>
        <span class="<?php echo $timerStyle;?>" id="Counter"><?php echo $timeHMS;?><span class='posspanSmall2'><?php echo $units; ?></span></span>
        </p>
        <?PHP
    }
    else
    {
        ?>
        <script type="text/javascript">
            var attr = $('#controls').data('refresh');
            if (attr == 0){
                //CORRECT PAGE IS DISPLAYED IN CONTROLS DIV
            }
            else
            {    //DISPLAY LOGIN PAGE IN CONTROL DIV
                $('#Status').fadeOut(500);
                $('#MessageHolder').fadeIn(500);
                $("#controls").fadeIn(300);
                $("#controls").animate({height:'130px'},"fast");
                $("#controls").load("./loginRGDX.php");
                $('#controls').data('refresh', '0'); //CONTROLS NOW LOADED
            }
        </script>
        <?PHP
        if ($total == 0){       //NO ONE IN QUEUE
            $timerTitle = "";
            $MessageStyle = "StatusMessage";
            $MessageID = 1;
            $Message = "RGDX Status: Reconfiguring Video.";
        } else {                //SOMEONE IN QUEUE
            $WaitingTime = $fgmembersite->MaxWaitTime();
            if ($WaitingTime >= 3600) {
                        $timeHMS =  gmdate("H:i", $WaitingTime);   // HH:MM:SS
                        $units = " hours";
                    } else {
                        $timeHMS =  gmdate("i:s", $WaitingTime);   // MM:SS
                        $units = " min";
                    }
            if ($total < 3){    //LESS THAN 3 PEOPLE IN QUEUE
                $timerTitle = "EXPECTED WAIT:";
                $MessageStyle = "StatusMessage";
                $MessageID = 2;
                // $Message = "Only a few people in the queue - jump in!";
                $Message = "RGDX Status: Reconfiguring Video.";
            } else {            //M0RE THAN 3 PEOPLE IN QUEUE
                $timerTitle = "EXPECTED WAIT:";
                $MessageStyle = "StatusMessage";
                $MessageID = 3;
                $Message = "RGDX Status: Reconfiguring Video.";
            }
        }
        ?>
        <script>
            $('#PosInfo2').fadeIn(500);
            var messageOld = $('#MessageHolder').data('messageOld');
            var messageNew = '<?PHP echo $MessageID; ?>';
            if (messageOld != messageNew) {
                $('#MessageHolder').fadeIn(500);
                $('#MessageHolder').text("<?PHP echo $Message; ?>");
                document.getElementById("MessageHolder").className = "<?PHP echo $MessageStyle; ?>";
            }
            $('#MessageHolder').data('messageID', messageNew);
        </script>
        <?PHP
    }
?>
<script>
    //LOAD THE QUEUE TABLE IN A SEPARATE DIV
    $('.QueueHolder').fadeIn(500);
    $('.QueueHolder').load('./getTable.php');
</script>