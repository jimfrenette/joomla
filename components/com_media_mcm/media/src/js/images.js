/**
 * images modal: image manager behavior for _MCM component
 *
 * @package     Joomla.Extensions
 * @subpackage  com_media_mcm
 */

var $ = require('jQuery');
var com_media_mcm = require('com_media_mcm');
var helper = require('./modules/helper');
var iresizer = require('./modules/iresizer');

var $applyBtn          = $('#image-apply'),
    $deleteBtn         = $('#image-delete'),
    $galleryImageFlag  = $('#gallery-image-flag'),
    $introImageFlag    = $('#intro-image-flag'),
    $loading           = $('.loading'),
    // form inputs under images and links tab in parent
    $galleryImage      = $('#jform_images_image_gallery', parent.document.body);
    $introImage        = $('#jform_images_image_intro', parent.document.body);
    $introImageCaption = $('#jform_images_image_intro_caption', parent.document.body);

_applyHandler = function() {
    // loading ...
    $loading.removeClass('hide');

    var path = $('#f_url').val(),
        caption = $.trim($('#f_caption').val());

    // set value of existing image intro inputs
    if ($introImageFlag.prop('checked')) {
        // insert values
        _setIntroImageValue(path, caption);
    } else {
        if (path == $introImage.val()) {
            // remove values
            _setIntroImageValue('','');
        }
    }

    var action = $galleryImageFlag.prop('checked') ? 'add' : 'remove';

    // set value of image gallery input
    _setGalleryImageValue(path, caption, action);

    // parent message: changes pending article save
    helper.showMessage(com_media_mcm.language.apply_article_msg,
        'info',
        'Image Changes Pending',
        $('#system-message-container', parent.document.body)
    );

    //TODO: _applyHandler callback for the loading ...
    setTimeout(function(){ $loading.addClass('hide'); }, 1500);
};

_deleteHandler = function(event) {
    event.preventDefault();

    // loading ...
    $loading.removeClass('hide');

    var path = $('#f_url').val(),
        caption = $.trim($('#f_caption').val()),
        file = path.split('/').pop(),
        url = $(event.currentTarget).data('target') + '&rm[]=' + file;

    $.get(url, function(data) {
        // update inputs
        _setIntroImageValue('','');
        _setGalleryImageValue(path, caption, 'remove');

        // close image manager
        $('[data-role="image-manager"]').addClass('hide');

        // refresh imagesList
        $('#imageframe').attr('src', function (i, val) { return val; });

        // changes pending, save to apply
        helper.showMessage(file + ' deleted. ' + _MCM.language.apply_article_msg,
            'success',
            'Image Changes Pending',
            $('#system-message-container')
        );

        // parent message: changes pending article save
        helper.showMessage(com_media_mcm.language.apply_article_msg,
            'info',
            'Images Removed',
            $('#system-message-container', parent.document.body)
        );

        //TODO: _deleteHandler callback for the loading ...
        setTimeout(function(){ $loading.addClass('hide'); }, 1500);
    });
};

_setGalleryImageValue = function(path, caption, action) {

    var gallery = JSON.parse($galleryImage.val()),
        index = helper.findIndexOf(gallery, 'image', path);

    if ('add' == action && index == -1) {
        // add to gallery
        gallery.push({image:path, caption:caption});
    }
    if ('remove' == action && index != -1) {
        // remove from gallery
        gallery.splice(index, 1);
    }

    // set input value
    $galleryImage.val(JSON.stringify(gallery));
};

_setIntroImageValue = function(path, caption) {
    // set inputs
    $introImage.val(path);
    $introImageCaption.val(caption);
};

// event listeners
$applyBtn.on('click', function() {
    _applyHandler();
});

$deleteBtn.on('click', function(event) {
    _deleteHandler(event);
});
