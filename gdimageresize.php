<?php

//error_reporting(0);
/*
 *
 * To use:
 * <?php include ("path/to/folder/gdimageresize.php"); //Where this file is located
 * $dir = 'path/to/folder/'; //Name of folder to scan for subfolders of images
 * $tempsave = 'path/to/folder/nameofimage.jpg'; //Where to save outputted image and name of image (must end in .jpg)
 * $maxwidth = 250; //This defaults to 200, use only if necessary
 * $filename = scnfolderforFile($dir);
 * gdimageResize($filename, $tempsave, $maxwidth); //Only include $maxwidth if not using default of 200
 * $tempimgsize = getimagesize($tempsave);
 * ?>
 * <img src="<?php echo $tempsave; ?>" <?php echo $tempimgsize[3]; ?> alt="some random image" />
 *
*/
function gdimageResize($filename, $tempsave, $maxwidth = 200) {
    list($width, $height) = getimagesize($filename);
    switch ($width > $maxwidth) {
        case true:
            $percent = $maxwidth/$width;
            $newwidth = $maxwidth;
            break;
        case false:
            $percent = 1;
            $newwidth = $width;
            break;
    }
    $newheight = $height * $percent;
    $thumb = imagecreatetruecolor($newwidth, $newheight);
    $testforimgtype = explode('.', $filename);
    switch($testforimgtype[1]) {
        case 'jpg':
        case 'jpeg':
            $source = imagecreatefromjpeg($filename);
            break;
        case 'png':
            $source = imagecreatefrompng($filename);
            break;
        case 'gif':
            $source = imagecreatefromgif($filename);
            break;
        default:
            $source = imagecreatefromjpeg($filename);
            break;
    }
    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    imagejpeg($thumb, $tempsave);
    imagedestroy($thumb);
}

function make_randomseed() {
  list($usec, $sec) = explode(' ', microtime());
  return (float) $sec + ((float) $usec * 100000);
}

function scnfolderforFile($dir) {
    switch (is_dir($dir)) { //Make sure it is a valid location
    case true:
        $x = scandir($dir, 1); //Flip listing over to get most current at top
        break;
    case false:
        die("Directory does not exist");
        break;
}
    $handle = opendir($dir); //Open the directory for reading
    while (($file = readdir($handle)) !== false) {
        if ($file != "." && $file != "..") { //Skip the notorious . and .. invisible files
            $cnt++; //Add 1 to the count
        }
    }
    closedir($handle); //Close the directory
        for ($i = 0; $i <= $cnt; $i++) {
            $file = $x[$i];
            $open = $dir . $file;
            if (is_dir($open)) {
                $folderslist[] = $file;
            }
        }
    $countfolder = count($folderslist)-1;
    mt_srand(make_randomseed());
    $randomfolder = mt_rand(0, $countfolder);
    $dir = $dir.$folderslist[$randomfolder]."/";
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            $testforimgtype = explode('.', $file);
            switch($testforimgtype[1]) {
                case 'jpg':
                case 'jpeg':
                case 'gif':
                case 'png':
                    if ($file != "." && $file != "..") {
                        $filearray[] = $file;
                    }
                    break;
                default:
                    break;
            }
        }
        closedir($handle);
    }
    $countfiles = count($filearray)-1;
    switch($countfiles>0) {
        case false:
            $filetouse = 0;
            break;
        case true:
            mt_srand(make_randomseed());
            $filetouse = mt_rand(0,$countfiles);
            break;
    }
    $filename = $dir.$filearray[$filetouse];
    return $filename;
}

function listFolders($dir) {
    $x = scandir($dir,1);
    $a = count($x);
    for ($i = 0; $i <= $a; $i++) {
        $file = $x[$i];
        $open = $dir . $file;
        if (is_dir($open)) {
            if ($file != "." && $file != "..") {
                echo <<<END
                    <a href="?file=$file">$file</a>
END;
            }
        }
    }
}

function buildFileslist($dir) {
    $x = scandir($dir,1);
    $a = count($x);
    for ($i = 0; $i <= $a; $i++) {
        $file = $x[$i];
        $open = $dir . $file;
        if (is_dir($open)) {
            if ($file != "." && $file != "..") {
                $filelist[] = $file;
            }
        }
    }
    return $filelist;
}

function displayFileslist($dir) {
    $build = buildFileslist($dir);
    $a = count($build);
    for ($i = 0; $i <= $a; $i++) {
        $file = $build[$i];
        $filesplit = explode('.',$file);
        $dimensions = getimagesize($file);
        $location = $dir.$file;
        echo "<a href=\"".$location."\" title=\""$filesplit[0]"\">";
        echo "<img src=\"".$location."\" ".$dimensions." alt=\""$filesplit[0]"\" />">;
        echo "</a>";
    }
}
