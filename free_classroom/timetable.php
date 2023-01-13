<head>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<title>Classroom Timetable</title>
</head>

<a href="./room.php">Empty Foom Finder</a><br><br><br>

<form method="post">
Classroom name: <input id="classroomsearch" name="rm" autocomplete="off"><br>
<input type="submit" value="Submit">
</form>

<?php
if(isset($_REQUEST["rm"])) {
	$rm = $_REQUEST["rm"];
	$json_string = file_get_contents('rooms.json'); 
    $rooms = json_decode($json_string, true);
	$json_string = file_get_contents('courses.json'); 
    $json_string = htmlspecialchars_decode($json_string);
    $courses = json_decode(stripslashes($json_string), true );
    $wds = array("Mo" => "Monday", "Tu" => "Tuesday", "We" => "Wednesday", "Th" => "Thusday", "Fr" => "Friday");
	if (isset($rooms[$rm])) {
		echo ('<table border=0 cellpadding="7" cellspacing="0" >');
		echo ("<tr><th>Time</th><th>Code</th><th>Name</th><th>Section</th><th>Instructor</th></tr>");
		foreach ($wds as $wd => $wdn) {
			echo ('<tr><th colspan="5">'.$wdn."</th></tr>");
			if(isset($rooms[$rm][$wd])) {
				foreach ($rooms[$rm][$wd] as $ts) {
					echo ("<tr>");
					echo ("<td>".$ts[0]."</td><td>".$ts[1]."</td><td>".$courses[$ts[1]]."</td><td>".$ts[2]."</td><td>");
					$cnt = 0;
					for ($i=0; $i<count($ts[3]); $i++) {
						if($i==0 || strcmp($ts[3][$i],$ts[3][$i-1])!=0) {
							$cnt++;
							if($cnt>1) {
								if (($cnt-1)%4==0) echo("<br>");
								else echo("; ");
							}
							echo ($ts[3][$i]);
						}
					}
					echo ("</td></tr>");
				}
			}
		}
		echo ("</table>");
	}
	else{
		echo ("Invalid Room Name.");
	}
}
?>

<script>
var allrooms = ['G001, CYT Bldg','G001, LSK Bldg','G002, CYT Bldg','G003, CYT Bldg','G003, LSK Bldg','G004, CYT Bldg','G005, CYT Bldg','G005, LSK Bldg','G009A, CYT Bldg','G009B, CYT Bldg','G010, CYT Bldg','G010, LSK Bldg','G011, LSK Bldg','G012, LSK Bldg','G021, LSK Bldg','LTL, CYT Bldg','Lecture Theater A','Lecture Theater B','Lecture Theater C','Lecture Theater D','Lecture Theater E','Lecture Theater F','Lecture Theater G','Lecture Theater H','Lecture Theater J','Lecture Theater K','Multi-function Room, LG4, LIB','Rm 1001, CYT Bldg','Rm 1001, LSK Bldg','Rm 1003, CYT Bldg','Rm 1003, LSK Bldg','Rm 1004, CYT Bldg','Rm 1005, LSK Bldg','Rm 1007, LSK Bldg','Rm 1009, LSK Bldg','Rm 1010, LSK Bldg','Rm 1011, LSK Bldg','Rm 1014, LSK Bldg','Rm 1026, LSK Bldg','Rm 1027, LSK Bldg','Rm 1032, LSK Bldg','Rm 1033, LSK Bldg','Rm 1034, LSK Bldg','Rm 105, Shaw Auditorium','Rm 1104, Acad Concourse','Rm 1206','Rm 1409, Lift 25-26','Rm 1410, Lift 25-26','Rm 2001, LSK Bldg','Rm 2003, LSK Bldg','Rm 2007, CYT Bldg','Rm 2014, CYT Bldg','Rm 209, Shaw Auditorium','Rm 2126A, Lift 19','Rm 2126B, Lift 19','Rm 2126C, Lift 19','Rm 2126D, Lift 19','Rm 2127A, Lift 19','Rm 2127B, Lift 19','Rm 2127C, Lift 19','Rm 2128A, Lift 19','Rm 2128B, Lift 19','Rm 2128C, Lift 19','Rm 2129A, Lift 19','Rm 2129B, Lift 19','Rm 2129C, Lift 19','Rm 2130A, Lift 19','Rm 2130B, Lift 19','Rm 2130C, Lift 19','Rm 2131A, Lift 19','Rm 2131B, Lift 19','Rm 2131C, Lift 19','Rm 2132A, Lift 19','Rm 2132B, Lift 19','Rm 2132C, Lift 19','Rm 2133, Lift 19, 21, 22','Rm 2136, Lift 22','Rm 2209','Rm 2302, Lift 17-18','Rm 2303, Lift 17-18','Rm 2304, Lift 17-18','Rm 2306, Lift 17-18','Rm 2404, Lift 17-18','Rm 2406, Lift 17-18','Rm 2407, Lift 17-18','Rm 2463, Lift 25-26','Rm 2464, Lift 25-26','Rm 2465, Lift 25-26','Rm 2502, Lift 25-26','Rm 2503, Lift 25-26','Rm 2504, Lift 25-26','Rm 2590, Lift 27-28','Rm 2610, Lift 31-32','Rm 2611, Lift 31-32','Rm 2612A, Lift 31-32','Rm 2612B, Lift 31-32','Rm 3111, Lift 19, 22','Rm 3115, Lift 19, 22','Rm 3119, Lift 19, 22','Rm 3123A, Lift 22','Rm 3207, Lift 21','Rm 3209A','Rm 3301, Lift 17-18','Rm 3401, Lift 17-18','Rm 3494, Lift 25-26','Rm 4047, LSK Bldg','Rm 4160, Lift 33','Rm 4210, Lift 19','Rm 4213, Lift 19','Rm 4223, Lift 23','Rm 4225C, Lift 23, 24','Rm 4227, Lift 23, 24','Rm 4402, Lift 17-18','Rm 4502, Lift 25-26','Rm 4503, Lift 25-26','Rm 4504, Lift 25-26','Rm 4582, Lift 27-28','Rm 4619, Lift 31-32','Rm 4620, Lift 31-32','Rm 5047, LSK Bldg','Rm 5504, Lift 25-26','Rm 5506, Lift 25-26','Rm 5508, Lift 25-26','Rm 5510, Lift 25-26','Rm 5560, Lift 27-28','Rm 5564, Lift 27-28','Rm 5566, Lift 27-28','Rm 5583, Lift 29-30','Rm 5619, Lift 31-32','Rm 5620, Lift 31-32','Rm 6122','Rm 6131, Lift 19, 22','Rm 6135, Lift 19, 22','Rm 6137, Lift 19, 22','Rm 6140','Rm 6558, Lift 29-30','Rm 6573, Lift 29-30','Rm 6581, Lift 27-28','Rm 6591, Lift 31-32','Rm 6602, Lift 31-32','Rm 7252','TBA','UG001, CYT Bldg','UG002, CYT Bldg'];

var suggestr = function(q, ret) {
	q = q.term.toLowerCase();
	var re = [];
	for (var i=0; i<allrooms.length; i++) {
		var s = allrooms[i].toLowerCase();
		if (s.indexOf(q)!=-1) {
      		re.push(allrooms[i]);
		}
	}
	ret(re);
}

$("input#classroomsearch").autocomplete({
	source: suggestr,
	delay: 0,
});
</script>