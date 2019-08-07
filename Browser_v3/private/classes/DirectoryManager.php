<?php namespace browser\filesystem;

class DirectoryManager 
{

    protected $cwd = null;
    protected $cwdPieces = null;
    protected $contents = null;
    protected $directoryHandle;
    protected $excludedDirs;

    public function __construct() 
    {
        $this->excludedDirs = array('.', '..');
    }

    public function __destruct() {}

    protected function validatePath($cwd) 
    {
        if(strpos($cwd, '..') !== false) {
            return "";
        }
        if(realpath(DIRECTORIES_DIR.DIRECTORY_SEPARATOR.$cwd) === false) {
            return "";
        }
        return $cwd;
    }

    public function getCWDPieces($refresh = false)
    {
        if($refresh === true) {
            $this->cwdPieces = null;
        }
        if($this->cwdPieces === null) {
            $pieces = explode(DIRECTORY_SEPARATOR, $this->getCWD());
            if($pieces[count($pieces)-1] == "") {
                unset($pieces[count($pieces)-1]);
            }
            $this->cwdPieces = $pieces;
        }
        return $this->cwdPieces;
    }

    public function getCWD($refresh = false) 
    {
        if($refresh === true) {
            $this->cwd = null;
        }
        if($this->cwd === null) {
            if(isset($_GET['dir'])) {
                $this->cwd = $_GET['dir'];
            } else {
                $this->cwd = "";
            }        
        }
        $path = $this->validatePath($this->cwd);
        return $path;
    }

    public function getFullCWD() 
    {
        return DIRECTORIES_DIR.DIRECTORY_SEPARATOR.$this->getCWD();
    }

    public function setCWD($cwd) 
    {
        $this->cwd = $this->validatePath($cwd);
    }

    public function menuNewFolderButton() 
    {
        $button = "";
        $button.= "<button id='CreateDirectoryButton' onclick='Browser.createDirectory(this)' data-path='".$this->getCWD()."' class='menubar-button button-glass'>";
        $button.= "<img class='icon-image' src='/".APP_NAME."/public/assets/create-folder-icon.png'> ";
        $button.= " New Folder";
        $button.= "</button>";
        return $button;
    }

    public function menuUploadFileButton() 
    {
        $button = "";
        // $button.= "<input value='' class='menubar-button button-glass' id='file-upload' type='file'>";
        // $button.= "<button class='menubar-button button-glass'>";
        // $button.= "<img class='icon-image' src='/".APP_NAME."/public/assets/upload-icon.png'> ";
        // $button.= " Upload";
        // $button.= "</button>";
        $button.= "<label for='file-upload' class='menubar-button button-glass'>";
        $button.= "<img class='icon-image' src='/".APP_NAME."/public/assets/upload-icon.png'> Upload (<i>Max Size ".ini_get("upload_max_filesize")."</i>)";
        $button.= "</label>";
        $button.= "<input id='file-upload' type='file' data-path='".$this->getCWD()."' onchange='Browser.uploadFile(this)'/>";

        return $button;
    }

    public function navUpButton() 
    {
        $url = "";
        $pieces = $this->getCWDPieces();
        $piecesCount = count($pieces);
        for($i=0; $i<$piecesCount-1; $i++) {
            if($i == 0) {
                $url = "?dir=";
            }
            $url.=$pieces[$i].DIRECTORY_SEPARATOR;
        }
        $up = "";
        $up.= "<li class='nav-item'>";
        $up.= "    <a class='nav-link' onclick='Browser.navigateCwdUp();' href='javascript: false;'>";
        $up.= "        <img class='icon-image' src='/".APP_NAME."/public/assets/up-arrow-icon.png'>";
        $up.= "    </a>";
        $up.= "</li>";
        return $up;
    }

    public function navHomeButton() {
        $home = "";
        $home.= "<li class='nav-item'>";
        $home.= "    <a class='nav-link' onclick='Browser.navigateHome();' href='javascript: false;'>";
        $home.= "        <img class='icon-image' src='/".APP_NAME."/public/assets/home-icon.png'>";
        $home.= "    </a>";
        $home.= "</li>";
        return $home;
    }

