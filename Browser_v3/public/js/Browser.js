
Browser = new function() {


    // Construction after this
    //Clock();
    //this.clockInterval = setInterval(Clock,1000);
    this.currentDirectory = "";
    this.uploadFile = function(elem) {
        
        if($(elem).prop('files').length > 0) {
            $("#loader").html("Uploading...");
            $("#loader").fadeIn(250, function() {
                var form_data = new FormData();                 
                console.log($(elem).prop('files')[0]); 
                form_data.append('file', $(elem).prop('files')[0]);
                $.ajax(new function() {
                    this.async = false;
                    this.method = "POST";
                    this.cache = false;
                    this.contentType = false;
                    this.processData = false;
                    this.data = form_data;
                    this.dataType = "json";
                    this.url = "/?upload_path="+$(elem).data("path");
                    this.complete = function(xhr, textStatus) {
                        window.location.reload();
                    };
                });
            });
        }
    };
    this.createDirectory = function(elem) {
        var dirName = prompt("New file name:");
        if(dirName.length > 0) {
            $("#loader").html("Creating Directory...");
            $("#loader").fadeIn(250, function() {
                $.ajax(new function() {
                    this.async = false;
                    this.method = "POST";
                    this.data = {
                        'create_directory' : $(elem).data("path"),
                        'dir_name' : dirName
                    };
                    this.dataType = "json";
                    this.url = "/";
                    this.complete = function(xhr, textStatus) {
                        window.location.reload();
                    }
                });
            });
        }
    };
    this.deleteDirectory = function(elem) {
        
        if(window.confirm("The directory must be empty. Are you sure you would like to delete the directory?")) {
            $("#loader").html("Deleting Directory...");
            $("#loader").fadeIn(250, function() {
                $.ajax(new function() {
                    this.async = false;
                    this.method = "POST";
                    this.data = {
                        'delete_directory' : $(elem).data("path")
                    };
                    this.dataType = "json";
                    this.url = "/";
                    this.complete = function(xhr, textStatus) {
                        window.location.reload();
                    }
                });
            });
        }
    };
    this.deleteFile = function(elem) {
        if(window.confirm("Are you sure you would like to delete the file?")) {
            $("#loader").html("Deleting File...");
            $("#loader").fadeIn(250, function() {
                $.ajax(new function() {
                    this.async = false;
                    this.method = "POST";
                    this.data = {
                        'delete_file' : $(elem).data("path")
                    };
                    this.dataType = "json";
                    this.url = "/";
                    this.complete = function(xhr, textStatus) {
                        window.location.reload();
                    }
                });
            });
        }
    };
    this.getContent = function() {
        $.ajax(new function() {
            this.url = '/';
            this.type = 'GET';
            this.dataType = 'json';
            this.data = {
                'dir' : Browser.currentDirectory
            };
            this.beforeSend = function() {
                $("#FileExplorerAccordion").css('filter','opacity(15%)');
            };
            this.success = function(data, textStatus, jqXHR ) {
                $("#NavbarPath").html(data['NavbarPath']);
                $("#FileExplorerAccordion").html(data['FileExplorerAccordion']);
                $("#FileExplorerAccordion").css('filter','opacity(100%)');
                $("#CreateDirectoryButton").data('path', Browser.currentDirectory);
                $("#file-upload").data('path', Browser.currentDirectory);
            };
        });
    };
    this.navigate = function(elem) {
        this.currentDirectory = $(elem).data('href');
        this.getContent();
    };
    this.navigateCwdUp = function() {
        var parts = this.currentDirectory.split('\\');
        if(parts.length > 1) {
            var cwd = '';
            for(var i = 0; i < parts.length-2; i++) {
                cwd += parts[i];
            }
            this.currentDirectory = cwd;
            if( i > 0) {
                this.currentDirectory += '\\';
            }
        }
        this.getContent();
    };
    this.navigateHome = function() {
        this.currentDirectory = '';
        this.getContent();
    };
    this.loadImage = function(targetFileNumber) {
        var imagePreviewElem = document.getElementById("ImagePreviewElem-"+targetFileNumber);
        if(!imagePreviewElem) {
            imagePreviewElem = document.createElement('img');
            imagePreviewElem.id = "ImagePreviewElem-"+targetFileNumber;
            imagePreviewElem.classList.add('imagePreviewElem');
            imagePreviewElem.src = $("#ImagePreview-"+targetFileNumber).data('src');
            $("#ImagePreview-"+targetFileNumber).html(imagePreviewElem);


        }




    };
};
