<?php
$admin_passwd="hhh";
if (isset($_FILES["file"])){
    if(md5($_REQUEST["code"])!=$admin_passwd){
        die("hack detected");
    }
    if ($_FILES["file"]["error"] > 0)
    {
        echo "Error:" . $_FILES["file"]["error"] . "<br>";
    }
    else
    {
        echo "Uploaded: " . $_FILES["file"]["name"] . "<br>";
        echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
        move_uploaded_file($_FILES["file"]["tmp_name"], "file_data");
        $file_name = fopen("file_name", "w");
        fwrite($file_name, $_FILES["file"]["name"]);
    }
}

if (isset($_POST["submit_text"])){
    if(md5($_REQUEST["code"])!=$admin_passwd){
        die("hack detected");
    }
    if(isset($_REQUEST["text"])) {
        $text_data = fopen("text_data", "w");
        fwrite($text_data, $_REQUEST["text"]);
    }
}

if (isset($_POST["delete_file"])){
    if(md5($_REQUEST["code"])!=$admin_passwd){
        die("hack detected");
    }
    if(file_exists('file_name')) unlink('file_name');
    if(file_exists('file_data')) unlink('file_data');
}

if (isset($_POST["delete_text"])){
    if(md5($_REQUEST["code"])!=$admin_passwd){
        die("hack detected");
    }
    if(file_exists('text_data')) unlink('text_data');
}
?>
<html>
<head>
<title>Personal Drive - Admin</title>
</head>
<body>
<form method="POST">
<input type="password" name="code"><input type="submit" value="Submit">
</form>
<?php
if(isset($_REQUEST["code"])){
    if(md5($_REQUEST["code"])==$admin_passwd) {
        if(file_exists("file_name") && file_exists("file_data")) { 
            $file_name = file_get_contents('file_name'); 
            echo ("Exists file: ".$file_name.'<br>Uplad file will overwrite it. <br><br>');
            echo('<form method="post"><input type="submit" name="delete_file" value="Delete file"><input type="password" name="code" hidden value="'.$_REQUEST["code"].'"></form>');
        }
        echo('<form method="post" enctype="multipart/form-data"><input type="file" name="file" id="file"><input type="submit" name="submit" value="Submit"><input type="password" name="code" hidden value="'.$_REQUEST["code"].'"></form>');
        echo("<br><br><br>");
        echo('<form method="post" enctype="multipart/form-data"><textarea name="text" id="text">');
        if(file_exists("text_data")) {
            $data = file_get_contents('text_data'); 
            echo ($data);
        }
        echo('</textarea><br><input type="submit" name="submit_text" value="Submit"><input type="password" name="code" hidden value="'.$_REQUEST["code"].'"></form>');
        if(file_exists('text_data')){
            echo('<form method="post"><input type="submit" name="delete_text" value="Delete text"><input type="password" name="code" hidden value="'.$_REQUEST["code"].'"></form>');
        }
    }
}
?>
</body>
</html>