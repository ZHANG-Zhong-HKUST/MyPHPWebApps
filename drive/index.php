<?php

?>
<html> 
<head>
<title>Personal Drive</title>
</head>
<body>

<?php
if(file_exists("file_name") && file_exists("file_data")) { 
    $file_name = file_get_contents('file_name'); 
    echo ("You may want to download: ".$file_name." ".'<a href="file_data" download="'.$file_name.'">Download</a><br>');
}
else{
    echo ("No file can be download. <br>");
}
echo("<br><br><br>");
if(file_exists("text_data")) { 
    $data = file_get_contents('text_data'); 
    echo ("You may want to copy:<br><br>");
    echo ($data);
    echo ("<br><br>");
}
else{
    echo ("No text waiting copy. <br>");
}
?>
</body>
</html>