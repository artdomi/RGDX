<?PHP
    session_start();
    require_once("./include/membersite_config.php");
    date_default_timezone_set('America/New_York');
    // $fgmembersite->VerifyAdmin();

    // if(isset($_POST['firstname']))
    // {
    //    // echo "Firstname is set! (".$_POST['firstname'].")";
    //    // echo "Submitted = (".$_POST['submitted'].")";
    //    $fgmembersite->GuestLogin();
    // }

    if(isset($_GET['kickID']))
    {
       $fgmembersite->KickOutUser();
    }
    if(isset($_GET['id']))
    {
       $fgmembersite->ChangePositions();
    }


    function check($dt) {
        $date = date("Y-m-d H:i:s");
        // print $date->format('Y-m-d H:i:s');
        // $date = gmdate("Y-m-d H:i:s");
        // date_default_timezone_set('America/New_York');
        // date_default_timezone_set('GMT');
        // date_default_timezone_set('Europe/Zurich');
        // $start = new DateTime($date);
        $start = new DateTime($date);
        // $date = localtime();
        // $start = new DateTimeZone('America/New_York');
        // print $start->format('Y-m-d H:i:s');
        $end   = new DateTime($dt);
        $diff  = $start->diff( $end );

        $i = $start->getTimestamp();
        $j = $end->getTimestamp();
        // $minutes = $diff->i;
        // return $minutes;
        // return ($j-$i);
        if ($end>$start) {$res = 'true';}else{$res='false';}
        // return ($end>$start);
        return ($res);
        // return $diff->format( ' %i days' );
    }
    // print check('2014-02-15 03:59:30');