    public function navBreadcrumbs() 
    {
        $pieces = $this->getCWDPieces();
        $url = "";
        $dotUrl = "";
        $breadcrumbs = "";
        $piecesCount = count($pieces);
        $maxBreadcrumbs = 4;
        
        for($i=0; $i<$piecesCount; $i++) {
            $url.=$pieces[$i].DIRECTORY_SEPARATOR;
            if($i > ($piecesCount - $maxBreadcrumbs)) {
                $breadcrumbs.= "<li class='nav-item breadcrumbs-item'>";
                $breadcrumbs.= "    <a class='nav-link breadcrumbs-link' onclick='Browser.navigate(this);' href='javascript: false;' data-href='".$url."' class='nav-link'>";
                $breadcrumbs.= $pieces[$i];
                $breadcrumbs.= "    </a>";
                $breadcrumbs.= "</li>";
            } else {
                $dotUrl.=$pieces[$i].DIRECTORY_SEPARATOR;
            }
        }
        $breadcrumbsStart = "";
        if($piecesCount < $maxBreadcrumbs) {
            $breadcrumbsStart.= "<li class='nav-item breadcrumbs-item'>";
            $breadcrumbsStart.= "    <a class='nav-link breadcrumbs-link' onclick='Browser.navigate(this);' href='javascript: false;' data-href='' class='nav-link'>";
            $breadcrumbsStart.= " ".DIRECTORY_SEPARATOR." ";
            $breadcrumbsStart.= "    </a>";
            $breadcrumbsStart.= "</li>";
        } else {
            $breadcrumbsStart.= "<li class='nav-item breadcrumbs-item'>";
            $breadcrumbsStart.= "    <a class='nav-link breadcrumbs-link' onclick='Browser.navigate(this);' href='javascript: false;' data-href='".$dotUrl."' class='nav-link'>";
            $breadcrumbsStart.= " ... ";
            $breadcrumbsStart.= "    </a>";
            $breadcrumbsStart.= "</li>";
        }
        return $breadcrumbsStart.$breadcrumbs;
    }

    public function listDirectory() 
    {
        $accordion = "";
        $directoryContents = $this->getDirectoryContents();
        foreach($directoryContents as $key => $content) {
            /** @var File $content  */
            $card = "";
            if($content->getIsDir()) {
                $card = $this->createDirectoryCard($content);
            }
            $accordion.= $card;
        }
        foreach($directoryContents as $key => $content) {
            /** @var File $content  */
            $card = "";
            if(!$content->getIsDir()) {
                $card = $this->createFileCard($content);
            }
            $accordion.= $card;
        }
        $accordion.= "</div>";
        return $accordion;
    }

    /**
     * @param File $content
     */
    public function createDirectoryCard(&$content) 
    {
        $card = "";
        $card.= "<div class='card'>";
        $card.= "   <div class='directory-card-header card-header' id='heading-".$content->getFileId()."'>";
        $card.= "       <div class='container-fluid'>";
        $card.= "           <div class='row'>";
        $card.= "               <div class='directory-col col-10'>";
        $card.= "                   <button class='btn btn-link' type='button' >";
        $card.= "                       <a href='javascript: false;' onclick='Browser.navigate(this);' data-href='".$content->getPath().DIRECTORY_SEPARATOR."'>";
        $card.= "                           <img class='directory-image' src='/".APP_NAME."/public/assets/folder-open-outline-filled-icon.png'>";
        $card.= " ".$content->getName();
        $card.= "                       </a>";
        $card.= "                   </button>";
        $card.= "               </div>";
        $card.= "               <div class='col-2'>";
        $card.= "                   <button class='btn card-options-button btn-link collapsed' type='button' data-target='#collapse-".$content->getFileId()."' data-toggle='collapse'>";
        $card.= "                       <img class='icon-image' src='/".APP_NAME."/public/assets/options-icon.png'>";
        $card.= "                   </button>";
        $card.= "               </div>";
        $card.= "           </div>";
        $card.= "       </div>";
        $card.= "   </div>";
        $card.= "   <div id='collapse-".$content->getFileId()."' class='collapse' aria-labelledby='heading-".$content->getFileId()."' data-parent='#FileExplorerAccordion'>";
        $card.= "       <div class='card-body'>";
        $card.= "           <div class='container-fluid'>";
        $card.= "               <div class='row'>";
        $card.= "                   <div class='col-lg-4 cl-md-6 col-sm-12 col-sx-12'>";
        $card.= "                       <button onclick='Browser.deleteDirectory(this)' data-path='".$content->getPath()."' class='btn btn-link' type='button'>";
        $card.= "                           <img class='options-icon-image' src='/".APP_NAME."/public/assets/delete-icon.png'>";
        $card.= " Delete";
        $card.= "                       </button>";
        $card.= "                   </div>";
        $card.= "                   <div class='col-lg-4 cl-md-6 col-xs-12'>";
        $card.= $content->getModifiedOnFormatted();
        $card.= "                   </div>";
        $card.= "               </div>";
        $card.= "           </div>";
        $card.= "       </div>";
        $card.= "   </div>";
        $card.= "</div>";
        return $card;
    }

