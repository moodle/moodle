import jQuery from 'jquery';
/*
* jQuery plugin that makes elements editable
*
* @author Victor Jonsson (http://victorjonsson.se/)
* @website https://github.com/victorjonsson/jquery-editable/
* @license GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
* @version 1.3.6.dev
* @donations http://victorjonsson.se/donations/
*/
export default (function($, window) {

    'use strict';

    if ($ === undefined) {
        $ = jQuery;
    }

    var $win = $(window), // Reference to window

    // Reference to textarea
    $textArea = false,

    // Reference to currently edit element
    $currentlyEdited = false,

    // Some constants
    EVENT_ATTR = 'data-edit-event',
    IS_EDITING_ATTR = 'data-is-editing',
    EMPTY_ATTR = 'data-is-empty',
    DBL_TAP_EVENT = 'dbltap',
    SUPPORTS_TOUCH = 'ontouchend' in window,
    TINYMCE_INSTALLED = 'tinyMCE' in window && typeof window.tinyMCE.init == 'function',

    // reference to old is function
    oldjQueryIs = $.fn.is,

    /*
     * Function responsible of triggering double tap event
     */
    lastTap = 0,
    tapper = function() {
        var now = new Date().getTime();
        if( (now-lastTap) < 250 ) {
            $(this).trigger(DBL_TAP_EVENT);
        }
        lastTap = now;
    },

    /**
     * Event listener that largens font size
     */
    keyHandler = function(e) {
        if( e.keyCode == 13 && e.data.closeOnEnter ) {
            $currentlyEdited.editable('close');
        }
    else if( e.keyCode == 27 ) {
            $textArea.val($currentlyEdited.attr('orig-text'));
            $currentlyEdited.editable('close');
        }
        else if( e.data.toggleFontSize && (e.metaKey && (e.keyCode == 38 || e.keyCode == 40)) ) {
            var fontSize = parseInt($textArea.css('font-size'), 10);
            fontSize += e.keyCode == 40 ? -1 : 1;
            $textArea.css('font-size', fontSize+'px');
            return false;
        }
    },

    /**
     * Adjusts the height of the textarea to remove scroll
     * @todo This way of doing it does not make the textarea smaller when the number of text lines gets smaller
     */
    adjustTextAreaHeight = function() {
        if( $textArea[0].scrollHeight !== parseInt($textArea.attr('data-scroll'), 10) ) {
            $textArea.css('height', $textArea[0].scrollHeight +'px');
            $textArea.attr('data-scroll', $textArea[0].scrollHeight);
        }
    },

    /**
     * @param {jQuery} $el
     * @param {String} newText
     */
    resetElement = function($el, newText, emptyMessage) {
        $el.removeAttr(IS_EDITING_ATTR);

        if (newText.length == 0 && emptyMessage) {
            $el.html(emptyMessage);
            $el.attr(EMPTY_ATTR, 'empty');
        } else {
            $el.html( newText );
            $el.removeAttr(EMPTY_ATTR);
        }
        $textArea.remove();
    },


    /**
     * Function creating editor
     */
    elementEditor = function($el, opts) {

        if( $el.is(':editing') ) {
            return;
        }

        $currentlyEdited = $el;
        $el.attr(IS_EDITING_ATTR, '1');

        if ($el.is(':empty')) {
            $el.removeAttr(EMPTY_ATTR);
            $el.html('');
        }

        var defaultText = $.trim( $el.html() ),
            defaultFontSize = $el.css('font-size'),
            elementHeight = $el.height(),
            textareaStyle = 'width: 96%; padding:0; margin:0; border:0; background:none;'+
                            'font-family: '+$el.css('font-family')+'; font-size: '+$el.css('font-size')+';'+
                            'font-weight: '+$el.css('font-weight')+';';

        $el.attr('orig-text', defaultText);
        if( opts.lineBreaks ) {
            defaultText = defaultText.replace(/<br( |)(|\/)>/g, '\n');
        }

        $textArea = $('<textarea></textarea>');
        $el.text('');

        // The editor should always be static
        textareaStyle += 'position: static';

        /*
          TINYMCE EDITOR
         */
        if( opts.tinyMCE !== false ) {
            var id = 'editable-area-'+(new Date().getTime());
            $textArea
                .val(defaultText)
                .appendTo($el)
                .attr('id', id);

            if( typeof opts.tinyMCE != 'object' ) {
                opts.tinyMCE = {};
            }

            opts.tinyMCE.mode = 'exact';
            opts.tinyMCE.elements = id;
            opts.tinyMCE.width = $el.innerWidth();
            opts.tinyMCE.height = $el.height() + 200;
            opts.tinyMCE.theme_advanced_resize_vertical = true;

            opts.tinyMCE.setup = function (ed) {
                ed.onInit.add(function(editor) {
                    var editorWindow = editor.getWin();
                    var hasPressedKey = false;
                    var editorBlur = function() {

                        var newText = $(editor.getDoc()).find('body').html();
                        if( $(newText).get(0).nodeName == $el.get(0).nodeName ) {
                            newText = $(newText).html();
                        }

                        // Update element and remove editor
                        resetElement($el, newText, opts.emptyMessage);
                        editor.remove();
                        $textArea = false;
                        $win.unbind('click', editorBlur);
                        $currentlyEdited = false;

                        // Run callback
                        if( typeof opts.callback == 'function' ) {
                            opts.callback({
                                content : newText == defaultText || !hasPressedKey ? false : newText,
                                fontSize : false,
                                $el : $el
                            });
                        }
                    };

                    // Blur editor when user clicks outside the editor
                    setTimeout(function() {
                        $win.bind('click', editorBlur);
                    }, 500);

                    // Create a dummy textarea that will called upon when
                    // programmatically interacting with the editor
                    $textArea = $('<textarea></textarea>');
                    $textArea.bind('blur', editorBlur);

                    editorWindow.onkeydown = function() {
                        hasPressedKey = true;
                    };

                    editorWindow.focus();
                });
            };

            window.tinyMCE.init(opts.tinyMCE);
        }

        /*
         TEXTAREA EDITOR
         */
        else {

            if( opts.toggleFontSize || opts.closeOnEnter ) {
                $win.bind('keydown', opts, keyHandler);
            }
            $win.bind('keyup', adjustTextAreaHeight);

            $textArea
                .html(defaultText)
                .blur(function() {

                    $currentlyEdited = false;

                    // Get new text and font size
                    var newText = $.trim( $textArea.val() ),
                        newFontSize = $textArea.css('font-size');

                    newText = $('<div />').text(newText).html();

                    if( opts.lineBreaks ) {
                        newText = newText.replace(new RegExp('\\n','g'), '<br />');
                    }

                    // Update element
                    resetElement($el, newText, opts.emptyMessage);
                    if( newFontSize != defaultFontSize ) {
                        $el.css('font-size', newFontSize);
                    }

                    // remove textarea and size toggles
                    $win.unbind('keydown', keyHandler);
                    $win.unbind('keyup', adjustTextAreaHeight);

                    // Run callback
                    if( typeof opts.callback == 'function' ) {
                        opts.callback({
                            content : newText == defaultText ? false : newText,
                            fontSize : newFontSize == defaultFontSize ? false : newFontSize,
                            $el : $el
                        });
                    }
                })
                .attr('style', textareaStyle)
                .appendTo($el)
                .css({
                    margin: 0,
                    padding: 0,
                    height : elementHeight +'px',
                    overflow : 'hidden'
                })
                .css(opts.editorStyle)
                .focus()
                .get(0).select();

            adjustTextAreaHeight();

        }

        $el.trigger('edit', [$textArea]);
    },

    /**
     * Event listener
     */
    editEvent = function(event) {

        if( $currentlyEdited !== false && !$currentlyEdited.children("textarea").is(clickedElement)) {
            // Not closing the currently open editor before opening a new
            // editor makes things go crazy
            $currentlyEdited.editable('close');
            elementEditor($(this), event.data);
        }
        else {
            elementEditor($(this), event.data);
        }
        return false;
    };

    /**
     * Jquery plugin that makes elments editable
     * @param {Object|String} [opts] Either callback function or the string 'destroy' if wanting to remove the editor event
     * @return {jQuery|Boolean}
     */
    $.fn.editable = function(opts) {

        if(typeof opts == 'string') {

            if( this.is(':editable') ) {

                switch (opts) {
                    case 'open':
                        if( !this.is(':editing') ) {
                            this.trigger(this.attr(EVENT_ATTR));
                        }
                        break;
                    case 'close':
                        if( this.is(':editing') ) {
                            $textArea.trigger('blur');
                        }
                        break;
                    case 'destroy':
                        if( this.is(':editing') ) {
                            $textArea.trigger('blur');
                        }
                        this.unbind(this.attr(EVENT_ATTR));
                        this.removeAttr(EVENT_ATTR);
                        break;
                    default:
//                        console.warn('Unknown command "'+opts+'" for jquery.editable');
                }

            } else {
//                console.error('Calling .editable() on an element that is not editable, call .editable() first');
            }
        }
        else {

            if( this.is(':editable') ) {
//                console.warn('Making an already editable element editable, call .editable("destroy") first');
                this.editable('destroy');
            }

            opts = $.extend({
                event : 'dblclick',
                touch : true,
                lineBreaks : true,
                toggleFontSize : true,
                closeOnEnter : false,
                emptyMessage : false,
                tinyMCE : false,
                editorStyle : {}
            }, opts);

            if( opts.tinyMCE !== false && !TINYMCE_INSTALLED ) {
//                console.warn('Trying to use tinyMCE as editor but id does not seem to be installed');
                opts.tinyMCE = false;
            }

            if( SUPPORTS_TOUCH && opts.touch ) {
                opts.event = DBL_TAP_EVENT;
                this.unbind('touchend', tapper);
                this.bind('touchend', tapper);
            }
            else {
                opts.event += '.textEditor';
            }

            this.bind(opts.event, opts, editEvent);
            this.attr(EVENT_ATTR, opts.event);

            // If it is empty to start with, apply the empty message
            if (this.html().length == 0 && opts.emptyMessage) {
                this.html(opts.emptyMessage);
                this.attr(EMPTY_ATTR, 'empty');
            } else {
                this.removeAttr(EMPTY_ATTR);
            }
        }

        return this;
    };

    /**
     * Add :editable :editing to $.is()
     * @param {Object} statement
     * @return {*}
     */
    $.fn.is = function(statement) {
        if( typeof statement == 'string' && statement.indexOf(':') === 0) {
            if( statement == ':editable' ) {
                return this.attr(EVENT_ATTR) !== undefined;
            } else if( statement == ':editing' ) {
                return this.attr(IS_EDITING_ATTR) !== undefined;
            } else if( statement == ':empty' ) {
                return this.attr(EMPTY_ATTR) !== undefined;
            }
        }
        return oldjQueryIs.apply(this, arguments);
    };

    // The latest element clicked
    var clickedElement;
    $(document).mousedown(function(e) {
        clickedElement = $(e.target);
    });

})(window.jQuery, window);
