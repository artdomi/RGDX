<?PHP
    session_start();
    require_once("./include/membersite_config.php");
    if (!$fgmembersite->VerifyAdmin()) $fgmembersite->RedirectToURL('index.html');

    if(isset($_GET['kickID']))
    {
       $fgmembersite->KickOutUser();
    }
    if(isset($_GET['id']))
    {
       $fgmembersite->ChangePositions();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- <meta charset="utf-8"> -->
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>RGDX Online LAB</title>
<script src="js/vendor/jquery-1.10.2.min.js"></script>
<script src="js/jquery-ui.min.js"></script>

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js"></script> -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script> -->
<script src="js/jquery.datetimepicker.js"></script>

<!-- <script type="text/javascript" src="js/timeout-dialog.js"></script> -->
<!-- <script type="text/javascript" src="http://www.princeton.edu/WebMedia/flash/player/jwplayer.js"></script> -->
<!-- <script src="AC_RunActiveContent.js" language="javascript"></script> -->
<!-- <script type="text/javascript" src="/jwplayer/jwplayer.js"></script> -->
<!-- <script type="text/javascript" src="js/jquery.noty.packaged.min.js"></script> -->
<!-- <link rel="stylesheet" type="text/css" href="css/LoginStyle.css"> -->
<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/ >
<link rel="stylesheet" type="text/css" href="css/admin.css">

<!-- <link rel="stylesheet" type="text/css" href="css/styles.css"> -->
<!-- <link rel="stylesheet" type="text/css" href="css/table.css"> -->
<!-- <link rel="stylesheet" href="css/timeout-dialog.css" type="text/css" media="screen, projection" /> -->
<link rel="stylesheet" href="font-awesome-4.0.3/css/font-awesome.min.css">
</head>
<body>

<!-- <header>
    <h1 class="adminTitle">RGDX Queue</h1>
</header>
<hr class="fancy-line"></hr> -->

<header>
    <div class="headercontainer">
        <div class="headerleft">
            <div class="ppplcontainer">
                    <!-- <a href="http://www.pppl.gov" target="_blank"><img src="./images/logo2.png" alt="PPPL_logo"
                    style="max-width:100%;max-height:70%" align="center"></a> -->
            </div>
        </div>
        <div class="headermain">
            <p class='adminTitle'><a><i class='adminTitle fa fa-users fa-fw'></i></a> RGDX Queue</p>
        </div>
        <div class="headerright">
        </div>
    </div>
</header>

<div id="adminHolder">
    <div id="QueueTable"></div>
</div>

<div class="LoginBox">
    <section id="LoginContent">
        <form id='login' method='post' accept-charset='UTF-8'>
            <!-- <p class="LoginTitle">LOG-IN</p> -->
            <div class="LogInContainer">
                <h2 class="LoginInfo">To reserve a place, enter the dates below:</h2>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
            </div>
            <div class="LogInContainer">
                <div class="LogInField">
                    <input autofocus type="text" maxlength="24" placeholder="First Name" required="" name="firstname" id="firstname"/>
                </div>
                <div class="LogInField">
                    <input required="" maxlength="100" placeholder="E-mail" name="email" id="email" type="text" />
                </div>
                <div class="LogInField">
                    <input required="" placeholder="From" name="reserve_from" id="reserve_from" type="text" />
                </div>
                <div class="LogInField">
                    <input required="" placeholder="To" name="reserve_to" id="reserve_to" type="text" />
                </div>
                <div class="LogInBtn">
                    <!-- <input type='submit' name='Submit' value="Go!" /> -->
                     <!-- <a href="#" onclick="SubmitForm('login');">Go</a> -->
                     <button type="submit" class="fa fa-chevron-right"></button>
                </div>
            </div>
        </form><!-- form -->
    </section><!-- content -->
</div><!-- container -->

</body>
<script>

    // $("#controls").show( "slide",{ }, 750 );
    // $("#Status").show( "puff",{ }, 750 );
    $(document).ready( function() {

        refreshTable();

        function getdate(){
            $date = $('#datetimepicker').val();
            alert();
        };

        // $('#date_timepicker').datetimepicker({
            // format:'Y-m-d H:i',
            // onChangeDateTime: getdate,
            // step: 30,
        // });

        $(function(){
         $('#reserve_from').datetimepicker({
          // format:'Y/m/d',
          format:'Y-m-d H:i',
          onShow:function( ct ){
           this.setOptions({
            maxDate:$('#reserve_to').val()?$('#reserve_to').val():false
           })
          },
          // timepicker:false
         });
         $('#reserve_to').datetimepicker({
          // format:'Y/m/d',
          format:'Y-m-d H:i',
          onShow:function( ct ){
           this.setOptions({
            minDate:$('#reserve_from').val()?$('#reserve_from').val():false,
            // minTime:$('#date_timepicker_start').val()?$('#date_timepicker_start').val():false
           })
          },
          // timepicker:false
         });
        });

    $(function() {
        $('#login').submit(function( event ) {
            $.ajax({
                    url: 'addguest.php',
                    type: 'POST',
                    dataType: 'html',
                    data: $('#login').serialize(),
                    success: function(content)
                    {
                        // $("#adminHolder").html(content);
                        $("#firstname").val("");
                        $("#reserve_from").val("");
                        $("#reserve_to").val("");
                    }
            });
            event.preventDefault();
            refreshTable();
        });
    });


    });

    //==========UPDATE STATUS, QUEUE TABLE AND LOAD CONTROLS AFTER WAIT=========

    function refreshTable(){
        $('#QueueTable').load('./getAdminTable.php', function(){
           setTimeout(refreshTable, 3000);
        });
    }
    //==========================================================================
    // function refreshQueue(){
    //     $('.QueueHolder').load('./getTable.php', function(){
    //        setTimeout(refreshQueue, 5000);
    //     });
    // }
</script>
</html>