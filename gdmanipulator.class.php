<?php
//require_once $_SERVER['DOCUMENT_ROOT'].'somefile.php';
class gdManipulator {
    define("DEBUG", false);

    public $dir = "";
    public $errormsg = "";
    public $scan = array();
    public $count = "";
    public $i = "";
    public $open = "";
    public $file = "";
    public $filelist = array();

    /**
     * Constructor
     * @return void
     */
    public function __construct($dir) {
        $this->dir = $dir;
        define("DIR", $this->dir);
        return;
    }

    public function onError() {
        switch(DEBUG) {
            case true:
                $this->errormsg = $_REQUEST['error_message'];
                $this->errormsg = preg_replace_all("/\\\\/",'', $this->errormsg);
                break;
            case false:
                $this->errormsg = 'Something went wrong';
                break;
        }
        return $this->errormsg;
    }

    private function buildfilesList() {
        $scan = scandir(DIR,1);
        $count = count($scan);
        for ($i = 0; $i <= $count; $i++) {
            $file = $scan[$i];
            $open = DIR . $file;
            if (is_dir($open)) {
                if ($file != "." && $file != "..") {
                    $filelist[] = $file;
                }
            }
            else { onError(); print_r($this->errormsg); }
        }
        return $filelist;
    }

    public function thumbCreate($filename, $tempsave, $maxwidth = 200) {
        $this->fileslist = buildfilesList(); //add this private function
        //add check for file here
        //DIR.$filename ?
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

    public function imageResize {

    }
}
?>