    /**
     * @param File $content
     */
    public function createFileCard(&$content) 
    {
        $card = "";
        $card.= "<div class='card'>";
        $card.= "   <div class='file-card-header card-header' id='heading-".$content->getFileId()."'>";
        $card.= "       <div class='container-fluid'>";
        $card.= "           <div class='row'>";
        $card.= "               <div class='file-col col-10'>";
        $card.= "                   <a style='margin-left:15px' href='/".DIRECTORIES_FILE_NAME."/".$content->getRelativeUrl()."' download='".$content->getRelativeUrl()."'>";
        $card.= "                       <img class='file-image' src='/".APP_NAME."/public/assets/download-icon.png'>";
        $card.= "                   </a>";
        $card.= "                   <button class='btn btn-link' type='button' >";
        $card.= "                       <a href='/".DIRECTORIES_FILE_NAME."/".$content->getRelativeUrl()."'>";
        $card.= "                           <img class='file-image' src='/".APP_NAME."/public/assets/file-icon.png'>";
        $card.= " ".$content->getName();
        $card.= "                       </a>";
        $card.= "                   </button>";
        $card.= "               </div>";
        $card.= "               <div class='col-2'>";
        $card.= "                   <button ".(in_array(strtoupper($content->getType()), ['PNG','JPEG','JPG','GIF']) ? "onclick='Browser.loadImage(".$content->getFileId().");'" : "") . " class='btn card-options-button btn-link collapsed' type='button' data-target='#collapse-".$content->getFileId()."' data-toggle='collapse'>";
        $card.= "                       <img class='icon-image' src='/".APP_NAME."/public/assets/options-icon.png'>";
        $card.= "                   </button>";
        $card.= "               </div>";
        $card.= "           </div>";
        $card.= "       </div>";
        $card.= "   </div>";
        $card.= "   <div id='collapse-".$content->getFileId()."' class='collapse' aria-labelledby='heading-".$content->getFileId()."' data-parent='#FileExplorerAccordion'>";
        $card.= "       <div class='card-body'>";
        $card.= "           <div class='container-fluid'>";
        $card.= "               <div class='row'>";
        $card.= "                   <div class='col-3'>";
        $card.= "                       <a style='background-color: initial !important; color: #007bff !important; text-decoration: initial !important;' href='/".DIRECTORIES_FILE_NAME."/".$content->getRelativeUrl()."' download='".$content->getRelativeUrl()."'>";
        $card.= "                           <img class='file-image' src='/".APP_NAME."/public/assets/download-icon.png'> Download";
        $card.= "                       </a>";
        $card.= "                   </div>";
        $card.= "                   <div class='col-3'>";
        $card.= "                       <button onclick='Browser.deleteFile(this)' data-path='".$content->getPath()."' class='btn btn-link' type='button'>";
        $card.= "                           <img class='options-icon-image' src='/".APP_NAME."/public/assets/delete-icon.png'> Delete";
        $card.= "                       </button>";
        $card.= "                   </div>";
        $card.= "                   <div class='col-lg-4 cl-md-6 col-sm-12 col-xs-12'>";
        $card.= "File type: ".$content->getType();
        $card.= "                   </div>";
        $card.= "                   <div class='col-lg-4 cl-md-6 col-sm-12 col-xs-12'>";
        $card.= "File size: ".$content->getSizeString();
        $card.= "                   </div>";
        $card.= "                   <div class='col-lg-4 cl-md-6 col-sm-12 col-xs-12'>";
        $card.= $content->getModifiedOnFormatted();
        $card.= "                   </div>";
        $card.= "               </div>";
        if(in_array(strtoupper($content->getType()), ['PNG','JPEG','JPG','GIF'])) {
            $card .= "               <div id='ImagePreview-".$content->getFileId()."' data-src='/".DIRECTORIES_FILE_NAME."/".$content->getRelativeUrl()."' class='row image-preview'></div>";
        }
        $card.= "           </div>";
        $card.= "       </div>";
        $card.= "   </div>";
        $card.= "</div>";
        return $card;
    }

