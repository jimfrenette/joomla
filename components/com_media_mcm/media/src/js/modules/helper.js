/**
 * helper functions
 *
 * @package     Joomla.Extensions
 * @subpackage  com_media_mcm
 */

var $ = require('jQuery');

exports.findIndexOf = function(array, key, value) {
    var i, arrayLen = array.length;
    for(i = 0; i < arrayLen; i++) {
        if(array[i].hasOwnProperty(key) && array[i][key] === value) {
            return i;
        }
    }
    return -1;
};

exports.showMessage = function(text, type, heading, element) {
    // optional parameters
    var type = type || 'success', // types: info, danger
        heading = heading || 'Message',
        $container = element || $('#system-message-container');
        $body =
            $('<div/>').attr('id', 'system-message').append(
                $('<div/>').attr('class', 'alert alert-' + type).append(
                    $('<a/>', {
                        class: 'close',
                        text: 'x'
                    }).attr('data-dismiss', 'alert')
                ).append(
                    $('<h4/>', {
                        class: 'alert-heading',
                        text: heading
                    })
                ).append(
                    $('<p/>', {
                        class: 'alert-message',
                        text: text
                    })
                )
            );

    $container.fadeOut(500, function() {
        $(this).html($body).fadeIn(500);
    });
}