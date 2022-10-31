<?php
function time_convert($a){
    $ans = 0;
    if(strcmp(substr($a,5,1),'P')==0 && strcmp(substr($a,0,2),"12")){
        $ans = $ans + 720;
    }
    return $ans+(int)substr($a,0,2)*60+(int)substr($a,3,2);
}
date_default_timezone_set("Asia/Shanghai");
?>
<html> 
<head>
<title>Empty Classrooms</title>
</head>
<body>
<a href="./timetable.php">Classroom Timetable</a><br><br><br>

Try to find an empty classroom when you can't find a seat in the Library T~T<br>
Default: find an empty classroom in the next 60 minutes start from now.
<form method="post">
Hour (0~23): <input type="number" min="0" max="23" name="hour" value=<?php if(isset($_REQUEST['hour'])) echo($_REQUEST['hour']); else echo(date("H"));?> ><br>
Minute (0~59): <input type="number" min="0" max="59" name="minute" value=<?php if(isset($_REQUEST['minute'])) echo($_REQUEST['minute']); else echo(date("i"));?>><br>
Duration(min): <input type="number" min="1" max="600" name="du" value=<?php if(isset($_REQUEST['du'])) echo($_REQUEST['du']); else echo("60");?>><br>

<select name="wd">
    <option value="Monday" <?php if((!isset($_REQUEST['wd']) && date("l")=="Monday")||(isset($_REQUEST['wd']) && $_REQUEST['wd']=="Monday")) echo("selected");?>>Monday</option>
    <option value="Tuesday" <?php if((!isset($_REQUEST['wd']) && date("l")=="Tuesday")||(isset($_REQUEST['wd']) && $_REQUEST['wd']=="Tuesday")) echo("selected");?>>Tuesday</option>
    <option value="Wednesday" <?php if((!isset($_REQUEST['wd']) && date("l")=="Wednesday")||(isset($_REQUEST['wd']) && $_REQUEST['wd']=="Wednesday")) echo("selected");?>>Wednesday</option>
    <option value="Thursday" <?php if((!isset($_REQUEST['wd']) && date("l")=="Thursday")||(isset($_REQUEST['wd']) && $_REQUEST['wd']=="Thursday")) echo("selected");?>>Thursday</option>
    <option value="Friday" <?php if((!isset($_REQUEST['wd']) && date("l")=="Friday")||(isset($_REQUEST['wd']) && $_REQUEST['wd']=="Friday")) echo("selected");?>>Friday</option>
</select>
<input type="submit" value="Submit">
</form>
No overnight!
<br><br><br>
<?php
if(isset($_REQUEST['hour']) && isset($_REQUEST['minute']) && isset($_REQUEST['wd']) && isset($_REQUEST['du']) ) {
    echo ("Free Classrooms from ");
    if(strlen($_REQUEST['hour'])<2) echo(0);
    echo($_REQUEST['hour'].":");
    if(strlen($_REQUEST['minute'])<2) echo(0);
    echo($_REQUEST['minute']." lasting ".$_REQUEST['du']." minutes on ".$_REQUEST['wd']);
    echo ("<br><br>");
    $l2 = (int)$_REQUEST['hour']*60 + (int)$_REQUEST['minute'];
    $r2 = $l2 + (int)$_REQUEST['du'];
    $wd = substr($_REQUEST['wd'],0,2);
    $json_string = file_get_contents('rooms.json'); 
    $data = json_decode($json_string, true);
    foreach($data as $room){
        $flag = 0;
        if(isset($room[$wd])){
            foreach ($room[$wd] as $ts) {
                $l1 = time_convert(substr($ts[0],0,7));
                $r1 = time_convert(substr($ts[0],10,7));
                if (!($r1<$l2 || $r2<$l1)){
                    $flag=1;
                }
            }
            if($flag == 0 && strcmp($room['name'],"TBA")!=0) {
                echo ($room['name']);
                echo ("<br>");
            }
        }
    }
}
?>
</body> 
</html>