    public function getDirectoryContents($refresh = false) 
    {
        if($refresh === true) {
            $this->contents = null;
        }
        if($this->contents === null) {
            $fullCWD = $this->getFullCWD();
            $contents = array();
            if($handle = opendir($fullCWD)) {
                while(false !== ($content = readdir($handle))){
                    if(!in_array($content, $this->excludedDirs)) {
                        $fullCWD = $this->getFullCWD();
                        if(realpath($fullCWD.DIRECTORY_SEPARATOR.$content) !== false) {
                            $contents[] = new File($content, $this->getCWD());
                        }
                    }
                }
                closedir($handle);
            }
            $this->contents = $contents;
        }
        return $this->contents;
    }

//    public function getFileContents()
//    {
//        $files = array();
//        if($handle = opendir($directory)) {
//            while(false !== ($file = readdir($handle))){
//                if(!in_array($file, $this->excludedDirs)) {
//                    $directoryPath = $directory."\\".$file;
//                    if(!is_dir($directoryPath)) {
//                        $files[$file] = $directoryPath;
//                    }
//                }
//            }
//            closedir($handle);
//        }
//        return $files;
//
//    }

    public function uploadFile($path, $file) 
    {
        $path = $this->validatePath($path);
        $tmp_name = $file['tmp_name'];
        if(file_exists($tmp_name)) {
            $fileName = basename($file["name"]);
            $fileName = str_replace("'", "", $fileName);
            $fileName = str_replace('"', "", $fileName);
            $destination = DIRECTORIES_DIR.DIRECTORY_SEPARATOR.$path;
            $counter = 0;
            $prefix = '';
            while(file_exists($destination.$prefix.$fileName) && $counter < 999) {
                $counter++;
                $prefix = ((string)$counter)."_";
            }
            $fileName = $prefix.$fileName;
            move_uploaded_file($tmp_name, $destination.$fileName);
        }

    }

    public function createDirectory($path, $name) 
    {
        $path = $this->validatePath($path).$name;
        if(strlen($path) > 0) {
            $path = str_replace("'", "", $path);
            $path = str_replace('"', "", $path);
            mkdir(DIRECTORIES_DIR.DIRECTORY_SEPARATOR.$path);
        }
        $var = 0;
    }

    public function deleteDirectory($path) 
    {
        $path = $this->validatePath($path);
        if(strlen($path) > 0) {
            rmdir(DIRECTORIES_DIR.DIRECTORY_SEPARATOR.$path);
        }
        $var = 0;
    }

    public function deleteFile($path) 
    {
        $path = $this->validatePath($path);
        if(strlen($path) > 0) {
            unlink(DIRECTORIES_DIR.DIRECTORY_SEPARATOR.$path);
        }
        $var = 0;
    }


}
