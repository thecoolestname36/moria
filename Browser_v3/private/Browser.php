<?php namespace browser;

include_once('classes\DirectoryManager.php');
include_once('classes\File.php');

class Browser {


    protected static $App = null;
    /** @var \browser\filesystem\DirectoryManager */
    protected static $_DirectoryManager = null;
    public static $SessionID;

    public function __construct() {
        Browser::$App = &$this;
        session_start();
        Browser::$SessionID = session_id();
        Browser::$_DirectoryManager = new filesystem\DirectoryManager();
        if(isset($_SESSION, $_SESSION['dir'])) {
            Browser::DirectoryManager()->setCWD($_SESSION['dir']);
        }
        if(isset($_GET, $_GET['dir'])) {
            $_SESSION['dir'] = $_GET['dir'];
            Browser::DirectoryManager()->setCWD($_GET['dir']);
            echo json_encode([
                "NavbarPath" => \browser\Browser::DirectoryManager()->navBreadcrumbs(),
                "FileExplorerAccordion" => \browser\Browser::DirectoryManager()->listDirectory(),
            ]);
        }
        if(isset($_GET, $_GET['upload_path'], $_FILES['file'])) {
            Browser::DirectoryManager()->uploadFile($_GET['upload_path'], $_FILES['file']);
        }
        if(isset($_POST, $_POST['create_directory'], $_POST['dir_name'])) {
            Browser::DirectoryManager()->createDirectory($_POST['create_directory'], $_POST['dir_name']);
        }
        if(isset($_POST, $_POST['delete_directory'])) {
            Browser::DirectoryManager()->deleteDirectory($_POST['delete_directory']);
        }
        if(isset($_POST, $_POST['delete_file'])) {
            Browser::DirectoryManager()->deleteFile($_POST['delete_file']);
        }
    }

    public static function &DirectoryManager() {
        return Browser::$_DirectoryManager;
    }

    public function __destruct() { 
        session_write_close();
    }

}
new Browser();