//     $timezone = date_default_timezone_get();
// echo "The current server timezone is: " . $timezone;
    // $s1 = '10-16-2003 15:45:10';
    // $s2 = '10-16-2003 15:55:20';
    // $s1 = '2003-10-16 15:45:10';
    // $s2 = '2014-02-20 01:10:00';
    // $datetime1 = date_create('2009-10-11');
    // $datetime2 = date_create('2009-10-13');
    // $datetime1 = date_create($s1);
    // $datetime2 = date_create($s2);

    // $datetime2 = strtotime($s2);
    // $datenow = new DateTime("now");
    // echo $datenow;
    // $now = date('Y-m-d H:i:s');
    // $now = new DateTime(date('Y-m-d H:i:s'));
    // $datetime2 = date('Y-m-d H:i:s', strtotime($s2));


    // $timestamp = date('Y-m-d H:i:s', strtotime($datefrom));
    // $timestamp2 = date('Y-m-d H:i:s', strtotime($dateto));
    // $timeFirst  = strtotime('2014-02-20 18:20:20');
    // $timeSecond = strtotime('2011-05-13 19:20:24');

    $dt = '2014-06-14 19:20:32';

    $timeSecond = new DateTime($dt);
    // $timeSecond = new DateTime();
    // $timeSecond = $timeSecond->getTimestamp();
    echo 'TTT = '.$timeSecond->getTimestamp().'  ';

    $arr0 = array('one', 'two', 'three', 'four', 'stop', 'five');
    $arr = array('one', 'two', 'three', 'four', 'stop', 'five');
    // for ($i = 0; $i < 5; ++$i) {
        // $val='';
        while (list(, $val) = each($arr)) {
            if ($val == 'stop') {
                break 1;    /* You could also write 'break 1;' here. */
            }
            echo "$val<br />\n";
        }
    // }

    // $timeSecond = $timeFirst+10;

    // $timeSecond = date('Y-m-d H:i:s');
    // $timeSecond = $timeSecond->getTimestamp();

    // $date = new DateTime('2000-01-01');
    // $result = $date->format('Y-m-d H:i:s');



    // $differenceInSeconds = $timeSecond - $timeFirst;
    // echo 'timefirst='.$timeFirst;
    // echo 'timelast='.$timeSecond;
    // echo 'Difference='.$differenceInSeconds;

    // $start  = date('Y-m-d H:i:s');
    // $end    = date('Y-m-d H:i:s',strtotime($s2));
    $d_start    = new DateTime();
    echo $d_start->format('Y-m-d H:i:s').' ';
    // $d_end      = new DateTime($end);

    echo $d_start->getTimestamp().' ';
    // echo $d_start->getTimestamp()+10;
    $diff= $d_start->getTimestamp()+10;
    echo date('Y-m-d H:i:s',$diff);


    $date = new DateTime();
    echo $date->getTimestamp(). "<br>";
    $date->add(new DateInterval('PT10S')); // adds 674165 secs
    echo $date->getTimestamp();

    // echo $diff->format('Y-m-d H:i:s');
    // $diff = $d_start->diff($d_end);


    // echo $diff->format('%H:%i:%s');
    // echo $diff->format('%u');
    // echo $diff->getTimestamp();

    // echo strtotime($diff);
    // echo $diff->format('%s');

    // $datetime2 = new DateTime($s2);
    // $datetime2 = strtotime($s2);
    // $interval = date_diff($datetime2, $now);
    // $interval = date_diff($datetime2, $now);

    // $interval = $datetime2->diff($now);
    // echo $interval->format('%R%a days');
    // $interval = date_diff($now, $datetime2);
    // echo $interval->format('%R%a days');
    // echo $interval;
    // echo $datetime2;
    // echo date('Y-m-d H:i:s').date($datetime2);
    // echo date($datetime2);

    // $s1 = '2003-10-16 15:45:10';
    // $s2 = '2003-10-16 15:55:20';



    // $first_date = strtotime($s1);
    // $second_date = strtotime($s2);




    // $diff = abs(strtotime($s1) - strtotime($s2));
    // echo $diff;

    // $date1 = new DateTime($s1);
    // $date2 = new DateTime($s2);

    // $date1 = strtotime($s1);
    // $date2 = strtotime($s2);
    // $interval = $date1->diff($date2);

    // echo "difference " . $interval->y . " years, " . $interval->m." months, ".$interval->d." days ";
    // echo "difference " . $interval->days . " days ";



    // function format_interval(DateInterval $interval) {
    //     $result = "";
    //     if ($interval->y) { $result .= $interval->format("%y years "); }
    //     if ($interval->m) { $result .= $interval->format("%m months "); }
    //     if ($interval->d) { $result .= $interval->format("%d days "); }
    //     if ($interval->h) { $result .= $interval->format("%h hours "); }
    //     if ($interval->i) { $result .= $interval->format("%i minutes "); }
    //     if ($interval->s) { $result .= $interval->format("%s seconds "); }

    //     return $result;
    // }

    // $first_date = new DateTime("2012-11-30");
    // $second_date = new DateTime("2012-12-21");

    // $difference = $first_date->diff($second_date);
    // echo format_interval($difference);

    // $date = date_create_from_format('Y-M-d:H:i:s', $s);



    // $ymd = DateTime::createFromFormat('m-d-Y H:i:s', $s1)->format('Y-m-d H:i:s');
    // $ymd2 = DateTime::createFromFormat('m-d-Y H:i:s', $s2)->format('Y-m-d H:i:s');


    // $date->getTimestamp();

    // $s = '06/10/2011 19:00:02';
    // $date = strtotime($s);
    // echo date('Y-M-d H:i:s', $date);
    // echo date($ymd);

    // echo (date($ymd2)-date($ymd));
    // echo ($ymd->getTimestamp());
    // define("TIME_OUT", 300);    //SESSION LENGTH








    $fgmembersite->DBLogin();
    $reserved = mysql_query("SELECT * FROM queue WHERE reserve_from IS NOT NULL ORDER BY reserve_from ASC;", $fgmembersite->connection);
    $inqueue = mysql_query("SELECT id FROM queue WHERE reserve_from IS NULL ORDER BY position ASC;", $fgmembersite->connection);

    // $DataInQueue = array();
    // while($row = mysql_fetch_array($inqueue) ){
    //     $DataInQueue[] = $row[0];
    // }

    $num_reserved = mysql_num_rows($reserved);
    $num_inqueue = mysql_num_rows($inqueue);
    print ' res='.$num_reserved.' ';
    print ' inqueue='.$num_inqueue.' ';

    // $tablename = 'queue';
    // $fname = 'liutauras';
    // $position = 1;
    // $playcode = 12312;
    // $insert_query = 'INSERT INTO '.$tablename.'(
    //             firstname,
    //             position,
    //             sid,
    //             ip,
    //             time_given,
    //             playcode
    //             )
    //             values
    //             (
    //             "' . $fname . '",
    //             "' . $position .'",
    //             "' . session_id() .'",
    //             "' . $_SERVER['REMOTE_ADDR'] . '",
    //             "' . TIME_OUT . '",
    //             "' . $playcode . '"
    //             );';

    // $res = mysql_query($insert_query ,$fgmembersite->connection);



    $fgmembersite->ReorderPositions();

    // $r = 0;
    // $q = 0;
    // $POSITION=1;
    // $prevDate = date("Y-m-d H:i:s");
    // $start = new DateTime($prevDate);

    // while($row = mysql_fetch_array($reserved))
    // {
    //     $end   = new DateTime($row['reserve_from']);
    //     // print $end->format('Y-m-d H:i:s');
    //     $timeToRes = ($end->getTimestamp()-$start->getTimestamp());
    //     print ' '.$timeToRes.' ';
    //     print TIME_OUT;
    //     if ($timeToRes > TIME_OUT) {
    //         $num_fit = floor($timeToRes/TIME_OUT);
    //         print ' num_fit='.$num_fit;
    //         $nth_fit = 0;
    //         // print ($num_inqueue>=$q);
    //         while (($num_inqueue>$q) && ($nth_fit<$num_fit)) { //REAL PEOPLE
    //             //INSERT GUEST TO POSITION
    //             $sql_insert = 'UPDATE queue SET position='.$POSITION.' WHERE id='.$DataInQueue[$q].';';
    //             $result_insert = mysql_query($sql_insert, $fgmembersite->connection);
    //             if(!$result_insert) return false;

    //             $q++;
    //             $POSITION++;
    //             $nth_fit++;
    //         }
    //     }
    //     if ($timeToRes < -TIME_OUT) {
    //         //kick
    //         $delete_query = 'DELETE FROM queue WHERE id='.$row['id'].';';
    //         $result_delete = mysql_query($delete_query, $fgmembersite->connection);
    //         if(!$result_delete) return false;
    //         $prevDate = date("Y-m-d H:i:s");
    //         $start = new DateTime($prevDate);
    //     }else{
    //         //INSERT Reservation into POS
    //         $sql_insert = 'UPDATE queue SET position='.$POSITION.' WHERE id='.$row['id'].';';
    //         $result_insert = mysql_query($sql_insert, $fgmembersite->connection);
    //         if(!$result_insert) return false;
    //         $POSITION++;
    //         // $start = $row['reserve_to'];
    //         $start = new DateTime($row['reserve_to']);
    //     }
    // }

    // while ($q < $num_inqueue) {
    //     //insert guest into pos
    //     $sql_insert = 'UPDATE queue SET position='.$POSITION.' WHERE id='.$DataInQueue[$q].';';
    //     $result_insert = mysql_query($sql_insert, $fgmembersite->connection);
    //     if(!$result_insert) return false;

    //     $POSITION++;
    //     $q++;
    // }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- <meta charset="utf-8"> -->
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>RGDX Online LAB</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js"></script> -->
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="js/jquery.datetimepicker.js"></script>
<!-- <script type="text/javascript" src="js/timeout-dialog.js"></script> -->
<!-- <script type="text/javascript" src="http://www.princeton.edu/WebMedia/flash/player/jwplayer.js"></script> -->
<!-- <script src="AC_RunActiveContent.js" language="javascript"></script> -->
<!-- <script type="text/javascript" src="/jwplayer/jwplayer.js"></script> -->
<!-- <script type="text/javascript" src="js/jquery.noty.packaged.min.js"></script> -->
<link rel="stylesheet" type="text/css" href="css/admin.css">
<link rel="stylesheet" type="text/css" href="css/LoginStyle.css">

