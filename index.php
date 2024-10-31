<?php
include("pwa.php");
$pwa = new PWA(file_get_contents("manifest.json"));

if(!!PWA_static::current_user_unique()){
    $key = $pwa->set_unique(PWA_static::current_user_unique());
    file_put_contents('user', $key);
}
else {
    $key = $pwa->set_unique();
    file_put_contents('user', $key);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php $pwa->include_manifest(); ?>
</head>
<body>
    
<?php echo "$key<br>"; ?>

<?php $pwa->include_script(); ?>
</body>
</html>