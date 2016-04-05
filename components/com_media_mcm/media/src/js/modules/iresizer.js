/**
 * image upload / resizer behavior for
 * image modal
 *
 * @package     Joomla.Extensions
 * @subpackage  com_media_mcm
 */

var $ = require('jQuery');
var com_media_mcm = require('com_media_mcm');

var dropTimer,
    $dropTarget = $('.drag-area'),
    $fileInput  = $('#' + com_media_mcm.iresizer.file_id),
    $imgPreview = $('[data-role="base64img-upload"]'),
    $imgCancel  = $('.upload-cancel'),
    maxWidth    = com_media_mcm.iresizer.max_width;

// required for drag and drop file access
$.event.props.push('dataTransfer');

$dropTarget.on("dragover", function(event) {
    clearTimeout(dropTimer);
    if (event.currentTarget == $dropTarget[0]) {
        $dropTarget.addClass('over');
    }

    return false; // required for drop to work
});

$dropTarget.on('dragleave', function(event) {
    if (event.currentTarget == $dropTarget[0]) {
        dropTimer = setTimeout(function() {
            $dropTarget.removeClass('over');
        }, 200);
    }
});

$dropTarget.on('drop', function(event) {
    event.preventDefault(); // or else the browser will open the file
    _handleDrop(event.dataTransfer.files);
});

$fileInput.on('change', function(event) {
    _handleDrop(event.target.files);
});

$imgCancel.on('click', function(event) {
    $imgPreview.addClass('hide');
    $fileInput.wrap('<form>').closest('form').get(0).reset();
    $fileInput.unwrap();
});

_handleDrop = function(files){
    $dropTarget.removeClass('over');
    $imgPreview.addClass('hide');

    var file = files[0]; // Multiple files can be dropped. Lets only deal with the "first" one.

    if (file.type.match('image.*')) {
        _resizeImage(file, maxWidth, function(result) {
            $('#base64img').attr('src', result);
            $('#base64str').val(result);
            $('#base64name').val(file.name);
            $imgPreview.removeClass('hide');
        });
    } else {
        alert("That file wasn't an image.");
    }
};

_resizeImage = function(file, size, callback) {

    console.log('SIZE',size);

    var fileTracker = new FileReader();
    fileTracker.onload = function() {
        var image = new Image();
        image.onload = function(){
            var canvas = document.createElement("canvas");
            /*
            if(image.height > size) {
                image.width *= size / image.height;
                image.height = size;
            }
            */
            if(image.width > size) {
                image.height *= size / image.width;
                image.width = size;
            }
            var ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            canvas.width = image.width;
            canvas.height = image.height;
            ctx.drawImage(image, 0, 0, image.width, image.height);

            //callback(canvas.toDataURL("image/png"));
            callback(canvas.toDataURL("image/jpeg", 0.9));
        };
        image.src = this.result;
    };

    fileTracker.readAsDataURL(file);

    fileTracker.onabort = function() {
        alert("The upload was aborted.");
    };

    fileTracker.onerror = function() {
        alert("An error occured while reading the file.");
    };
};