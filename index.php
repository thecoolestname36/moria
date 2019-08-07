<?php

define("DIRECTORIES_FILE_NAME", "directories");
define("DIRECTORIES_DIR", __DIR__.DIRECTORY_SEPARATOR.DIRECTORIES_FILE_NAME);
define("APP_NAME", "Browser_v3");
include_once(__DIR__.DIRECTORY_SEPARATOR.APP_NAME.DIRECTORY_SEPARATOR."private".DIRECTORY_SEPARATOR."Browser.php");
if(isset($_GET, $_GET['dir'])) {
    return;
}
?>

<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sradzone: Place of Awesomeness!</title>
        <link   rel='icon'
                type='image/png'
                href="/<?=APP_NAME ?>/public/assets/favicon.png"
        >
        <link rel="stylesheet" type="text/css" href="<?=APP_NAME ?>/public/assets/main.css">
        <link rel="stylesheet" type="text/css" href="<?=APP_NAME ?>/public/bootstrap/css/bootstrap.min.css">
        <script src="/<?=APP_NAME ?>/public/jquery/jquery-3.3.1.min.js"></script>
        <script src="/<?=APP_NAME ?>/public/bootstrap/js/bootstrap.min.js"></script>
        <script src="/<?=APP_NAME ?>/public/js/Clock.js"></script>
        <script src="/<?=APP_NAME ?>/public/js/Browser.js"></script>
        <script>
            $(document).ready(Browser);
        </script>
    </head>
    
    <body>
        <div id="bg"></div>
        <!-- <section>
            <header>
                <div id="clockbox"></div>
            </header>
        </section> -->
        <div id="main-container" class="container-fluid">
            <div class="row">
                <div class="file-directory-main col-lg-12 col-md-12 col-sm-12">
                    <section>
                        <article id="MenuBar" class="container-fluid">
                            <div class="row">
                                <div id="MenuNewFolder">
                                    <?=\browser\Browser::DirectoryManager()->menuNewFolderButton() ?>
                                </div>
                                <div id="MenuUploadFile">
                                    <?=\browser\Browser::DirectoryManager()->menuUploadFileButton() ?>
                                </div>
                            </div>
                            <div class="row">
                                <nav class="navbar navbar-expand-sm">
                                    <?=\browser\Browser::DirectoryManager()->navHomeButton() ?>
                                    <?=\browser\Browser::DirectoryManager()->navUpButton() ?>
                                    <ul id="NavbarPath" class="navbar-path navbar-nav mr-auto">
                                        <?=\browser\Browser::DirectoryManager()->navBreadcrumbs() ?>
                                    </ul>
                                </nav>
                            </div>
                        </article>
                        <article id="FileExplorer">
                            <div id="FileExplorerAccordion" class="accordion">
                                <?=\browser\Browser::DirectoryManager()->listDirectory() ?>
                            </div>
                        </article>
                    </section>
                </div>
            </div>
        </div>
        <div id="loader"></div>
    </body>
</html>
