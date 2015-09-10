<?PHP
/*
    Registration/Login script from HTML Form Guide
    V1.0

    This program is free software published under the
    terms of the GNU Lesser General Public License.
    http://www.gnu.org/copyleft/lesser.html


This program is distributed in the hope that it will
be useful - WITHOUT ANY WARRANTY; without even the
implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.

For updates, please visit:
http://www.html-form-guide.com/php-form/php-registration-form.html
http://www.html-form-guide.com/php-form/php-login-form.html

*/
require_once("./include/class.phpmailer.php");
require_once("./include/formvalidator.php");
define("TIME_OUT", 3600);    //SESSION LENGTH
define("PULSE_FREQ", 15);   //MAXIMUM TIMEOUT BETWEEN PULSES
define("PULSE_LIVES", 1);   //NUMBER OF PULSES NEGLECTED AT THE START
class FGMembersite
{
    var $admin_email;
    var $from_address;

    var $username;
    var $pwd;
    var $database;
    var $tablename;
    var $connection;
    var $rand_key;

    var $error_message;

    //-----Initialization -------
    function FGMembersite()
    {
        $this->sitename = 'RGDX Online Experiment';
        $this->rand_key = '0iQx5oBk66oVZep';
    }

    function InitDB($host,$uname,$pwd,$database,$tablename)
    {
        $this->db_host  = $host;
        $this->username = $uname;
        $this->pwd  = $pwd;
        $this->database  = $database;
        $this->tablename = $tablename;
        $this->from_address = 'rgdx.pppl@gmail.com';

    }
    function SetAdminEmail($email)
    {
        $this->admin_email = $email;
    }

    function SetWebsiteName($sitename)
    {
        $this->sitename = $sitename;
    }

    function SetRandomKey($key)
    {
        $this->rand_key = $key;
    }


    //-------Main Operations ----------------------
	function GuestLogin()
	{
		if(!isset($_POST['submitted']))
        {
           return false;
        }

        $formvars = array();
		$this->CollectGuestRegistrationSubmission($formvars);
        $_SESSION['SuperuserKey'] = '0';

		if($formvars['firstname'] == "PPPL*1234") {
            $this->RedirectAdmin("admin.php");
            return false;
        }
        if($formvars['firstname'] == "hal9000") {
			// $this->RedirectAdmin("admin.php");
            $_SESSION['SuperuserKey'] = $this->rand_key;
			// return true;
		}

		if(!$this->SaveGuestToDatabase($formvars))
        {
            return false;
        }
		return true;
	}

	function CollectGuestRegistrationSubmission(&$formvars)
	{
		$formvars['firstname'] = $this->Sanitize($_POST['firstname']);
        // $formvars['reserve_from'] = NULL;
        // $formvars['reserve_to'] = NULL;
        if(isset($_POST['reserve_from'])) { $formvars['reserve_from'] = $this->Sanitize($_POST['reserve_from']); }
        if(isset($_POST['reserve_to'])) { $formvars['reserve_to'] = $this->Sanitize($_POST['reserve_to']); }
	}

