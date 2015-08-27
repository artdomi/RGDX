<?PHP
session_start();
require_once("./include/membersite_config.php");
$fgmembersite->DBLogin();
if(isset($_POST['firstname']))
{
   // echo "Firstname is set! (".$_POST['firstname'].")";
   // echo "Submitted = (".$_POST['submitted'].")";
   $fgmembersite->GuestLogin();
   $fgmembersite->ReorderPositions();
}
if(isset($_POST['email']))
{
   // if ($fgmembersite->GuestAddEmail($_POST['email']))
   // $message = $fgmembersite->RegisterUser();
   if ($fgmembersite->RegisterUser())
   {
    $message="Thanks!";
   }
   else
   {
    $message="Sorry. We couldn't send you an email.";
   }
}
else{
    $message="Please wait for your turn.";
}

if(isset($_SESSION['playcode']))
{
    $playURL = "http://$_SERVER[HTTP_HOST]/RGDX/index.html?playcode=".$_SESSION['playcode'];
}

// $fgmembersite->CheckTimer();    //CHECK REMAINING TIME OF THE 1'ST POS - FORCE KICKOUT AUTHORIZED
// $fgmembersite->CheckPulse();    //CHECK PULSE OF THE 1'ST USER - FORCE KICKOUT AUTHORIZED
// $fgmembersite->ReorderPositions();
if ($fgmembersite->CheckValidUser())
{
//-------------------- NO QUEUE - WAIT TIL JAVASCRIPT LOADS CONTROLS -----------
    ?>
        <html>
        <!-- <head> -->
        <!-- <link rel="stylesheet" type="text/css" href="css/LogInMessage.css" /> -->
        <!-- </head> -->
        <body>
        <div id="queueLogContainer">
            <div align="center" id="queueLogMessage" style="color:gray">
                <i class="fa fa-spinner fa-spin" style="font-size:35px; margin:15px;"></i>
                <h2>Loading the Plasma Lab...</h2>
            </div>
        </div>
        </body>
        <script type="text/javascript">
            // $('.QueueHolder').load('./getTable.php');
            // $('#Status').load('./getTime.php');
            $('#PosInfo2').fadeOut(500);
            $('.StatusInfo').load('./getTime.php'); //SPEED UP CONTROL LOADING

            // $( "#Status" ).effect('shake', 800);
            // $( "#Status" ).clearQueue();
            // $( "#Status").effect('shake', 'slow');
            $( "#Status").fadeIn(500);
            // $( "#controls" ).effect( "bounce", "slow" );
        </script>
        </html>
    <?PHP
//------------------------------------------------------------------------------
}
else
{
//--------------------- PLEASE WAIT MESSAGE + EMAIL NOTIFICATION----------------
    ?>
        <html>
        <head>
        <!-- <link rel="stylesheet" type="text/css" href="css/LogInMessage.css" /> -->
        </head>
        <body>
        <div id="queueLogContainer">
                    <div align="center"  id="queueLogMessage">
                        <!-- <i class="fa fa-spinner fa-spin" style="font-size:55px; margin:15px;"></i> -->
                        <h1 id="queue-warning">
                        <!-- <a style="color:#989898; height:70px;"><i class="fa fa-spinner fa-spin fa-lg"></i></a> -->
                            <script type="text/javascript">
                            // $("#Status").fadeOut({queue: false, duration: 'slow'});
                            // $("#controls").animate({height:'230px'},{queue: false, duration: 'slow'});
                            $('.QueueHolder').load('./getTable.php');
                            // $("#MessageHolder").animate({height:'60px'},"slow");
                            $('#PosInfo2').fadeOut(500);
                            $('#MessageHolder').fadeOut(300);   //SHOW QUIT BTN TO EXIT QUEUE OR RGDX
                            // $('.QueueHolder').effect('shake', "slow");
                            // $('.ButtonsLeft').show();   //SHOW QUIT BTN TO EXIT QUEUE OR RGDX
                            // $('#Status').animate({height:'240px'},"fast");
                            // $('.QueueHolder').load('./getTable.php');
                            // $( "#textcontainerRect" ).effect(shake, 700);
                            </script>
                            <?PHP
                            // echo "<h1 class='warning'>".$message."</h1>";
                            echo $message;
                            ?>
                        </h1>
                        <p class="playURLinfo"> To play on any other (compatible) computer, use this URL: </p>
                        <a href='<?PHP echo $playURL;?>' class="playURL"><?PHP echo $playURL;?></a>
                    </div>
                    <div id="EmailContent">
                        <form id='emailForm' method='post' accept-charset='UTF-8'>
                            <!-- <h2 class="LoginInfo">Want to receive a notification when you're close?</h2> -->
                            <input type='hidden' name='emailSubmitted' id='emailSubmitted' value='1'/>
                            <div class="LogInContainer">
                                <div class="LogInField">
                                    <input autofocus type="text" maxlength="64" placeholder="To get notified, enter your email here" required="" name="email" id="email"/>
                                </div>
                                <div class="LogInBtn">
                                    <!-- <input type='submit' name='Submit' value='Notify me!' /> -->
                                    <button type="submit">
                                       <i class="fa fa-envelope"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
        </div>
        </body>
        <script type="text/javascript">

        $(function() {
        // Handler for .ready() called.
        $("#controls").animate({height:'220px'},{queue: false, duration: 'slow'});

        $('#emailForm').submit(function( event ) {
            $(".LogInContainer").fadeOut(400);
            $.ajax({
                    url: 'queuepage.php',
                    type: 'POST',
                    dataType: 'html',
                    data: $('#emailForm').serialize(),
                    success: function(content)
                    {
                        $(".LogInBtn").fadeOut(200);
                        $("#controls").html(content);
                    }
            });
            event.preventDefault();
        });
        });
        </script>
        </html>
    <?PHP
//------------------------------------------------------------------------------
}
?>