<!-- <link rel="stylesheet" type="text/css" href="css/styles.css"> -->
<!-- <link rel="stylesheet" type="text/css" href="css/table.css"> -->
<!-- <link rel="stylesheet" href="css/timeout-dialog.css" type="text/css" media="screen, projection" /> -->
<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/ >
<!-- <script src="jquery.js"></script> -->
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
            <p class='adminTitle'><a ><i class='adminTitle fa fa-users fa-fw'></i></a> RGDX Queue</p>
        </div>
        <div class="headerright">
            <!-- <div id="CurrentLevel"> -->
                <!-- <a href="#" class="LevelTab"><i class="fa fa-home fa-fw"></i> Level 1</a> -->
                <!-- <a href="#" class="LevelTab2"><i class="fa fa-rocket fa-fw"></i> Level 2</a> -->
            <!-- </div> -->
        </div>
    </div>
</header>

<div id="adminHolder">
    <!-- <div id="QueueTable"></div>
    <div id="BookingDIV">
        <input id="date_timepicker_start" type="text" >
        <input id="date_timepicker_end" type="text" >
    </div> -->

    <div class="LoginBox">
        <section id="LoginContent">
            <form id='login' method='post' accept-charset='UTF-8'>
                <!-- <p class="LoginTitle">LOG-IN</p> -->
                <h2 class="LoginInfo">To reserve a place, enter the dates below:</h2>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <div class="LogInContainer">
                    <div class="LogInField">
                        <input autofocus type="text" maxlength="24" placeholder="First Name" required="" name="firstname" id="firstname"/>
                        <input required="" name="reserve_from" id="reserve_from" type="text" >
                        <input required="" name="reserve_to" id="reserve_to" type="text" >
                        <!-- <div class="input_frame"> -->
                          <!-- <span class="icon"></span> -->
                          <!-- <input type="text" placeholder="email address..."> -->
                        <!-- </div> -->
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