	function SaveGuestToDatabase(&$formvars)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }

        if (!$this->VerifyAdmin()){
            if(!$this->IsGuestFieldUnique($formvars))
            {
    			return false;
            }
        }

        $playcode = (mt_rand (10000000,100000000));
        // // session_start();
        $_SESSION['playcode'] = $playcode;

        if(!$this->InsertGuestIntoDB($formvars,'queue'))
        {
            $this->HandleError("Inserting to Database failed!");
            return false;
        }

        // if(!$this->InsertGuestIntoDB($formvars,'members'))
        // {
        //     $this->HandleError("Inserting to Database failed!");
        //     return false;
        // }

        return true;
    }

	function IsGuestFieldUnique($formvars) //REDUNDANT
    {
        $unique_query = "SELECT sid, ip FROM queue;";
        $result = mysql_query( $unique_query, $this->connection);
        while($row = mysql_fetch_array($result))
        {
            if($row['sid']==session_id() AND $row['ip']==$_SERVER['REMOTE_ADDR'])
			{
				return false;
			}
        }
        return true;
    }

	function InsertGuestIntoDB(&$formvars,$tablename)
    {
        $position = $this->GetOpenPosition();
		$positionRes = 0; //Position for Reservations
        // $playcode = (mt_rand (1000000000,10000000000));
        // $playcode = (mt_rand (10000000,100000000));
        // // session_start();
        // $_SESSION['playcode'] = $playcode;
        $_SESSION['firstname'] = $formvars['firstname'];
        // $datefrom = '2003-10-16 15:45:10';
        // $dateto = '2014-02-13 00:00:00';

        if(isset($_POST['reserve_from'])) {
            $datefrom = strtotime($this->SanitizeForSQL($formvars['reserve_from']));
            $dateto = strtotime($this->SanitizeForSQL($formvars['reserve_to']));
            $timestamp = date('Y-m-d H:i:s', $datefrom);
            $timestamp2 = date('Y-m-d H:i:s', $dateto);
            $differenceInSeconds = $dateto - $datefrom;
            if ($dateto <= $datefrom) {
                $dateto = $datefrom + 1800; //Precaution, set to 30min if faulty Reservation
                $timestamp2 = date('Y-m-d H:i:s', $dateto);
                $differenceInSeconds = $dateto - $datefrom;
            }

            $insert_query = 'INSERT INTO '.$tablename.'(
                firstname,
                position,
                time_given,
                playcode,
                time_expected,
                reserve_from,
                reserve_to
                )
                values
                (
                "' . $this->SanitizeForSQL($formvars['firstname']) . '",
                "' . $positionRes .'",
                "' . $differenceInSeconds . '",
                "' . $_SESSION['playcode'] . '",
                "' . $timestamp . '",
                "' . $timestamp . '",
                "' . $timestamp2 . '"
                );';
        }
        else{
            $insert_query = 'INSERT INTO '.$tablename.'(
                firstname,
                position,
                sid,
                ip,
                time_given,
                playcode
                )
                values
                (
                "' . $this->SanitizeForSQL($formvars['firstname']) . '",
                "' . $position .'",
                "' . session_id() .'",
                "' . $_SERVER['REMOTE_ADDR'] . '",
                "' . TIME_OUT . '",
                "' . $_SESSION['playcode'] . '"
                );';
        }

		if(!mysql_query($insert_query ,$this->connection))
        {
            // print 'ohno';
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
			return false;
        }
		return true;
    }
	function GetOpenPosition()
	{
        // $counter = mysql_query("SELECT COUNT(*) AS id FROM queue", $this->connection);
        // $num = mysql_fetch_array($counter);
        // $count = $num["id"];
        // echo("$count");

        // $position_query = "SELECT * FROM queue WHERE position > 0";
		$position_query = "SELECT MAX(position) AS max FROM queue;";
		$result = mysql_query( $position_query ,$this->connection);
        $row = mysql_fetch_array($result);
		// $row = mysql_num_rows($result);

        if(!$result || mysql_num_rows($result) <= 0)
        {
            return 1;
        }
        return $row['max']+1;
		// return $row+1;
	}

	function DisplayQueue()
    {
        $result = mysql_query("SELECT * FROM queue ORDER BY position;", $this->connection);
        // $result = mysql_query("SELECT ip, sid, position, firstname, time_start, reserve_from FROM queue ORDER BY position;", $this->connection);
        // $time_result = mysql_query("SELECT timediff(now(),time_start) as diff FROM queue;", $this->connection);
        $time_result = mysql_query("SELECT time_given - TIME_TO_SEC(timediff(now(),time_start)) as diff FROM queue WHERE position=1;", $this->connection);
        // mysql_query("SELECT time_given - TIME_TO_SEC(timediff(now(),time_start)) as diff FROM queue WHERE position=1

        // echo "<section class='queue-bckg'>";
        echo "<table><thead><tr class='headerrow'>";
        echo "<th><a><i class='fa fa-list-ol fa-fw'></i></a></th>";
        echo "<th><a><i class='fa fa-user fa-fw'></i>User</a></th>";
        // echo "<th class='Name'><i id='UserIcon'>User</i></th>";
        // echo "<th><i id='TimeIcon'>Elapsed</i></th></tr>";
        echo "<th><a><i class='fa fa-clock-o fa-fw'></i>Left</a></th>";
        echo "</tr></thead><tbody>";
        // echo"<div class='toprow'> <div class='posdiv_top' align='center'> Position </div>";
        // echo"<div class='playerdiv_top' align='center'> User / Time Elapsed </div></div>";
        // echo"<div class='timediv_top' align='center'>Time</div></div>";
        $q = 1;
        $Datetime_previous = new DateTime();
        $time_previous = $Datetime_previous->getTimestamp();

        while($row = mysql_fetch_array($result))
        {
            $playerClass = '';
            $timeHMS = '';
            $tag = '';

            $Datetime_next = new DateTime($row['time_expected']);
            $time_next = $Datetime_next->getTimestamp();
            if (($time_next - $time_previous) > TIME_OUT){
                $num_fit = floor(($time_next - $time_previous) / TIME_OUT);
                $nth_fit = 0;
                while ($nth_fit<$num_fit) { //REAL PEOPLE
                    echo "<tr class='freediv'><td>".$q.$tag."</td><td>FREE SLOT</td><td></td></tr>";
                    $q+=1;
                    $nth_fit++;
                }
            }
            $Datetime_previous = new DateTime($row['time_expected']);
            $time_previous = $Datetime_previous->getTimestamp();
            $time_previous += $row['time_given'];

            if ($q == 1) {
                $time_row = mysql_fetch_array($time_result);
                $timeHMS =  gmdate("i:s", $time_row['diff']).' min';     // MM:SS
            }

            if ($row['sid'] == session_id() AND $row['ip'] == $_SERVER['REMOTE_ADDR'])
            { $playerClass = 'playerdiv'; }

            if (!($row['reserve_from'] == NULL))
            {   $playerClass = 'reservediv';
                $tag = "<i class='fa fa-lock fa-fw'></i>";}

            echo "<tr class='".$playerClass."''><td>".$q.$tag."</td><td>".$row['firstname']."</td><td>".$timeHMS."</td></tr>";
            $q+=1;
        }
        echo "</tbody></table>";
        // echo "</section>";
        // echo "</table>";
        // echo "</div>";
    }

    function DisplayAdminQueue()
    {
        $result = mysql_query("SELECT * FROM queue ORDER BY position;", $this->connection);
        $num_rows = mysql_num_rows($result);

        // $time_result = mysql_query("SELECT timediff(now(),time_start) as diff FROM queue;", $this->connection);

        echo "<table class='adminTable'><thead><tr>";
        echo "<th><a><i class='fa fa-tag fa-fw'></i>ID</a></th>";
        echo "<th><a><i class='fa fa-user fa-fw'></i>Name</a></th>";
        echo "<th><a><i class='fa fa-list fa-fw'></i>Position</a></th>";
        echo "<th><a><i class='fa fa-tag fa-fw'></i>Session</a></th>";
        echo "<th><a><i class='fa fa-location-arrow fa-fw'></i>IP</a></th>";
        echo "<th><a><i class='fa fa-clock-o fa-fw'></i>Given</a></th>";
        echo "<th><a><i class='fa fa-clock-o fa-fw'></i>Start</a></th>";
        echo "<th><a><i class='fa fa-clock-o fa-fw'></i>Pulse</a></th>";
        echo "<th><a><i class='fa fa-envelope-o fa-fw'></i></a></th>";
        echo "<th><a><i class='fa fa-envelope-o fa-fw'></i></a><a><i class='fa fa-check fa-fw'></i></a></th>";
        echo "<th><a><i class='fa fa-key fa-fw'></i></a></th>";
        echo "<th class='btn-col'> </th>";
        // echo "<th class='btn-col'> </th>";
        // echo "<th class='btn-col'> </th>";
        echo "</tr></thead><tbody>";
        // echo"<div class='toprow'> <div class='posdiv_top' align='center'> Position </div>";
        // echo"<div class='playerdiv_top' align='center'> User / Time Elapsed </div></div>";
        // echo"<div class='timediv_top' align='center'>Time</div></div>";
        $q = 1;
        while($row = mysql_fetch_array($result))
        {

            if ($q == $num_rows) {$btn_down_class='btn btn-up btn-inactive';}
                            else {$btn_down_class='btn btn-up';}
            if ($q == 1)         {$btn_up_class='btn btn-up btn-inactive';}
                            else {$btn_up_class='btn btn-up';}
            // $time_row = mysql_fetch_array($time_result);

            // if ($q == 1) {
                echo "<tr>   <td class='table-id'>".$row['id']."</td>".
                            "<td class='table-name'>".$row['firstname']."</td>".
                            "<td class='table-pos'>".$row['position']."</td>".
                            "<td class='table-sid'>".$row['sid']."</td>".
                            "<td class='table-ip'>".$row['ip']."</td>".
                            "<td>".$row['time_given']."</td>".
                            "<td>".$row['time_start']."</td>".
                            "<td>".$row['time_pulse']."</td>".
                            "<td>".$row['email']."</td>".
                            "<td>".$row['email_sent']."</td>".
                            "<td>".$row['playcode']."</td>".
                            "<td class='btn-col'><a class='btn-admin ".$btn_up_class."' href='./admin.php?id=".$row['id']."&from=".$row['position']."&to=".($row['position']-1)."'><i class='fa fa-arrow-up fa-lg'></i></a>".
                            "<a class='btn-admin ".$btn_down_class."' href='./admin.php?id=".$row['id']."&from=".$row['position']."&to=".($row['position']+1)."'><i class='fa fa-arrow-down fa-lg'></i></a>".
                            "<a class='btn-admin btn-danger' href='./admin.php?kickID=".$row['id']."'><i class='fa fa-trash-o fa-lg'></i></a></td>".
                    "</tr>";
                // echo"<div class='row' style='color:limegreen;'><div class='posdiv' align='center'>".$q."</div>";
                // echo"<div class='playerdiv' align='center'>".$row['firstname']." / " . $time_row['diff'] . "</div></div>";
                // echo"<div class='timediv' align='center'>".$time_row['diff']."</div></div>";
            // }

            // else
            // {
            //     echo "<tr><td>".$q."</td><td>".$row['firstname']."</td><td></td></tr>";
            //     // echo"<div class='row'><div class='posdiv' align='center'>".$q."</div>";
            //     // echo"<div class='playerdiv' align='center'>".$row['firstname']."</div>";
            // }
            $q+=1;
        }
        echo "</tbody></table>";
        // echo "</table>";
        // echo "</div>";
    }

	function ReorderPositions()
	{

    $reserved = mysql_query("SELECT * FROM queue WHERE reserve_from IS NOT NULL ORDER BY reserve_from ASC;", $this->connection);
    $inqueue = mysql_query("SELECT id, position, time_given, time_start FROM queue WHERE reserve_from IS NULL ORDER BY position ASC;", $this->connection);
    $player = mysql_query("SELECT time_given, time_start FROM queue WHERE position=1;", $this->connection);

    $dateStart = new DateTime();
    $timeExpected = new DateTime();


    $timeExpected = $dateStart->getTimestamp();
    if($row = mysql_fetch_array($player))
        {
            // $timeExpected = $row['time_start'];
            $player_start  = new DateTime($row['time_start']);
            $timeExpected  = $player_start->getTimestamp();
        }
    else{
            $dateStart = new DateTime();
            $timeExpected = $dateStart->getTimestamp();
    }


    $QueueDataID = array();
    $QueueDataTIMEGIVEN = array();
    while($row = mysql_fetch_array($inqueue)){
        $QueueDataID[] = $row['id'];
        $QueueDataTIMEGIVEN[] = $row['time_given'];
        $QueueDataTIMESTART[] = $row['time_start'];
    }

    $num_reserved = mysql_num_rows($reserved);
    $num_inqueue = mysql_num_rows($inqueue)+1;

    if (($num_reserved > 0) OR ($num_inqueue>0)){
        $playerID = $this->getPlayerID();
    }
    $r = 0;
    $q = 0;
    $POSITION=1;
    $prevDate = date("Y-m-d H:i:s");
    $start = new DateTime($prevDate);
    // $dateStart = new DateTime();
    // $timeExpected = $dateStart->getTimestamp();
    $timeTOTALWAIT = $dateStart->getTimestamp();

    while($row = mysql_fetch_array($reserved))
    {
        $end   = new DateTime($row['reserve_from']);
        // print $end->format('Y-m-d H:i:s');
        $timeToRes = ($end->getTimestamp()-$start->getTimestamp());
        // print ' '.$timeToRes.' ';
        // print TIME_OUT;
        if ($timeToRes > TIME_OUT) {
            $num_fit = floor($timeToRes/TIME_OUT);
            // print ' num_fit='.$num_fit;
            $nth_fit = 0;
            // print ($num_inqueue>=$q);
            while (($num_inqueue>$q) && ($nth_fit<$num_fit)) { //REAL PEOPLE
                //INSERT GUEST TO POSITION

                 if ($q == ($num_inqueue-1)) {
                    $timeTOTALWAIT = $timeExpected;
                    $q++;
                    $nth_fit++;
                    break 1;    /* You could also write 'break 1;' here. */
                }

                // $diff= $d_start->getTimestamp()+10;
                // echo date('Y-m-d H:i:s',$diff);
                if ($row['position'] == 1 AND !($QueueDataTIMESTART[$q] == NULL)) {
                    $dateStart = new DateTime($row['time_start']);
                    $timeExpected = $dateStart->getTimestamp();
                }

                if (($POSITION==1) AND !($playerID == $QueueDataID[$q])){
                    $this->ReconfigurePlayers($QueueDataID[$q], $playerID, $timeExpected);
                }
                else{
                    $sql_insert = 'UPDATE queue SET position='.$POSITION.', time_expected=FROM_UNIXTIME('.$timeExpected.') WHERE id='.$QueueDataID[$q].';';
                    $result_insert = mysql_query($sql_insert, $this->connection);
                    // if(!$result_insert) return false;
                }

                $timeExpected += $QueueDataTIMEGIVEN[$q];
                $q++;
                $POSITION++;
                $nth_fit++;
            }
        }
        if ($timeToRes < -$row['time_given']) {
            $delete_query = 'DELETE FROM queue WHERE id='.$row['id'].';';
            $result_delete = mysql_query($delete_query, $this->connection);
            // if(!$result_delete) return false;
            $prevDate = date("Y-m-d H:i:s");
            $start = new DateTime($prevDate);
        }else{
            //INSERT Reservation into POS
            // if ($timeToRes < 0 AND $POSITION == 1) {$POSITION++;};

            if ($POSITION == 1) {
                $player_start = new DateTime($row['time_start']);
                // $given   = new DateTime($row['time_given']);
                // $time_given = $given->getTimestamp();
                $to   = new DateTime($row['reserve_to']);
                // $time_to = $to->getTimestamp();
                $till_end = ($to->getTimestamp() - $start->getTimestamp());
                // $timeExpected += $QueueDataTIMEGIVEN[$q];


                // $datefrom = strtotime($this->SanitizeForSQL($formvars['reserve_from']));
                // $timestamp0 = strtotime($player_start);
                // $timestamp0 = strtotime($row['time_start']);
                // $timestamp = date('Y-m-d H:i:s', $timestamp0);


                // $sql_insert = 'UPDATE queue SET position='.$POSITION.', time_given='.$till_end.', time_expected='.$timestamp.' WHERE id='.$row['id'].';';
                $sql_insert = 'UPDATE queue SET position='.$POSITION.', time_given='.$till_end.' WHERE id='.$row['id'].';';
            }
            else
            {
                $sql_insert = 'UPDATE queue SET position='.$POSITION.' WHERE id='.$row['id'].';';
            }
            // $sql_insert = 'UPDATE queue SET position=3 WHERE id=1121;';
            $result_insert = mysql_query($sql_insert, $this->connection);
            // if(!$result_insert) return false;
            $POSITION++;
            // $start = $row['reserve_to'];
            $start = new DateTime($row['reserve_to']);
            $timeExpected = $start->getTimestamp();
        }
    }

    while ($q < $num_inqueue) {
        //insert guest into pos
        // if (($POSITION==1) AND !($playerID == $QueueDataID[$q])){
            // $this->ReconfigurePlayers($QueueDataID[$q], $playerID);
        // }
        if ($q == ($num_inqueue-1)) {
            $timeTOTALWAIT = $timeExpected;
            $q++;
            break 1;    /* You could also write 'break 1;' here. */
        }

        $sql_insert = 'UPDATE queue SET position='.$POSITION.', time_expected=FROM_UNIXTIME('.$timeExpected.') WHERE id='.$QueueDataID[$q].';';
        // print date('Y-m-d H:i:s',$timeExpected);
        $result_insert = mysql_query($sql_insert, $this->connection);
        if(!$result_insert) return false;

        $timeExpected += $QueueDataTIMEGIVEN[$q];
        $POSITION++;
        $q++;
    }

    //UPDATE QUEUE INFO
    // echo 'TOT='.date('Y-m-d H:i:s',$timeTOTALWAIT);
    // $dateStart = new DateTime();
    // $timeExpected = $dateStart->getTimestamp();
    // $datestring = date('Y-m-d H:i:s',$timeExpected);
    // $datestring = $dateStart->format('Y-m-d H:i:s');
    // $datestring =date('Y-m-d H:i:s', strtotime('2010-10-12 15:09:00') );

    // $sql_insert = 'UPDATE queuestats SET total_wait_time='.$datestring.';';
    $sql_insert = 'UPDATE queuestats SET total_wait_time=FROM_UNIXTIME('.$timeTOTALWAIT.');';
    // $sql_insert = 'UPDATE queuestats SET status='.$timeExpected.';';
    $result_insert = mysql_query($sql_insert, $this->connection);
    if(!$result_insert) return false;


		// $result = mysql_query("SELECT MIN(position) AS min FROM queue;", $this->connection);
		// $row = mysql_fetch_array($result);
		// if(!$result)
  //       {
  //           $this->HandleDBError("Error reading data from the table");
		// 	return false;
  //       }
		// $row2 = mysql_fetch_array(mysql_query("SELECT time_start FROM queue WHERE position=1;", $this->connection));

		// if($row['min'] < 2)
		// {
		// 	if($row2['time_start'] == NULL){
		// 	    mysql_query("UPDATE queue SET time_start=NOW() WHERE position=1;",$this->connection);
  //           }
		// 	return false;
		// }
		// else
		// {
		// 	$diff = $row['min']-1;
		// 	mysql_query("UPDATE queue SET position = position - $diff;", $this->connection);
  //           mysql_query("UPDATE queue SET time_start=now() WHERE position=1;",$this->connection);
		// }
		return true;
	}

    function ReconfigurePlayers($newPlayerID, $oldPlayerID, $timeExpected)
    {
        $this->ChangeAccessKey();
        $sql_update = 'UPDATE queue SET position=1, time_expected=FROM_UNIXTIME('.$timeExpected.'), time_start=NOW() WHERE id='.$newPlayerID.';';
        $result_update = mysql_query($sql_update, $this->connection);
        if( !$result_update ) return false;

        $sql_update = 'UPDATE queue SET time_given=(time_given-TIME_TO_SEC(timediff(now(),time_start))), time_start=NULL WHERE id='.$oldPlayerID.' AND time_start IS NOT NULL;';
        $result_update = mysql_query($sql_update, $this->connection);
        if( !$result_update ) return false;

        return true;
    }

    function ChangePositions()
    {

        if(!$this->DBLogin())
            {
                $this->HandleError("Database login failed!");
                return false;
            }

        $id = $this->SanitizeForSQL($_GET['id']);
        $old_pos = $this->SanitizeForSQL($_GET['from']);
        $new_pos = $this->SanitizeForSQL($_GET['to']);
        if ($new_pos < 1) $new_pos = 1;


        if ($new_pos == 1) {    //FINALLY CHANGE THE INDIVIDUAL USER'S POSITION
            $sql_temp = "UPDATE queue SET position=0 WHERE id=$id;";
            mysql_query($sql_temp, $this->connection);
        } elseif ($old_pos == 1) {
            $sql_temp = "UPDATE queue SET position=0 WHERE id=$id;";
            mysql_query($sql_temp, $this->connection);
        }

        // ELEMENTS BETWEEN OLD AND NEW POSITION
        if ($new_pos > $old_pos) {
            $sql_reorder = "
                UPDATE queue
                   SET position=position-1
                 WHERE position > $old_pos
                   AND position <= $new_pos;";
        } else {
            $sql_reorder = "
                UPDATE queue
                   SET position=position+1
                 WHERE position < $old_pos
                   AND position >= $new_pos;";
        }

        $result_reorder = mysql_query($sql_reorder, $this->connection);

        if ($new_pos == 1) {    //FINALLY CHANGE THE INDIVIDUAL USER'S POSITION
            $sql_user = "UPDATE queue SET position=$new_pos, time_start=now() WHERE id=$id;";
            $sql_oldplayer = "UPDATE queue SET time_given=". TIME_OUT .", time_start=NULL, time_pulse=NULL WHERE position=2;";
        } elseif ($old_pos == 1) {
            $sql_user = "UPDATE queue SET position=$new_pos, time_start=NULL, time_pulse=NULL  WHERE id=$id;";
            $sql_oldplayer = "UPDATE queue SET time_start=NOW() WHERE position=1;";
        } else {
            $sql_user = "UPDATE queue SET position=$new_pos WHERE id=$id;";
        }

        if(isset($sql_oldplayer))
        {
            $result_oldplayer = mysql_query($sql_oldplayer, $this->connection);
            if(!$result_oldplayer) return false;
        }

        $result_user = mysql_query($sql_user, $this->connection);

        if(!$result_reorder || !$result_user){
            return false;
        }

        return true;
    }


	function RedirectFirstPerson()
	{
        $this->ChangeAccessKey();
		$result = mysql_query("SELECT position,sid,ip FROM queue;",$this->connection);

		while($row = mysql_fetch_array($result))
		{
			if($row['position'] == 1 AND $row['sid'] == session_id() AND $row['ip'] == $_SERVER['REMOTE_ADDR'])
			{
				mysql_query("UPDATE queue SET time_given=$time_out WHERE position=1;",$this->connection);
                $this->Pulse();
                return true;
			}
		}
        return false;
	}

	function CheckValidUser()
	{
		$result = mysql_query("SELECT sid, ip FROM queue WHERE position=1;", $this->connection);
		if($row = mysql_fetch_array($result))
		{
			if($row['sid'] == session_id() AND $row['ip'] == $_SERVER['REMOTE_ADDR'])
			{
                return true;
			}
		}
        return false;
	}

    function UserInQueue()
    {
        $result = mysql_query("SELECT * FROM queue WHERE sid='".session_id()."' AND ip='".$_SERVER['REMOTE_ADDR']."';", $this->connection);
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return false;
        }
        return true;
    }

	function RemoveCurrentUser()
	{
        $this->ChangeAccessKey();
		$delete_query = "DELETE FROM queue WHERE position=1;";
		$result = mysql_query($delete_query, $this->connection);
        if (!$result) {
            return false;
        }
        return true;
	}

    function RemoveUser()
    {
        session_start();
        $sessionid = session_id();
        $sessionip = $_SERVER['REMOTE_ADDR'];
        $result = mysql_query("DELETE FROM queue WHERE sid='$sessionid' AND ip='$sessionip';", $this->connection);
        // echo mysql_errno($result) . ": " . mysql_error($result). "\n";
        if (!$result) {
            return false;
        }
        return true;
    }

	function PurgeUsers()
	{
		$purge = "DELETE FROM queue WHERE position > 0;";
		mysql_query($purge, $this->connection);
	}
	function CheckTimer()
	{
        $result = mysql_query("SELECT reserve_to, time_start FROM queue WHERE position=1;", $this->connection);
        $row = mysql_fetch_array($result);

        if($row['time_start'] == NULL){
             mysql_query("UPDATE queue SET time_start=NOW() WHERE position=1;",$this->connection);
        }

        if (($row['reserve_to'] == NULL) && ($this->GetRemainingTime() <= 0)) {
    		// if($this->GetRemainingTime() <= 0)
    		// {
    			$this->RemoveCurrentUser();
                $this->ReorderPositions();
    		// }
        }

        if (!($row['reserve_to'] == NULL) && ($this->GetRemainingReservedTime() <= 0)) {
            // if($this->GetRemainingTime() <= 0)
            // {
                $this->RemoveCurrentUser();
                $this->ReorderPositions();
            // }
        }
	}
    function Pulse()
    {
        $result = mysql_query("SELECT time_pulse FROM queue WHERE position=1;", $this->connection);
        if($row=mysql_fetch_array($result)) {
            mysql_query("UPDATE queue SET time_pulse=NOW() WHERE position=1;", $this->connection);
            return true;
        }
        return false;
    }
    function CheckPulse()
    {
        $result = mysql_query("SELECT time_start, time_pulse, reserve_from FROM queue WHERE position=1;", $this->connection);
        if($row = mysql_fetch_array($result)) {
            if (($row['reserve_from']==NULL) && ($this->GetElapsedTime() > (PULSE_FREQ*PULSE_LIVES))) {
            // if ($this->GetElapsedTime() > (PULSE_FREQ*PULSE_LIVES))) {
                if ($row['time_pulse'] == NULL) {
                    $this->RemoveCurrentUser();
                    $this->ReorderPositions();
                    return false;
                }
                if ($this->GetLastBeatDiff() > PULSE_FREQ) {
                    $this->RemoveCurrentUser();
                    $this->ReorderPositions();
                    return false;
                }
            }
        }
        return true;
    }

    function GetLastBeatDiff()
    {
        $result = mysql_query("SELECT TIME_TO_SEC(timediff(now(),time_pulse)) as beat_diff FROM queue WHERE position=1;", $this->connection);
        if($row = mysql_fetch_array($result))
        {
            return $row['beat_diff'];
        } else {
            return 0;
        }
    }
	function GetAlottedTime()
	{
		$result = mysql_query("SELECT time_given FROM queue;", $this->connection);
		if($row = mysql_fetch_array($result))
		{
			return $row['time_given'];
		} else {
			return 0;
		}
	}

    function GetCurrentUser()
    {
        $sessionid = session_id();
        $sessionip = $_SERVER['REMOTE_ADDR'];
        $result = mysql_query("SELECT firstname FROM queue WHERE sid='$sessionid' AND ip='$sessionip'", $this->connection);
        if($row = mysql_fetch_array($result))
        {
            return $row['firstname'];
        } else {
            return "";
        }
    }
	function getPlayerID()
	{
        // $sessionid = session_id();
        // $sessionip = $_SERVER['REMOTE_ADDR'];
		$result = mysql_query("SELECT id FROM queue WHERE position=1;", $this->connection);
		if($row = mysql_fetch_array($result))
		{
			return $row['id'];
		} else {
			return false;
		}
	}
    function GetRemainingReservedTime()
    {
         $result = mysql_query("SELECT time_given - TIME_TO_SEC(timediff(NOW(), reserve_from)) as diff FROM queue WHERE position=1;", $this->connection);
         $row = mysql_fetch_array($result);
         return $row['diff'];
    }

	function GetRemainingTime()
	{
		 $result = mysql_query("SELECT time_given - TIME_TO_SEC(timediff(NOW(),time_start)) as diff FROM queue WHERE position=1;", $this->connection);
		 $row = mysql_fetch_array($result);
		 return $row['diff'];
	}

    function GetElapsedTime()
    {
         $result = mysql_query("SELECT TIME_TO_SEC(timediff(NOW(),time_start)) as diff FROM queue WHERE position=1;", $this->connection);
         $row = mysql_fetch_array($result);
         return $row['diff'];
    }

    function GetUserPosition()
    {
        $result = mysql_query("SELECT position FROM queue WHERE sid='".session_id()."' AND ip='".$_SERVER['REMOTE_ADDR']."';", $this->connection);
        if ($row = mysql_fetch_array($result))
        {
            return $row['position'];
        }
        return 0;
    }

    function EstimateWaitTime()
    {
        // $time_result = mysql_query("SELECT TIME_TO_SEC(timediff(now(),time_start)) AS diff FROM queue WHERE position=1", $this->connection);
        $time_result = mysql_query("SELECT TIME_TO_SEC(timediff(time_expected, now())) AS diff FROM queue WHERE sid='".session_id()."' AND ip='".$_SERVER['REMOTE_ADDR']."';", $this->connection);
        if($row = mysql_fetch_array($time_result)) {
            // $position = $this->GetUserPosition();
            // if($position > 1) {
            $estimate = $row['diff'];
            if ($estimate < 0|| $estimate > 80000) $estimate = 0;
            return $estimate;
            // }
        }
        return 0;
    }

    function MaxWaitTime()
    {

        // $reserved = mysql_query("SELECT * FROM queue WHERE reserve_from IS NOT NULL ORDER BY reserve_from ASC;", $this->connection);
        // // $inqueue = mysql_query("SELECT id FROM queue WHERE reserve_from IS NULL ORDER BY position ASC;", $this->connection);
        // $getLast = mysql_query("SELECT MAX(position) FROM queue WHERE reserve_from IS NULL;", $this->connection);
        // $lowestInQueue = mysql_fetch_array($getLast);

        // $reservedBelow = mysql_query("SELECT * FROM queue WHERE reserve_from IS NOT NULL AND position > ".$lowestInQueue." ORDER BY reserve_from ASC;", $this->connection);
        // $timeFrom = $getLast['time_start']+TIME_OUT;
        // while($row = mysql_fetch_array($reservedBelow)){
        //     // $nextReserved = mysql_query("SELECT reserve_from, reserve_to FROM queue WHERE reserve_from IS NOT NULL AND position = ".$position.";", $this->connection);
        //     // $resultNext = mysql_fetch_array($nextReserved)
        //     if $row['reserve_from']-$timeFrom > ){

        //     }
        //     $position++;
        // }

        $time_result = mysql_query("SELECT TIME_TO_SEC(timediff(total_wait_time, now())) as diff FROM queuestats WHERE id=1;", $this->connection);
        if($row = mysql_fetch_array($time_result))
        {
            $estimate = $row['diff'];
            if ($estimate < 0|| $estimate > 80000) $estimate = 0;
            // $estimate = ($this->GetOpenPosition()-1) * TIME_OUT - $row['diff'];
            return $estimate;
        } else {
            return 0;
        }
    }

    function RegisterUser()
    {
        // if(!isset($_POST['submitted']))
        // {
           // return false;
        // }

        $formvars = array();

        // if(!$this->ValidateRegistrationSubmission())
        // {
            // return false;
        // }

        $this->CollectRegistrationSubmission($formvars);

        $this->GuestAddEmail($formvars['email']);
        // if(!$this->SaveToDatabase($formvars))
        // {
            // return false;
        // }

        if(!$this->SendUserConfirmationEmail($formvars))
        {
            return false;
        }

        // $this->SendAdminIntimationEmail($formvars);

        return true;
    }

    function ConfirmUser()
    {
        if(empty($_GET['code'])||strlen($_GET['code'])<=10)
        {
            $this->HandleError("Please provide the confirm code");
            return false;
        }
        $user_rec = array();
        if(!$this->UpdateDBRecForConfirmation($user_rec))
        {
            return false;
        }

        $this->SendUserWelcomeEmail($user_rec);

        $this->SendAdminIntimationOnRegComplete($user_rec);

        return true;
    }

    function Login()
    {
        if(empty($_POST['username']))
        {
            $this->HandleError("username is empty!");
            return false;
        }

        if(empty($_POST['password']))
        {
            $this->HandleError("Password is empty!");
            return false;
        }

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if(!isset($_SESSION)){ session_start(); }
        if(!$this->CheckLoginInDB($username,$password))
        {
            return false;
        }

        $_SESSION[$this->GetLoginSessionVar()] = $username;

        return true;
    }

    function CheckLogin()
    {
         if(!isset($_SESSION)){ session_start(); }

         $sessionvar = $this->GetLoginSessionVar();

         if(empty($_SESSION[$sessionvar]))
         {
            return false;
         }
         return true;
    }

    function ChangeAccessKey()
    {
        $newKey = mt_rand(1000,10000);
        $result = mysql_query("UPDATE secret SET number='$newKey' WHERE user='public';", $this->connection);
        if($row = mysql_fetch_array($result))
        {
            return 1;
        } else {
            return 0;
        }
    }

    function GetAccessKey()
    {
        $result = mysql_query("SELECT number FROM secret WHERE user='public';", $this->connection);
        if($row = mysql_fetch_array($result))
        {   
            $newKey = $row['number'];
            return $newKey;
        } else {
            return 0;
        }
    }


    function UserFullName()
    {
        return isset($_SESSION['name_of_user'])?$_SESSION['name_of_user']:'';
    }

    function UserEmail()
    {
        return isset($_SESSION['email_of_user'])?$_SESSION['email_of_user']:'';
    }

    function LogOut()
    {
        session_start();

        $sessionvar = $this->GetLoginSessionVar();

        $_SESSION[$sessionvar]=NULL;

        unset($_SESSION[$sessionvar]);
    }

    function EmailResetPasswordLink()
    {
        if(empty($_POST['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        $user_rec = array();
        if(false === $this->GetUserFromEmail($_POST['email'], $user_rec))
        {
            return false;
        }
        if(false === $this->SendResetPasswordLink($user_rec))
        {
            return false;
        }
        return true;
    }

    function ResetPassword()
    {
        if(empty($_GET['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        if(empty($_GET['code']))
        {
            $this->HandleError("reset code is empty!");
            return false;
        }
        $email = trim($_GET['email']);
        $code = trim($_GET['code']);

        if($this->GetResetPasswordCode($email) != $code)
        {
            $this->HandleError("Bad reset code!");
            return false;
        }

        $user_rec = array();
        if(!$this->GetUserFromEmail($email,$user_rec))
        {
            return false;
        }

        $new_password = $this->ResetUserPasswordInDB($user_rec);
        if(false === $new_password || empty($new_password))
        {
            $this->HandleError("Error updating new password");
            return false;
        }

        if(false == $this->SendNewPassword($user_rec,$new_password))
        {
            $this->HandleError("Error sending new password");
            return false;
        }
        return true;
    }

    function ChangePassword()
    {
        if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
            return false;
        }

        if(empty($_POST['oldpwd']))
        {
            $this->HandleError("Old password is empty!");
            return false;
        }
        if(empty($_POST['newpwd']))
        {
            $this->HandleError("New password is empty!");
            return false;
        }

        $user_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$user_rec))
        {
            return false;
        }

        $pwd = trim($_POST['oldpwd']);

        if($user_rec['password'] != md5($pwd))
        {
            $this->HandleError("The old password does not match!");
            return false;
        }
        $newpwd = trim($_POST['newpwd']);

        if(!$this->ChangePasswordInDB($user_rec, $newpwd))
        {
            return false;
        }
        return true;
    }

    //-------Public Helper functions -------------
    function GetSelfScript()
    {
        return htmlentities($_SERVER['PHP_SELF']);
    }

    function SafeDisplay($value_name)
    {
        if(empty($_POST[$value_name]))
        {
            return'';
        }
        return htmlentities($_POST[$value_name]);
    }

    function RedirectToURL($url)
    {
        // header("Location: $url");
        // $_SESSION['AdminKey'] = $this->rand_key;
        ?><script>window.location.href="<?PHP echo $url; ?>";</script><?PHP
        exit;
    }

    function RedirectAdmin($url)
    {
        $_SESSION['AdminKey'] = $this->rand_key;
        ?><script>window.location.href="<?PHP echo $url; ?>";</script><?PHP
        exit;
    }

    function VerifyAdmin()
    {
        // header("Location: $url");
        if(isset($_SESSION['AdminKey'])){
            if($_SESSION['AdminKey'] == ($this->rand_key)){
                // AUTHORIZED
                return true;
            }
            else
            {
                // $this->RedirectToURL('main.php');
                return false;
            }
        }
        else
        {
            // $this->RedirectToURL('main.php');
            return false;
        }
    }

    function VerifySuperuser()
    {
        // header("Location: $url");
        if(isset($_SESSION['SuperuserKey'])){
            if($_SESSION['SuperuserKey'] == ($this->rand_key)){
                // AUTHORIZED
                return true;
            }
            else
            {
                // $this->RedirectToURL('main.php');
                return false;
            }
        }
        else
        {
            // $this->RedirectToURL('main.php');
            return false;
        }
    }

    function KickOutUser()
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        $kickID = $this->SanitizeForSQL($_GET['kickID']);

        $delete_query = mysql_query("DELETE FROM queue WHERE id='$kickID';", $this->connection);

        if(!$delete_query)
        {
            $this->HandleError("Wrong user ID!");
            return false;
        }

        $this->ReorderPositions();
        return true;
    }

    function GetSpamTrapInputName()
    {
        return 'sp'.md5('KHGdnbvsgst'.$this->rand_key);
    }

    function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
        $errormsg = nl2br(htmlentities($this->error_message));
        return $errormsg;
    }
    //-------Private Helper functions-----------

    function HandleError($err)
    {
        $this->error_message .= $err."\r\n";
    }

    function HandleDBError($err)
    {
        $this->HandleError($err."\r\n mysqlerror:".mysql_error());
    }

    function GetFromAddress()
    {
        if(!empty($this->from_address))
        {
            return $this->from_address;
        }

        $host = $_SERVER['SERVER_NAME'];

        $from ="nobody@$host";
        return $from;
    }

    function GetLoginSessionVar()
    {
        $retvar = md5($this->rand_key);
        $retvar = 'usr_'.substr($retvar,0,10);
        return $retvar;
    }

    function GuestAddEmail($email)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        // if(!isset($_POST['email']))
        // {
        //    // echo "no email provided!";
        //    return false;
        // }

        $email = $this->Sanitize($_POST['email']);

        $sessionid = session_id();
        $sessionip = $_SERVER['REMOTE_ADDR'];

        $qry = "UPDATE queue SET email='$email' WHERE sid='$sessionid' AND ip='$sessionip'";
        $qryMembers = "UPDATE members SET email='$email' WHERE sid='$sessionid' AND ip='$sessionip'";

        $result = mysql_query($qry,$this->connection);
        $resultMembers = mysql_query($qryMembers,$this->connection);

        if(!$result || !$resultsMembers)
        {
            $this->HandleError("Error updating user's email info.");
            return false;
        }

        return true;
    }

    function CheckLoginInDB($username,$password)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        $username = $this->SanitizeForSQL($username);
        $pwdmd5 = md5($password);
        $qry = "Select name, email from $this->tablename where username='$username' and password='$pwdmd5' and confirmcode='y'";

        $result = mysql_query($qry,$this->connection);

        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Error logging in. The username or password does not match");
            return false;
        }

        $row = mysql_fetch_assoc($result);


        $_SESSION['name_of_user']  = $row['name'];
        $_SESSION['email_of_user'] = $row['email'];

        return true;
    }

    function UpdateForCode()
    {
        // $this->DBLogin();
        // if(!$this->DBLogin())
        // {
            // $this->HandleError("Database login failed!");
            // return false;
        // }
        $playcode = $this->SanitizeForSQL($_GET['playcode']);
        // $playcode = $this->$_GET['playcode'];
        // $playcode = $_GET['playcode'];
        // $playcode = htmlspecialchars($_GET["playcode"]);

        $result = mysql_query("SELECT firstname, email FROM queue WHERE playcode=$playcode", $this->connection);
        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Wrong confirm code.");
            return false;
        }
        // $row = mysql_fetch_assoc($result);
        // $user_rec['name'] = $row['name'];
        // $user_rec['email']= $row['email'];

        $sessionid = session_id();
        $sessionip = $_SERVER['REMOTE_ADDR'];

        $qry = mysql_query("UPDATE queue SET sid='$sessionid', ip='$sessionip' WHERE  playcode=$playcode", $this->connection);

        // if(!mysql_query( $qry ,$this->connection))
        if(!$qry)
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }
        return true;
    }



    function UpdateDBRecForConfirmation(&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        $confirmcode = $this->SanitizeForSQL($_GET['code']);

        $result = mysql_query("Select name, email from $this->tablename where confirmcode='$confirmcode'",$this->connection);
        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Wrong confirm code.");
            return false;
        }
        $row = mysql_fetch_assoc($result);
        $user_rec['name'] = $row['name'];
        $user_rec['email']= $row['email'];

        $qry = "Update $this->tablename Set confirmcode='y' Where  confirmcode='$confirmcode'";

        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }
        return true;
    }

    function ResetUserPasswordInDB($user_rec)
    {
        $new_password = substr(md5(uniqid()),0,10);

        if(false == $this->ChangePasswordInDB($user_rec,$new_password))
        {
            return false;
        }
        return $new_password;
    }

    function ChangePasswordInDB($user_rec, $newpwd)
    {
        $newpwd = $this->SanitizeForSQL($newpwd);

        $qry = "Update $this->tablename Set password='".md5($newpwd)."' Where  id=".$user_rec['id']."";

        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error updating the password \nquery:$qry");
            return false;
        }
        return true;
    }

    function GetUserFromEmail($email,&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        $email = $this->SanitizeForSQL($email);

        $result = mysql_query("Select * from $this->tablename where email='$email'",$this->connection);

        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("There is no user with email: $email");
            return false;
        }
        $user_rec = mysql_fetch_assoc($result);


        return true;
    }

    function SendUserWelcomeEmail(&$user_rec)
    {
        $user_rec = array();
        // $row = mysql_fetch_assoc($result);
        $user_rec['name'] = 'Liutauras';
        $user_rec['email']= 'liutauras.rusaitis@gmail.com';


        $mailer = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
        $mailer->IsSendmail(); // telling the class to use SendMail transport

        $mailer->CharSet = 'utf-8';

        $mailer->AddAddress($user_rec['email'],$user_rec['name']);

        $mailer->Subject = "Welcome to ".$this->sitename;

        $mailer->From = $this->GetFromAddress();

        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "Welcome! Your registration  with ".$this->sitename." is completed.\r\n".
        "\r\n".
        "Regards,\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending user welcome email.");
            return false;
        }
        return true;
    }

    function SendAdminIntimationOnRegComplete(&$user_rec)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();

        $mailer->CharSet = 'utf-8';

        $mailer->AddAddress($this->admin_email);

        $mailer->Subject = "Registration Completed: ".$user_rec['name'];

        $mailer->From = $this->GetFromAddress();

        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$user_rec['name']."\r\n".
        "Email address: ".$user_rec['email']."\r\n";

        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }

    function GetResetPasswordCode($email)
    {
       return substr(md5($email.$this->sitename.$this->rand_key),0,10);
    }

    function SendResetPasswordLink($user_rec)
    {
        $email = $user_rec['email'];

        $mailer = new PHPMailer();

        $mailer->CharSet = 'utf-8';

        $mailer->AddAddress($email,$user_rec['name']);

        $mailer->Subject = "Your reset password request at ".$this->sitename;

        $mailer->From = $this->GetFromAddress();

        $link = $this->GetAbsoluteURLFolder().
                '/resetpwd.php?email='.
                urlencode($email).'&code='.
                urlencode($this->GetResetPasswordCode($email));

        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "There was a request to reset your password at ".$this->sitename."\r\n".
        "Please click the link below to complete the request: \r\n".$link."\r\n".
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }

    function SendNewPassword($user_rec, $new_password)
    {
        $email = $user_rec['email'];

        $mailer = new PHPMailer();

        $mailer->CharSet = 'utf-8';

        $mailer->AddAddress($email,$user_rec['name']);

        $mailer->Subject = "Your new password for ".$this->sitename;

        $mailer->From = $this->GetFromAddress();

        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "Your password is reset successfully. ".
        "Here is your updated login:\r\n".
        "username:".$user_rec['username']."\r\n".
        "password:$new_password\r\n".
        "\r\n".
        "Login here: ".$this->GetAbsoluteURLFolder()."/login.php\r\n".
        "\r\n".
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }

    function ValidateRegistrationSubmission()
    {
        //This is a hidden input field. Humans won't fill this field.
        if(!empty($_POST[$this->GetSpamTrapInputName()]) )
        {
            //The proper error is not given intentionally
            $this->HandleError("Automated submission prevention: case 2 failed");
            return false;
        }

        $validator = new FormValidator();
        $validator->addValidation("name","req","Please fill in Name");
        $validator->addValidation("email","email","The input for Email should be a valid email value");
        $validator->addValidation("email","req","Please fill in Email");
        $validator->addValidation("username","req","Please fill in Username");
        $validator->addValidation("password","req","Please fill in Password");


        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
            return false;
        }
        return true;
    }

    function CollectRegistrationSubmission(&$formvars)
    {
        // $formvars['name'] = $this->Sanitize($_POST['name']);
        $formvars['email'] = $this->Sanitize($_POST['email']);
        // $formvars['email'] = 'liutauras.rusaitis@gmail.com';
        // $formvars['firstname'] = 'User';
        // $formvars['username'] = $this->Sanitize($_POST['username']);
        // $formvars['password'] = $this->Sanitize($_POST['password']);
    }

    function SendUserConfirmationEmail(&$formvars)
    {
        $formvars['name'] = 'NAME';

        $mail = new PHPMailer(true); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch

        try {
          $mail->IsSMTP(); // enable SMTP
          $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
          $mail->SMTPAuth = true; // authentication enabled
          $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
          // $mail->SMTPSecure = "tls";
          $mail->Host = "smtp.gmail.com";
          $mail->Port = 465;//465; // or 587
          $mail->IsHTML(true);
          $mail->Username = "rgdx.pppl@gmail.com";
          $mail->Password = "PPPL*1234";

          // ------------------------------------------------------------------------
          $body = file_get_contents('EmailContent.html');
          $link = 'http://scied-web.pppl.gov/RGDX/?playcode='.$_SESSION['playcode'];
          $body = str_replace('{LINK}', $link, $body);
          $body = str_replace('{NAME}', $_SESSION['firstname'], $body);
          // $body = preg_replace('/[\]/','',$body);

          $mail->AddReplyTo('rgdx.pppl@gmail.com', 'RGDX');
          // $mail->AddAddress('liutauras.rusaitis@icloud.com', 'Liu Rus');
          $mail->AddAddress($formvars['email'], $formvars['name']);
          $mail->SetFrom('rgdx.pppl@gmail.com', 'RGDX Team');
          $mail->AddReplyTo('rgdx.pppl@gmail.com', 'RGDX Team');
          $mail->Subject = 'Welcome to the RGDX Experiment!';
          $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically

          $mail->MsgHTML($body);
          // $mail->AddAttachment('images/phpmailer.gif');      // attachment
          // $mail->AddAttachment('images/phpmailer_mini.gif'); // attachment
          $mail->Send();

        } catch (phpmailerException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    function GetAbsoluteURLFolder()
    {
        $scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
        $scriptFolder .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
        return $scriptFolder;
    }

    function SendAdminIntimationEmail(&$formvars)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();

        $mailer->CharSet = 'utf-8';

        $mailer->AddAddress($this->admin_email);

        $mailer->Subject = "New registration: ".$formvars['name'];

        $mailer->From = $this->GetFromAddress();

        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$formvars['name']."\r\n".
        "Email address: ".$formvars['email']."\r\n".
        "Username: ".$formvars['username'];

        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }

    function SaveToDatabase(&$formvars)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        if(!$this->Ensuretable())
        {
            return false;
        }
        if(!$this->IsFieldUnique($formvars,'email'))
        {
            $this->HandleError("This email is already registered");
            return false;
        }

        if(!$this->IsFieldUnique($formvars,'username'))
        {
            $this->HandleError("This username is already used. Please try another username");
            return false;
        }
        if(!$this->InsertIntoDB($formvars))
        {
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
        return true;
    }

    function IsFieldUnique($formvars,$fieldname)
    {
        $field_val = $this->SanitizeForSQL($formvars[$fieldname]);
        $qry = "select username from $this->tablename where $fieldname='".$field_val."'";
        $result = mysql_query($qry,$this->connection);
        if($result && mysql_num_rows($result) > 0)
        {
            return false;
        }
        return true;
    }

    function DBLogin()
    {

        $this->connection = mysql_connect($this->db_host,$this->username,$this->pwd);
        // $this->connection = mysql_connect('localhost',$this->username,$this->pwd);

        if(!$this->connection)
        {
            $this->HandleDBError("Database Login failed! Please make sure that the DB login credentials provided are correct");
            return false;
        }
        if(!mysql_select_db($this->database, $this->connection))
        {
            $this->HandleDBError('Failed to select database: '.$this->database.' Please make sure that the database name provided is correct');
            return false;
        }
        if(!mysql_query("SET NAMES 'UTF8'",$this->connection))
        {
            $this->HandleDBError('Error setting utf8 encoding');
            return false;
        }
        return true;
    }

    function Ensuretable()
    {
        $result = mysql_query("SHOW COLUMNS FROM $this->tablename");
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return $this->CreateTable();
        }
        return true;
    }

    function CreateTable()
    {
        $qry = "Create Table $this->tablename (".
                "id INT NOT NULL AUTO_INCREMENT ,".
                "name VARCHAR( 128 ) NOT NULL ,".
                "email VARCHAR( 64 ) NOT NULL ,".
                "username VARCHAR( 16 ) NOT NULL ,".
                "password VARCHAR( 32 ) NOT NULL ,".
                "confirmcode VARCHAR(32) ,".
                "PRIMARY KEY ( id )".
                ")";

        if(!mysql_query($qry,$this->connection))
        {
            $this->HandleDBError("Error creating the table \nquery was\n $qry");
            return false;
        }
        return true;
    }

    function InsertIntoDB(&$formvars)
    {

        $confirmcode = $this->MakeConfirmationMd5($formvars['email']);
        $formvars['confirmcode'] = $confirmcode;
        $confirmcode1='y';//temporary bypass
        $insert_query = 'insert into '.$this->tablename.'(
                name,
                email,
                username,
                password,
                confirmcode
                )
                values
                (
                "' . $this->SanitizeForSQL($formvars['name']) . '",
                "' . $this->SanitizeForSQL($formvars['email']) . '",
                "' . $this->SanitizeForSQL($formvars['username']) . '",
                "' . md5($formvars['password']) . '",
                "' . $confirmcode1 . '"
                )';



		if(!mysql_query( $insert_query ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
			return false;
        }


		return true;
    }
    function MakeConfirmationMd5($email)
    {
        $randno1 = rand();
        $randno2 = rand();
        return md5($email.$this->rand_key.$randno1.''.$randno2);
    }
    function SanitizeForSQL($str)
    {
        if( function_exists( "mysql_real_escape_string" ) )
        {
              $ret_str = mysql_real_escape_string( $str );
        }
        else
        {
              $ret_str = addslashes( $str );
        }
        return $ret_str;
    }

 /*
    Sanitize() function removes any potential threat from the
    data submitted. Prevents email injections or any other hacker attempts.
    if $remove_nl is true, newline chracters are removed from the input.
    */
    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
                );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }
    function StripSlashes($str)
    {
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    }
}
?>