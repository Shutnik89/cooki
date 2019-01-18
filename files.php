<?php
function reArrayFiles(&$file_post) {
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);
    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }
    return $file_ary;
}
$connection = new PDO('mysql:host=localhost; dbname=academy; charset=utf8','root','');
if (isset($_POST['submit'])) {
    $files = reArrayFiles($_FILES['file']);
    foreach ($files as $file) {
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileType = $file['type'];
        $fileError = $file['error'];
        $fileSize = $file['size'];
        $fileExt = strtolower(end(explode('.', $fileName)));
        $fileName = explode('.', $fileName)[0];
        $fileName = preg_replace('/[0-9]/', '', $fileName);
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        if (in_array($fileExt, $allowedExtensions)) {
            if ($fileSize < 500000000) {
                if ($fileError === 0) {
                    $connection->query("INSERT INTO `images` (`imgname`,`extension`) VALUES ('$fileName','$fileExt')");
                    $lastID = $connection->query("SELECT MAX(id)FROM `images` ");
                    $lastID = $lastID->fetchAll();
                    $lastID = $lastID[0][0];
                    $fileNameNew = $lastID . $fileName . '.' . $fileExt;
                    $fileDestination = 'upload/' . $fileNameNew;
                    move_uploaded_file($fileTmpName, $fileDestination);
                    echo 'Успех';
                } else {
                    echo 'Что то пошло не так';
                }
            } else {
                echo 'Very big files';
            }
        } else {
            echo 'Неправильный файл';
        }
    }
}
echo "<pre>";
var_dump($files);
echo "</pre>";
$data = $connection->query('SELECT * FROM `images`');
foreach ($data as $img) {
    $delete = "delete".$img['id'];
    $image= "upload/" . $img['id'].$img['imgname']. '.' .$img['extension'] ;
    if (isset($_POST[$delete])) {
        $imageID = $img['id'];
        $connection->query("DELETE FROM `academy`.`images` WHERE id = '$imageID'");
        if (file_exists($image)) {
            unlink($image);
        }

    }
    if (file_exists($image)){
        echo "<div>";
        echo "<img width='150' height='150' src='$image'>";
        echo "<form method = 'POST'> <button name='delete".$img['id']."'>Удалить</button></form>";
        echo "</div>";
    }
}


//echo "<pre>";
//var_dump($_FILES);
//echo "</pre>";

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="file[]" multiple required >
    <button name="submit">Отправить</button>
</form>
</body>
</html>