</div>
</body>
<script>

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
    // Handler for .ready() called.
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
        // $("#Status").fadeOut({queue: false, duration: 'slow'});
        // $("#controls").animate({height:'220px'},{queue: false, duration: 'slow'});
    });

    });


    // $('#datetimepicker2').datetimepicker({
    //     datepicker:false,
    //     format:'H:i'
    // });

    // $('#datetimepicker4').datetimepicker({
    //   format:'d.m.Y H:i',
    //   lang:'ru'
    // });

    // $('#datetimepicker5').datetimepicker({
    //  datepicker:false,
    //  allowTimes:[
    //   '12:00', '13:00', '15:00',
    //   '17:00', '17:05', '17:20', '19:00', '20:00'
    //  ]
    // });

    // $('#datetimepicker7').datetimepicker({
    //  timepicker:false,
    //  formatDate:'Y/m/d',
    //  minDate:'-1970/01/02',//yesterday is minimum date(for today use 0 or -1970/01/01)
    //  maxDate:'+1970/01/02'//tommorow is maximum date calendar
    // });

    // var logic = function( currentDateTime ){
    //     // 'this' is jquery object datetimepicker
    //     if( currentDateTime.getDay()==6 ){
    //         this.setOptions({
    //             minTime:'11:00'
    //         });
    //     }else
    //         this.setOptions({
    //             minTime:'8:00'
    //         });
    // };
    // $('#datetimepicker_rantime').datetimepicker({
    //     onChangeDateTime:logic,
    //     onShow:logic
    // });

</script>
</html>