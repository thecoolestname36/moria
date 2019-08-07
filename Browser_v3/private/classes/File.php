<?php namespace browser\filesystem;

class File 
{

    private static $FileCount = 0;

    protected $fileId = 1;
    protected $name = "";
    protected $size = 0;
    protected $type = "";
    protected $isDir = "";
    protected $path = "";
    protected $modifiedOn = 0;

    public function __construct($name, $path) 
    {
        $this->fileId = File::$FileCount;
        File::$FileCount = File::$FileCount + 1;
        $this->name = $name;
        $this->path = $path;
        $fullPath = DIRECTORIES_DIR.DIRECTORY_SEPARATOR.$this->path.DIRECTORY_SEPARATOR.$this->name;
        if(is_dir($fullPath)) {
            $this->isDir = true;
        } 
        $this->type = "N/a";
        $parts = pathinfo($fullPath);
        if(isset($parts) && array_key_exists('extension', $parts) && isset($parts['extension'])) {
            $this->type= $parts['extension'];
        }
        if(is_file($fullPath)) {
            $size = filesize($fullPath);
            if($size !== false) {
                $this->size = $size;
            }
        }
        $this->modifiedOn = filemtime($fullPath);

    }

    public function getFileId() {
        return $this->fileId;
    }

    public function getName() {
        return $this->name;
    }

    public function getSizeString() {
        $size = (string)round($this->size / 1048576, 2);
        $size.= " MB";
        return $size;
    }

    public function getSize() {
        return $this->size;
    }

    public function getType() {
        return $this->type;
    }

    public function getIsDir() {
        return $this->isDir;
    }

    public function getModifiedOn() {
        return $this->modifiedOn;
    }

    public function getModifiedOnFormatted() {
        return ((new \DateTime())->setTimestamp($this->modifiedOn))->format("Y-m-d H:i:s");
    }
    
    public function getData() {
        return "Data";
    }

    public function getPath() {
        $path = $this->path.$this->name;
        return $path;
    }

    public function getRelativeUrl() {
        $url = "";
        $pathArr = explode(DIRECTORY_SEPARATOR, $this->getPath());
        $pathArrCount = count($pathArr);
        for($i=0; $i<$pathArrCount; $i++) {
            if($i > 0) {
                $url.= "/";
            }
            $url.= $pathArr[$i];
        }
        return $url;
    }


}
