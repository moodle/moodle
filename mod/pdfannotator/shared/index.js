
/**
 * Function removes the spacing between tab navigation and document tool bar
 *
 * @param {type} Y
 * @return {undefined}
 */
 function adjustPdfannotatorNavbar(Y) {
    let navbar = document.getElementsByClassName('nav');
    for (let i = 0; i < navbar.length; i++) {
        (function(innerI) {
            tab = navbar[innerI];
            tab.classList.add('pdfannotatornavbar');
        })(i);
    }
}

//The MIT License (MIT)
//
//Copyright (c) 2016 Instructure, Inc. (https://github.com/instructure/pdf-annotate.js/blob/master/docs/index.js, 1.3.2018)
//modified      2018 RWTH Aachen, Rabea de Groot, Anna Heynkes and Friederike Schwager (see README.md)
//
//Permission is hereby granted, free of charge, to any person obtaining a copy
//of this software and associated documentation files (the "Software"), to deal
//in the Software without restriction, including without limitation the rights
//to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
//copies of the Software, and to permit persons to whom the Software is
//furnished to do so, subject to the following conditions:
//
//The above copyright notice and this permission notice shall be included in all
//copies or substantial portions of the Software.
//
//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
//IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
//FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
//AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
//LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
//OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
//SOFTWARE.
//
//R: The first parameter has to be Y, because it is a default YUI-object, because moodle gives this object first.
function startIndex(Y,_cm,_documentObject,_contextId, _userid,_capabilities, _toolbarSettings, _page = 1,_annoid = null,_commid = null, _editorSettings){ // 3. parameter war mal _fileid

    // Require amd modules.
   require(['jquery','core/templates','core/notification','mod_pdfannotator/jspdf', 'core/fragment'], function($,templates,notification,jsPDF, Fragment) {
        var currentAnnotations = [];
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

	var _twitterText = __webpack_require__(1);

	var _twitterText2 = _interopRequireDefault(_twitterText);

	var _ = __webpack_require__(2);

	var _2 = _interopRequireDefault(_);

	var _initColorPicker = __webpack_require__(4);

	var _initColorPicker2 = _interopRequireDefault(_initColorPicker);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	var UI = _2.default.UI; 
	var documentId = _documentObject.annotatorid;// The id of the pdfannotator.

	var PAGE_HEIGHT = void 0;
	var RENDER_OPTIONS = {
	  documentId: _documentObject.annotatorid,// The id of the pdfannotator.
          documentPath: _documentObject.fullurl,// The path to pdf.
	  pdfDocument: null,
	  scale: parseFloat(localStorage.getItem(documentId + '/scale'), 10) || 1.0,
	  rotate: parseInt(localStorage.getItem(documentId + '/rotate'), 10) || 0
	};

        /* *********************** eigener Store Adapter!! **********************************/
        let MyStoreAdapter = new _2.default.StoreAdapter({
            /**
             * This function get all annotations of a specific document on a specific page.
             * @param {type} documentId of the pdfannotator
             * @param {type} pageNumber, of which you want to have the annotations
             * @returns {unresolved} array of annotation objects
             */
            getAnnotations(documentId, pageNumber) {
                return $.ajax({
                    type: "POST",
                    url: "action.php",
                    data: { "documentId": documentId, "page_Number": pageNumber, "action": 'read', sesskey: M.cfg.sesskey}
                }).then(function(data){
                    return JSON.parse(data);
                });
            },

            /**
             * Method selects an annotation by id and returns it to JavaScript for shifting
             * 
             * @param {type} documentId
             * @param {type} annotationId
             * @return {unresolved} annotation object
             */
            getAnnotation(documentId, annotationId) {
                return $.ajax({
                    type: "POST",
                    url: "action.php",
                    data: { "documentId": documentId, "annotationId": annotationId, "action": 'readsingle', sesskey: M.cfg.sesskey}
                }).then(function(data){
                    return JSON.parse(data);
                });
            },

            /**
             * This function sends the annotation to save in database
             * @param {type} documentId
             * @param {type} pageNumber
             * @param {type} annotation
             * @returns {annotation object} The annotation object with the right id. In case of error an error object returns
             */
            addAnnotation(documentId, pageNumber, annotation) {
                var tmp = annotation;
                tmp.newAnno = true;
                currentAnnotations[pageNumber].push(tmp);
                annotation = JSON.stringify(annotation);
                return $.ajax({
                    type: "POST",
                    url: "action.php",
                    data: { "documentId": documentId, "page_Number": pageNumber, "annotation": annotation, "action": 'create', sesskey: M.cfg.sesskey}
                }).then(function(data){
                    data = JSON.parse(data);

                    if(data.status === "success") {
                        var index = currentAnnotations[pageNumber].indexOf(tmp);
                        currentAnnotations[pageNumber][index] = data;
                        return data;

                    } else if (data.status === 'error') {
                            notification.addNotification({
                                message: data.reason,
                                type: "error"
                            });
                            if (data.log){
                                console.error(data.log);
                            }
                    }
                    return {'status':'error'};
                });
            },

            /**
             * Method passes the edited annotation object along with its old id on to action.php for updating/saving
             * 
             * @param {type} documentid
             * @param {type} annotationId
             * @param {type} annotation
             * @return {unresolved}
             */
            editAnnotation(documentid, page, annotationId, annotationJS) {

                var annotation = JSON.stringify(annotationJS);
                return $.ajax({
                    type: "POST",
                    url: "action.php",
                    data: { "documentId": documentid, "annotationId": annotationId, "annotation": annotation, "action": 'update', sesskey: M.cfg.sesskey}
                }).then(function(data){
                        data = JSON.parse(data);
                        if(data.status == 'error') {
                            console.error(M.util.get_string('error:editAnnotation', 'pdfannotator'));
                            return false;
                        }
                        for (var anno in currentAnnotations[page]){
                            if(currentAnnotations[page][anno].uuid == annotationId){
                                currentAnnotations[page][anno]=annotationJS.annotation;
                                break;
                            }
                        }
                        return data; 
                });
            },
            /**
             * This function sends the delete instruction to the server and notifies the user, if the deletion was successful
             * @param {type} documentId
             * @param {type} annotation
             * @returns {unresolved} the status success or error
             */
            deleteAnnotation(documentId, annotation, deletionInfo=true) {
                return $.ajax({
                    type: "POST",
                    url: "action.php",
                    data: { "documentId": documentId, "annotation": annotation, "cmid": _cm.id, "action": 'delete', sesskey: M.cfg.sesskey}
                }).then(function(data){
                    data = JSON.parse(data);
                    if(data.status === "success") {
                        if(deletionInfo) {
                            notification.addNotification({
                                message: M.util.get_string('annotationDeleted', 'pdfannotator'),
                                type: "success"
                            });
                            setTimeoutNotification();
                        }                            
                        var node = document.querySelector('[data-pdf-annotate-id="'+data.deleteannotation+'"]');
                        if(node){
                            node.parentNode.removeChild(node);
                            document.querySelector('.comment-list-container').innerHTML = '';
                            document.querySelector('.comment-list-form').setAttribute('style','display:none');
                            UI.renderQuestions(documentId,$('#currentPage').val());     
                        }
                    } else if (data.status === 'error') {
                            notification.addNotification({                                   
                                message: M.util.get_string('deletionForbidden', 'pdfannotator') + data.reason,
                                type: "error"
                            });
                    }

                    return data;
                });
            },
            /**
             * 
             * @param {type} documentId
             * @param {type} annotationId
             * @param {type} content
             * @param {type} visibility
             * @param {type} isquestion
             * @returns {unresolved}
             */
            addComment(documentId, annotationId, content, visibility = "public", isquestion = 0) {
                var pdfannotator_addcomment_editoritemid = document.querySelectorAll('.pdfannotator_addcomment_editoritemid')[0].value;  
                return $.ajax({
                    type: "POST",
                    url: "action.php",
                    data: { "documentId": documentId, "annotationId": annotationId, "content": content, "visibility": visibility, "action": 'addComment', "isquestion": isquestion, "cmid":_cm.id, "pdfannotator_addcomment_editoritemid": pdfannotator_addcomment_editoritemid, sesskey: M.cfg.sesskey}
                }).then(function(data){
                    data = data.substring(data.indexOf('{'),data.length);    
                    //TODO compare to data before data.substring
                    data = JSON.parse(data);
                    if (data.status === 'success') {
                        return data;
                    }else if (data.status == -1){
                        notification.alert(M.util.get_string('error','pdfannotator'),M.util.get_string('missingAnnotation','pdfannotator'),'ok');
                        return false;
                    } else {
                        notification.addNotification({
                            message: M.util.get_string('error:addComment','pdfannotator'),
                            type: "error"
                        });
                        return false;
                    }
                });
            },
            
            /**
             * Updates db after a comment has been edited
             * 
             * @param {type} documentId
             * @param {type} commentId
             * @param {type} content
             * @returns {unresolved}
             */
            editComment(documentId, commentId, content, editForm) {
                var pdfannotator_editcomment_editoritemid = editForm.querySelectorAll('.pdfannotator_editcomment_editoritemid')[0].value;
                return $.ajax({
                    type: "POST",
                    url: "action.php",
                    data: { "documentId": documentId, "commentId": commentId, "content": content, "action": "editComment", "pdfannotator_editcomment_editoritemid": pdfannotator_editcomment_editoritemid, "cmid":_cm.id, sesskey: M.cfg.sesskey}
                }).then(function(data){
                    return JSON.parse(data);      
                });
            },

            deleteComment(documentId, commentId, action) {
                if (action) { // Report comment to manager

                    return $.ajax({
                        type: "POST",
                        url: "action.php",
                        data: { "documentId": documentId, "commentId": commentId, "action": 'reportComment', sesskey: M.cfg.sesskey} 
                    }).then(function(){
                        alert('Comment has been reported');
                    });

                } else { // Delete comment if authorised to do so
                    return $.ajax({
                        type: "POST",
                        url: "action.php",
                        data: { "documentId": documentId, "commentId": commentId, "cmid": _cm.id, "action": 'deleteComment', sesskey: M.cfg.sesskey}
                    }).then(function(data){
                        data = JSON.parse(data);
                        if(data.status === "success") {                    
                            // remove comment from DOM
                            var child = document.getElementById('comment_'+commentId);
                            if(child !== null){
                                if(data.wasanswered){
                                    $('#comment_'+commentId+' .chat-message-text p').html('<em>'+M.util.get_string('deletedComment', 'pdfannotator') + '</em>');
                                    $('#comment_'+commentId+' .chat-message-meta .time').remove();
                                    $('#comment_'+commentId+' .chat-message-meta .user').remove();
                                    $('#comment_'+commentId+' .countVotes').remove();
                                    $('#comment_'+commentId+' .comment-like-a').attr("disabled","disabled").css("visibility", "hidden");
                                    $('#comment_'+commentId+' .edited').remove();
                                    if (data.isquestion == 0) {
                                        $('#comment_'+commentId+' .dropdown').remove();
                                    } else {
                                        $('#comment_'+commentId+' .chat-message-meta .dropdown .comment-report-button').remove();
                                        $('#comment_'+commentId+' .chat-message-meta .dropdown .comment-delete-a').remove();
                                        $('#comment_'+commentId+' .chat-message-meta .dropdown .comment-edit-a').remove();
                                        $('#comment_'+commentId+' .chat-message-meta .dropdown .comment-forward-a').remove();
                                        $('#comment_'+commentId+' .chat-message-meta .dropdown #hidebutton'+commentId).remove();
                                    }
                                } else {
                                    var parent = child.parentNode;
                                    parent.removeChild(child);
                                }
                            }

                            notification.addNotification({
                                message: M.util.get_string('commentDeleted', 'pdfannotator'),
                                type: "success"
                            });
                            setTimeoutNotification();

                            // If the predecessor comment was marked as deleted, remove it from DOM as well
                            // (This is currently irrelevant, because we jump back to overview after deletion, but I'd prefer to stay in the thread.)
                            data.followups.forEach(function(element){ 
                                var id = 'comment_'+ element;
                                var child = document.getElementById(id);
                                var parent = child.parentNode;
                                parent.removeChild(child);
                            });
                            // If the annotation is deleted as well, remove it from the pdf.
                            if(data.deleteannotation !== 0) {                                                               
                                var node = document.querySelector('[data-pdf-annotate-id="'+data.deleteannotation+'"]');
                                node.parentNode.removeChild(node);
                                document.querySelector('.comment-list-container').innerHTML = '';
                                document.querySelector('.comment-list-form').setAttribute('style','display:none');
                                UI.renderQuestions(documentId,$('#currentPage').val());                                
                            }
                        } else {
                            notification.addNotification({
                                message: M.util.get_string('deletionForbidden', 'pdfannotator'),
                                type: "error"
                            });
                        }
                        return data;
                    });
                }

            },
            /**
             * Hide a comment from participants' view / display as deleted to anyone
             * but the manager/teacher/editing teacher
             * 
             * @param {type} documentId
             * @param {type} commentId
             * @param string action
             * @returns {unresolved}
             */
            hideComment(documentId, commentId) {
                return $.ajax({
                    type: "POST",
                    url: "action.php",
                    data: { "documentId": documentId, "commentId": commentId, "cmid": _cm.id, "action": 'hideComment', sesskey: M.cfg.sesskey}
                }).then(function(data){
                    data = JSON.parse(data);
                    if (data.status === "success") {
                        $("#comment_" + commentId).addClass('dimmed_text'); // render chat box in grey.
                        $('#chatmessage' + commentId).append("<br><span id='taghidden" + commentId + "' class='tag tag-info'>" + M.util.get_string('hiddenforparticipants', 'pdfannotator') + "</span>");
                        let comment = document.getElementById("comment_" + commentId);
                        renderMathJax(comment);
                        notification.addNotification({
                            message: M.util.get_string('successfullyHidden', 'pdfannotator'),
                            type: "success"
                        });
                        setTimeoutNotification();
                    } else {
                        notification.addNotification({
                            message: M.util.get_string('error:hideComment','pdfannotator'),
                            type: "error"
                        });                                        
                    }
                });
            },
            /**
             * 
             * @param {type} documentId
             * @param {type} commentId
             * @returns {unresolved}
             */
            redisplayComment(documentId, commentId) {

                return $.ajax({
                    type: "POST",
                    url: "action.php",
                    data: { "documentId": documentId, "commentId": commentId, "cmid": _cm.id, "action": 'redisplayComment', sesskey: M.cfg.sesskey}
                }).then(function(data){
                    data = JSON.parse(data);
                    if (data.status === "success") {
                        $("#comment_" + commentId).removeClass('dimmed_text'); // render chat box in grey.
                        $('#taghidden' + commentId).remove();
                        let comment = document.getElementById("comment_" + commentId);
                        renderMathJax(comment);
                        notification.addNotification({
                            message: M.util.get_string('successfullyRedisplayed', 'pdfannotator'),
                            type: "success"
                        });
                        setTimeoutNotification();
                    } else {
                        notification.addNotification({
                            message: M.util.get_string('error:redisplayComment','pdfannotator'),
                            type: "error"
                        });                                        
                    }
                });

            },
            /**
             * Method collects all comments of one annotation
             * 
             * @param {type} documentId
             * @param {type} annotationId
             * @return {unresolved}
             */
            getComments(documentId, annotationId){
                if (annotationId === undefined) {
                    annotationId = 0;
                }
                return $.ajax({
                    type: "POST",
                    url: "action.php",
                    data: { "documentId": documentId, "annotationId": annotationId, "action": 'getComments', sesskey: M.cfg.sesskey}
                }).then(function(data){
                    return JSON.parse(data);
                });
            },
            
            /**
             * This function collects all Questions (Annotations with min. one comment)
             * @param {type} documentId
             * @param {type} pageNumber
             * @returns {unresolved} array of comments objects (only questions) with an additional attribute 'answercount'
             */
            getQuestions(documentId, pageNumber, pattern){
                return $.ajax({
                    type: "POST",
                    url: "action.php",
                    data: { "documentId": documentId, "page_Number": pageNumber, "action": 'getQuestions', "pattern": pattern, sesskey: M.cfg.sesskey}
                }).then(function(data){
                    return JSON.parse(data);
                });
            },

            /**
             * Get all information about an annotation. This function only retrieves information about annotations of types 'drawing' and 'textbox'.
             * @param {type} documentId
             * @param {type} commentId
             * @returns {unresolved}
             */
            getInformation(documentId, annotationId){
                return $.ajax({
                    type: "POST",
                    url: "action.php", 
                    data: { "documentId": documentId, "annotationId": annotationId, "action": 'getInformation', sesskey: M.cfg.sesskey}
                }).then(function(data){
                    return JSON.parse(data);
                });   
            },
            /**
             * inserts a vote into the database
             * @param {type} documentId
             * @param {type} commentId
             * @returns {unresolved}
             */
            voteComment(documentId, commentId){
                return $.ajax({
                    type: "POST",
                    url: "action.php", 
                    data: { "documentId": documentId, "commentid": commentId, "action": 'voteComment', sesskey: M.cfg.sesskey}
                }).then(function(data){
                    return JSON.parse(data);
                });   
            },

            subscribeQuestion(documentId, annotationId){
                return $.ajax({
                    type: "POST",
                    url: "action.php", 
                    data: { "documentId": documentId, "annotationid": annotationId, "action": 'subscribeQuestion', sesskey: M.cfg.sesskey}
                }).then(function(data){
                    return JSON.parse(data);
                });  
            },

            unsubscribeQuestion(documentId, annotationId){
                return $.ajax({
                    type: "POST",
                    url: "action.php", 
                    data: { "documentId": documentId, "annotationid": annotationId, "action": 'unsubscribeQuestion', sesskey: M.cfg.sesskey}
                }).then(function(data){
                    return JSON.parse(data);
                });  
            },

            markSolved(documentId, comment){
                return $.ajax({
                    type: "POST",
                    url: "action.php", 
                    data: { "documentId": documentId, "commentid": comment.uuid, "action": 'markSolved', sesskey: M.cfg.sesskey}
                }).then(function(data){
                    data = JSON.parse(data);
                    if (data.status === 'success') {
                        let i = $('#comment_'+comment.uuid+' .comment-solve-a i');
                        let span = $('#comment_'+comment.uuid+' .comment-solve-a span.menu-action-text');
                        let img = $('#comment_'+comment.uuid+' .comment-solve-a img');

                        comment.solved = !comment.solved;
                        if(comment.isquestion){
                            if(comment.solved){
                                $('#comment_'+comment.uuid+' .solved').append("<i class=\"icon fa fa-lock fa-fw solvedquestionicon\" title=\""+M.util.get_string('questionSolved', 'pdfannotator')+"\"></i>");
                                span.text(M.util.get_string('markUnsolved', 'pdfannotator'));
                            } else {
                                $('#comment_'+comment.uuid+' .solved').empty();
                                span.text(M.util.get_string('markSolved', 'pdfannotator'));
                            }                
                            i.toggleClass('fa-lock');
                            i.toggleClass('fa-unlock');
                        } else { // comment is answer
                            if(comment.solved){
                                $('#comment_'+comment.uuid+' .solved').append("<i class=\"icon fa fa-check fa-fw correctanswericon\" title=\""+M.util.get_string('questionSolved', 'pdfannotator')+"\"></i>");
                                span.text(M.util.get_string('removeCorrect', 'pdfannotator'));
                                img.attr('src',M.util.image_url('i/completion-manual-n','core'));
                            } else {
                                $('#comment_'+comment.uuid+' .solved').empty();
                                span.text(M.util.get_string('markCorrect', 'pdfannotator'));
                                img.attr('src',M.util.image_url('i/completion-manual-enabled','core'));
                            } 
                            $('#comment_'+comment.uuid).toggleClass('correct');
                        }
                    } else {
                        let message = comment.isquestion? M.util.get_string('error:closequestion','pdfannotator') : M.util.get_string('error:markcorrectanswer','pdfannotator');
                        notification.addNotification({
                            message: message,
                            type: "info"
                        });
                    }
                });  
            },

            getCommentsToPrint(documentId, getUrl = false){
                if (!getUrl) {
                    return $.ajax({
                    type: "POST",
                    url: "action.php", //?XDEBUG_SESSION_START=netbeans-xdebug", 
                    data: { "documentId": documentId, "action": 'getCommentsToPrint', sesskey: M.cfg.sesskey}
                    }).then(function(data){
                        return JSON.parse(data);
                    }).catch(function(err) {
                        notification.addNotification({
                            message: M.util.get_string('error:printcommentsdata','pdfannotator'),
                            type: "error"
                        });
                    });
                }
            },
        });

        /* ************** END Store Adapter!! **********************************/

	_2.default.setStoreAdapter(MyStoreAdapter);
	pdfjsLib.GlobalWorkerOptions.workerSrc = 'shared/pdf.worker.js?ver=00002';
	// Render stuff
	var NUM_PAGES = 0;
        var oldPageNumber;
        
        /**
         * This function determines which page the user should see after the scroll event
         * @param {type} e
         * @returns {void}
         */
        function handleScroll(e){
          var height = document.getElementById('viewer').clientHeight;
          var visiblePageNum = Math.round((e.target.scrollTop*100/ height) * NUM_PAGES / 100) + 1;
	  
          var visiblePage = document.querySelector('.page[data-page-number="' + visiblePageNum + '"][data-loaded="false"]');
          var visiblePageAfter = document.querySelector('.page[data-page-number="' + (visiblePageNum+1) + '"][data-loaded="false"]');
          var visiblePageBefore = document.querySelector('.page[data-page-number="' + (visiblePageNum-1) + '"][data-loaded="false"]');

	  if (visiblePage) {
	    setTimeout(function () {
	      UI.renderPage(visiblePageNum, RENDER_OPTIONS);
              if(visiblePageAfter) UI.renderPage(visiblePageNum + 1, RENDER_OPTIONS);
              if(visiblePageBefore) UI.renderPage(visiblePageNum - 1, RENDER_OPTIONS);
	    });
	  }else{
              // Anyway if the other pages are not loaded, they should be loaded.
              if(visiblePageAfter) UI.renderPage(visiblePageNum + 1, RENDER_OPTIONS);
              if(visiblePageBefore) UI.renderPage(visiblePageNum - 1, RENDER_OPTIONS);
          }
          if(visiblePageNum !== oldPageNumber && $('.comment-list-form')[0].style.display === 'none' && document.querySelector('.comment-list-container p') === null){
                UI.renderQuestions(documentId,visiblePageNum);
          }
          document.getElementById('currentPage').value = visiblePageNum;
          oldPageNumber = visiblePageNum;
        }

        // Add EventListener to GUI-Elements.
        // Add scroll event to dynamically load the single pages of the pdf.
        var scrollTimer = null;
        document.getElementById('content-wrapper').addEventListener('scroll', function (e) {
        if(scrollTimer) {
                clearTimeout(scrollTimer);
            }
            scrollTimer = setTimeout(handleScroll, 500, e);
        });

        // Add click event to cancel-Button of commentswrapper to close the comments view and load the questions of this page.
        document.getElementById('commentCancel').addEventListener('click',function (e){
            var visiblePageNum = document.getElementById('currentPage').value;
            document.querySelector('.comment-list-form').setAttribute('style','display:none');
            document.getElementById('commentSubmit').value = M.util.get_string('answerButton','pdfannotator');
            document.getElementById('id_pdfannotator_content').value = "";
            var editorComment = document.querySelectorAll('#id_pdfannotator_contenteditable')[0].childNodes;
            if(editorComment) {
                editorComment.forEach(comment => {
                    comment.remove();
                });
            }
            document.querySelector('.comment-list-container').innerHTML = '';
            // Disable and then enable to delete overlay and directly add the function to create an overlay.
            UI.disableEdit();
            UI.enableEdit();
            UI.renderQuestions(documentId,visiblePageNum);
        });

        // 'Overview' tab receives a dropdown navigation menu.
            addDropdownNavigation(null, _capabilities, _cm.id);

        // Initialise the print option for printing the document or its discussions.
           (function (){

                if (_toolbarSettings.useprint || _capabilities.useprint) {
                    $('#pdfannotator_print_button').click(function () {
                        openDocumentCallback();
                        setTimeout(function(){
                            // Activate cursor icon ('hand') again.
                            document.getElementById('pdfannotator_cursor').click(); 
                        }, 2000); // Wait 2 seconds to prevent race condition.
                    });
                    
                    function openDocumentCallback() {
                        var url = document.getElementById('myprinturl').innerHTML;
                        location.href = url;
                    }
                }

                if (_toolbarSettings.useprintcomments || _capabilities.useprintcomments) {
                    $('#pdfannotator_printannotations_button').click(function () {
                        openCommentsCallback();
                        setTimeout(function(){
                            // See above.
                            document.getElementById('pdfannotator_cursor').click(); 
                        }, 2000); // See above.
                    }); // end of click event handler
                    
                    function openCommentsCallback() {
                        _2.default.getStoreAdapter().getCommentsToPrint(RENDER_OPTIONS.documentId)
                            .then(function(data){
                                if(data.status === "success") {

                                    // Get annotation type images.
                                    var mypin = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAgCAYAAAD5VeO1AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAISSURBVEhL7ZTvS1NRGMf9i/pHeln/QC/LfshqZG+E0LJSUkhsYW5zuiYYrGzDbOgYmxKz3Svmr93ubZutlKYoZIzS+Oa5PIwdz7n3HoWIoA98X+x5zvOBu/OjBX+Q/3Ipf1/+rX4AvbRjZ3f/B1W9cZXnrW34IgVcDmm4M2nauTaio/2Zbve8cJSH0hY6JlYQfftVmu5JA2PZMq2WI5XPGzXcjC1Lpc3pemlg5v0mTYlI5ddHNalMlrYRDQeHv2iSR5BXat/RGV8XJLfjhp3j9QcJE3mzRtM8gnx8roShzGdOcDGoo+V8yM6FwALXC+e2EEgZNM0jyINpE4F0lROce5hryM/ez3A9loHX6zTNI8iza1u4l7C44cGZTzjjT9jpf1Pher1TJcRyH2maR5CzzWkd5j/dLb6IjvrPQ5rmEeSMp7Mf0D9dlsqaMzhbRV9ylaZEpHKGf0zDcPaLVMrCNrIt/I5Wy3GUszfkksvfczVcwOZenVbLcZQzxufL6E2agnggVTk6IUVa5YyrnMEeruPy1uACdd3xlM8Va+h8UWyIe5IWXhWq1HXHU864Mao35L6IRlVvlOSRjGVfJHZ6Hk3Jb6MMJTk7Of7oEjqerx09bPtU9UZJzvBHF3ElVKBfaijLb8UW7ZwEZfnd+DLao+qbyVCWP54uoi/h/I7IUJbH8xt4kvK+lc0oy0/DvyoHfgN0wePwx9o1ZAAAAABJRU5ErkJggg==';
                                    var myhighlight = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABsAAAAdCAYAAABbjRdIAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsIAAA7CARUoSoAAAAS1SURBVEhLfVbfcxRFEO7dvducSMVLIGVUKgEjSCGlhVW+UMWL5Yt/nVW++OijpahPgFaBhJRFISCiBAwJEsLJRSNJiEHOY7M//L7umdvNnUlf9fVs78z3TffMTk/w8XftIo5CqUNrtUDqIWwUSC0UiWAjPMNIGARQEYGlKaBSFJKjkcNmsFmeS4ZGmouksFt4TlPYLJcEGnxy6Y9iqB6BDERKCiIlM9IIDFQSBY4IhjxKWDjCDH9UkqQkIZlqrvb5ViZhrCRGFDMyhBRDh9AeUmt+2gZso24+Wj5X35sGOt5wygDIE9ZcCo3ULNPKAexMbRCEJMgArRJWnyt9dVwfHi15QMYHpE1n4TpDywgHdTe/H6ftCi55wppbE24MC9k6+QFxLZLYReCj8up9fK/9KiQ9LG4w4JNHI+tXnR2spZPr4dpQbhxNC6z32Tr5Pjb+/3BDz8qc0mGzcjNTEANmP+5CKsW3bTwnZf19ZqhGwvFuR3uyHqmqJ+Y3ZiS7iZLiTyMmAcf34RoZOoX64GcJdTOrEuVZJjPffCmt+3fN0SclYSDt3271CIir+MrjnXhQdk0ZicNtEd35+QfZ+9KITEwddZ5BYX8OaS/OOZwqrik6oeFmhj76ssIjf7Yfyl/tlrzz3inn2VlWHj2QeKhhOA7X4/PIc0eQpUzPQH3WsdLtPJPZ69/LiZPvS60em3MXWVq4LZNvHFMcYlSPOI3BnFXFn5ObVy/JoSPHZXT/y86zs2ysP5bOs6dy4ODhQUz8GERFShLK4vysbKw9llcnp5xnd7l/95auaRhFzrMdbzuZO8p5mlOao2OSbiUyffYLmZ/9EeViy70ZlO6/HV1XplDH8494FQlZIpQDijKktYk+yujYuK4Tt/3C7Z9k+szn0lqc13f98gBrNX7goDRe2KPjiUM8QpkWErIW+Zd0WjG0DpTh5qg1IJz9L1dnZObbr2R1Zdl57Rvk9zd19O0BDD95PoNse/HLvWVPCL+Xftl8siZXLp6RG5cvSOefTY1273BThkf26bgqjmEZaYgi2nP4l6lXVNnNjXVHMSjLrUWZPnda15NREcOPrRJpGzaszqAksZKeJIlukp2EUTdH9sv4a5My9sqETo7j7Gpg2iOFBl/fXC8arFcsK9BqWQ/yVCLJ5OmTVZnDccWUUbhpXn/zuH5/9bihKSJgopMEIW48CfQ5Ljtmc+niDhKcvrGGCw9JXPHDQapksK1fr8nKwwWZAOihI2/J8tI9Wbp3R949+YF+5MiMEvlsWEYsOpKQ3MgyXHhwu/rs2qqSWVQksbqUdv6W+StnpcBNyUdz4tSHujsDt2m4hzRNtCAhae9G5a5wjEoJQRYyz35WZRoK+X3ueo+I0nhxWOI9TfQRHehVI0CKEpcyqicinscmT/Dp5ZXC3x+0wnqL2bOu+TqnlQCHnMbEUwjR2EdbbgLgKTAvpxohiWFtErg3cga2i8oOGjbzrG1cMKFdjSSTLnxs0+oz/RoV+9k4i87hVfBDC7kk6Vcl1DQZaDWFfrfxvV+bfu2Rom/w0flHSKNLHXJm1wKzetdHyrTKMoUsFawXTvwxxB2p36tuFKwP2roXYH3GkjSX/wAPXk0w5HWeHQAAAABJRU5ErkJggg==';
                                    var mystrikeout = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABsAAAAdCAIAAADU74AfAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAASsSURBVEhLjZb5T1pLGIbn//+lSZOm16JtjFetRa3AZRMFqqjIJjsoLoiHfTmAoIDY9hmGEmpvTM2EjN+873O+2b5zxNv94j/e8tJhdfmktnba+BJqbkeapljrv0Tbnmw7Ux1XuuPKdPcyXX7pEyHOKBqU6HHhhQAHmnjvLX88qq3465+Dza1IyxTTrYmOM93dy957Lnrfcr2Dy/7hZd93JX/pEyHOKBqU6HHhhQAHmlg6rCjc9lnLHG/bUx3S8V708B9d9/03D4H8Q/D2UTX6RIgzigYlelx4FRSaWD6uSVykZYm3HanOfvb+INc7vu6f5h/ChcczbRArDuOlYaIkf+kTIc4oGpToceGFAAeaWD2tk7Y5rjPgPr/n4Sc3D6HCY1QbJEvDdGWUrT6d16aNPhHijKJBiR4XXghwoImNYGMnqtuTMjuGmVT4bkA66crTRX181RzftJ5v9WmjT4Q4o2hQoseFFwIcaGIr0mTj2EeWnGciYoLkctkYg7hrfy92ftBKXflLI0KcUTQo0ePCCwEONLETbZEz28d6MxGejJREChMWoD8bcUbRoESPCy8EONCEJa6zZRwLFpvVYTo8/xWcagqKEj0uvBDgQBO2RJulVQmy5KwRk3odpxoalOhxqTThQBOkyiqwwBwLHshctN9x/mh2w7htWFxcXd+wOt2hxKXJ6lRDKNHjwgsBDjSxm+6QMEc3Xhxma0/53xP0HocNBoP/LFNojfL1R18wvrT0kYgaRYkeF14IcKAJV6bD9nMf2DimUNCfZ7jrSg+zxbE3i9CSV2Wg0NW/6HHhhQAHmmA5ubNyEcvDXGPM4ZiZI+mbhYUF47ZpFlHN6T7M3jZUHz0uvBDgQJsnjl4QSQcif6BnQRq4C01X/V/E0f8RS8Mcs54jau1ndkNBPUdB/p0NzRp6XGq7p0RKnlpHzio3lwMxbyCX5ZV/FRR6PFecH6Whx4V3uo7sDBWUEsIRpa5kqk/c3BeH8brat7u8bJHiOj0+TR+rIZToceGVhzzXgyYoyFQ6SlPkbpAqj7gG80v5ekOJHhdeCHCgCa43lYMlkBOfHEmVy9//4cILAQ40YY7pu+lp4eFR7BrnK//H3F80ebZbkys4SVCVHzjQBAXd9qs4cu659qnKFLqyun5V6b9guX1B8vpqsaNBiR4XXghwoAljuEn55TUkC9pk7hJaHrGDG5tfLY59FkujdThM3+mbbS6ILl8YDUr0uPBCgANNrAcagFXRZWkpIYFb+XrhYpkcHsw7Vle21KMi5OpD90n0g8Gw9mUrrkkNSvS4VLmFA02snNR451LQrUn5XmaYNeYohAsD27eQ4yC0709umu2bJvsnTua60eYNRAp9RtGgVDi8EOBAE5+OqqunDWO4paC7ma7nXL5a1euQm0DdP6Np8pc+EeKMokGJXuEgwIEmPhyUeSWuBaZQkqfG8YLn4VQnssDMVqpGnwhxRtGgRK9wEOBAE+/cpUU+UY5rPEF+ovAdENPZNflZkelSlll1/KrRJ0KcUTQo0ePCCwEONPHGpb3zlGCTMKvA0honXFOUbwK+WOTXD7moRp8IcUbRoESPCy8EOG9c2k8N39Uo7RdC/gAAAABJRU5ErkJggg==';
                                    var myarea = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAdCAYAAAC5UQwxAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAB5SURBVEhLY/hPZzBwFr76+P3/1cfvaYJBZsMA3MKw3v3/GULm0wSDzIYBFAsd67djdSElGGQmTguRJagF0M0dtZBiMGohlEc9MGohlEc9MGohlEc9MGohlEc9MGohlEc9gNdCurdpYK0samOsFtK9XUovQGcL//8HABAZtWaLsIHAAAAAAElFTkSuQmCC';

                                    // Create pdf.
                                    var doc = new jsPDF({filters: ['ASCIIHexEncode']});

                                    // Set a font compatible with Greek. It is a base64-encoded string of a .ttf file
                                    var NotoSans = "AAEAAAARAQAABAAQR0RFRkByP9MABSJEAAAHnEdQT1PjWEomAAUp4AAArYBHU1VCVhK4IgAF12AAAH0CT1MvMnfRmRwAAAGYAAAAYGNtYXDq6rMGAAAyeAAABwRjdnQgGa8axQAAQ0wAAAD+ZnBnbTYLFgwAADl8AAAHtGdhc3AAFgAjAAUiNAAAABBnbHlmFHM8QQAAdNAABKkAaGVhZAM2yywAAAEcAAAANmhoZWEOUgw4AAABVAAAACRobXR4S/Q09QAAAfgAADB+bG9jYRwsnwgAAERMAAAwhG1heHAOuAU7AAABeAAAACBuYW1lZ1qRGAAFHdAAAARCcG9zdP9pAGYABSIUAAAAIHByZXBmtKnnAABBMAAAAhoAAQAAAAEKPQBYtbFfDzz1AAsIAAAAAADPKrtZAAAAAM8qu1v7MPzaCpIIYgAAAAkAAgABAAAAAAABAAAIjf2oAAAKtvsw97oKkgABAAAAAAAAAAAAAAAAAAAMHwABAAAMIAFSAFQAhAALAAIAEAAXAFwAAAHJA0sAAwABAAMEkQGQAAUACAWaBTMAAAEfBZoFMwAAA9EAZgIACAICCwUCBAUEAgIE4ACC/0AAeP8AAAAhAAAAAE1PTk8AQAAA//0Ijf2oAAAIjQJYIAABn9/XAAAESgW2AAAAIAAEBM0AwQAAAAAEFAAAAhQAAAInAJMDRACFBSsAMwSTAH8GpgBkBdsAbQHNAIUCZgBSAmYAPQRoAFQEkwBmAiUAVAKTAFICJQCTAvoAFASTAGQEkwC2BJMAYgSTAFwEkwArBJMAgQSTAHEEkwBaBJMAZASTAGYCJQCTAiUAPwSTAGYEkwBzBJMAZgN5ABkHMQB3BR0AAAUzAMcFDgB9BdcAxwRzAMcEJwDHBdMAfQXuAMcCtgBSAi//YAT0AMcEMQDHB0IAxwYUAMcGPwB9BNcAxwY/AH0E+gDHBGQAaARzABQF2QC4BM0AAAdxABkEsAAIBIcAAASTAE4CogCkAvoAFAKiADMEkwBOA43//ASkAYMEfQBeBOwArgPXAHEE7ABxBIMAcQLBAB8E7ABxBPIArgIQAKACEP+PBEYArgIQAK4HewCuBPIArgTXAHEE7ACuBOwAcQNOAK4D1QBoAuMAIQTyAKIEEAAABkoAFwQ7ACUEFAACA8MAUAMKADkEaAHpAwoAQgSTAGYCFAAAAicAkwSTALoEkwBCBJMAeQSTAB0EaAHpBBsAeQSkATEGqABkAtsAQgQSAFIEkwBmApMAUgaoAGQEAP/6A20AewSTAGYCzQAxAs0AIwSkAYME/ACuBT0AcQIlAJMBzQAdAs0ATAMCAEIEEgBQBlIARwZSAC4GUgAgA3kAMwUdAAAFHQAABR0AAAUdAAAFHQAABR0AAAcM//4FDgB9BHMAxwRzAMcEcwDHBHMAxwK2ADwCtgBSArb//QK2ADwF1wA9BhQAxwY/AH0GPwB9Bj8AfQY/AH0GPwB9BJMAgwY/AH0F2QC4BdkAuAXZALgF2QC4BIcAAATXAMcFDACuBH0AXgR9AF4EfQBeBH0AXgR9AF4EfQBeBukAXgPXAHEEgwBxBIMAcQSDAHEEgwBxAhD/1AIQAKcCEP+vAhD/6gTXAHEE8gCuBNcAcQTXAHEE1wBxBNcAcQTXAHEEkwBmBNcAcQTyAKIE8gCiBPIAogTyAKIEFAACBOwArgQUAAIFHQAABH0AXgUdAAAEfQBeBR0AAAR9AF4FDgB9A9cAcQUOAH0D1wBxBQ4AfQPXAHEFDgB9A9cAcQXXAMcE7ABxBdcAPQTwAHEEcwDHBIMAcQRzAMcEgwBxBHMAxwSDAHEEcwDHBIMAcQRzAMcEgwBxBdMAfQTsAHEF0wB9BOwAcQXTAH0E7ABxBdMAfQTsAHEF7gDHBPIArgXuAAAE8gASArb/5AIQ/5ICtgAsAhD/3AK2AB4CEP/KArYAUgIQADMCtgBSAhAArgTlAFIEHQCgAi//YAIQ/48E9ADHBEYArgRGAK4EMQDHAhAApQQxAMcCEABcBDEAxwIQAK4EMQDHApoArgQxABsCEP/uBhQAxwTyAK4GFADHBPIArgYUAMcE8gCuBYcAAwYUAMcE8gCuBj8AfQTXAHEGPwB9BNcAcQY/AH0E1wBxB20AfQeRAG8E+gDHA04ArgT6AMcDTgBiBPoAxwNOAH4EZABoA9UAaARkAGgD1QBoBGQAaAPVAGgEZABoA9UAaARzABQC4wAhBHMAFALjACEEcwAUAuMAIQXZALgE8gCiBdkAuATyAKIF2QC4BPIAogXZALgE8gCiBdkAuATyAKIF2QC4BPIAogdxABkGSgAXBIcAAAQUAAIEhwAABJMATgPDAFAEkwBOA8MAUASTAE4DwwBQApwArgSTAL4FHwAABH0AXgcM//4G6QBeBj8AfQTXAHEEZABoA9UAaAS+AQQEvgEEBLYBKwS+AR8CEACgBJ4BbQGWACMEvgECBKAA3wSeAfgEngEQBR0AAAIlAJME8P/QBoH/0AOk/94Gg//iBZP/zgaD/+ICxf/mBR0AAAUzAMcEMQDHBKYAKQRzAMcEkwBOBe4AxwY/AHsCtgBSBPQAxwTdAAAHQgDHBhQAxwRxAEgGPwB9BdkAxwTXAMcEjwBKBHMAFASHAAAGbwBoBLAACAZvAG0GQgBOArYAPASHAAAE7ABxA90AWATyAK4CxQCoBOcAogTsAHEFDACuBCUACgTVAG8D3QBYA98AcQTyAK4EwwBxAsUAqARGAK4EVv/0BPwArgRgAAAD0QBvBNcAcQVGABkE1wCiA98AcQTwAHED1QAUBOcAogXNAHEEaP/pBhcAogZCAHMCxf/qBOcAogTXAHEE5wCiBkIAcwRzAMcF7gAUBDEAxwUlAH0EZABoArYAUgK2ADwCL/9gB30AAgeqAMcF7gAUBPIAxwT+ABcF2QDHBR0AAATsAMcFMwDHBDEAxwWHAAwEcwDHBtUAAgS0AE4GJQDJBiUAyQTyAMcFqgACB0IAxwXuAMcGPwB9BdkAxwTXAMcFDgB9BHMAFAT+ABcGbwBoBLAACAXuAMcFlgCkCEwAxwhUAMcFiQAQBt0AxwUjAMcFEAA9CGoAxwUdAC0EfQBeBMsAdQScAK4DdwCuBKYAJwSDAHEGAAACA+wARAUlAK4FJQCuBDsArgSgAA4F+ACuBRkArgTXAHEE/gCuBOwArgPXAHEDzwApBBQAAgXNAG8EOwAlBQ4ArgTnAJgHLwCuBz8ArgWRACUGOQCuBMEArgP0ADsGsACuBHkAIQSDAHEE8gASA3cArgP2AHED1QBoAhAAoAIQ/+wCEP+PBrwADgcXAK4E8gASBDsArgQUAAIFAgCuBEIAxwN9AK4HcQAZBkoAFwdxABkGSgAXB3EAGQZKABcEhwAABBQAAgQAAFIIAABSCAAAUgNK//wBZgAZAWYAGQIAAD8BZgAZAt8AGQLfABkDVAAbBBkAhQQZAHsDAgCeBlQAkwlqAGQBzQCFA0QAhQJ7AFICewBQA/4AkwEK/nkDLQBtBJMAXASTAEQGNQCcBJMALwaRAIUEKQBvCCkAwwYvACMGQgBOBPQAZgZSAEUGUgAjBlIARwZSAGYEpgBiBKYAKQXpAMUFDABIBJMAZgRkACUFpAB3AxkACgSTAGAEkwBmBJMAZgSTAGYEqgBqBNEAHwTRAB8EngDPAhD/jwQAAYUEAAFvBAABewLNABQCzQA9As0AOQLNADMEAAAACAAAAAQAAAAIAAAAAqoAAAIAAAABVgAABHkAAAIlAAABmgAAAM0AAAAAAAAAAAAACAAAVAgAAFQCEP+PAWYAGQUQAAwEkwAABtkAFwdCAMcHewCuBR0AAAR9AF4HhwABAqoAcwMUAJMHkQAfB5EAHwZGAH0E7gBxBj0AuAVkAKIAAPxNAAD9BwAA/BMAAP0EAAD9MQRzAMcGJQDJBIMAcQUlAK4IJwCDBpwAAAVmABIFFAASB2AAxwXwAK4FdwAABJMACAdvAMcGPQCuBdUAFwUfAAwH3wDHBssArgSyAD0D7AAZBm8AbQYXAKIGQgB9BNcAcQUUAAAEIQAABRQAAAQhAAAJwwB9CI0AcQaRAH0FRgBxCBAAewaHAG8IJwCDBpwAAAUlAHsD8ABxBN8AaAR1AMkEngD4BJ4B3QSeAd8H6QApB6YAKQZIAMcFRgCuBOcALwTBABIE5wDHBOwArgQ9AC8DeQAQBTUAxwREAK4HOwACBl4AAgS0AE4D7ABEBVwAxwR1AK4E9ADHBFIArgT0AC8ERgASBYsADgT8ACUGCgDHBUIArgaFAMcF5wCuCJYAxwbwAK4GOwB9BSMAcQUOAH0D1wBxBHMAEgPNACkEhwAABBAAAASHAAAEEAAABQwACARqACUG5QASBcsAKQWgAKQE+ACYBZYApATZAJgFlgDHBL4ArgbLADcFUgAtBssANwVSAC0CtgBSBtUAAgYAAAIFkQDHBHsArgXBAAIEsgAOBdkAxwT4AK4GDADHBUwArgWWAKQE5wCYB1YAxwYKAK4CtgBSBR0AAAR9AF4FHQAABH0AXgcM//4G6QBeBHMAxwSDAHEF6QB5BIMAaAXpAHkEgwBoBtUAAgYAAAIEtABOA+wARASsAEgD/AAdBiUAyQUlAK4GJQDJBSUArgY/AH0E1wBxBkIAfQTXAHEGQgB9BNcAcQUQAD0D9AA7BP4AFwQUAAIE/gAXBBQAAgT+ABcEFAACBZYApATnAJgEPQDHA3cArgbdAMcGOQCuBD0ALwN5ABAFDgAIBGYAJQSwAAYEOwAlBOwAfwTsAHEHNwB/BzEAbwc9AEgGeQBOBRAASARMAE4H4wAABt8ADggfAMcHVgCuBhQAfQUjAHEFuAASBT8AKQS2AG0D3QBYBbIAAgSwAA4FHQAABH0AXgUdAAAEfQBeBR0AAAR9AF4FHQAABH0AJQUdAAAEfQBeBR0AAAR9AF4FHQAABH0AXgUdAAAEfQBeBR0AAAR9AF4FHQAABH0AXgUdAAAEfQBeBR0AAAR9AF4EcwDHBIMAcQRzAMcEgwBxBHMAxwSDAHEEcwDHBIMAcQRzAE4EgwA/BHMAxwSDAHEEcwDHBIMAcQRzAMcEgwBxArYAUgIQAHsCtgBSAhAAmwY/AH0E1wBxBj8AfQTXAHEGPwB9BNcAcQY/AH0E1wBUBj8AfQTXAHEGPwB9BNcAcQY/AH0E1wBxBkYAfQTuAHEGRgB9BO4AcQZGAH0E7gBxBkYAfQTuAHEGRgB9BO4AcQXZALgE8gCiBdkAuATyAKIGPQC4BWQAogY9ALgFZACiBj0AuAVkAKIGPQC4BWQAogY9ALgFZACiBIcAAAQUAAIEhwAABBQAAgSHAAAEFAACBPAAcQAA+9sAAPxqAAD7jQAA/GoAAPxmAAD8cQAA/HEAAPxxAAD8ZgGkAC0BtgAZBHMAFALjACEE7AASBdcAFATsAMcE7ACuBRQAuATlAKgFDgA/BQ4AfQQGAHEF1wA9BnsAFATsAGgE7ABxBNUAbwRzAHsF6QB5BLQAbwQn/+kF0wB9BI8AAAd5AK4CyQC4ArYARgT0AMcERgCuAhAAHwRW//QIPQC4BhT/6QTyAK4GQgB9CJoAfQbDAHEFewAUBOwArgT6AMcEZABgA9UAXASPAEoCef+NAuMAIQScABQC4wAhBHMAFAZCAEwEzQAABIcAAARWAAIEkwBOA8MAUASsAEgErABxA/wARgP8ADkEjwBiBKwASAPsAEQDtABKBLIArgQhAcEEIQC6BCEAhQInAJMKVgDHCZoAxwiuAHEGYADHBkIAxwQhAK4IRADHCCUAxwcCAK4FHQAABH0AXgK2AAMCEP+vBj8AfQTXAHEF2QC4BPIAogXZALgE8gCiBdkAuATyAKIF2QC4BPIAogXZALgE8gCiBIMAaAUdAAAEfQBeBR0AAAR9AF4HDP/+BukAXgXTAH0E7ABxBdMAfQTsAHEE9ADHBEYArgY/AH0E1wBxBj8AfQTXAHEErABIA/wAHQpWAMcJmgDHCK4AcQXTAH0E7ABxB4EAxwVKAMcGFADHBPIArgUdAAAEfQBeBR0AAAR9AF4EcwCgBIMAcQRzAMcEgwBxArb/hQIQ/zECtgAdAhD/yQY/AH0E1wBxBj8AfQTXAHEE+gCmA04AIwT6AMcDTgCuBdkAuATyAKIF2QC4BPIAogS0AE4D/AAUBe4AxwTyAK4F7gDHBOwAcQVeAHcE1wBxBJMATgPDAFAFHQAABH0AXgRzAMcEgwBxBj8AfQTXAHEGPwB9BNcAcQY/AH0E1wBxBj8AfQTXAHEEhwAABBQAAgMMAA4F9gCuAxsAHQfHAHEHxwBxBR0AAAUOAH0D1wBxBDEAFARzABQD1QBoA8MAUAOWAAQDfQAZBTMAHwXZABQE3QAABHMAxwSDAHECL/9gAhD/jwYjAH0E7ABxBPoAFANOABQEhwAABBQAAgR9AKYE7ABxBOwArwTsAK4D1wBEBD8AYgTsAHEE7ABxBIMAaASDAGgGGwBoA90AWAPsAEQFMwBEBM0AcQIQ/48E7ABvBOwAcQSYAHEEEAAABBD/+gTyAKYE8gCuBPIArgIQABQCxQCoAo8ASgMGAAoCzf/sAg4ArgVCAK4HewCmB3sApgd7AK4E8v/FBPIArgUOAK4E1wBxBukAcQZCAHMFzQBvA04AHwNOAB8DTgAfA04ArgNOAK4CxQCoAsUAJQR5AK4EeQCuA9UAaAIQ/8UCEP/FAhD/4wIQ/x8C4wAtAuMAIQTyABQE1wA9BOcApgQQAAAGSgAXBBQAAAPLAAADwwBQBFYAUAP8AB0D/P/XA2gAGQNoADUDaAAZA8EAcQY/AH0EnACuBM0AXASYAHEFGQCuAhD/PQRGABIDewCuBOwAcQNoABkDaAA1B64AcQfZAHEIQgBxBhIAIQQSACEG5QAhBuUAHwVMAK4E0wCuBBIAAAS4AK4FBP/XBQT/1wQZAJ4EGQCeAfD/xwLdAJ4C3QAxAt0AMQO8AJ4FGwAnA3MAFAFmABkC3wAZAWYAGQFmABkAAP+TAAD/kwJQABACUAAhBJMAZgSTAGYEkwBQBJMAUAAA/64AAP+vAAD+twAA/64AAP7SAAD/MwAA/zMAAP9KAAD/SgAA/5MAAP+TAAD/KQAA/ykAAP8pAAD/KQAA/skAAP8vA28AFAHwAJ4DQgBqA48AKwLyAEQDWACgA1gAoANYAKADWACgA1gAoANYAKADWACgAAD+pwAA/lkC3wAZAAD+qgAA/qoAAP8AAAD/AAAA/zsAAP6TAAD+kwAA/poAAP+CAAD/VgAA/1YAAP9WAAD/VgAA/jcAAP43AAD+LwAA/qcAAP7SAAD+VgAA/sIAAP+XAAD+4AAA/QQAAP8gAAD+kAAA/qcAAP+uAAD/CgAA/sEAAP7BAAD/ZAAA/2YAAP9kAAD/ZgAA/zMAAP8zAAD/TAAA/0wAAP6TAAD/LQAA/5MAAP8pAAD/KQAA/ykAAP7SAAD+lgAAAAAAAP7gAAD/IAAA/34AAP85AAD/WQAA/64AAP6TAAD+fQAA/qcAAP6nAAD+wgAA/sEAAP6IAAD+0gAA/jUAAP5ZAAD+rAAA/pMAAP0fAAD+1wAA/moAAP+TAAD+kwAA/zUAAP59AAD/LwAA/30AAP5XAAD+twAA/68AAP6IAAD/fgAA/sEAAP+xAAD+QgAA/lcAAP8KAAD/QgAA/ocAAP6HAAD+qAAA/poAAP9GAAD9JQAA/1QAAP8hAAD+wQAA/z0AAP9UAAD/VAAA/ocAAAAAAAABBgAA/ycAAP53AAD/PQAA/1QAAP9UAAD/QgAA/0IAAP9UAAD/VAAA/1QAAP7dAAD+zwAA/64AAP62AAD+0QAA/vgAAP7FAAD+0wAA/goAAP8pAAD+0QAA/qgAAP6uAcUAKQHFACkBxQCeA9cARAPXAHED1wBEAiUAPwS4AGYFpP/OBJMAAAXNAG8FM//2BhsAfQTXAHEE5wB9BHEAcQRGAMcDrACwA/b/9gRzAGIEvv/sBCv/Zgg9ALgHewCmBWQAcwTuAHEFNQDHBF4ArgRkAGAEMwAxBKYAKQRUACEGGwB9BNcAcQRzACkD/gAfBTP/9gTXAHED1wBxAhD/jwZCAH0D9gBxA/QAOwTXAMcE7ACuBQ4AfQdCAMcFzwCuBNcACAUOAD8FDgB9BQ4APwAA/ocGPwB9BOwAcQdxABkGSgAXBDcAFAWsABAG6QBoBEoAFAQtAHkExQCwBMUASgO4ALAD6QBWAjMAsAIG/6QEGQCwA4cAKwXTALAE8gCwBRIAeQQtAEgE2QAzBNkAMwTZAAYHkQBqBGgAdQUSAHkFEgB5BAQAsAQ3ADEENwAxA7gAKwTFAKYE8gBOBkwATgTyAE4D/AAUBfgAKQPRAFYD7ABEA64AZAQKACEDhwCwA/wAFATFALAEBACwBTUAbQSiAB0D5QASBT8ADgP4AKQD+AASBGgApANxAKQDcQBvBGQAcQR5AKQCOwBUAd//qgPLAKQDRACkBWIApASTAKQEngCkBLAAcQQUAG0DtgCkA88ApANxACcEaACaBYUAJQN5AFoDeQCLA8UAaAUnAGIDxQCRA8UAaAN9AGgDfQBiAwgAWAMUAEgDxQBmAcsAiQNSAJEFiwCRA8sAkQO2AGgDBABGA7YAaAO2AGgDxQCRAlwAMQPLAIsDywBYBYsAiwMtABIC2QAOA9sAkQM7ABkDtABmBGIAaANoAAIBywCJAqYAkQPLAIsDLQASA9sAkQM7ABkDtgCLBGIAaANoAAIHcQCmBOz/vATsAHECwf/yB3v/4QTy/+EE7P+2A07/uALF/7gD1f/0AuP/1QPDAEYE7ACwBCUApASwAFYH1QAhAhAAFALFAAoE7AAUBMUAFATXABQE7ACuBOwAcQLBAB8GPQBvBEYArgIQAFIHewCuBPIArgTsAK4DTgBSA9UAaANk/8UEEAAABDsAJQPDAFAEfQBeBOwAcQTsAHEEgwBxA/IAWAPsAEQFMQBoAhAAoAPXAEQCEP/FBPIAogPsAEQDxQCRAwQAaANOAFoDtgBoAxQASAJEAC8By//LA8UAZgPLAI0BywAnAkgAjQIhAE4CIQBOAcv/kQHJAJEByf/BAsUAkQWLAJEFiwCLA8v/8gPLAJED3QCRA7YAaARiAGYDAgBiAcv/8gJcADEDywAnA7YARAPHAIsDwwCLAy0AEgL4AFIC+ABSA14AUgMfAC0DqABoAAD+ogAA/n0AAP+FAAD+hwAA/tEAAP7JAAD+0QAA/skAAP5CAAD+QgAA/1oAAP9UAAD+hwUzAMcE7ACuBTMAxwTsAK4FMwDHBOwArgUOAH0D1wBxBdcAxwTsAHEF1wDHBOwAcQXXAMcE7ABxBdcAxwTsAHEF1wDHBOwAcQRzAMcEgwBxBHMAxwSDAHEEcwDHBIMAcQRzAMcEgwBxBHMAxwSDAHEEJwDHAsEAHwXTAH0E7ABxBe4AxwTyAK4F7gDHBPIArgXuAMcE8gCuBe4AWgTyAD4F7gDHBPIArgK2/+QCEP+QArYAKQIQ//YE9ADHBEYArgT0AMcERgCuBPQAxwRGAK4EMQDHAhAAngQx//UCEP/aBDEAxwIQ/9gEMQDHAhD/rwdCAMcHewCuB0IAxwd7AK4GFADHBPIArgYUAMcE8gCuBhQAxwTyAK4GFADHBPIArgY/AH0E1wBxBj8AfQTXAHEGPwB9BNcAcQY/AH0E1wBxBNcAxwTsAK4E1wDHBOwArgT6AMcDTgCuBPoAxwNOAJ4E+gDHA04AngT6AMcDTv/cBGQAaAPVAGgEZABoA9UAaARkAGgD1QBoBGQAaAPVAGgEZABoA9UAaARzABQC4wAhBHMAFALjACEEcwAUAuMAIQRzABQC4wAhBdkAuATyAKIF2QC4BPIAogXZALgE8gCiBdkAuATyAKIF2QC4BPIAogTNAAAEEAAABM0AAAQQAAAHcQAZBkoAFwdxABkGSgAXBLAACAQ7ACUEsAAIBDsAJQSHAAAEFAACBJMATgPDAFAEkwBOA8MAUASTAE4DwwBQBPIArgLjACEGSgAXBBQAAgR9AF4CnACuBawAuATsAHEE7ABxBOwAcQTsAHEE7ABxBOwAcQTsAHEE7ABxBR0AAAUdAAAF7AABBgAAAQXDAAEFwwABBcv/zgXL/84D3QBYA90AWAPdAFgD3QBYA90AWAPdAFgFJQABBRkAAQZWAAEGTAABBiMAAQYjAAEE8gCuBPIArgTyAK4E8gCuBPIArgTyAK4E8gCuBPIArgagAAEGkwABB9EAAQfHAAEHsgABB7IAAQfZ/84Hxf/OAsUAnwLFAJUCxf/+AsX/+wLFADgCxQAPAsX/rwLF/5MDpgABA5oAAQS4AAEErgABBOwAAQTsAAEE9P/OBPT/zgTXAHEE1wBxBNcAcQTXAHEE1wBxBNcAcQa0AAEGxwABB/oAAQfwAAEHsgABB7IAAQTnAKIE5wCiBOcAogTnAKIE5wCiBOcAogTnAKIE5wCiBbwAAQbHAAEG2wABBxf/zgZCAHMGQgBzBkIAcwZCAHMGQgBzBkIAcwZCAHMGQgBzBrYAAQbTAAEH8gABB/IAAQe+AAEHyQABB7L/zgey/84E7ABxBOwAcQPdAFgD3QBYBPIArgTyAK4CxQBEAsUAoATXAHEE1wBxBOcAogTnAKIGQgBzBkIAcwTsAHEE7ABxBOwAcQTsAHEE7ABxBOwAcQTsAHEE7ABxB+EAAAfhAAAIsAABCMUAAQiHAAEIhwABCI//zgiP/84E8gCuBPIArgTyAK4E8gCuBPIArgTyAK4E8gCuBPIArglkAAEJWAABCpYAAQqLAAEKdwABCncAAQqe/84Kif/OBkIAcwZCAHMGQgBzBkIAcwZCAHMGQgBzBkIAcwZCAHMJewABCZgAAQq2AAEKtgABCoMAAQqNAAEKd//OCnf/zgTsAHEE7ABxBOwAcQTsAHEE7ABxBOwAcQTsAHEFHQAABR0AAAUdAAAFHQAAB+EAAAIQAJEEngHnAhAAkQS+AOgEpADyBPIArgTyAK4E8gCuBPIArgTyAK4FIf/NBRL/zQac/80Gjf/NCLIAxwSeAT8EngFoBL4A8gLF/9QCxf/gAsX/wALF/8YCxf+bAsX/pQK2AB4CtgAsA4P/zQOo/80EngErBJ4BaAS+APIE5wCiBOcAogTnAKIE5wCiBNcAogTXAKIE5wCiBOcAogSHAAAEhwAABbD/zQWs/80FhwABBJ4A/ASeAPwEngGTBkIAcwZCAHMGQgBzBkIAcwZCAHMGz//NBoP/zQbR/80Ghf/NCQYATgSeAekCEACeAAD/1QAA/yEAAP/XAAD+TASTAFIEaAEJAt8AGQAA/9cAAP5OAAD/EgAA/xIAAP8SAZoAAAS6AIUEAP/6AiUAkwAA/xIAAP8SAAD/EAAA/xAAAP8QAAD/EgLNACcCzQApAs0AIwN5AFoDfQBoA7YAaANMACcDfQBiBJMASgSTAGQEkwBzB3sArgSTABQGqgCqBVwAFASTAB8EkwAnB8MAMQSTABkEkwAUBdMAfQTdAAAEZAAUBQ4AfQSTAKIAAP4iBqgAZAXfAAoDfwA9BlIALgZSADEELQBGCAABogQAARAIAAGiBAABEAgAAaIEAAEQBAABEAEK/nkCJQCTB9UBmAXBARcEqgBkBNUAngSTAGoE1QIjBNUBBAWq//YFAAHXBaoCjQWq//YFqgKNBar/9gWqAo0Fqv/2Bar/9gWq//YFqv/2Bar/9gWqAdkFqgKNBaoB2QWqAdkFqv/2Bar/9gWq//YFqgKNBaoB2QWqAdkFqv/2Bar/9gWq//YFqgKNBaoB2QWqAdkFqv/2Bar/9gWq//YFqv/2Bar/9gWq//YFqv/2Bar/9gWq//YFqv/2Bar/9gWq//YFqgAABaoAAAWqAAAFqgAABaoC1QWqAGYFqgAABdUAAATVAHsE1QAGAtUAbQLVAG0IAAAAB+wBngfsAZEH7AGeB+wBkQTVAKgEwQBiBNUAsgTVACkE1QApAtUAcwgrAbAIagHRB1YBRgYAAdkGAAFSBD8AOwU/ADsEwQBmBBQAQgQAAMUGAAEQBGgAZgQxABQCEAAUBDH/+gTXABQE+gDHBH0AXgLjACEGCgDHBQoArgU3AMcEeQCuBJMATgPDAFAGIwB9BCUAAAecABkGbwAXBBAAFAREAMcDtgCuBc0AcQK0ACEAAP+TAAD/kwAA/t8AAP7wA+MAjwPjAI8CJwCTAicAkwInAJMAAP7wAAD+8AAAAPkCJQCTA5MAZgInAKYCJwCmAAD+3wAA/tMEAP/6AAD85QAA//YAAPzsAAAAAARWAKAEVgCgBFYAoARWAKAEVgBOBFYAUgRWAE4EVgBOBFYARgMQAEYEVgA1BFYANQRWAFAEVgAtBFYASAMQAC0EVgAlBFYAJQRWACUEVgAnBFYALwMQACUEVgAdBFYAFwRWADUEVgA1BFYALwMQACkEVgBQBFYATARWAEwEVgBMBFYAXgMQAEwEVgCgBFYAoARWAKAEVgCgBFYAUARWAEwEVgBGBFYATARWAEwDEABMBFYALwRWADkEVgA/BFYAPwRWAD8DEAA/BFYANQRWADUEVgA1BFYANQRWADUDEAA1BFYATARWAEwEVgBMBFYATARWAEwDEABoBFYATARWAEYEVgBMBFYATARWAEwDEABMBFYAoARWAKAEVgCgBFYAoARWAFYEVgBWBFYAWARWAFYEVgBWAxAAXARWADcEVgA3BFYANwRWADcEVgA3AxAANwRWAEgEVgBGBFYARgRWAEYEVgBGAxAARgRWAIEEVgCBBFYAOQRWADkEVgA5AxAAOQRWAJEEVgCRBFYAkQRWAJEEVgCRAxAATARWAKAEVgCgBFYAoARWAKAEVgBMBFYATARWAEwEVgBMBFYAUAMQAFAEVgAvBFYANQRWADUEVgAXBFYAHQMQACkEVgAvBFYAJwRWACUEVgAlBFYAJQMQACUEVgBIBFYALQRWAFAEVgA1BFYANQMQAC0EVgBGBFYATgRWAE4EVgBSBFYATgMQAEYEVgCgBFYAoARWAKAEVgCgBOwAcQTsAHEE7ABxBOwAcQTsAHEE7ABxBOwAcQTsAHECxf/iAsX/4gLF/+ICxf/iAsX/1gLF/9YCxf/WAsX/1gTnAKIE5wCi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AADNyAAAzcgAAM3IAADNyAAAzcgAAM3IAADNyAAAzcgAAM3IAADNyAAA0IwAANFAAADRaAAA0XgAANKAAADS+AAA08gAANP4AADUIAAA1EgAANRwAADUnAAA1RwAANWYAADV2AAA1hQAANbQAADXmAAA2CwAANjkAADY+AAA2QwAANkgAADZiAAA2cQAANn0AADaJAAA2kwAANp0AADbbAAA3BAAANzcAADdsAAA3owAAN94AADgHAAA4OAAAOGsAADilAAA43gAAOR4AADlfAAA5pQAAOgAAADpZAAA6XQAAOmEAADqTAAA6yAAAOuUAADsDAAA7EAAAOxsAADtXAAA7YQAAO5sAADvTAAA8OQAAPJsAADynAAA8sQAAPNMAADz1AAA9GQAAPTYAAD1UAAA9aQAAPX0AAD37AAA+TAAAPoAAAD60AAA+6QAAPyIAAD9RAAA/iAAAP6cAAD/GAAA/8gAAQBwAAEBFAABAbwAAQHkAAECDAABApgAAQMUAAEDtAABBEwAAQT4AAEFoAABBiQAAQaYAAEHLAABB9QAAQhgAAEI/AABCbgAAQpoAAELgAABDJQAAQy8AAEM5AABDUgAAQ2sAAENvAABDiQAAQ6oAAEPOAABD8QAARBUAAEQ0AABEUwAARHUAAESXAABEvwAAROgAAEUEAABFHwAARV0AAEWUAABF1wAARhMAAEYXAABGIwAARi0AAEZZAABGggAARqYAAEbIAABG8QAARxwAAEdDAABHbgAAR5AAAEeyAABH2gAASAMAAEgHAABIEwAASB0AAEgqAABINAAASDgAAEg8AABISAAASFEAAEh9AABIgQAASI4AAEiYAABIpQAASK8AAEi8AABIxgAASO4AAEkWAABJJQAASS4AAEk7AABJRQAASVIAAElcAABJYAAASWQAAElxAABJewAASYgAAEmTAABJoQAASaoAAEm3AABJwQAASc4AAEnYAABJ5QAASe8AAEoFAABKGwAASigAAEozAABKPQAASmoAAEqUAABKvgAASuoAAEsWAABLNgAASzoAAEtrAABLoQAAS9gAAEwJAABMOgAATGUAAEyQAABMuQAATOcAAE0YAABNQwAATWsAAE2MAABNrQAATeIAAE3mAABODwAATjcAAE5BAABOSwAATlgAAE5iAABOcAAATnsAAE6JAABOlAAATqIAAE6tAABO9wAATwIAAE8SAABPIAAATy4AAE85AABPRwAAT1IAAE9hAABPbAAAT7oAAE/FAABP1QAAT+MAAE/tAABP9wAAUAQAAFAOAABQGgAAUCMAAFAxAABQOwAAUEkAAFBWAABQZAAAUG4AAFC/AABQyQAAUNoAAFDnAABQ9QAAUP8AAFEJAABREwAAUR0AAFEnAABRNAAAUT4AAFFMAABRVwAAUWUAAFFvAABRfQAAUYgAAFHbAABR5QAAUfYAAFIEAABSEAAAUhkAAFIlAABSLwAAUjwAAFJGAABSUgAAUlsAAFJlAABSbwAAUnkAAFKDAABSkAAAUpwAAFKoAABSsQAAUr0AAFLHAABS1AAAUt4AAFLqAABS8wAAUv0AAFMHAABTEQAAUxsAAFMoAABTMgAAUz4AAFNHAABTUQAAU2sAAFOLAABTrAAAU94AAFQPAABUMAAAVFEAAFSDAABUsgAAVMYAAFTZAABU4wAAVO0AAFUpAABVaQAAVW0AAFWaAABVwQAAVegAAFYIAABWOAAAVmAAAFZkAABWjQAAVrUAAFbjAABXFgAAVzcAAFc7AABXcAAAV5UAAFfMAABX+AAAWCQAAFg6AABYYQAAWIkAAFiwAABYzAAAWQUAAFkwAABZVgAAWVoAAFleAABZkgAAWcIAAFnsAABaIgAAWksAAFp2AABaoAAAWqQAAFrSAABa/gAAWxkAAFtEAABbXwAAW4wAAFutAABbygAAW/IAAFwaAABcRgAAXG4AAFyWAABcvQAAXPIAAF0lAABdTAAAXXAAAF2eAABdwwAAXc8AAF3WAABd/wAAXgMAAF4TAABeIgAAXjAAAF46AABeRAAAXk4AAF5YAABeYgAAXmwAAF54AABeggAAXo4AAF6YAABepAAAXq4AAF66AABewwAAXtEAAF7cAABe7AAAXvcAAF8HAABfEgAAXyIAAF8tAABfXAAAX2oAAF91AABffwAAX4oAAF+ZAABfowAAX9wAAGAfAABgKwAAYDQAAGBAAABgTAAAYFYAAGBgAABgcgAAYIAAAGCMAABglgAAYKAAAGCqAABgtAAAYMAAAGDJAABg9QAAYRsAAGEnAABhMQAAYT4AAGFJAABhVQAAYV8AAGFsAABhdwAAYYMAAGGNAABhmgAAYaUAAGGxAABhuwAAYcgAAGHTAABh3wAAYekAAGH2AABiAQAAYg0AAGIXAABiJAAAYi8AAGI7AABiRQAAYkkAAGJ6AABihgAAYpIAAGKwAABitAAAYuUAAGMYAABjIgAAYyYAAGMyAABjPAAAY0YAAGNQAABjXgAAY2kAAGN4AABjgwAAY48AAGOZAABjpAAAY64AAGO8AABjxQAAY/EAAGQuAABkZgAAZKMAAGThAABlFQAAZVAAAGWGAABlowAAZcgAAGYBAABmJgAAZkQAAGZgAABmowAAZtAAAGbUAABnFAAAZ2AAAGeBAABnsQAAZ+gAAGgaAABoRAAAaGwAAGiUAABoxgAAaM4AAGj1AABo/QAAaS4AAGlMAABpgwAAabgAAGnrAABp8QAAaiAAAGpdAABqYQAAamUAAGqnAABq3QAAav4AAGs4AABrPAAAa2IAAGuSAABrygAAa+kAAGwPAABsPQAAbGYAAGxqAABshAAAbLAAAGzYAABs7QAAbRoAAG0iAABtTwAAbYEAAG2nAABtzwAAbekAAG3tAABuKAAAblgAAG5cAABuZAAAbn4AAG6jAABuvgAAbuAAAG71AABvCwAAbzAAAG85AABvbgAAb4sAAG+5AABv1gAAcAMAAHAvAABwUgAAcIUAAHCwAABw0QAAcOYAAHEKAABxKQAAcT0AAHFjAABxlAAAcZgAAHHfAABx+wAAcgEAAHIeAAByPQAAcksAAHJPAAByhAAAcrUAAHK5AABy6wAAcwoAAHMaAABzTwAAc3kAAHOjAABz3gAAdCoAAHR+AAB0uwAAdPQAAHVFAAB1ggAAdbIAAHXQAAB2BgAAdicAAHZOAAB2fQAAdpoAAHa8AAB23QAAdvUAAHcMAAB3MAAAd1UAAHd2AAB3lAAAd5gAAHecAAB3oAAAd6QAAHe8AAB31AAAd+4AAHf0AAB3+QAAd/4AAHgPAAB4GgAAeB4AAHgjAAB4KAAAeDQAAHg+AAB4QgAAeEYAAHhZAAB4ZQAAeGoAAHhvAAB4cwAAeHcAAHh7AAB4hAAAeJcAAHibAAB4xgAAeNIAAHj1AAB5EQAAeSsAAHk5AAB5SgAAeVwAAHltAAB5ewAAeYkAAHmbAAB5qAAAebQAAHm4AAB5yAAAedkAAHnpAAB5+QAAegcAAHodAAB6NAAAek4AAHpTAAB6XwAAemsAAHp2AAB6gQAAepAAAHqbAAB6qwAAerAAAHq1AAB6wgAAescAAHrMAAB60QAAetUAAHraAAB63wAAeuQAAHrvAAB6/gAAex0AAHszAAB7PwAAe0sAAHtXAAB7YwAAe3MAAHuDAAB7kQAAe58AAHusAAB7uQAAe8oAAHvZAAB76AAAe/4AAHwIAAB8FwAAfCYAAHwxAAB8OwAAfEAAAHxFAAB8SgAAfFYAAHxjAAB8fgAAfIsAAHyYAAB8owAAfK4AAHy6AAB8xAAAfM0AAHzSAAB87QAAfPYAAHz/AAB9CQAAfRMAAH0kAAB9MwAAfUMAAH1eAAB9dAAAfY0AAH2YAAB9nQAAfaIAAH2nAAB9rgAAfbgAAH29AAB9zAAAfdEAAH3eAAB96wAAfhEAAH5EAAB+egAAfooAAH6XAAB/TAAAf1sAAH9fAAB/gQAAf5AAAH+jAAB/tgAAf9gAAH/cAAB/4AAAf/YAAIAhAACAOgAAgE4AAIBZAACAZgAAgG8AAICMAACApgAAgLMAAIDZAACA+wAAgRAAAIEmAACBPAAAgVAAAIFxAACBigAAgasAAIG/AACB1QAAgeYAAIH4AACCCAAAghkAAIIeAACCIgAAgjEAAIJBAACCRQAAgoUAAIKRAACCngAAgqIAAILaAACDAwAAgycAAINHAACDcQAAg5MAAIO2AACD4wAAhBUAAIRBAACEbgAAhKEAAITVAACE/QAAhSUAAIVLAACFbwAAhaYAAIXbAACGCQAAhjkAAIZtAACGmQAAhsIAAIbkAACHEQAAh0gAAIdMAACHUAAAh1QAAIdYAACHXAAAh2AAAIdkAACHaAAAh4gAAIemAACH1gAAh9oAAIfnAACH9AAAiA4AAIgSAACIFgAAiBoAAIgeAACIPAAAiGoAAIi2AACI7QAAiQsAAIknAACJXQAAiX4AAImyAACJygAAid8AAIn7AACKHAAAijoAAIpVAACKdgAAipMAAIqzAACK0gAAiwYAAItMAACLegAAi5AAAIumAACLxgAAi+sAAIwQAACMIgAAjDsAAIxaAACMlgAAjM4AAIzhAACNAQAAjRsAAI1CAACNbAAAjZEAAI2hAACNtAAAjccAAI3LAACN8AAAjgwAAI4oAACOUQAAjnwAAI6vAACOyAAAjuQAAI8AAACPIQAAjz0AAI9WAACPawAAj4gAAI+XAACPtQAAj88AAI/oAACQBQAAkDAAAJBNAACQbgAAkIAAAJCXAACQtwAAkOQAAJEQAACRMwAAkXUAAJGZAACRvgAAkeUAAJIJAACSOAAAkmYAAJKSAACSrAAAkssAAJLyAACTEwAAky4AAJNHAACTWQAAk2oAAJOOAACTrQAAk8gAAJPjAACUCgAAlB4AAJRCAACUbQAAlIYAAJS0AACU3gAAlQYAAJUfAACVNwAAlT4AAJVFAACVTQAAlVQAAJV4AACVgAAAlYcAAJXDAACWDQAAllkAAJaZAACW7gAAlzAAAJd6AACXtAAAl+kAAJgnAACYZQAAmKMAAJjYAACY9wAAmSsAAJl3AACZkQAAmbUAAJn0AACaJAAAmmQAAJqaAACa0gAAmwAAAJtCAACbcAAAm4kAAJvCAACb7gAAnCUAAJxOAACchgAAnLUAAJzaAACdAwAAnSYAAJ1pAACdngAAndoAAJ4PAACeUAAAno8AAJ7LAACe8wAAnxsAAJ9DAACfbwAAn6AAAJ/DAACf3QAAoBcAAKBKAACgTgAAoGwAAKCLAACgjwAAoKsAAKDVAACg6QAAoQIAAKErAAChXQAAoXAAAKGEAAChkwAAocEAAKHpAACiCwAAoi4AAKJIAACicwAAoqQAAKLRAACi6wAAoxAAAKNBAACjaAAAo4AAAKOeAACjsgAAo8oAAKPsAACkHwAApEIAAKRvAACkkQAApLAAAKTWAACk7AAApPsAAKUKAAClFgAApSIAAKU5AAClUAAApWMAAKV0AACllgAApaIAAKWsAACltgAApcAAAKXOAACl3AAApewAAKX5AACmBQAApg8AAKYZAACmIwAApjEAAKY/AACmSQAAplMAAKZiAACmcQAApoIAAKaMAACmnQAApqcAAKa2AACmxQAAptYAAKbnAACm9wAApwQAAKcQAACnHAAApyoAAKczAACnPwAAp0sAAKdVAACnXwAAp2wAAKd5AACnggAAp4sAAKeaAACnqgAAp7oAAKfLAACn2wAAp+YAAKfyAACn/gAAqAgAAKgSAACoIAAAqC4AAKg4AACoQgAAqFUAAKhoAACodgAAqIQAAKiTAACoogAAqK4AAKi4AACowgAAqMwAAKjYAACo4gAAqOwAAKj2AACpBAAAqRIAAKkhAACpMAAAqT8AAKlKAACpWgAAqWUAAKl2AACpgQAAqZIAAKmdAACpqQAAqbMAAKm/AACpyQAAqdUAAKnfAACp6QAAqfMAAKoHAACqFQAAqiMAAKoxAACqPQAAqkcAAKpRAACqWwAAqmgAAKpyAACqfwAAqokAAKqZAACqqAAAqrQAAKrBAACqywAAqtUAAKrjAACq8QAAqwAAAKsPAACrHwAAqy8AAKtAAACrUQAAq2AAAKtvAACrfgAAq4gAAKubAACrpgAAq7IAAKu7AACrxQAAq88AAKvbAACr5QAAq+8AAKv5AACsBQAArA8AAKwcAACsJgAArDIAAKw8AACsSAAArFEAAKxbAACsZQAArHMAAKyBAACsjwAArJoAAKykAACsrgAArLgAAKzEAACs9AAArP4AAK0IAACtEgAArRwAAK0mAACtMAAArToAAK1EAACtTwAArVoAAK1nAACtdAAArYEAAK2OAACtngAAra4AAK24AACtwgAArcwAAK3WAACt4AAAreoAAK32AACuAgAArg8AAK4cAACuKQAArjYAAK5AAACuSgAArlQAAK5eAACuaAAArnIAAK58AACuhgAArpIAAK6eAACuqwAArrgAAK7FAACu0gAAruIAAK7xAACu+gAArwQAAK8OAACvGAAAryIAAK8sAACvNgAAr0EAAK9NAACvWQAAr2YAAK9zAACvgAAAr40AAK+cAACvqwAAr7UAAK+/AACvyQAAr9MAAK/dAACv5wAAr/MAAK//AACwDAAAsBkAALAmAACwMwAAsD0AALBHAACwUQAAsFsAALBlAACwbwAAsHkAALCDAACwjwAAsJwAALCpAACwuAAAsMIAALDMAACw1wAAsOEAALDrAACw9QAAsP8AALEJAACxFQAAsSEAALEuAACxOwAAsUgAALFVAACxZQAAsXUAALF/AACxiAAAsZIAALGbAACxpQAAsa4AALG4AACxwgAAscwAALHVAACx3wAAsegAALHyAACx/AAAsgoAALIZAACyKAAAsjcAALJFAACyUwAAsmEAALJwAACygAAAspAAALKiAACytAAAssYAALLYAACy7AAAswAAALMOAACzHAAAsyoAALM5AACzRwAAs1UAALNjAACzcQAAs4IAALOTAACzpQAAs7cAALPJAACz2wAAs+8AALQCAAC0EQAAtCAAALQvAAC0PwAAtE4AALRdAAC0bQAAtH0AALSOAAC0nwAAtLEAALTDAAC01QAAtOcAALT8AAC1EQAAtRsAALUlAAC1MwAAtT0AALVLAAC1VQAAtWMAALVvAAC1fQAAtYkAALWVAAC1nwAAtbsAALXTAAC12gAAtd4AALYZAAC2JwAAtjEAALY+AAC2RwAAtlQAALZgAAC2bAAAtngAALaEAAC2jgAAtq8AALbPAAC2/QAAtwcAALcRAAC3HAAAtycAALcxAAC3PAAAt0gAALdWAAC3YgAAt24AALePAAC3sAAAt90AALfnAAC38QAAt/wAALgHAAC4EQAAuBsAALglAAC4MAAAuDwAALhKAAC4VgAAuGIAALhuAAC4lgAAuL4AALjRAAC44AAAuOoAALj5AAC5AwAAuRIAALkeAAC5KQAAuTUAALlAAAC5SgAAuV0AALl5AAC5ggAAuZYAALmnAAC5twAAucIAALnJAAC54QAAuewAALn3AAC6BgAAuhkAALosAAC6LAAAujIAALo2AAC6igAAuqAAALq0AAC6wgAAutMAALrnAAC6+wAAuxYAALs9AAC7ZAAAu2wAALt0AAC7fAAAu5gAALugAAC73AAAvCcAALxbAAC8kgAAvOIAAL0sAAC9kAAAvbcAAL3pAAC+LwAAvmkAAL6/AAC++gAAvzcAAL98AAC/qwAAv+AAAL/lAADAJgAAwHAAAMCHAADAkgAAwJ0AAMC7AADA1QAAwOoAAMEEAADBGQAAwT4AAMFdAADBgQAAwYUAAMGJAADBlwAAwa8AAMHNAADB4gAAwegAAMIBAADCGQAAwiQAAMIwAADCPwAAwk0AAMJcAADCagAAwnwAAMKMAADCnQAAwq4AAMLEAADC3AAAwu4AAMMIAADDHAAAwzoAAMNTAADDZgAAw4MAAMOdAADDsQAAw84AAMPnAADD+gAAxBYAAMQzAADESwAAxG4AAMSJAADEnwAAxMIAAMTfAADE9QAAxRkAAMU2AADFTAAAxXAAAMWXAADFuAAAxecAAMXzAADF/wAAxgsAAMYXAADGIwAAxwcAAMjIAADKeAAAyoMAAMqVAADKoQAAyrQAAMq/AADKygAAytYAAMriAADK7QAAyw4AAMuhAADLswAAy8wAAMvyAADMEAAAzFsAAMyWAADM2wAAzQ0AAM1HAADNbAAAzaIAAM29AADN0QAAzfEAAM4bAADOVAAAzoEAAM6oAADO1gAAzwwAAM85AADPiQAAz78AAM/DAADP6QAA0AwAANAyAADQTwAA0GsAANCXAADQtgAA0OAAANEMAADRQQAA0VoAANF3AADRoAAA0bcAANHRAADR6gAA0gIAANIOAADSHwAA0jAAANJJAADSYQAA0nkAANKJAADSlwAA0qQAANLBAADS0wAA0t8AANLrAADTEwAA0zsAANM/AADTUAAA02EAANN7AADTlgAA06sAANPAAADT1QAA0+gAANP8AADUEAAA1CIAANQ2AADUSQAA1FsAANRvAADUhAAA1JgAANSsAADUvQAA1M8AANTjAADU+AAA1Q4AANUiAADVNQAA1UcAANVbAADVcQAA1YcAANWdAADVrgAA1b8AANXRAADV5wAA1f0AANYSAADWJgAA1jcAANZKAADWXwAA1nQAANaHAADWmwAA1rAAANbFAADW1wAA1uoAANb8AADXEAAA1yUAANc6AADXTwAA12IAANd0AADXiAAA150AANezAADXyAAA19oAANfrAADX/QAA2BMAANgpAADYPgAA2FIAANhjAADYdAAA2IkAANieAADYswAA2McAANjZAADY7AAA2QEAANkWAADZKQAA2T0AANlSAADZZwAA2XwAANmNAADZnwAA2bMAANnIAADZ3gAA2fQAANoGAADaFwAA2igAANo+AADaVAAA2mkAANp9AADajgAA2qEAANq2AADaywAA2uAAANr0AADbBgAA2xkAANssAADbQQAA21YAANtpAADbewAA244AANujAADbuAAA28sAANvfAADb9AAA3AoAANwgAADcMgAA3EMAANxUAADcagAA3IAAANyWAADcqgAA3LsAANzOAADc4gAA3PgAAN0NAADdIQAA3TMAAN1EAADdWAAA3WwAAN2CAADdlgAA3agAAN27AADdzwAA3eEAAN31AADeCQAA3hsAAN4uAADeQgAA3lcAAN5sAADedwAA3oIAAN6NAADemAAA3qMAAN6uAADeuQAA3sQAAN7PAADe2gAA3uUAAN7wAADe+wAA3wYAAN8RAADfHAAA3ycAAN8yAADfPQAA30gAAN9TAADfXgAA32kAAN90AADffwAA34oAAN+VAADfoAAA36wAAN+4AADfxAAA39AAAN/2AADgGwAA4EsAAOB+AADgqQAA4NQAAOD/AADhKwAA4UcAAOFoAADhiQAA4cAAAOH7AADiNwAA4k8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AAQoWAAEKPgABCmQAAQqSAAEKmQABCqAAAQqnAAEKrgABCrUAAQq8AAEKwwABCsoAAQrRAAEK2AABCt8AAQrmAAELAgABCwkAAQsQAAELFwABCx4AAQslAAELLAABCzMAAQs6AAELQQABC0gAAQtRAAELWgABC2MAAQtsAAELdQABC34AAQuHAAELkAABC5kAAQuiAAELyQABDBEAAQxhAAEMkQABDMkAAQz7AAENNwABDWsAAQ2eAAEN1wABDh0AAQ5eAAEOqAABDuYAAQ8oAAEPZgABD34AAQ+UAAEP2QABEAwAARBPAAEQmQABENUAAREiAAERdgABEa4AARHYAAESAgABEisAARJPAAESfwABErgAARLvAAETKQABE2EAARObAAETzwABE/UAARP8AAEUKQABFDAAARRtAAEUpAABFNcAARURAAEVVQABFZQAARXBAAEVyAABFc8AARXYAAEWIAABFkQAARZmAAEWbQABFnQAARaOAAEWpgABFr4AARbXAAEW8QABFwsAARckAAEXPQABF1YAARdvAAEXiQABF6MAAReqAAEXsQABF7gAARe/AAEXxgABF80AARfUAAEX2wABF+IAARfpAAEX8AABF/cAARgeAAEYPwABGGIAARiGAAEYqgABGNAAARj0AAEZGgABGUEAARlnAAEZjgABGbUAARnpAAEaFwABGkcAARp4AAEaqQABGtsAARsMAAEbPwABG3IAARulAAEb2QABHA0AARwNAAEcJgABHD8AARxaAAEcYQABHGgAARxvAAEclAABHLcAARzcAAEdDQABHT0AAR1vAAEdkgABHbUAAR3NAAEd8gABHhcAAR43AAEeVwABHoMAAR6vAAEe6QABHy4AAR9zAAEfwgABIAYAASBVAAEgbwABIKQAASDdAAEg/AABIRsAASFEAAEhawABIaEAASHLAAEh0gABIdkAASHgAAEh5wABIe4AASH1AAEh/AABIgMAASIjAAEiRQABImQAASJrAAEijgABIq4AASK1AAEi1AABIvAAASL3AAEjIQABI1oAASOeAAEjuAABI9EAASPYAAEj8wABJA8AASQmAAEkRwABJHYAASSxAAEkwwABJPUAASU4AAElWAABJYYAASWMAAElkwABJZoAASWhAAElqgABJbMAASW8AAElwwABJcoAASXRAAEl2AABJew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AQcAegIEAAAAC7YBRR4YDxUlASs1AAAA//8AxwAAA/gHcwImACgAAAEHAEP/twFSABW0AQ0FJgG4/6m0ERUCCyUBKzUAKzUA//8AxwAAA/gHcwImACgAAAEHAHYAQgFSABNACwEVBSYBMxURAgslASs1ACs1AAAA//8AxwAAA/gHcwImACgAAAEHAUv/+wFSABW0ARkFJgG4//q0GRMCCyUBKzUAKzUA//8AxwAAA/gHKQImACgAAAEHAGoADgFSABdADQIBIQUmAgEADB4CCyUBKzU1ACs1NQAAAP//ADwAAAJiB3MCJgAsAAABBwBD/rkBUgAVtAENBSYBuP+wtBEVBgslASs1ACs1AP//AFIAAAKIB3MCJgAsAAABBwB2/2kBUgATQAsBFQUmAWAVEQYLJQErNQArNQAAAP////0AAAKxB3MCJgAsAAABBwFL/vkBUgAVtAEZBSYBuP/9tBkTBgslASs1ACs1AP//ADwAAAJ8BykCJgAsAAABBwBq/wsBUgAXQA0CASEFJgIBAgweBgslASs1NQArNTUAAAAAAgA9AAAFWgW2AAwAGAByQEcGBBIWFggEAA0NFAQDGRoVBgcGaVkSGAcBegcBSAcBDwdvB38HnwevBwUPB68HzwffB/8HBQsDBwcECQkRa1kJAwQWa1kEEgA/KwAYPysREgA5GC9fXl1xXV1xMysRADMREgEXOREzETMzETMRMzEwARAAISERIzUzESEgAAMQACEjESEVIREzIAVa/nf+i/55mJgBtAFVAXzC/ur+7eIBbf6TuQJSAun+mf5+AoOgApP+h/6mARgBHv4KoP4b//8AxwAABU4HMwImADEAAAEHAVIAmgFSABNACwEdBSYBCR0pCRMlASs1ACs1AAAA//8Aff/sBcMHcwImADIAAAEHAEMAdwFSABW0AhkFJgK4/6i0HSEGACUBKzUAKzUA//8Aff/sBcMHcwImADIAAAEHAHYBDgFSABNACwIhBSYCPyEdBgAlASs1ACs1AAAA//8Aff/sBcMHcwImADIAAAEHAUsAtAFSABW0AiUFJgK4//K0JR8GACUBKzUAKzUA//8Aff/sBcMHMwImADIAAAEHAVIAngFSABW0AhgFJgK4//i0IS0GACUBKzUAKzUA//8Aff/sBcMHKQImADIAAAEHAGoA0wFSABdADQMCLQUmAwIEGCoGACUBKzU1ACs1NQAAAAABAIMBDgQMBJgADgAqQBcNCgMAAgUFAAwKBA8QLwxfDH8MrwwEDAAZL10REgEXOREzETMRMzEwAQEXAQAXByYnASc2NwE3AkgBXmb+pAEgOmbnd/6qa4PX/qRrAzkBX2n+pP7cOGnnc/6maYHbAVprAAAAAAMAff/BBcMF+AATABsAIwBfQDQRABcFBwoeBRwKBQgWHxIPABQUDx8ICgUkJR8WHhcEGSEPEggFBAMNDSFpWQ0EAxlpWQMTAD8rABg/KxESABc5ERIXORESARc5ETMRMxEzETMRMxEzETMRMxEzMTABEAAhIicHJzcmERAAITIXNxcHFgMQJwEWMzISARAXASYjIgIFw/6d/sHok2J9arQBXwFHzKBffGjBw2j9cHKn6/T8P2ECjW2d7vYC3f6h/m5gi1GYxgFvAWUBiVyHVJLL/pYBCJb8Xk4BLAEm/v2SA5tI/tMAAP//ALj/7AUfB3MCJgA4AAABBwBDAEIBUgAVtAETBSYBuP+otBcbCAElASs1ACs1AP//ALj/7AUfB3MCJgA4AAABBwB2ANUBUgATQAsBGwUmATobFwgBJQErNQArNQAAAP//ALj/7AUfB3MCJgA4AAABBwFLAH8BUgAVtAEfBSYBuP/ytB8ZCAElASs1ACs1AP//ALj/7AUfBykCJgA4AAABBwBqAJgBUgAZtgIBJwUmAgG4//60EiQIASUBKzU1ACs1NQD//wAAAAAEhwdzAiYAPAAAAQcAdgA3AVIAE0ALARIFJgFEEg4HAiUBKzUAKzUAAAAAAgDHAAAEbwW2AAwAFQA8QB4JDQUFBgARBhEWFwkVa1kJCQYHBA1rWQQEBgcDBhIAPz8SOS8rERIAORgvKxESATk5ETMRMxEzMzEwARQEISMRIxEzFTMgBAEzMjY1NCYjIwRv/tP+46a4uMUBGQES/RCT3cG2xbYDDuLv/sMFtv7T/fSOnZCHAAEArv/sBLAGHwAvAFBAKxAcKCkFHAAiCxYWIhwpBDAxAgUIAwsAHxwZAxYiKRUtJV1ZLQEOFF5ZDhYAPysAGD8rABg/ERIXORESFzkREgEXOREzETMRMxEzETMxMAEUBwYGFRQWFxYWFRQGIyInNRYWMzI1NCYnJiY1NDY3NjY1NCYjIBURIxE0NjMyFgQtj09BQHWJZMW4u25An0TTUXB2aURISkGFf/7vtOTh0ugE8ItwPkkiKEJMXJ1koaxFoigusEdoR0t9Vz9pNTdcM05W3ftUBKy1vqAAAP//AF7/7APXBiECJgBEAAABBgBDkAAADrkAAv+KtComExklASs1AAD//wBe/+wD1wYhAiYARAAAAQYAdjEAAAu2AisvKhMZJQErNQD//wBe/+wD1wYhAiYARAAAAQYBS9wAAA65AAL/47QzLRMZJQErNQAA//8AXv/sA9cF4QImAEQAAAEGAVLGAAAOuQAC/+m0LzsTGSUBKzUAAP//AF7/7APXBdcCJgBEAAABBgBq5gAAELEDArj/4LQmOBMZJQErNTX//wBe/+wD1waJAiYARAAAAQYBUP0AABCxAwK4//S0LCYTGSUBKzU1AAMAXv/sBoEEXgAoADMAOgB5QEEKAB4XKQAYAy8XODgvAAM7PCMQJg0DMGBZAzc3GGRZGTcBAw83ARAGNzcmDTQHDQdeWRMNECEaYVkhJiYsXlkmFgA/KxEAMysAGD8zKxEAMxESORgvX15dX10rABgQxSsREgA5ORESARc5ETMRMzMRMxEzETMxMBMQJTc1NCYjIgcnNjYzMhYXNjYzMhIVFSESITI2NxUGBiMgJwYGIyImNxQWMzI2NTUHBgYBIgYHITQmXgH0uHF0i6g4R8tngKUrNqpwxen9QggBNViaXliYZv7dfVLGiKS4u2hWjKOZsaMDuHeICwH8fQExAU4QB0V6dlSHKDZTXVVd/vLdb/6BISueJyDnfGurmFxWo5ZjBwdqAiqhmZii//8Acf4UA5MEXgImAEYAAAEHAHoBTAAAAAu2ATEdFwMJJQErNQAAAP//AHH/7AQbBiECJgBIAAABBgBDtQAADrkAAv/AtCElAwolASs1AAD//wBx/+wEGwYhAiYASAAAAQYAdlAAAAu2AlslIQMKJQErNQD//wBx/+wEGwYhAiYASAAAAQYBS/kAAAu2AhEpIwMKJQErNQD//wBx/+wEGwXXAiYASAAAAQYAagoAAA23AwIVHC4DCiUBKzU1AAAA////1AAAAXAGIQImAPMAAAEHAEP+UQAAAA65AAH/mrQJDQIDJQErNf//AKcAAAJDBiECJgDzAAABBwB2/yQAAAALtgFtDQkCAyUBKzUAAAD///+vAAACYwYhAiYA8wAAAQcBS/6rAAAAC7YBARELAgMlASs1AAAA////6gAAAioF1wImAPMAAAEHAGr+uQAAAA23AgECBBYCAyUBKzU1AAACAHH/7ARoBh8AGwAnAGxAPBgMHBMQIiIGGQ4AHBwOEQYEKCkQEQ4WGRgGFw8ADxAPIA8DCQMLAw8PCRQJH15ZCQkDExcUAQMlXVkDFgA/KwAYPzMzEjkvKxESADkYLxE5X15dERIXORESARc5ETMRMxEzETMzETMzMTABEAAjIgA1NAAzMhc3JicFJzcmJzcWFzcXBxYSAzQmIyIGFRQWMzI2BGj+9vbh/uoBA97hXgk7w/71TeVWYkmcZuxOy5emtq+WpqConqmcAjP+5v7TAQ/i5QEHdwTWsZlwgzoze0lLiW51jP51/uiPpq2zpbPGAAD//wCuAAAETAXhAiYAUQAAAQYBUhAAAAu2AQ0eKgoUJQErNQD//wBx/+wEaAYhAiYAUgAAAQYAQ9AAAA65AAL/tbQdIQcAJQErNQAA//8Acf/sBGgGIQImAFIAAAEGAHZaAAALtgI+IR0HACUBKzUA//8Acf/sBGgGIQImAFIAAAEGAUsMAAAOuQAC//60JR8HACUBKzUAAP//AHH/7ARoBeECJgBSAAABBgFS9QAAC7YCAiEtBwAlASs1AP//AHH/7ARoBdcCJgBSAAABBgBqGQAAELEDArj//rQYKgcAJQErNTUAAwBmAPgEKwSqAAMADQAXAFFANw4EEggDCAQABBgZBqALAQALMAtAC3ALoAuwC/ALBwsLFT8QARAQAC8BXwF/Aa8BzwHvAf8BBwEAL10zMy9dMzIvXXEzERIBFzkRMxEzMTATNSEVATQzMhUUBiMiJhE0MzIVFAYjIiZmA8X9qnNwQi4wQ3NwQi4wQwKJkpL+7Ht7Qjs7Avx7e0I7OwAAAAMAcf+8BGgEhwATABoAIgBUQC8dDxYFFAoFCBIPABsbDxcIBwoGIyQXHRYeBCAZDxIIBQQDDQ0ZXVkNEAMgXVkDFgA/KwAYPysREgAXORESFzkREgEXOREzETMRMxEzETMRMzEwARAAIyInByc3JhEQADMyFzcXBxYFFBcBJiMgATQnARYzMjYEaP7w8JdxUnZcgwEM8pd1UHldgfzDMQHDSWz+wQKDL/49RWydpAIn/vP+0kNzUH+cAQABDAErSnNOgZv2pmACcjT+YJhk/Y0v1wD//wCi/+wERAYhAiYAWAAAAQYAQ8IAAA65AAH/oLQaHhQKJQErNQAA//8Aov/sBEQGIQImAFgAAAEGAHZ1AAALtgFTHhoUCiUBKzUA//8Aov/sBEQGIQImAFgAAAEGAUsUAAAOuQAB//+0IhwUCiUBKzUAAP//AKL/7AREBdcCJgBYAAABBgBqIQAAELECAbj//7QVJxQKJQErNTX//wAC/hQEFAYhAiYAXAAAAQYAdhcAAAu2AV0hHQAKJQErNQAAAgCu/hQEewYUABUAIQBBQCITGhUMBA8PEAYfEB8iIwsACQMRABAbAxZdWQMQCR1dWQkWAD8rABg/KwAYPz8REjk5ERIBOTkRMxEzERczMTABNjYzMhIREAIjIicjFhURIxEzERQHJSIGBxUUFjMgETQmAWRDp2rV7u7V3ngMDLS0BgE+oZUClKYBJY8DtltN/tX+9P7z/tKfhCj+NQgA/jZGThGzxiffxwGo0M4AAP//AAL+FAQUBdcCJgBcAAABBgBquQAAELECAbj//7QYKgAKJQErNTX//wAAAAAFGwa8AiYAJAAAAQcBTQBCAVIAH0AWAg8REAUGJQJ/EY8RnxGvEc8RBREFJgArXTUBKzUAAAD//wBe/+wD1wVqAiYARAAAAQYBTfkAAA65AAL//bQpKBMZJQErNQAA//8AAAAABRsHPgImACQAAAEHAU4ALwFSABNACwIAERkFBiUCDgUmACs1ASs1AAAA//8AXv/sA9cF7AImAEQAAAEGAU7oAAAOuQAC/+60KTETGSUBKzUAAP//AAD+PQUbBbwCJgAkAAABBwFRA5wAAAAOuQAC/+O0GhsAByUBKzX//wBe/j0EBgRcAiYARAAAAQcBUQKRAAAAC7YCADIzABolASs1AAAA//8Aff/sBM8HcwImACYAAAEHAHYBCAFSABNACwEhBSYBsyEdDxUlASs1ACs1AAAA//8Acf/sA5MGIQImAEYAAAEGAHZIAAALtgGXIBwDCSUBKzUA//8Aff/sBM8HcwImACYAAAEHAUsAvgFSABNACwElBSYBdiUfDxUlASs1ACs1AAAA//8Acf/sA6UGIQImAEYAAAEGAUvtAAALtgFJJB4DCSUBKzUA//8Aff/sBM8HNwImACYAAAEHAU8CEgFSABNACwEhBSYBdRgeDxUlASs1ACs1AAAA//8Acf/sA5MF5QImAEYAAAEHAU8BTgAAAAu2AVUXHQMJJQErNQAAAP//AH3/7ATPB3MCJgAmAAABBwFMAL4BUgATQAsBIgUmAXYkHw8VJQErNQArNQAAAP//AHH/7AOtBiECJgBGAAABBgFM9QAAC7YBUSMeAwklASs1AP//AMcAAAVaB3MCJgAnAAABBwFMAFgBUgAVtAIbBSYCuP+mtB0YBQAlASs1ACs1AP//AHH/7AWPBhQCJgBHAAABBwI4AxQAAAALtgJcISEODiUBKzUAAAD//wA9AAAFWgW2AgYAkgAAAAIAcf/sBNkGFAAbACgAfEBIFRcOJiYDExcXCQwQAxkZHwMDKSoIGgAGFg4PDl9ZEwgPGA8CEQ8PHw8CFAMPDwYRABgVBiNdWQAGEAYgBgMJAwYQABxdWQAWAD8rABg/X15dKwAYPz8SOS9fXl1eXTMrEQAzERI5ORESARc5ERczMxEzETMRMxEzMTAFIgIREBIzMhczJiY1NSE1ITUzFTMVIxEjJyMGJzI2NTU0JiMiBhUUFgIz1uzr1993DQMK/kwBtLScnJMbCHPCo5aYpYyVkBQBJgENAQ8BKqIObih9h7a2h/spk6eVscoj5r7W0MXXAP//AMcAAAP4BrwCJgAoAAABBwFNAAQBUgAdQBQBfw+PD58Prw8EDwUmAQAPDgILJQErNQArXTUA//8Acf/sBBsFagImAEgAAAEGAU0IAAALtgIdHx4DCiUBKzUA//8AxwAAA/gHPgImACgAAAEHAU4ADAFSABNACwEMBSYBCg8XAgslASs1ACs1AAAA//8Acf/sBBsF7AImAEgAAAEGAU79AAALtgIUHycDCiUBKzUA//8AxwAAA/gHGgImACgAAAEHAU8BZgE1ABNACwEVBSYBEAwSAgslASs1ACs1AAAA//8Acf/sBBsF5QImAEgAAAEHAU8BUgAAAAu2AhUcIgMKJQErNQAAAP//AMf+PQP4BbYCJgAoAAABBwFRAm0AAAALtgHaFhEBACUBKzUAAAD//wBx/loEGwReAiYASAAAAQcBUQJiAB0ADrkAAgEBtCYhAxIlASs1//8AxwAAA/gHcwImACgAAAEHAUwADAFSABNACwEWBSYBChgTAgslASs1ACs1AAAA//8Acf/sBBsGIQImAEgAAAEGAUz9AAALtgIVKCMDCiUBKzUA//8Aff/sBTsHcwImACoAAAEHAUsA+AFSABNACwEpBSYBeikjCAIlASs1ACs1AAAA//8Acf4UBD0GIQImAEoAAAEGAUsKAAALtgIRNjAUHSUBKzUA//8Aff/sBTsHPgImACoAAAEHAU4A+AFSABNACwEcBSYBeR8nCAIlASs1ACs1AAAA//8Acf4UBD0F7AImAEoAAAEGAU4OAAALtgIULDQUHSUBKzUA//8Aff/sBTsHNwImACoAAAEHAU8CWAFSABNACwElBSYBhRwiCAIlASs1ACs1AAAA//8Acf4UBD0F5QImAEoAAAEHAU8BWgAAAAu2AgwpLxQdJQErNQAAAP//AH3+OwU7BcsCJgAqAAABBwI5ASUAAAALtgE7JSEIAiUBKzUAAAD//wBx/hQEPQYhAiYASgAAAQcCOgCFAAAAC7YCLS4yFB0lASs1AAAA//8AxwAABSUHcwImACsAAAEHAUsAlgFSABW0ARkFJgG4//60GRMGCyUBKzUAKzUA//8ArgAABEwHqgImAEsAAAEHAUsAIQGJABNACwEkAiYBAiQeChYlASs1ACs1AAAAAAIAAAAABewFtgATABcAbUA8EgMXDw8AEAcLCwQUDAkMEAMYGQoWEhMSbFkHAwATEBNgEwMMAxMTEAEXDmlZMBcBkBcBFxcQBQEDDBASAD8zPzMSOS9dcSsREgA5GC9fXl0zMysRADMzERIBFzkRMzMzETMRMzMRMzMyMTATNTMVITUzFTMVIxEjESERIxEjNQE1IRXHuALuuMfHuP0SuMcEbf0SBMPz8/PzlPvRAqr9VgQvlP6J4+MAAAAAAQASAAAETAYUAB4Ac0BBEgALCRAUFgMICA0JHgAJAB8gFgkaEwsMC19ZEAgMGAwCEQ8MARQDDAwaDhoEXVm/GgEAGhAaIBoDGhoJDgAACRUAPzM/EjkvXV0rERIAORgvX15dXl0zKxEAMxESORESATk5ETMRMzMRFzMRMxEzMTAhETQmIyIGFREjESM1MzUzFSEVIRUUBzM2NjMyFhURA5p3f6mZtJyctAGy/k4KDDW3bMfJApaFg7nV/fAE1Ye4uIeyWEBVVcHS/V7////kAAAC1AczAiYALAAAAQcBUv7iAVIAE0ALAQwFJgECFSEGCyUBKzUAKzUAAAD///+SAAACggXhAiYA8wAAAQcBUv6QAAAAC7YBAg0ZAgMlASs1AAAA//8ALAAAAowGvAImACwAAAEHAU3/AQFSAB1AFAF/D48Pnw+vDwQPBSYBAg8OBgslASs1ACtdNQD////cAAACPAVqAiYA8wAAAQcBTf6xAAAAC7YBBAcGAgMlASs1AAAA//8AHgAAApsHPgImACwAAAEHAU7+/wFSABNACwEMBSYBAg8XBgslASs1ACs1AAAA////ygAAAkcF7AImAPMAAAEHAU7+qwAAAAu2AQAHDwIDJQErNQAAAP//AFL+PQJiBbYCJgAsAAABBwFRAJEAAAALtgEDFhIGCyUBKzUAAAD//wAz/j0BhQXlAiYATAAAAQYBURAAAAu2AgocHQEAJQErNQD//wBSAAACYgc3AiYALAAAAQcBTwBQAVIAE0ALARUFJgEADBIGCyUBKzUAKzUAAAAAAQCuAAABYgRKAAMAFkAJAAEBBAUCDwEVAD8/ERIBOREzMTAhIxEzAWK0tARKAAD//wBS/nsEKwW2ACYALAAAAQcALQK2AAAAC7YBJRQUChslASs1AAAA//8AoP4UA38F5QAmAEwAAAEHAE0CDAAAABCxAwK4//u0GBgAKyUBKzU1AAD///9g/nsCdQdzAiYALQAAAQcBS/69AVIAE0ALAQMbFQkKJQEbBSYAKzUBKzUAAAD///+P/hQCXQYhAiYCNwAAAQcBS/6lAAAAFrkAAf/7QAkbFQkKJQEbESYAKzUBKzX//wDH/jsE9AW2AiYALgAAAQcCOQCJAAAADrkAAf+etBYSBgAlASs1//8Arv47BDMGFAImAE4AAAEGAjkxAAAOuQAB/7O0GRUMBiUBKzUAAAABAK4AAAQzBEoADgA1QBsNCwcHCAIFAwEFCAQPEAUCDQMGBggACQ8ECBUAPzM/MxI5ERczERIBFzkRMxEzETMzMTABMwEBIwEHESMRMxEUBzcDOdn+YQHA1/6Yh7+/DVQESv4a/ZwB8G/+fwRK/uOLiWYA//8AxwAAA/4HcwImAC8AAAEHAHb/bgFSABNACwEJDw4BAiUBDwUmACs1ASs1AAAA//8ApQAAAkEHrAImAE8AAAEHAHb/IgGLABNACwFrDQkCAyUBDQImACs1ASs1AAAA//8Ax/47A/4FtgImAC8AAAEGAjkzAAAOuQAB/8O0DwsBBSUBKzUAAP//AFz+OwFiBhQCJgBPAAABBwI5/u0AAAALtgEABwgBACUBKzUAAAD//wDHAAAD/gW3AiYALwAAAQcCOAEl/6MAEkAKAQkDAeUJCgEEJQErNQA/Nf//AK4AAAK2BhQCJgBPAAABBgI4OwAAC7YBogcHAwMlASs1AP//AMcAAAP+BbYCJgAvAAABBwFPAgj9aQALtgFTBgwCBCUBKzUAAAD//wCuAAACvwYUACYATwAAAQcBTwFM/TgAC7YBigQEAAAlASs1AAAAAAEAGwAAA/4FtgANAEhAKAMABwsLBAANCQADDg8DAQQKBwkGDwgfCAIJAwhAAgIABQMAC2lZABIAPysAGD8SOS8azV9eXRc5ERIBFzkRMzMRMxEzMTAzEQcnNxEzESUXBREhFcdlR6y4ARlJ/p4CfwH6OXpnAxT9WKaBzf4+pAAAAf/uAAACIwYUAAsAM0AaAg0IAAQECQUFDA0GCAkDAAIGAQcHBQoABRUAPz8SOS/NFzkREgE5ETMzETMyETMxMAE3FwcRIxEHJzcRMwFUg0zPtGlJsrQDZlt5jP1EAkhCeXMDIgAAAP//AMcAAAVOB3MCJgAxAAABBwB2AQgBUgATQAsBHQUmAU4dGQkTJQErNQArNQAAAP//AK4AAARMBiECJgBRAAABBgB2fQAAC7YBUR4aChQlASs1AP//AMf+OwVOBbYCJgAxAAABBwI5ANEAAAAOuQAB/7m0HRkJEyUBKzX//wCu/jsETAReAiYAUQAAAQYCOVgAAA65AAH/zbQeGgoUJQErNQAA//8AxwAABU4HcwImADEAAAEHAUwArAFSABNACwEeBSYBACAbCRMlASs1ACs1AAAA//8ArgAABEwGIQImAFEAAAEGAUwjAAALtgEEIRwKFCUBKzUA//8AAwAABN0FtgAnAFEAkQAAAQYCB+oAAA65AAH/drQWFgoKJQErNQABAMf+ewVOBbYAGQA7QB4KDQ0OFAgXEhIIAg4EGhsJEg4VDwMIDhIABWlZACIAPysAGD8zPzMSOTkREgEXOREzETMRMxEzMTABIic1FjMyNjcBIxYVESMRMwEzJjURMxEUBgPNYjpHVWZtAvzGCBGq1QMMCA6sx/57G5sUdW4Evv+m/OcFtvttmv8C+vpWxM0AAAAAAQCu/hQETgReAB0APEAfEw8PEBsHBwIQAx4fExAXEQ8QFRcLXVkXEAAFXVkAGwA/KwAYPysAGD8/ERI5ERIBFzkRMxEzETMxMAEiJzUWMzI1ETQmIyIGFREjETMXMzY2MzIWFREUBgMnVjs8Pop3faqatJEdCjS0bsrIj/4UGZEUrANrhYG70f3FBEqWUli/0vyPmq4AAP//AH3/7AXDBrwCJgAyAAABBwFNAMUBUgAdQBQCfxuPG58brxsEGwUmAgAbGgYAJQErNQArXTUA//8Acf/sBGgFagImAFIAAAEGAU0QAAAOuQAC//+0GxoHACUBKzUAAP//AH3/7AXDBz4CJgAyAAABBwFOAMEBUgAVtAIYBSYCuP//tBsjBgAlASs1ACs1AP//AHH/7ARoBewCJgBSAAABBgFODAAADrkAAv/9tBsjBwAlASs1AAD//wB9/+wFwwdzAiYAMgAAAQcBUwEZAVIAF0ANAwIrBSYDAkchJwYAJQErNTUAKzU1AAAA//8Acf/sBGgGIQImAFIAAAEGAVNcAAANtwMCPiEnBwAlASs1NQAAAAACAH3/7AbyBc0AFAAfAG5AQBgGDxMTHQ0AABEdBgQgIQ8SaVnYDwE6DwEJDwEPAA+gDwISAw8PAQsBE2lZARILDmlZCwMJFWlZCQQDG2lZAxIAPysAGD8rABg/KwAYPysREgA5GC9fXl1eXV1dKxESARc5ETMRMxEzETMxMCEhBiMgABEQACEyFyEVIREhFSERIQEiAhEQEjMyNxEmBvL89WZg/rv+oQFZAUFqWgMX/bQCJf3bAkz8NfL4+PB1VlUUAYoBaQFnAYcXov44oP32BIn+0v7g/t/+zyMEXCEAAAMAb//sBycEXAAeACoAMQBtQDscFQ4CAhYfCBYlFS8vJQgDMjMOAgULLhZeWRkuAQMPLgEQBi4uBQsrKAsoXVkRCxAAGGFZBSJdWQAFFgA/MysrABg/MysRADMREjkYL19eXV9dKxESADk5ERIBFzkRMxEzETMRMxEzETMxMAUgJwYGIyIAERAAMzIWFzY2MzISFRUhEiEyNjcVBgYBFBYzMjY1NCYjIgYlIgYHITQmBZj+3oA/0Yji/vMBCO2CzD48wH7N8P0nCAFEWppoXZr7LJKjoJOVoqGQBD17jwwCFoEU43FyATQBBwEKAStyb210/vfkbf6DHy2eKB8CO9DW0c3W0tPVn5mXof//AMcAAATbB3MCJgA1AAABBwB2AHsBUgAVtAIfBSYCuP/7tB8bDBMlASs1ACs1AP//AK4AAAMvBiECJgBVAAABBgB24gAAC7YBRBsXDAIlASs1AP//AMf+OwTbBbYCJgA1AAABBwI5AIEAAAAOuQAC/6K0HxsMEyUBKzX//wBi/jsDLwReAiYAVQAAAQcCOf7zAAAAC7YBBxUWCwolASs1AAAA//8AxwAABNsHcwImADUAAAEHAUwAHQFSABW0AiAFJgK4/6q0Ih0MEyUBKzUAKzUA//8AfgAAAzIGIQImAFUAAAEHAUz/egAAAA65AAH/6rQeGQwCJQErNf//AGj/7AQEB3MCJgA2AAABBwB2AE4BUgATQAsBcS8rExglAS8FJgArNQErNQAAAP//AGj/7AN5BiECJgBWAAABBgB27wAAC7YBWy0pEhclASs1AP//AGj/7AQEB3MCJgA2AAABBwFL/+oBUgATQAsBGjMtExglATMFJgArNQErNQAAAP//AGj/7AN5BiECJgBWAAABBgFLmQAAC7YBEjErEhclASs1AP//AGj+FAQEBcsCJgA2AAABBwB6ASsAAAAOuQAB/9y0LCYGACUBKzX//wBo/hQDeQReAiYAVgAAAQcAegDdAAAADrkAAf/UtCokEgAlASs1//8AaP/sBAQHcwImADYAAAEHAUz/5gFSABNACwEWMi0TGCUBMAUmACs1ASs1AAAA//8AaP/sA3kGIQImAFYAAAEGAUylAAALtgEeMCsSFyUBKzUA//8AFP47BFwFtgImADcAAAEGAjkZAAAOuQAB//y0CwwBACUBKzUAAP//ACH+OwK2BUYCJgBXAAABBgI5swAAC7YBFhobCQQlASs1AP//ABQAAARcB3MCJgA3AAABBwFM/94BUgATQAsBEgUmAQQUDwQGJQErNQArNQAAAP//ACH/7ALuBhQCJgBXAAABBgI4cwAAC7YBhyAgEBAlASs1AAABABQAAARcBbYADwBHQCYHCwsADAUJDA4CBRARCg4PDmtZBw8PAQsDDw8DDBIGAgMCaVkDAwA/KxEAMxg/EjkvX15dMysRADMREgEXOREzMxEzMTABESE1IRUhESEVIREjESE1Adv+OQRI/jcBMP7QuP7PAzEB4aSk/h+X/WYCmpcAAAABACH/7AK2BUYAHQBkQDUKDggTFxsbDAgVAwMZCAMeHxoKCwpfWRcAC2ALAg0DCwsGExEREBMNFhMWZFkTDwYAXVkGFgA/KwAYPysRADMRMzMYLxESOS9fXl0zKxEAMxESARc5ETMRMzMRMzMRMzMxMCUyNxUGBiMgETUjNTM1IzU3NzMVIRUhFSEVIRUUFgIhVUAaazn+xIuLm51IawE9/sMBK/7VVX8XigwUAV7ziPlWSOr8jPmI6Wpr//8AuP/sBR8HMwImADgAAAEHAVIAcwFSABNACwESBSYBARsnCAElASs1ACs1AAAA//8Aov/sBEQF4QImAFgAAAEGAVL9AAALtgEEHioUCiUBKzUA//8AuP/sBR8GvAImADgAAAEHAU0AkQFSAB1AFAF/FY8VnxWvFQQVBSYBABUUCAElASs1ACtdNQD//wCi/+wERAVqAiYAWAAAAQYBTRsAAAu2AQMYFxQKJQErNQD//wC4/+wFHwc+AiYAOAAAAQcBTgCLAVIAFbQBEgUmAbj//bQVHQgBJQErNQArNQD//wCi/+wERAXsAiYAWAAAAQYBThQAAA65AAH//7QYIBQKJQErNQAA//8AuP/sBR8H2wImADgAAAEHAVAAngFSACCyAgEVuP/AQA4JC0gVBSYCAQAYEggBJQErNTUAKys1NQAA//8Aov/sBEQGiQImAFgAAAEGAVAnAAANtwIBAhsVFAolASs1NQAAAP//ALj/7AUfB3MCJgA4AAABBwFTAOUBUgAXQA0CASUFJgIBSBshCAElASs1NQArNTUAAAD//wCi/+wERAYhAiYAWAAAAQYBU3EAAA23AgFMHiQUCiUBKzU1AAAA//8AuP49BR8FtgImADgAAAEHAVECIwAAAAu2AQQcGAgBJQErNQAAAP//AKL+PQRpBEoCJgBYAAABBwFRAvQAAAAOuQAB//y0ISILCiUBKzX//wAZAAAHVgdzAiYAOgAAAQcBSwFWAVIAFbQBJgUmAbj//bQmIAkYJQErNQArNQD//wAXAAAGMwYhAiYAWgAAAQcBSwDHAAAAC7YBAC0nCR4lASs1AAAA//8AAAAABIcHcwImADwAAAEHAUv/5gFSABNACwEWBSYBABYQBwIlASs1ACs1AAAA//8AAv4UBBQGIQImAFwAAAEGAUuzAAALtgEGJR8ACiUBKzUA//8AAAAABIcHKQImADwAAAEHAGr/9QFSABdADQIBHgUmAgECCRsHAiUBKzU1ACs1NQAAAP//AE4AAAREB3MCJgA9AAABBwB2AEIBUgATQAsBSRMPBQYlARMFJgArNQErNQAAAP//AFAAAANzBiECJgBdAAABBgB27wAAC7YBVRMPBQYlASs1AP//AE4AAAREBzcCJgA9AAABBwFPAT0BUgAWuQAB//xACQoQBQYlARMFJgArNQErNf//AFAAAANzBeUCJgBdAAABBwFPANsAAAAOuQAB//m0ChAFBiUBKzX//wBOAAAERAdzAiYAPQAAAQcBTP/vAVIAE0ALAQMWEQUGJQEUBSYAKzUBKzUAAAD//wBQAAADcwYhAiYAXQAAAQYBTIgAAA65AAH/+7QWEQUGJQErNQAAAAEArgAAAuUGHwAMACFADwoOBAUFDQ4FFQgAXVkIAQA/KwAYPxESATkRMxEzMTABIgYVESMRECEyFwcmAhdeV7QBa2RoL1oFiXV2+2IEngGBJ44fAAEAvv4UBBQFywAfAERAJBkdHQwIEhsICgIFICEJHBkcZFkMGRkQABAVXVkQBAAFXVkAGwA/KwAYPysREgA5GC8zKxEAMxESARc5ETMzETMxMAEiJzUWMzI2NREjNTc1NDYzMhcHJiMiBhUVIRUhERQGAUpJQ0Y7XEzX16K5XXUtZjleTgEU/vCk/hQTlRJgcwPCVD6FwbQrjCFkeY2M/D67rgAABAAAAAAFHweqABEAGAAiAC4AeUBHBDAJEgAYKQwDFQAjHiMVGQoMBi8wIUANFkghIRwmAA9gDwIJAw8PLBwYB2lZGBgKAxUDLEAsUCwCLAnvHAEcQA0SSBwFCRIAPzMvK10SOV0RFzM5LysREgA5GC9fXl0zEjkvKxESARc5ETMRMxEzMxEzMhEzMTABFAYHASMDIQMjASY1NDYzMhYTAyYnBgcDEzY2NzMVBgYHIxM0JiMiBhUUFjMyNgNxNi0CEb+o/aSgvAIQZHhnZ38SrBsvHyiqjzlfFtkesT9500AzMUE7NzNABZhBXhr7IQGJ/ncE3TaDYnd4/DYBqD2Sa2j+XASHQ4wnECqkKv70Nzs7NzY9OwAAAAUAXv/sA9cHqgAaACUAMQA9AEcAjkBXJjI4LBMjIwgLHhoBAR4ILDI+QwdJSO9C/0ICQkAJDkhCQD5QPgI+NTsfLwEfLy8vzy8DLwApECkgKQMJAykWAgAWCx9gWQsLFgAVFg9eWRYQBRteWQUWAD8rABg/KwAYPxI5LysREgA5GBDWX15d1F1xMzLWXcQrXRESARc5ETMRMxEzETMRMxEzMTAhJyMGBiMiJjUQJTc1NCYjIgYHJzY2MzIWFRElMjY1NQcGBhUUFgEUBiMiJjU0NjMyFgc0JiMiBhUUFjMyNgM1NjY3IRUGBgcDVCMIUqN8orgCD7psd1ebRDdTxGDHwv4Kl62iva1pAal9Zmd5eGhlfnFBMTJBOzgzP+MuahYBDBWkgJxnSaqbAU4QB0F9dzQghywysMD9FH2jlmMHB2pyVlwFN2V2dmNhdnZhNj09NjY9PQFdECp4HwwYaUQAAAD////+AAAGkQdzAiYAiAAAAQcAdgJUAVIAFbQCHQUmArgBXbQdGQUPJQErNQArNQD//wBe/+wGgQYhAiYAqAAAAQcAdgGLAAAAC7YDbERAABclASs1AAAA//8Aff/BBcMHcwImAJoAAAEHAHYBGwFSABNACwMtBSYDTC0pCgAlASs1ACs1AAAA//8Acf+8BGgGIQImALoAAAEGAHZYAAALtgM8LCgKACUBKzUA//8AaP47BAQFywImADYAAAEGAjkXAAAOuQAB/9O0LysGACUBKzUAAP//AGj+OwN5BF4CJgBWAAABBgI53AAADrkAAf/etC0pEgAlASs1AAAAAQEEBNkDuAYhAA0AKkAXDQcODwoNDwNvAwIDAwigDQEPDV8NAg0AL11dMzMvXRI5ERIBOTkxMAE2NjczFhcVIyYnBgcjAQR9Zxi4NMx/WoWDWHsE8IaAK2XMFzWDgDgAAQEEBNkDuAYhAAwAKkAXDAcNDgMKBQ8AbwACAACgCgEPCl8KAgoAL11dMy9dMhE5ERIBOTkxMAEzFhc2NzMVBgcjJicBBHtyaX5hf80zuDzABiFKc34/G81gZscAAAAAAQErBNkDiwVqAAMAIEATAwIEBQMPAC8AXwB/AJ8AzwAGAAAvXTIREgE5OTEwASEVIQErAmD9oAVqkQAAAQEfBNkDnAXsAA0ALEAaAwsODwoPAx8DLwOfAwQDAwegAAEPAF8AAgAAL11dMjIvXTMREgE5OTEwASImJzMWFjMyNjczBgYCWI2jCW4IVHNlYghxDawE2YqJRzs/Q4OQAAAAAAEAoAUAAXMF5QALACRAFQAGDA0DzwnvCQIACSAJAjAJgAkCCQAvXXFdMxESATk5MTATNDYzMhYVFAYjIiagPS0qPz8qLT0Fczw2Njw7ODgAAgFtBNkDLwaJAAsAFwAwQBkSBgAMBgwYGQ8JHwkBCcAVoAMBDwNfAwIDAC9dXTMazHEvMhESATk5ETMRMzEwARQGIyImNTQ2MzIWBzQmIyIGFRQWMzI2Ay99Zmd4eGdlfnFBMTJBOzgzPwW0ZXZ1ZGJ1dmE2PT02Nj09AAEAI/49AXUAAAAPACBADg0AAAoKBhARAyAIAQgMAC8vXTMREgE5OREzETMxMBcUFjMyNxUGIyI1NDczBga2MSssN0U606B/RkbuLi4NcxPBi3dCbQAAAAABAQIE2QPyBeEAFwAwQBsJFRgZFAWvDAEMgAkRYAABoADAAAIPAM8AAgAAL11dcjIyGs1dMjIREgE5OTEwASIuAiMiBgcjNjYzMh4CMzI2NzMGBgMSK1JPSSIxMg5oDHRhLVVOSCAwMQ9nDHQE2yUrJTs8eowlKyU7PHePAAAAAAIA3wTZA74GIQAJABMAK0AZDwUTCQQUFQ0PA28DAgMDE6AJAQ8JXwkCCQAvXV0zMy9dMxESARc5MTATNjY3MxUGBgcjJTY2NzMVBgYHI98jaCfFIa1CZwFpL2oZxCGtQmYE8i6xUBU4xDcZQbY4FTjENwAAAQH4BNkDFAZxAAkAHEAOCQUKCwOgCQEPCV8JAgkAL11dxBESATk5MTABNjY3MxUGBgcjAfgdNQrAD2k4bAT2S+dJFz/qWAAAAwEQBQwDjQa0AAgAEwAeAD1AIg4JGRQEFAgJBB8gAoAICBEWCwsczxEBABEgEQIwEYARAhEAL11xXTMzETMSOS8azBESARc5ETMRMzEwATY3MxUGBgcjJzQzMhYVFAYjIiYlNDMyFhUUBiMiJgH+OCTFHXE9Vu5fJjg4Jik2AcFeJTkyLCo0BYWPoBQ7rUsGZC81NTIyNWQvNS06MgAA//8AAAAABRsGCAImACQAAAEHAVT+HP+XABSzAhEAArj+9bQSEgUFJQErNQA/NQAA//8AkwJIAZEDXgIGAHkAAP///9AAAAR1BggAJgAofQABBwFU/dj/lwAUswEPAAG4/6i0ERECAiUBKzUAPzUAAP///9AAAAW4BggAJwArAJMAAAEHAVT92P+XABSzAQ8AAbj/krQREQYGJQErNQA/Nf///94AAANQBggAJwAsAO4AAAEHAVT95v+XABSzAQ8AAbj/urQREQYGJQErNQA/Nf///+L/7AYHBggAJgAyRAABBwFU/er/lwASQAoCGwACPRwcBgYlASs1AD81////zgAABZMGCAAnADwBDAAAAQcBVP3W/5cAFLMBDAABuP/etA4OBwclASs1AD81////4gAABjgGCAAmAXZCAAEHAVT96v+XABJACgEjAAE5JSUNDSUBKzUAPzX////m/+wCoAa0AiYBhgAAAQcBVf7WAAAAEEAJAwIBIxkpDwAlASs1NTUAAP//AAAAAAUbBbwCBgAkAAD//wDHAAAExQW2AgYAJQAAAAEAxwAABAAFtgAFAB9ADgMEBAEGBwQSBQJpWQUDAD8rABg/ERIBOTkRMzEwARUhESMRBAD9f7gFtqT67gW2AP//ACkAAAR9BbYCBgIoAAD//wDHAAAD+AW2AgYAKAAA//8ATgAABEQFtgIGAD0AAP//AMcAAAUlBbYCBgArAAAAAwB7/+wFwwXNAAMADwAbAFpANxAKBBYWAgMKBBwdAANpWRgAAUoAAXoAAUkAAW8AfwACDwCvAAILAwAABw0NGWlZDQQHE2lZBxMAPysAGD8rERIAORgvX15dcV1dcXErERIBFzkRMxEzMTABIRUhJRAAISAAERAAISAAARASMzISERACIyICAekCa/2VA9r+m/7B/rv+oQFfAUcBPQFl+3r27O/y9Ovt9wM3n0X+of5uAYoBaQFlAYn+c/6d/tz+0gEtASUBJQEn/tj//wBSAAACYgW2AgYALAAA//8AxwAABPQFtgIGAC4AAAABAAAAAATbBbYACgAaQAsJAQsMBQkKAwIJEgA/Mz8SORESATkyMTABASMBJicGBwEjAQLNAg7C/rxJIhRS/r/DAgwFtvpKA5rPhWPv/GQFtgAAAP//AMcAAAZ7BbYCBgAwAAD//wDHAAAFTgW2AgYAMQAAAAMASAAABCcFtgADAAcACwBMQC4JBgIDBwoGDA0AA2lZ2AABOgABCQABDwAAoAACEgMAAAoEBAdpWQQDCgtpWQoSAD8rABg/KxESADkYL19eXV5dXV0rERIBFzkxMBMhFSEDIRUhARUhNcMC6f0XUgON/HMDtvwhA0ygAwqi+46iogAA//8Aff/sBcMFzQIGADIAAAABAMcAAAUQBbYABwAlQBEEBQABBQEICQEFEgYDaVkGAwA/KwAYPzMREgE5OREzETMxMCEjESERIxEhBRC2/SW4BEkFFPrsBbYAAAD//wDHAAAEbwW2AgYAMwAAAAEASgAABF4FtgANAEFAIgMACAoJAg0GAgoABQ4PCQIABAMHBAdpWQQDAQsAC2lZABIAPysRADMYPysRADMREjk5ERIBFzkRMxEzETMxMDM1AQE1IRUhJwEBJSEVSgHf/i0Dzf1mYAHN/h4BTgH8mAJkAiGZpAL96v2iAqIAAAD//wAUAAAEXAW2AgYANwAA//8AAAAABIcFtgIGADwAAAADAGj/7AYEBcsAGQAiACsAZUA3JxQCGg0NGSsOBx4eDhQDLC0iJBgka1kCABgQGAIQAxgYDgAaKgwQECprWVAQYBACEBAOAAQOEwA/PxE5L10rEQAzETMREjkYL19eXTMrEQAzERIBFzkRMxEzMzMRMzMRMzEwATMVMzIWFhUUAgQjIxUjNSMiJAI1NDY2MzMTMzI2NTQmKwMiBhUUFjMzAtu2RK79hJT++rIntiuy/vySiP6sQbYZxdvLtji2N7XM2sgWBcu0i/iepf7+guHhhQECopv5jfxN1720z9GyvdcAAP//AAgAAASoBbYCBgA7AAAAAQBtAAAGAgW2ABsAREAjCgcQAAANARYTEwEHAxwdEAwbAwMMa1lvAwEDAwEUDggDARIAPz8zMxI5L10rEQAzETMREgEXOREzETMzETMRMzEwISMRIyIkJjURMxEQITMRMxEzIBERMxEUBgQjIwOPtC26/v+GuAGcGrQdAZq8jv79sy8BvIPwpAHj/iH+gwNc/KQBeQHj/h+m93wAAQBOAAAF9gXNAB8ARUAkFh0KAxgTCAMNEx0dGQcNBCAhEABpWRAEGgYIFgkICWlZGQgSAD8zKxEAMxI5ORg/KxESARc5ETMRMzMRMxEzETMxMAEiBhUUEhcVITUhJgI1EAAhIAARFAIHIRUhNTYSNTQCAyHq8aWx/bIBbJegAWQBOgE+AWKhlQFr/bKxp/MFK//34P6+gJOidAFYzQE0AV7+pP7Mzv6mc6KTfwFH3PYBAAD//wA8AAACfAcpAiYALAAAAQcAav8LAVIAF0ANAgEhBSYCAQIMHgYLJQErNTUAKzU1AAAA//8AAAAABIcHKQImADwAAAEHAGr/8wFSABdADQIBHgUmAgEACRsHAiUBKzU1ACs1NQAAAP//AHH/7ATNBnECJgF+AAABBgFUHwAAC7YCNTYxDxklASs1AP//AFj/7AOYBnECJgGCAAABBgFU0gAAC7YBYC8rEB0lASs1AP//AK7+FARMBnECJgGEAAABBgFUPwAAC7YBSB4aChQlASs1AP//AKj/7AKgBnECJgGGAAABBwFU/swAAAAOuQAB//m0GRgPACUBKzX//wCi/+wEeQa0AiYBkgAAAQYBVTsAABBACQMCAR0eLgQPJQErNTU1AAIAcf/sBM0EXgALACwAQ0AiGSMdCQ8dKSkEDwMtLioVDBIYDxIHXVkSECAADABdWSYMFgA/MysRADMYPysAGD8REjk5ERIBFzkRMxEzETMzMTAlMjY1NTQmIyARFBYXIgIREBIzMhYXMzY3MwYCFREUFjMyNxUGBiMiJicjBgYCUqWSlaT+24171O7033mgNg0YKY4YHDEjIB4QQSJXWhEPPKWBvtgM4cP+WM7QlQEqAQsBEgErVFRcOEX+/Wb+Vj80CoMJEVZRV1AAAAIArv4UBLAGHwAUACgAVEAsBQYGJhgQEBEDJgkdHSYhEQQpKgUhIiIhXVkiIgwAERsAFV1ZAAEMG11ZDBYAPysAGD8rABg/ERI5LysREgA5ERIBFzkRMxEzETMRMxEzETMxMAEyFhUQBRUWFhUUBCMiJicRIxE0NhcgEREWFjMgETQmIyM1MzI2NTQmApjd+v7Iu77+++9voUq0/uf+z0ebaAFQuKxtWJWemAYf0Lf+2jMIFce70OQhJP3jBjTg95b+tvyUJS8BLZifmI6GeYEAAAAAAQAK/hQEGwRKABMAIUAQEAQBBQQUFQoEBAEPBQ8BGwA/PzMSOREzERIBFzkxMAEjNBI3ATMTFhYXMzY2NxMzAQYCAiHDPC3+Q7vnJUEJCAZBHdm7/motN/4UWwEiewQ+/cBdxzAo100CSPvRdf7YAAIAb//sBGYGFAAeACoARUAjCRYAECUcEAMWHx8DHAMrLBMWECIAHwMGDV1ZBgAZKF1ZGRYAPysAGD8rERIAOTkREjkREgEXOREzETMRMxEzETMxMAEmJjU0NjMyFhcHJiYjIgYVFBYXFhYVFAAjIiQ1NBIBNCYnBgYVFBYzMjYCG4tzx6lovoBOZaRXUmBtpdWs/vLy5f7u4AJdd4u9wqqRnqgDpk+fYoSaLkCNODBMQUVrW3X0nez+9fjSswEB/nd8skkt1qGKqbUAAAEAWP/sA5gEXgAlAGVAORQTEyMEECMXHQsLARcQBCYnFAIlJQJdWUUlARklAQgl6CUCEA8lARQDJSUNGhohXVkaEA0HXVkNFgA/KwAYPysREgA5GC9fXl1eXV1dKxESADkREgEXOREzETMRMxEzETMxMAEVIyAVFBYzMjY3FQYjIiY1NDY3NSYmNTQ2MzIWFwcmJiMiFRQhAteV/sqUj1WrZIvj3PFxg2Nq579vrVdEY4RK+AE5AoWTvVldJy+eS6uUY4MmCxyAXYecJSmPLBycqAAAAQBx/nEDqAYUACQANkAZEgMFGgAXHgwABQwFJSYiCQMaFxgXXVkYAAA/KxEAMxgvLjMREgE5OREzETMzETMRMzMxMAUUByM2NTQmJicmJjU0PgM3DgIHIzUhFQYAAhUUFhYXFhYDqIGyfzFuWcrBLlR5ncoHOVSb9gL61/7hhzt7ppiJUpGsqWUoLSYQI9jGZ7amn6S2AQIDApWHtP69/uSjYHZHIh9xAAAAAAEArv4UBEwEXgAUADNAGQwICAkUAAkAFRYMCRAKDwkVABsQBF1ZEBAAPysAGD8/PxESORESATk5ETMRMxEzMTABETQmIyIGFREjETMXMzY2MzIWFREDmnaAqZm0kRsKM7hvysT+FASqhIW/z/3HBEqWUVnEz/tJAAMAcf/sBFIGHwALABIAGQBnQEAWEBAGABcPBg8aGxYQXVnnFgHWFgGFFpUWtRYDSRZZFgJpFgFYFgEPFo8WnxYDCwMWFgMJCRNdWQkBAwxdWQMWAD8rABg/KxESADkYL19eXV1dcV1dXSsREgE5OREzMxEzETMxMAEQAiMiAhEQEjMyEgEyEhMhEhITIgIDIQICBFL5+/T59vf3/f4MoJcH/YsEl5yWlwoCcw2aAwT+bv56AZMBhQGWAYX+bPv0ASgBKP7P/uEFDP71/uQBIAEHAAAAAQCo/+wCoARKAA8AH0AOAQ4OCBARDw8LBF1ZCxYAPysAGD8REgE5OREzMTABERQWMzI2NxUGBiMiJjURAVpKVCxiGhtwNqSTBEr8+mNiDwiKDBSqrAMI//8ArgAABDMESgIGAPoAAAAB//T/7ARWBiEAIgAxQBgAFggBIyQBHx8LABULBl1ZCwEYE11ZGBYAPysAGD8rABg/EjkRMxESATk5MzIxMCMBJy4CIyIHNTYzMhYWFwEWFjMyNxUGIyImJwMmJyMGBwMMAdk3IjFDMT41RENefFs4AWIULyQYJTJDSlogllURCCFQ+gQ1mllCIQyRETyBm/wlOTYKhRhKWwGk81N+vv3BAAAA//8Arv4UBE4ESgIGAHcAAAABAAAAAAQMBEoAEAAaQAsADBESBxALAA8QFQA/PzIRORESATkyMTARMxMeAxczNhIRMxACByO63A0jIhwICKyass/hwgRK/bAlYWNbHrABtQFN/pT+BOIAAQBv/nEDqAYUADAAZEAzKCsJFA8ZJQQDAxQMBh0AFAYlKysGAAMxMgQZGBgZX1kPGAERBhgYDSIuKA8MDQxdWQ0AAD8rABgQxC8uMxI5L19eXSsREgA5ERIBFzkRMxEzETMRMxEzETMRMzMRMxEzMTATNDY3NSY1NDY3BiMjNSEVIyIGBhUUFjMzFSMiBhUUHgIXFhYVFAcjNjY1NCYnJiZvm4PZjKOQZz4CxTaC3H2irKqwrtEyXIRSlYR7qjdCd4PIywGmidAqDD7Zc50vDJWLTo5dcGmJqpBOYTsjESFuWYqzSpQyNjsYIskAAAD//wBx/+wEaAReAgYAUgAAAAEAGf/sBQQESgAUADdAHQoLEwcRAwcLDQUVFgsVEgkNDw1dWQ8PBQBdWQUWAD8rABg/KxEAMzMYPxESARc5ETMRMzEwJTI3FQYjIhERIREjESM1NyEVIxEUBIs1JTFW4/4vst+TBFjXfxSNGgEGAsL8TAO0TkiW/Up/AAACAKL+FARmBF4AEAAcADNAGQYVCQkKABoKGh0eChsOEV1ZDhADF11ZAxYAPysAGD8rABg/ERIBOTkRMxEzETMzMTABEAIjIicjFhURIxEQEjMyACUiBhURFjMyNjU0JgRm/ueweQoKtv3s2wEA/h2ZknSzoY6NAiX+8P7XXETN/t0EHwELASD+0JnKzP60ZNTQ0tAAAQBx/nEDqgReAB8ALkAVGgcKDQAUBw0UDSAhBBEKFx1hWRcQAD8rABgvLjMREgE5OREzETMRMxEzMTABFBYWFxYWFRQHIzY2NTQmJicmJjUQADMyFhcHJiMiBgErOYqfkYp7qjRHL3Fa0MMBEf9Snjk5jGyqpAIIgIBOIh9vXJCtRZc0Ji0oECj81gEfATkiGZY01QAAAAACAHH/7AS+BEoADQAZADVAGgwUCwAOBwAUBxQaGwwXCRddWQkPBBFdWQQWAD8rABg/KxEAMxESATk5ETMRMxEzETMxMAEUBgYjIgA1ECEhFSEWARQWMzI2NRAnIyIGBGZ755zt/vYCVAH5/vay/MWlnZumqj/awAH4nO+BASH/Aj6Wp/7/wcrAswEFu8sAAAABABT/6QOeBEoAEgAsQBcDDgEIDhAEExQCEBIQXVkSDwsFXlkLFgA/KwAYPysRADMREgEXOREzMTABFSERFDMyNxUGBiMiJjURITU3A57+VMRoRidxMLeq/tmWBEqY/Z7VFocPEqupAnVQSAABAKL/7AR5BEoAFAApQBMOCwYDEgsDCxUWDgQPAAhdWQAWAD8rABg/MxESATk5ETMRMxEzMTAFIiYRETMRECEyNjU0JiczFhYVEAACderptAEto50bJbQnG/77FPsBCwJY/a7+h+r1gNGbleN8/sL+1AAAAAIAcf4UBVwEXgAYACIARkAkBwoKBCAYGAwAExkZAAQDIyQGEAAbEBxdWRAQIAwBDF1ZFwEWAD8zKxEAMxg/KwAYPz8REgEXOREzETMzETMRMxEzMTABESQAERA3FwYGFRAFETQ2MzISFRQCBgcRATQmIyIGFRE2NgKD/vv+89GLWU8BXqqaudyI+qcBc3hlR0+vxP4UAdoOASEBDgEq/2B133v+fyMCYrbF/tr5sf78kwn+JgQpuNRycv2cEOgAAAH/6f4UBFwEUAAgADdAHRcIGA8HGB4EISIVBRcAABxeWQAGDxcbEQxdWREbAD8rABg/P8QrERIAOTkREgEXOREzMjEwEzIWFhcTATMBExYWMzI3FQYjIiYnAwEjAQMmJiMiBzU2uDpQPy2LATrA/lK/KVFCLDBBPnOOPJL+nMEB06geRTQoHDUEUC1ZdP6gAlT8/v4ca1EIixF2oQF9/WwDSAG0UlwMjREAAAABAKL+FAWaBhIAGQBBQCEHBBYTAQ4OGA8KBAQPEwMaGxkABxQPDxsBGBAYXVkNEBYAPzMrEQAzGD8/Mz8REgEXOREzETMzETMRMxEzMTABETY2NTQmJzMSFRAABREjESQAEREzERAFEQNov8IdJLJA/uL+7LD+9v70sgFkBhL6cxLeyIPlpf7s8v7r/tAR/iYB2gkBIAESAiH92f55GQWPAAAAAAEAc//sBc8ESgAnAENAIRwZBwoKAxMQIBkZEAMDKCkmBhERABwGDxYNAA1dWSMAFgA/MisRADMYPzMSOS8RORESARc5ETMRMxEzETMRMzEwBSICETQSNzMGAhUUFjMyNjURMxEUFjMyNjU0AiczFhIVEAIjIicjBgH6t9A7PrhCO3VqX2asZV1oejtCuEI30LfeRQpBFAEoAQChAQmMlf78n73UjnwBNv7KgIrKx50BCJOa/v6a/v/+2bi4AAAA////6v/sAqAF1wImAYYAAAEHAGr+uQAAAA23AgEJECIPACUBKzU1AP//AKL/7AR5BdcCJgGSAAABBgBqNwAAELECAbj/+7QVJwQSJQErNTX//wBx/+wEaAZxAiYAUgAAAQYBVCMAAAu2AjwhHQcAJQErNQD//wCi/+wEeQZxAiYBkgAAAQYBVCkAAAu2ASEeGgQSJQErNQD//wBz/+wFzwZxAiYBlgAAAQcBVADTAAAAC7YBODEtAyAlASs1AAAA//8AxwAAA/gHKQImACgAAAEHAGoAIQFSABdADQIBIQUmAgESDB4CCyUBKzU1ACs1NQAAAAABABT/7AVUBbYAHQBEQCUWDg4PGwgIFAIPEQUeHxYNaVkWFhIPEhUREhFpWRIDAAVpWQATAD8rABg/KxEAMxg/EjkvKxESARc5ETMRMxEzMTAFIic1FjMyNjU1NCYjIREjESE1IRUhESEyFhUVFAYD32c0O1hjZH+H/oe3/qwDx/5EAYfQ3sUUGKAVdm6DfG39IQUUoqL+bcGyj77VAAD//wDHAAAEAAdzAiYBYQAAAQcAdgBeAVIAE0ALAQ8FJgFLDwsFASUBKzUAKzUAAAAAAQB9/+wE6QXNABgASkAqAwYRFgwFEQQZGgMGaVnZAwE6AwEDDwMBDwYDAw4UFABpWRQEDglpWQ4TAD8rABg/KxESADkYL19eXV9dXSsREgEXOREzMzEwASIEByEVIRIAMzI3FQYjIAAREAAhMhcHJgNC3P74GgLI/TMMAQXypMqe6f6z/qEBeAFR67hLrwUp9eqg/vX+7jqgOwGEAW0BXQGTWp5UAAD//wBo/+wEBAXLAgYANgAA//8AUgAAAmIFtgIGACwAAP//ADwAAAJ8BykCJgAsAAABBwBq/wsBUgAXQA0CASEFJgIBAgweBgslASs1NQArNTUAAAD///9g/nsBdQW2AgYALQAAAAIAAv/pBy8FtgAaACMAX0A3FxsbBAAfHwQNAyQlFyNpWdgXAToXAQkXAQ8AF6AXAhIDFxcEFQQba1kEEhUGaVkVAwsQa1kLEgA/KwAYPysAGD8rERIAORgvX15dXl1dXSsREgEXOREzETMRMzEwARQEISERIQICBgYjIic1FjMyNjYSEyERMyAEATMyNjU0JiMjBy/+7P75/rH+mzhVU4xtRUA0PTpROEdIArh5ARgBIf1Ogb61uthiAazP3QUU/lf98P11GZoZbPIBxQIQ/ZbP/iGFiYZ6AAIAxwAAB1wFtgASABsAYkA3CwcHCA8TEwwEABcXBAgDHB0bBgsGaVkP2AsBOgsBCQsBDwALoAsCEgMLCwQNCQMIEgQTa1kEEgA/KwAYPz8zEjkvX15dXl1dXTMrEQAzERIBFzkRMxEzMxEzETMRMzEwARQEISERIREjETMRIREzETMgBAEzMjY1NCYjIwdc/uv+/f6u/Y24uAJzuncBGAEh/VCBvrO61mIBrNHbAqr9VgW2/ZYCav2Wz/4hhYmGegAAAAABABQAAAVUBbYAEwA+QCASBgAMDA0FBgYNDwMUFQALaVkAABAGDRITDxAPaVkQAwA/KxEAMxg/MxI5LysREgEXOREzETMRMxEzMTABITIWFREjETQmIyERIxEhNSEVIQIfAY3N27h3hf5/uf6uBAD+CwN/vLf99AH2e2z9IwUSpKQAAP//AMcAAATyB3MCJgG0AAABBwB2AKoBUgATQAsBFAUmAR4UEAUAJQErNQArNQAAAP//ABf/7AT+B2ICJgG9AAABBwI2AEYBUgATQAsBFwUmAQgaIgkSJQErNQArNQAAAAABAMf+fwUQBbYACwAwQBgIBQIDAAkJAwUDDA0KBgMDIgUIaVkBBRIAPzMrABg/PzMREgEXOREzETMRMzEwISERIxEhETMRIREzBRD+NL3+QLgC27b+fwGBBbb67gUSAP//AAAAAAUbBbwCBgAkAAAAAgDHAAAEgwW2AAwAFQBXQDEHAAkNDQQAEQQRFhcJFWlZ2AkBOgkBCQkBDwAJoAkCEgMJCQQFBQhpWQUDBA1rWQQSAD8rABg/KxESADkYL19eXV5dXV0rERIBOTkRMxEzETMRMzEwARQEISERIRUhETMgBAEzMjY1NCYjIwSD/vz+9P5UA2T9VNkBGAET/PzhvKmw08MBrNjUBbai/jjG/hh/j4x0AAD//wDHAAAExQW2AgYAJQAA//8AxwAABAAFtgIGAWEAAAACAAz+fwVaBbYADQATAD9AIBMEBAUMDgABAQ4FAxQVAQUiChBqWQoDDBMGAwZpWQMSAD8rEQAzMxg/KwAYPzMREgEXOREzETMRMxEzMTABIxEhESMRMzYSEyERMyERIQYCBwVasPwSsHGU2BMCpLr+j/61E81//n8Bgf5/AiX8As4BSPruBGz5/Wjb//8AxwAAA/gFtgIGACgAAAABAAIAAAbRBbYAEQA9QCEBEQYNDQMOCAoKCQ4AEQUSEwACDwYMCQYRBwQBAw4LERIAPzMzPzMzEhc5ERIBFzkRMxEzMxEzETMxMAEBMwERMxEBMwEBIwERIxEBIwJO/cnMAi+xAi/M/ckCStP9xbH9w9MC8ALG/TwCxP08AsT9PP0OAuX9GwLl/RsAAAEATv/sBEYFywAnAF1AMwMEBBwjDAAcBxMTHBcMBCgpAxcYGBdrWToYAQMPGN8YAg8GGBgKJSUfa1klBAoQa1kKEwA/KwAYPysREgA5GC9fXl1fXSsREgA5ERIBFzkRMxEzETMRMxEzMTABFAYHFRYWFRQEISAnNRYWMzI2NTQmIyM1MzI2NTQmIyIGByc2ITIEBCe2pLq//sr+6P75o2PjYsbJ4uDRxtnXn4dyt21Y0wEd4QECBGCOtRkIGbSRzeVPqC8xkomDh5qRe2p7Nkd9mMQAAQDJAAAFYAW2ABEAKEASBREJDgoRChITDgURBwADChESAD8zPzIROTkREgE5OREzMxEzMTATMxEUAgczATMRIxE0EjcjASPJrAoFCQMkzawOAwn82s0FtvzdVf7oKgS6+koDG2gBFSn7PwD//wDJAAAFYAdiAiYBsgAAAQcCNgDjAVIAE0ALARIFJgEbFR0RCSUBKzUAKzUAAAAAAQDHAAAE8gW2AAoAMEAXCQAADAcDAwQECgsMBwIKAwQIBQMBBBIAPzM/MxIXORESATk5ETMRMxEzETMxMCEjAREjETMRATMBBPLe/Wu4uAKF0f2FAuX9GwW2/TwCxP06AAAAAAEAAv/pBOMFtgASAClAFAABCgETFAESEQNpWREDCA1rWQgTAD8rABg/KwAYPxESATk5ETMxMCEjESEHAgIGIyInNRYzMjYSEyEE47j+MR8/XpeCSjs0PU9dbTcDIAUU7v4U/lanGZoZxwK+Aa4AAP//AMcAAAZ7BbYCBgAwAAD//wDHAAAFJQW2AgYAKwAA//8Aff/sBcMFzQIGADIAAP//AMcAAAUQBbYCBgFuAAD//wDHAAAEbwW2AgYAMwAA//8Aff/sBM8FywIGACYAAP//ABQAAARcBbYCBgA3AAAAAQAX/+wE/gW2ABYAKUAUEggCCQMXGAgNDQARCQMABWlZABMAPysAGD8zEjkRMxESARc5MzEwBSInNRYzMjY3ATMBFhczNzcBMwEOAgElcVZbZmuDPf3NygGiGRIICB8BXsP+LVOIrxQerilkhAQ//NMvORhSAyv76rqqUAAAAP//AGj/7AYEBcsCBgFzAAD//wAIAAAEqAW2AgYAOwAAAAEAx/5/BcMFtgALADJAGQgFAAkCAwMJBQMMDQoGAwMiAAgFCGlZBRIAPysRADMYPz8zERIBFzkRMxEzETMxMCUzESMRIREzESERMwUQs7H7tbgC27ai/d0BgQW2+u4FEgAAAAABAKQAAATPBbYAEwArQBULCAARAQgBFBUFDmlZBQUBEgkDARIAPz8zEjkvKxESATk5ETMzETMxMCEjEQYGIyImNREzERQWMzI2NxEzBM+4lcho0N64fIxfsaO4Alg1J8GyAkf903Z1HjYCxAABAMcAAAeDBbYACwAxQBgEAQgFAAkJBQEDDA0KBgIDCAQBBGlZARIAPysRADMYPzMzERIBFzkRMxEzETMxMCEhETMRIREzESERMweD+US4Aki4Akq6Bbb67gUS+u4FEgABAMf+fwgXBbYADwA7QB4DAAcECwgNDg4IBAAEEBEJBQEDDiILBwMAA2lZABIAPysRADMzGD8/MzMREgEXOREzETMRMxEzMTAzETMRIREzESERMxEzESMRx7gCObsCO7ixsQW2+u4FEvruBRL67P3dAYEAAAIAEAAABSEFtgAMABUAVUAxCQ0NBAAREQQGAxYXCRVpWdgJAToJAQkJAQ8ACaAJAhIDCQkEBwcGaVkHAwQNa1kEEgA/KwAYPysREgA5GC9fXl1eXV1dKxESARc5ETMRMxEzMTABFAQjIREhNSERMyAEATMyNjU0JiMjBSH+9/3+Sf6sAgvnAQoBFfz67bKor8fRAazQ3AUUov2W0f4jhYmGegAAAwDHAAAGFwW2AAoAEwAXAFVAMQcLCwQADxQVFQ8EAxgZBxNpWdgHAToHAQkHAQ8AB6AHAhIDBwcEFgUDFRIEC2tZBBIAPysAGD8/MxI5L19eXV5dXV0rERIBFzkRMxEzETMRMzEwARQEIyERMxEzIAQBMzI2NTQmIyMBIxEzBH3++f/+ULjhAQkBFP0C57KmrMbNBJi5uQGsz90Ftv2W0v4ih4mGeP1WBbYAAAIAxwAABLoFtgAKABIASkAqBwsLBAAOBA4TFAcSaVnYBwE6BwEJBwEPAAegBwISAwcHBAUDBAtrWQQSAD8rABg/EjkvX15dXl1dXSsREgE5OREzETMRMzEwARQEIyERMxEhIAQBISARNCYjIwS6/uz+/h+4ARIBDQEc/MUBFwFmt8r8AazO3gW2/ZbV/icBDoV7AAABAD3/7ASRBcsAGwBZQDUDDwkZFhYXDwMcHRgXaVnZGAE6GAEIGAEPMBhAGAIAGKAYAhwDGBgMBQUAaVkFBAwTaVkMEwA/KwAYPysREgA5GC9fXl1xXl1dXSsREgEXOREzMxEzMTABIgcnNjMyBBIVEAAhIiYnNRYWMyAAEyE1ISYkAdunq0yu8tkBOaL+k/6rca9lVq9jAQgBDQj9OQLFFP75BSlOmlaw/rrh/or+bhgjoBcjARgBB6Le/QAAAAACAMf/7AfsBc0AEgAeAF9ANwwICAkTDQYAGRkGCQMfIAwHaVnYDAE6DAEJDAEPAAygDAISAwwMCQoDCRIQHGlZEAQDFmlZAxMAPysAGD8rABg/PxI5L19eXV5dXV0rERIBFzkRMxEzMxEzETMxMAEQACEgAAMhESMRMxEhEgAhIAABEBIzMhIREAIjIgIH7P6s/s7+1P6rDP6muLgBXhcBUAEeATIBWPuu5eHi6eXi4+cC3f6e/nEBbgFQ/VYFtv2WATUBTP5y/p7+3v7QASwBJgElASn+0wACAC0AAARWBbYADQAVAEdAJQMSAhIGCxUMBgwWFwMAFQBrWQAVEBUCIQMVFQkMAhIJD2tZCQMAPysAGD8zEjkvX15dKxEAMxESATk5ETMzETMzETMxMAEBIwEmJjU0JCEhESMRESMiBhUQITMCf/6F1wGam5IBEQERAZq43beyAXHVAl79ogJ/Ms6extP6SgJeAruAhf7m//8AXv/sA9cEXAIGAEQAAAACAHX/7ARcBh8AGAAiAExAKAYTIQATGwAbIyQMFhAQHV1ZDxA/EAILAxAQFgUFBmFZBQEWGV1ZFhYAPysAGD8rERIAORgvX15dKxESADkREgE5OREzETMRMzEwExASNzYlFwcGBwYGBzM2NjMyEhUQACMiAAUgERAhIgYGBxB13e3fARMfd+iNkJEKDTrBbcrk/vbs6v75AgABKf7zSYlvIwKTAW8BjTQtL54TJh0g5dpRYf786v77/uEBZtEBfQFqPGI7/fIAAAMArgAABFgESgAOABYAHwBqQDwDBAQXHBQUCwAXBw8PFwsDICEDExwcE11ZRRwBGRwBCBzYHOgcAxAPHAEUAxwcCwwMG11ZDA8LFF5ZCxUAPysAGD8rERIAORgvX15dXl1dXSsREgA5ERIBFzkRMxEzETMRMxEzETMxMAEUBgcVFhYVFAYjIREhIAM0JiMhESEgAzQmIyERITI2BDV4b4t/5df+EgHsAZuRkIv+2QErARcfd3j+zAETk30DNWpvFAkTf2ucpgRK/QJcSv6fApRNQv7TSQAAAAEArgAAA0wESgAFAB9ADgIDAwAGBwMVBAFdWQQPAD8rABg/ERIBOTkRMzEwASERIxEhA0z+FrQCngOy/E4ESgACACf+gwR9BEoADQATAD9AIBMEBAUMDgABAQ4FAxQVAQUiChBeWQoPDBMGAwZdWQMVAD8rEQAzMxg/KwAYPzMREgEXOREzETMRMxEzMTABIxEhESMRMzYSEyERMyERIwYCBwR9rv0ErFiFlAQCQaD+svQPjWb+gwF9/oMCFbkB5wES/E4DJN3+Q4oA//8Acf/sBBsEXgIGAEgAAAABAAIAAAX6BEoAEgA3QB8CCQkSCgYEBQoOEA0HExQOEgsCBQgGDQMAEA8KBw0VAD8zMz8zMxIXORESARc5ETMzETMxMAEzEQEzAQEjAREjEQEjAQAnMwECqqgBvsP+OwHszf4lqP4lzQHs/sqPxQG8BEr96wIV/ev9ywIt/dMCLf3TAjUBcKX96wAAAAEARP/sA48EXgAjAGVAOQ8QEAIIGA0CEx4eAiIYBCQlDyIjIyJdWUUjARkjAQgj6CMCEA8jARQDIyMWCgoEXVkKEBYbXVkWFgA/KwAYPysREgA5GC9fXl1eXV1dKxESADkREgEXOREzETMRMxEzETMxMAEgNTQjIgYHJzYzMhYVFAcVFhYVFAYjIic1FjMyNjU0JiMjNQGHATf5T4hfP6vUwdrOfXb62/KEt72NmJqflAKFqJweKI9Mmoe7OAgkiGeXrEeiVl5cXluTAAEArgAABHUESgANADBAFwEDAwwGCAoHDAcODwMKDA0PBxUEDwwVAD8/Pz8SOTkREgE5OREzMzMRMxEzMTABEQcHATMRIxE3NwEjEQFYCAQCTN2oAwX9uN8ESv1iwjgDmPu2AoeLhPxqBEoA//8ArgAABHUGEAImAdIAAAEGAjZGAAALtgEBERkNBiUBKzUAAAEArgAABCMESgAKACtAFgoGBgcDAQIHBAsMCgUCAwcACA8EBxUAPzM/MxIXORESARc5ETMRMzEwATMBASMBESMRMxEDN8X+KwH80f4QtLQESv3v/ccCLf3TBEr96wAAAAEADv/yA/IESgAQAClAFAABCQEREgEVDwNdWQ8PBwxkWQcWAD8rABg/KwAYPxESATk5ETMxMCEjESECAgYjIic1FjMyEhMhA/K3/r0cX5l3QR4VI26DJQKWA7T+mP5lvw6HCAHQAfsAAQCuAAAFSARKABUAKkAUEBEFBhEGFhcPBwADCwMSDwsGERUAPzMzPzMSFzkREgE5OREzETMxMCU2NwEzESMRBgcBIwEmJicRIxEzARYC9h4uAR7ooiMv/u6S/u4UJxOi4QEVJaxtdAK9+7YDiW14/VwCqDByP/x3BEr9Xl4AAAABAK4AAARqBEoACwBVQDIBCQkKBQIGCgYMDQEIXVkEAQH0AQEGtQEBA48BAU0BXQECfQEBBb8BAQEBCgMLDwYKFQA/Mz8zEjkvXV9dcV1fXV9dcSsREgE5OREzMxEzETMxMAERIREzESMRIREjEQFiAlS0tP2stARK/jcByfu2Aen+FwRKAAD//wBx/+wEaAReAgYAUgAAAAEArgAABE4ESgAHACVAEQABBAUBBQgJBQEVAgdhWQIPAD8rABg/MxESATk5ETMRMzEwISMRIREjESEBYrQDoLT9yARK+7YDsAAAAP//AK7+FAR7BF4CBgBTAAD//wBx/+wDkwReAgYARgAAAAEAKQAAA6QESgAHACVAEgIDAAMFAwgJAxUBBQYFXVkGDwA/KxEAMxg/ERIBFzkRMzEwASERIxEhNSEDpP6Zsv6eA3sDsvxOA7KYAP//AAL+FAQUBEoCBgBcAAAAAwBv/hQFXAYUABEAFwAcAExAJxIJDxsEBAwUBQAYGAUJAx0eDQAFGxoVDBVdWQ8MEBsUBhRdWQMGFgA/MysRADMYPzMrEQAzGD8/ERIBFzkRMxEzMzMRMzMRMzEwARQABREjESYANTQAJREzERYABRAFEQYGBRAlESQFXP7h/wCw/P7eASABBKr9ASL7zwFovKwDd/6bAWUCJfX+1BT+JAHcFQEs9PoBJxQBuv5GGf7U8P6DJQNCE9O6AXcn/MAnAAAA//8AJQAABBcESgIGAFsAAAABAK7+gwTpBEoACwAyQBkGAwoHAAEBBwMDDA0BIggEDwoGAwZdWQMVAD8rEQAzGD8zPxESARc5ETMRMxEzMTABIxEhETMRIREzETME6bD8dbQCOLSb/oMBfQRK/E4DsvxMAAAAAQCYAAAEOQRKABIAK0AVAREJBgoRChMUDgNdWQ4OCgcSDwoVAD8/MxI5LysREgE5OREzMxEzMTABERQzMjY3ETMRIxEGBiMiJjURAUzTXKVltLRusWykvgRK/nC8Nz4B1/u2AelHOKyYAZwAAQCuAAAGfwRKAAsAMUAYCAUACQQBAQkFAwwNCgIGDwAIBQhdWQUVAD8rEQAzGD8zMxESARc5ETMRMxEzMTAlIREzESERMxEhETMD8AHbtPovtAHZtZgDsvu2BEr8TgOyAAAAAAEArv6FBx0ESgAPADtAHgwJAA0EAQYHBwENCQQQEQciDgIKDwQADAkMXVkJFQA/KxEAMzMYPzMzPxESARc5ETMRMxEzETMxMCUhETMRMxEjESERMxEhETMD8AHbsqC1+ka0Adm1mAOy/Ez97wF7BEr8TgOyAAAAAAIAJQAABSMESgAKABIAaUBAABAQBgMLCwYIAxMUAA9dWYQAlAACBkUAAQMfAAENAN0A7QADEAVgAHAAAg8AARQDAAAGCQkIXVkJDwYQXlkGFQA/KwAYPysREgA5GC9fXl1xX15dXV9dX10rERIBFzkRMxEzETMxMAEhIBEQISERITUhATQmIyERISACNwEvAb3+Qf4f/qICEgI6eZj+1wEvAQsCh/7J/rADspj8/FpQ/qEAAAMArgAABYsESgAKABIAFgBpQEAAEBAIBAsTFBQLCAMXGAAPXVmEAJQAAgZFAAEDHwABDQDdAO0AAxAFYABwAAIPAAEUAwAACBUJDxQVCBBeWQgVAD8rABg/PzMSOS9fXl1xX15dXV9dX10rERIBFzkRMxEzETMRMzEwASEyFhUUBiMhETMBNCYjIREhIAUjETMBYgEd1szY0v43tAINd5D++gEEAQkCHLS0Aoecm6epBEr8/FlT/p+RBEoAAgCuAAAEVARKAAkAEgBeQDkADw8HAwoHChMUAA5dWYQAlAACBkUAAQMfAAENAN0A7QADEAVgAHAAAg8AARQDAAAHCA8HD15ZBxUAPysAGD8SOS9fXl1xX15dXV9dX10rERIBOTkRMxEzETMxMAEhIBEUBiMhETMBNCYjIREhMjYBYgFGAaze1v4OtAI+fJH+zwE2f4kCh/7JpKwESvz8WFT+n1wAAAAAAQA7/+wDgwReABgAXUA5EAIWCgcHCAIDGRoJCF1ZlQkBaQkBOAkBWAkBbwl/CQIPCR8JnwkDCwMJCQATEw1hWRMQAAVhWQAWAD8rABg/KxESADkYL19eXXFdcV1dKxESARc5ETMzETMxMAUiJzUWMyATITUhJiYjIgcnNjYzIAAREAABXql6mo0BUBf94wIbDqKcaZczQKVMAQEBCf7hFDucPgFnk6icNpIdIv7d/ur+8f7WAAAAAAIArv/sBj8EXgASAB4AbUBBDAgICRMNBgAZGQYJAx8gDAddWYQMlAwCBkUMAQMfDAENDN0M7QwDEAUPDAEUAwwMCQoPCRUQHF1ZEBADFl1ZAxYAPysAGD8rABg/PxI5L19eXV9eXV1fXV9dKxESARc5ETMRMzMRMxEzMTABEAAjIgInIREjETMRITY2MzIAARQWMzI2NTQmIyIGBj/+/eLW/Q7+6bS0ARkW/NHeAQP88o6dnYyOm52OAif+8v7TAQvy/hcESv435fj+zv770NbW0M3T0wAAAAIAIQAAA8sESgANABUAPUAeAg4BDgUKEQsFCxYXAg0QDV1ZEBAICwEVCBNdWQgPAD8rABg/MxI5LysRADMREgE5OREzMxEzMxEzMTAzIwEmJjU0NjMhESMRIQEUISERISIG8tEBOX6CzrcB7LT+9f78AQwBA/7bc3cBzSCid5is+7YBtAFQugFqWgD//wBx/+wEGwXXAiYASAAAAQYAagYAAA23AwIRHC4DCiUBKzU1AAAAAAEAEv4UBE4GFAAnAIFAShkHEhAXGx0DDw8UECUHBwIQAygpHRAhGhITEl9ZFwgTGBMCEQ8TARQDExMhFSELXVm/IQEAIRAhICEDCQMhIRAVABAVAAVdWQAbAD8rABg/PxI5L19eXV0rERIAORgvX15dXl0zKxEAMxESORESARc5ETMRMzMRFzMRMxEzMTABIic1FjMyNRE0JiMiBhURIxEjNTM1MxUhFSEVFAczNjYzMhYVERQGAzFQOTc6gXd9qZm2nJy0AYr+dggKMbRzyMqQ/hQZlBWqA0KFgbvT/fAE14W4uIW0PVtOXL/S/LqgqgAAAP//AK4AAANMBiECJgHNAAABBgB29wAAC7YBSw8LBAUlASs1AAABAHH/7AOwBF4AGgBfQDoPEhIDCRkZEQMDGxwPEl1ZlQ8BaQ8BOA8BWA8Bbw9/DwIPDx8Pnw8DCwMPDwAGBgxhWQYQABVhWQAWAD8rABg/KxESADkYL19eXXFdcV1dKxESARc5ETMRMxEzMTAFIgAREAAzMhYXByYjIgYHIRUhFhYzMjY3FQYCe/r+8AET/VSgOzWJdZ6jEQIb/eMJpKFdjj54FAEhARIBFwEoIRqUNKCkk7ivJRmcOwAA//8AaP/sA3kEXgIGAFYAAP//AKAAAAFzBeUCBgBMAAD////sAAACLAXXAiYA8wAAAQcAav67AAAADbcCAQQEFgIDJQErNTUA////j/4UAXMF5QIGAE0AAAACAA7/8gZQBEoAFQAeAHNARgAbGwYDFhYGDgMfIAAaXVmEAJQAAgZFAAEDHwABDQDdAO0AAxAFYABwAAIPAAEUAwAABhQGG15ZBhUUCF1ZFA8MEGRZDBUAPysAGD8rABg/KxESADkYL19eXXFfXl1dX11fXSsREgEXOREzETMRMzEwATMgERAhIREhAgIGIyInNRYzMhITIQE0JiMjETMyNgOs/gGm/kb+YP8AG2CWdkMeHRlriCUCUAHwfZbd44SJAof+yf6wA7L+m/5jvg6FCAHJAgT8/FlR/qFcAAAAAAIArgAABqgESgARABkAcUBBDwsLDAETExAIBRYWCAwDGhsSCg8KXVkBhA+UDwIGRQ8BAx8PAQ0P3Q/tDwMQBQ8PARQDDw8IEQ0PDBUIE15ZCBUAPysAGD8/MxI5L19eXV9eXV1fXV9dxCsAGBDFERIBFzkRMxEzMxEzETMRMzEwAREzMhYVECEhESERIxEzESERExEzIDU0JiMECPbczv5K/lz+GLi4Aeyy5QENfZQESv47m5r+sAHp/hcESv43Acn9pv6htVpQAAAAAAEAEgAABEwGFAAeAHNAQRIACwkQFBYDCAgNCR4ACQAfIBYJGhMLDAtfWRAIDBgMAhEPDAEUAwwMGg4aBF1ZvxoBABoQGiAaAxoaCQ4AAAkVAD8zPxI5L11dKxESADkYL19eXV5dMysRADMREjkREgE5OREzETMzERczETMRMzEwIRE0JiMiBhURIxEjNTM1MxUhFSEVFAczNjYzMhYVEQOad3+pmbScnLQBsv5OCgw1t2zHyQKWhYO51f3wBNWHuLiHslhAVVXB0v1e//8ArgAABCMGIQImAdQAAAEGAHY/AAALtgEnFBAIAyUBKzUA//8AAv4UBBQGEAImAFwAAAEGAja/AAALtgEBGyMACiUBKzUAAAEArv6FBFIESgALADBAGAQBCgsIBQULAQMMDQsiBgIPAQRdWQkBFQA/MysAGD8zPxESARc5ETMRMxEzMTAhIREzESERMxEhESMCLf6BtAI8tP6LsARK/E4Dsvu2/oUAAAEAxwAABBIG4wAHACdAEgUGAwAGAAgJAQcGEgcEaVkHAwA/KwAYPxDGERIBOTkRMxEzMTABETMRIREjEQNmrP1tuAW2AS3+L/ruBbYAAAAAAQCuAAADUAWJAAcAJ0ASAgMABQMFCAkGBAMVBAFkWQQPAD8rABg/EMYREgE5OREzETMxMAEhESMRIREzA1D+ErQB8rADvvxCBEoBPwAAAP//ABkAAAdWB3MCJgA6AAABBwBDARIBUgAVtAEaBSYBuP+stB4iCRglASs1ACs1AP//ABcAAAYzBiECJgBaAAABBgBDdQAADrkAAf+htCUpCR4lASs1AAD//wAZAAAHVgdzAiYAOgAAAQcAdgGwAVIAE0ALASIFJgFJIh4JGCUBKzUAKzUAAAD//wAXAAAGMwYhAiYAWgAAAQcAdgEhAAAAC7YBTSklCR4lASs1AAAA//8AGQAAB1YHKQImADoAAAEHAGoBZAFSABm2AgEuBSYCAbj//rQZKwkYJQErNTUAKzU1AP//ABcAAAYzBdcCJgBaAAABBwBqANMAAAAQsQIBuP//tCAyCR4lASs1NQAA//8AAAAABIcHcwImADwAAAEHAEP/kgFSABW0AQoFJgG4/6C0DhIHAiUBKzUAKzUA//8AAv4UBBQGIQImAFwAAAEHAEP/YQAAAA65AAH/p7QdIQAKJQErNQABAFIB1QOuAnUAAwAoQBkAAwQFALUBAYoBAS8BXwG/Ac8B7wH/AQYBAC9dXV0zERIBOTkxMBM1IRVSA1wB1aCgAAAAAAEAUgHVB64CdQADAChAGQADBAUAtQEBigEBLwFfAb8BzwHvAf8BBgEAL11dXTMREgE5OTEwEzUhFVIHXAHVoKAAAAAAAQBSAdUHrgJ1AAMAKEAZAAMEBQC1AQGKAQEvAV8BvwHPAe8B/wEGAQAvXV1dMxESATk5MTATNSEVUgdcAdWgoAAAAAAC//z+OQNO/8sAAwAHAEtALgQACQUBAQiXAqcCxwLXAucCBQIQASABYAGwAeAB8AEGAZgFqAXIBdgF6AUFBQa4/8CzDxNIBgAvKzNdL10zXREBMxEzETMyMTABITUhNSE1IQNO/K4DUvyuA1L+OYOMgwAAAAEAGQPBAU4FtgAHABK2AQUICQAEAwA/zRESATk5MTATJzYSNzMGByUMFmI4hUIlA8EWWgEMef73AAAAAAEAGQPBAU4FtgAGABK2BAEHCAQGAwA/xhESATk5MTABFwYDIxI3AT8PNHyFRiAFthbH/ugBHdgAAQA//vgBdQDuAAYAHkARBAEHCAQvBj8Grwa/Bs8GBQYAL13GERIBOTkxMCUXBgMjEjcBZg8wgIZDJO4Xuv7bAQPzAAABABkDwQFQBbYABwAStgYCCAkDBwMAP80REgE5OTEwExYXIyYCJzfpJUKFLW0YDgW2+/peARxlFgAAAAACABkDwQLHBbYABwAPABpADAUBDQkEEBEACAQMAwA/M80yERIBFzkxMAEnNhI3MwIHISc2EjczAgcBng8bai6FQyT9xQwUZjaDQyQDwRZqARVg/vfsFlMBGnL+9+wAAAACABkDwQLHBbYABgAOABpADAgLAQQEDxALBA4GAwA/M8YyERIBFzkxMAEXBgMjEjchFwYDIzYSNwE9DzF/g0EjAjsPMX+IGkINBbYWwv7jAQjtFsL+42QBNF0AAAIAG/74AssA7gAGAA4AJ0AXCAsBBAQPEAsEBA4vBj8Grwa/Bs8GBQYAL10zMy8zERIBFzkxMCUXBgMjNjchFwYDIzYSNwFCDjCAhUElAjsPMICIGz4Q7he6/tv6/Be6/ttoASZoAAAAAQCFAAADlgYUAAsAOUAcCQICCAMKAQEHBAAEAwUEDA0BBAQKBwcDCAADEgA/PxI5LzMzETMREgEXOREzMxEzETMzETMxMAElEyMTBTUFAzMDJQOW/qEzzDH+tgFKMcwzAV8D3x/8AgP+H7IeAaH+Xx4AAAABAHsAAAOeBhQAFQBpQDgQBAQVDwUFCgwHFQoRFAADAwMJCw4DBhMCAgYKBwQWFxQLEQ4ODwMGBgAPCR8JAgkOCQ4FDwAFEgA/PxI5OS8vXTMzETMROS8zMzMREgEXOREzERczMxEXMxEzETMRMxEzETMRMzEwASUVJRMjEwU1BQMTBTUFAzMDJRUlEwI/AV/+oTLPMf6oAVgrK/6oAVgxzzIBX/6hKwHuHq4d/oUBex2uHgEkARUfrh4BfP6EHq4f/usAAQCeAe4CZAPpAAsAEbUABgwNAwkAL80REgE5OTEwEzQ2MzIWFRQGIyImnnRvbnV3bG51Aux6g4N6eoSFAAAAAAMAk//jBcEA+AALABYAIgAmQBQdFxEMBgAGIyQaDgMJA31ZIBQJEwA/MzMrEQAzMxESARc5MTA3NDYzMhYVFAYjIiYlNDMyFhUUBiMiJiU0NjMyFhUUBiMiJpNBPD1ERD07QgIZfTxDRDs2RwIXQjo9RUY8NEhvQkdHQkFLSkKJRURDSUJKRURFRERIQgAAAAcAZP/sCQYFywAJABUAIAAsADAAOgBGAF1AMTs2MUEAEAoFFichHBwtJwUvEEE2CEhHOEQeHioDDSoNKg0kEzADLxIHEwQ0PhkZJBMAPzMSOTk/Mz8/ERI5OS8vETMRMxI5ORESARc5ETMRMxEzETMRMxEzMTATFBYzMhEQIyIGBRQGIyImNTQ2MzIWARQWMzI2NRAjIgYFFAYjIiY1NDYzMhYBASMBARQWMzIRECMiBgUUBiMiJjU0NjMyFvpHTp6eTkcByZ2Xjp2ZkpOhAbZHTlFNnk5HAcmelo6dmJOTof71/NWdAysCo0dPnp5PRwHJm5iQm5iTkqEEAqWnAUwBSqWl5Onv3uXk7fzap6SjqAFIo6Xj6e/d5eTtAyL6SgW2/AKnpAFLAUijpeLq8Nzl5O3//wCFA6YBSAW2AgYACgAA//8AhQOmAr4FtgIGAAUAAAABAFIAcwIrA8UABgAkQBIDBgIEBgQHCAUgAQEQATABAgEAL11xLxESATk5ETMRMzEwEwEXAQEHAVIBWIH+4QEfgf6oAikBnEr+ov6hSwGbAAEAUABzAikDxQAGACRAEgQCAAMCAwcIASAFARAFMAUCBQAvXXEvERIBOTkRMxEzMTABAScBATcBAin+pn8BH/7hfwFaAg7+ZUsBXwFeSv5kAAAA//8Ak//jA2gFtgAnAAQB1wAAAQYABAAAABCxAwK4/l20GhoEISUBKzU1AAAAAf55AAACjwW2AAMAE7cABQIEAwMCEgA/PxEBMxEzMTABASMBAo/8g5kDfQW2+koFtgAAAAEAbQMdAskFxwASADxAIgwICAkSAAkAExQMCgAwCWAJkAkDAAkQCUAJAwkPCh4EDx8APzM/EMRdcTIRMxESATk5ETMRMxEzMTABETQmIyIGFREjETMXMzYzIBURAkxMTm9afGYODUmQAQIDHQGhVUVlfP6mAp1YZfr+UAABAFwAAAQjBbYAEQBhQDcHBQAOBAQJBQwQAgUEEhMDBwgHd1kACAgFDg4RdFlJDgEPDj8OXw5vDgQLAw4OCgUYCg11WQoGAD8rABg/EjkvX15dXSsREgA5GC8zKxEAMxESARc5ETMzETMzETMxMAEhFSERIxEjNTMRIRUhESEVIQG8ATb+yrKurgMZ/ZkCQP3AAY+F/vYBCoUEJ6L9/qEAAQBEAAAESgXJACEAjUBSEhgdGRUMCA8PHxsYCg4UAg4YFQUiIw8ZGhl3WQwAGhAaAgkDGgsdHh13WRoILx4BDx4fHj8eTx6vHr8eBgkDHh4VAAAFc1kABxYSFRUSdVkVGAA/KxESADkYPysREgA5GC9fXl1xMzMrEQAzGC9fXl0zKxEAMxESARc5ETMRMzMzETMzETMzETMxMAEyFwcmIyIVFSEVIRUhFSEUBgchFSE1NhEjNTM1IzUzNRACsMalQJaT7QGd/mMBnf5hP00DE/v6zMbGxsYFyVCNRf60haCHcZEtpJgrARCHoIWHAcMAAwCc/+wF7gW2ABYAIAApAGlANwsJIRwcHRclEBQUCQQSCSUdBSorDg4NEAoTEBNfWRAQHR4bIWtZGxseHRgeKWtZHgYGAF5ZBhkAPysAGD8rABg/EjkvKxESADkYLysRADMRMzMYLxESARc5ETMRMxEzETMRMxEzMTAlMjY3FQYjIiY1ESM1NzczFTMVIxEUFgEUBCEjESMRISABMzI2NTQmIyMFZiVREkNwdn2eoD9p4eE1/on+6f70P7IBEgIC/Z41wrWnsVR9DgaFII+JAcFSScPVif5WTFIDi+Tr/ccFtv0hjZyOiwAAAAABAC//7AR5BckAJgDSQI8dFxcZBwgaHBkFCAgfFiQRBAoWGQYnKAgYCxcPGB8YLxgDEwUXGHdZvxfPF98XA48XARAXAQAXEBcgF6AXsBfAFwYJAxcFHQAdEB0gHQMTBR4dd1kXAg8eAQ8eHx4vHk8eXx6PHp8e7x7/HgkPHh8eLx5fHp8erx6/Ht8eCAkDHh4TIiIAdFkiBxMOdFkTGQA/KwAYPysREgA5GC9fXl1xcjMzKwBfXl0RMxgvX15dcV1xKwBfXl0RMxEzERIBFzkRMzMRMxEzMxEzETMRMzEwASADIRUhBxUXIRUhFhYzMjcVBiMiAAMjNTMnNTcjNTMSADMyFwcmAwr+yEsB9P3+AgIBxP5MI8eomJmSquz+3S6klAIClKIoASjpzaJMogUr/nmFOD4sha6/QqBBAQsBAYUqKk6FAQgBHV+TVAAAAAAEAIX/9gYMBcEAAwAPABkALgBNQConHSIsEAoEFhYACiwCHQYvMCkaoBqwGgIYDRoNGg0HIAMDAhIlIAMTBxIAPzM/Mz8/ERI5OS8vETNdETMREgEXOREzETMRMxEzMTABASMBARQGIyImNTQ2MzIWBRQWMzI2NTQjIiUiJjU0NjMyFwcmIyIVFDMyNxUGBgUf/NWeAysBi6iXjqmplIuu/hdYVlNZrK79wKW5uq1nWyNTT9fTZ1gfaQW2+koFtvuYn7m7naO1uZ9zd3dz6dmyoqi1JWsf6ucjaw8YAAAAAAIAb//sA6IFywAcACQARkAhAxYjGhoPCRYdHQkMAyUmIw8fDRkKEw0MAgwCDBMABh8TAC8zLzMROTkvLxEzEjk5ERI5ORESARc5ETMRMzMRMxEzMTAlMjczBgYjIiY1NQYHNTY3ETQ2MzIWFRQCBxEUFhM0IyIGFRE2An2qEmkImpaZolBwTnKZjniMzrVQqntBPvp306m1t63nHht5FSYB6pCfoou6/tRO/uxneAQhvFVn/laDAAAEAMMAAAfHBbYADwAbACcAKwB1QEEDBgYHAA0LHBYQIisiKBYLBwYsLQoCBwgZJW1ZDxkfGQITAxkZEwgTH21ZDxMfEwIJAxMTKA4IAwEHKCgpbFkoEgA/KxEAMzMYPzMSOS9fXl0rERIAORgvX15dKxESADk5ERIBFzkRMxEzETMzETMRMzEwISMBIxIVESMRMwEzJjURMwEUBiMiJjU0NjMyFgUUFjMyNjU0JiMiBgM1IRUEx8n9XggQoc4CmggOogMAo5WMo6KUiqf+IlBaWk5OWllRYAIPBLj+4G381QW2+0z1jAMz/LmkubyhpLa5oXF1dXFybW39H42NAAAAAgAjAuUFhwW2AAcAGABCQCQAAQoMDA0TFhQUDQYBAwUZGgkXEAMEDQgUAwEHAw4RAwEEBAMAPxczETMvFzMSFzkREgEXOREzMxEzETMRMzEwASMRIzUhFSMBAyMXESMRMxMTMxEjETcjAwFzgc8CIdECVMUIBnvBwMe6gwYIzwLlAmNubv2dAit//lQC0f3VAiv9LwGiif3VAAD//wBOAAAF9gXNAgYBdgAAAAIAZv/dBIsESAAXAB8ANEAbHw4MFRgOBAUgIQ0fLx8/HwIUHxQfEQgRABwIAC8zLzIREjk5Ly9dETMREgEXOREzMTAFIiYCNTQ2NjMyFhIVIREWFjMyNjcXBgYTESYmIyIHEQJ5nfGFivSVmPOH/MUxplKDt1FIYtmTMqNYrXojkwEFnav/jI7+/aX+nDVGaYEpm3wCiwEVNUJ1/ukAAP//AEX/7AYEBbYAJwIXAmQAAAAmAHv5AAEHAj4Dav2zAAu0BAMCGRIAPzU1NQAAAP//ACP/7AYbBckAJwIXAqoAAAAnAj4Dgf2zAQYAdQAAAAu0AwIBDhIAPzU1NQAAAP//AEf/7AYXBbYAJwIXAqQAAAAmAjwKAAEHAj4Dff2zAAu0BAMCLRIAPzU1NQAAAP//AGb/7AYPBbYAJwIXAk4AAAAnAj4Ddf2zAQYCPS0AAAu0AwIBDhIAPzU1NQAAAAACAGL/7AQ5BccAGgAnAEFAIh4OFCUlBwAPDw4HAygpCyFkWQsLGAQYEV1ZGAQEG11ZBBYAPysAGD8rERIAORgvKxESARc5ETMRMxEzETMxMAEQAgQjIiY1NBI2MzIWFzcCISIGBzU2NjMyEgEyEjcmJiMiBgYVFBYEOaf+7LGwu4nqmFmNLgQE/vI6jTs9mkPS3P2ejtojFH1QY6BhYQOk/vn+N+jLwKoBNJ9RRUwBhSoorB0h/u37zwE66VZsgvZ7dH4AAgApAAAEfQW2AAUADAAnQBIJBQQKBQoNDgYFAQMFCWlZBRIAPysAGD8SORESATk5ETMRMzEwNwEzARUhAQYHASEBJikBzbgBz/usAiczK/78AsT/AENxBUX6uW8E7siC/P4C+skAAAABAMX+FAUlBbYABwAlQBEDBAcABAAICQAEGwUCaVkFAwA/KwAYPzMREgE5OREzETMxMAERIREjESERBG39ELgEYP4UBv75Agei+F4AAQBI/hQE4QW2AAsAQEAiAwAHCQsGCAIJAAYMDQIIBAEJAAMHBAQHaVkEAwAJaVkAGwA/KwAYPysREgA5ERI5Ejk5ERIBFzkRMxEzMTATNQEBNSEVIQEBIRVIAnL9ngRI/LoCOf2vA5/+FHEDlAMrcqL9CfyZogAAAQBmAokEKwMbAAMAIkAVAAMEBQAvAV8BfwGvAc8B7wH/AQcBAC9dMxESATk5MTATNSEVZgPFAomSkgAAAQAl//IEwwaeAAgAIEANCAMJCgMEBgQGBAcBBwAvLxI5OS8vETMREgE5MzEwBSMBIzUhEwEzAnOF/uu0ASXnAgCSDgMKj/1eBbUAAAMAdwGRBS0EDgATAB8AKwBKQCUPBQUXHQojFwApKRcKAywtIxcHDSYUFAMHIBoaBxF/DQFADQENAC9dXTMzMxEzLzMzETMREjk5ERIBFzkRMxEzETMRMxEzMTABFAYjIicGIyImNTQ2MzIXNjMyFgEyNjcmJiMiBhUUFgEiBgcWFjMyNjU0JgUtqIG5fH2uiKWphLR5fLeEpfx/P2w0MWtFTV9eApw/azUxbERMYF8Cz4a42dSwjYmy19Ou/r9XYV5aaVFRZQFsWV9eWmhQTmoAAAAAAQAK/hQDAAYUABQAHkAOCBIDEg0DFRYQCxsFAAAAPzI/MxESARc5ETMxMAEyFxUmIyIVERQGIyInNRYzMjUREAJ/Uy47OKqopU89PT6wBhQSlRjp+uW0uRWTGOkFGwFsAAAAAAIAYAGDBC8EIwAXAC8AWEA5GwMoEAMQMDEnGA8eHx4vHq8eBB4kGx4DICoBACoQKiAqAyoADyoDDwYfBi8GrwYEBgwDBgOfEgESAC9dFzMvXRczL11xFzMvXTMzERIBOTkRMxEzMTABIgYHNTYzMhYXFhYzMjY3FQYjIiYnJiYDIgYHNTYzMhYXFhYzMjY3FQYjIiYnJiYBTjZ/OWyURHZTSV8vNX05aZdDb1hLXDA2gTdqlj9sYkFhNTx8M2iYRXZPWVcB/EA5nm4eIx8bQjmfbR0lHxgBlUE3nW0ZKRscRjOebiAhJRQAAAAAAQBmAKIEKQUEABMAR0AsBgINEREKCw8FAQACCBQVEg8DA08CAQACAQIOCwbHBgECBg8HLwevB+8HBAcAL10zM10SOTkvXV0XMxESARc5ETMRMzEwJTchNSETITUhExcHIRUhAyEVIQMBDmn+7wFUef4zAhGHhWwBEv6sfQHR/euD3d+SAQaRAR894pH++pL+5gAAAAIAZgAABCsE4wAGAAoAQUAmBQEABAoKBwEDCwyAAMAAAgAwA3ADsAMDAwIBAAMELwVfBQIFCAcALzMZL10XMxgvXS9dERIBFzkRMzMRMzEwJQE1ARUJAjUhFQQr/DsDxfz8AwT8OwPF9AGoZgHhn/6T/rz+bZGRAAIAZgAABCsE4wAGAAoASUAsBgIHBQEKAQcDCwyABsAGAgYwA3ADsAMDAwQFAwYEoAEBkAEBLwFfAQIBCAcALzMZL11xchczGC9dL10REgEXOREzETMzMTATAQE1ARUBFTUhFWYDBPz8A8X8OwPFAZMBQgFvn/4fZv5Y9JGRAAAAAgBqAAAEPQXBAAUACQAnQBIIAAcJAwYGCQADCgsJBwEFAQQAPy8SOTkREgEXOREzETMRMzEwEwEzAQEjCQNqAcNOAcL+Pk4BXv7J/ssBNQLfAuL9Hv0hAt8CCP34/fj//wAfAAAENAYfACYASQAAAQcATALBAAAADbcCAZcXFgInJQErNTUA//8AHwAABCMGHwAmAEkAAAEHAE8CwQAAAAu2AZcXFgIbJQErNQAAAAABAM8E2QPLBhAADQAmQBQDCw4PCg8DAQMDB6AAAQ8AXwACAAAvXV0yMi9dMxESATk5MTABIiYnMxYWMzI2NzMGBgJIwK8KqAlbcWdjC6oPvATZkqVoUlhiopUAAAH/j/4UAWIESgANAB9ADgILCAgODwkPAAVdWQAbAD8rABg/ERIBOREzMjEwEyInNRYzMjY1ETMRFAYtXkBFQ05JtJ3+FBmRFFVXBPT7EqSkAAAAAAEBhQTNAnsGFAAJABlACwkFCgtgCQEJgAMAAD8azF0REgE5OTEwATY2NzMVBgYHIwGFFycGsg1WMWIE5UK6MxI1uEgAAQFv/jsCdf+DAAkAHEANCQUKC2AJAQmADwMBAwAvXRrMXRESATk5MTABNjY3MxUGBgcjAW8WNgiyEGEzYv5UM7pCEj+7PAAAAQF7BNkCgwYhAAkAIkASBQAKC28IAQiAoAQBDwRfBAIEAC9dXRrNXRESATk5MTABBgYHIzU2NjczAoMbNAeyD2MyZAYIO7o6EzjAPQAAAAACABQCSgK6BbwACgARAEBAIAACEQUJAgILDgMFAxITDgMBBQUJDxEfEQIREQcDIAceAD8/EjkvXTMzETMRORESATk5ETMzMxEzETMRMzEwASMVIzUhNQEzETMhNTQ3BgcHArp9mf5wAZSVff7qBghapAMOxMRrAkP9zb9rZBKM8AAAAAABAD0CNwKPBbYAHgBCQCcbAwkdGAMQGBAfIBNvAH8AjwDfAO8A/wAGAAAQAAIAAAYcGR4NBiEAPzM/MxI5L11dMxESATk5ETMRMzMRMzEwATIWFRQGIyImJzUWFjMyNjU0JiMiBgcmJxMhFSEHNgFOkbCrqUqLKTiMNlxtbGM2Sx8dIiEB8f6FEkEEc5R7j54fF4kiJlFZT1URCBgRAapw4A0AAAEAOQJKApYFtgAGACBADwUBAQACAwcIACAFAgIDHgA/MxI5PxESARc5ETMxMBMBITUhFQGiAVz+OwJd/qMCSgLxe2T8+AAAAAMAMwI5ApoFxwAVACIALQBNQCoFKxAmFg0mEwMrBxwcKxMNBC4vBRAgIAooGigCDygfKAIoKAojAB8ZCiEAPzM/MhE5L11xMxI5ORESARc5ETMRMxEzETMRMxEzMTABMhYVFAcWFRQGIyImNTQ2NyYmNTQ2AxQWMzI2NTQmJycGBhMiBhUUFzY2NTQmAWh+l5SxqYmVoEpURzyfL1FVV1FbTxpERqhCSY88TEsFx3ZogkxKnHGLg3NFcy0sXUZnff1oO0hIOzxOGgogUwHsOzZaORdEODY7ABYAVP6BB8EF7gAFAAsAEQAXABsAHwAjACcAKwAvADMANwA7AD8AQwBHAFMAWwBrAHQAfACJASlAwGNkZHowPEAFBA8PADE9QQQMVE4DESAcSFgjHzQsbHZ2azcvYHBnejgYOxuHhAYSCSQoRAQXFyUpRQoEFBQShBt/GHpwL2ssH1gcEU4MEYqLY3V1e2yLbAJabGpsAgNsbGtcgn19VktLdmtaUURrVGtka9RrBCBrMGsCAnRRhWsEMFxAXHBcgFwEwFwBL1xPXAJcXAAKQipBKT5GPUUyJjElDRUQDAEZHS0TBA8PEhgcLAQMIDQ4BgQEByE1OQQFAQAvFzMRFzMvFzMzERczERIXOTkvXV1xFzNfXV0vMy8zMy8zMy8zERI5L19xcTMSORESARc5ERczMxEXMxEzETMRMxEzETMRMxEzETMRMxEzETMRMxEzETMRMxEXMzMRFzMRMxEzMTATESEVIxUlNSERIzUBETMVMxUhNTM1MxEhNSEVITUhFQE1IRUBIxEzESMRMwE1IRUBIxEzATUhFTM1IRUBIxEzNSMRMwEjETMFFAYjIiY1NDYzMhYFFDMyNTQjIiUzMhYVFAYHFRYWFRQGIyMTMzI2NTQmIyMVFTMyNjU0IwEiJzUWMzI1ETMRFAZUAS/ABc4BMG35AG/ABQ7Dbf1JARH74QEO/vIBDgS3bW1tbfvCARD8MG9vAsABEHcBEfqob29vbwb+bW37n4d/f4eHf36I/nOHh4eHAeGsbXAuLD0ubV7Pe0IuJCovO0oxJVoBXjQcKxlWfWkEvgEwb8HBb/7QwfkCAS/CbW3C/tFtbW1tBv5vb/qoAQ4CAgEP+jttbQGmAQ4ESm9vb2/8LwEQeQEP/WgBEEmRnJyRkpuak8XFxGFDUzFCCAgORDVRWQFiIiAiHeOaKyVK/voKZghWAZL+cl9jAAMAVP7BB6oGFAADAB4AKgAsQBcBCxclBB4fEQMJKyweKBQOKCIOIg4CAAAvLzk5Ly8zETMSORESARc5MTAJAwU1NDY3NjY1NCYjIgYHFzYzMhYVFAYHBgYVFQMUFjMyNjU0JiMiBgP+A6z8VPxWA+ssQWdJu6VPukdSoFo/PjFIVDsbR0ZCSUhDSEUGFPxW/FcDqfsvMkExUn5Yh5o4KrJQOi81SzZEcEo7/u0/SEk+QElI////j/4UAmMGIQImAjcAAAEHAUz+qwAAAAu2AQAaFQkKJQErNQAAAP//ABkDwQFOBbYCBgIHAAAAAgAM/+wE8gYfACoANABnQDcRGQgVDDIiAB0qKCgdHysiDAgHNTYrLykfJSoqKWRZKioFJRMOXlkTEwUlJS9dWSUBBRteWQUWAD8rABg/KxESADkYLysREgA5GC8rERIAORESORESARc5ETMRMxEzETMRMzMxMAEWFRAAISImNTQ3NjU0IyIHJzYzMhUUBwYVFDMgETQnJCQ1NDYzMgATMxUlLgIjIgYVFAQEagX+3P75xckPDkQsMCdeYbYOD+YBbgT+tf6Vu6nSAQMrkP66E2CHTllfAQ8DRjk0/p/+dKevPW9vHlIbfyu6K3d3Q8kCYjssA97HkaD+1P7ei4uEy2tXSIaUAAAAAQAAAAAEiQXDABUAKEATFBESEgkWFwASFAMSEgYLa1kGBAA/KwAYPz8SORESATk5ETMyMTABNzYSNjYzMhcVJiMiBgICBxEjEQEzAj9FOYVMXUA6JBgjLUWkfSO7/iPJAtufiQEibDIRjwZL/rv+52H94QIvA4cAAgAX/+wGmgRKABQAKABWQCsLJgUXCg0GFwMgHQ0mJh0DAykqEwgeHgAICxUGCAZdWQgPIxoAGl1ZEAAWAD8yKxEAMxg/KxEAMzMREjkYLxE5ERIBFzkRMxEzETMzETMRMxEzMTAFIiY1NBMhNTchFSMWFRQGIyInIwYBAhUUFjMyNjU1MxUUFjMyNjU0JwI3vcp//uiPBfT8cMu+3kUIR/7Me2x0XGiualt0aWoU6/DmAQdOSJb78vDruLgDyP7rzq2jjX23t4CKqKj66QAA//8AxwAABnsHdQImADAAAAEHAHYBngFUABNACwEdBSYBTh0ZBw0lASs1ACs1AAAA//8ArgAABtUGIQImAFAAAAEHAHYBzwAAAAu2AV4sKBIiJQErNQAAAP//AAD90QUbBbwCJgAkAAABBwJYATsAAAANtwMCAhQOBAclASs1NQD//wBe/dED1wRcAiYARAAAAQcCWADLAAAADbcDAgUsJggaJQErNTUA//8AAf/sBwsFzQAnADIBSAAAAQcCWf9uAAAADbcDAkEiHTIGJQErNTUAAAAAAgBz/dECN/+DAAsAFwA0QB4SBgAMBgwYGRUQAyADAgMPbwkBCUAZHEgJQAkNSAkALysrcTPEXTIREgE5OREzETMxMAEUBiMiJjU0NjMyFgc0JiMiBhUUFjMyNgI3fWhneHplY4JyQjEzQDo5M0D+rGN4dWRkdXVkODs7ODY9PQACAJMEaALXBcUACQAZACZAEhUQEAoFCQ4KBBobDQMXCYADAwA/GtzEEMYREgEXOREzETMxMAE2NjczFQYGByMlNDY3FQYVFB4CFRQjIiYBtBQ/DcMiejNU/t94fHsgJSBhN0YEhzW/OxREsj14TnMdSCk1FBMQGhxKRQD//wAfAAAG9AYfACYASQAAACcASQLBAAABBwBMBYEAAAAbsQMCuAH3tS0sAj0lAbj/aLQZGAI9JSs1KzU1AAAA//8AHwAABuMGHwAmAEkAAAAnAEkCwQAAAQcATwWBAAAAGbkAAgH3tS0sAjElAbj/aLQZGAIxJSs1KzUAAAIAff/sBnEGFAAUACAAP0AfECITABUGABsGGyEiDgATCwsDCQkeaVkJBAMYaVkDEwA/KwAYPysREgA5ETMYPxESATk5ETMRMxEzETMxMAEQACEgABEQACEgFzY2NTMXBgYHFgUQEjMyEhEQAiMiAgXB/p3+w/69/p8BYQFFAUW1RD/CDx+GaF37fvTu7fDv7PHzAt3+nv5xAYkBagFoAYbVE3yNFqCuJ67+/t3+0QErAScBJAEq/tIAAAAAAgBx/+wFKwTyABcAIwBSQC0TJRYAGAcAHgceJCUAESARoBHQEQQRDxEBFwMRFg0NAwoKIV1ZChADG11ZAxYAPysAGD8rERIAOREzGC9fXl1eXRESATk5ETMRMxEzETMxMAEQACMiJgI1EAAzMhYXPgI1MxcGBgcWBRQWMzI2NTQmIyIGBGj+8PCV5nwBDPJstkIyOxzBDiB+akX8w56kqZibqamWAif+8/7SiwEErAEMAStJRA9DZWoXoa8njLHYztbQzdPTAAABALj/7AaLBhQAGgA4QBsGHBMQAQoKGRAZGxwEAAkBAQ0aEQMNFmlZDRMAPysAGD8zEjkvMz8REgE5OREzETMRMxEzMTABFTY2NTMXBgYHERAAISAANREzERQWMzI2NREFH1JNvw4hsJv+3/72/vD+1LnCxbS8BbbGC4GYFrm6Gv2T/wD+6AEg/AOu/EqxxL65A7QAAQCi/+wFqgT0AB0AWkAyDx8BHAoTEwcVHBUeH1ANAQANIA2gDdANBBEPDQEXAw0WGR0SCgoUCB0PFBUZBF1ZGRYAPysAGD8/MxI5LzMREjkvX15dXl1xERIBOTkRMzMRMxEzETMxMAERFBYzMjY1ETMVNjY1MxcGBgcRIycjBgYjIiY1EQFYd32pmrVQSb8OILGVlBoJMrJ0ycoESP0/hYG80QI6eQ2AmBe/vRH8sJFPVr7RAs0A///8TQTZ/ekGIQAHAEP6ygAAAAD///0HBNn+owYhAAcAdvuEAAAAAP///BME2f8DBeEABwFS+xEAAAAAAAH9BAS4/ncGkQAQAC1AHgIFBQofDwEPAAQgBPAEA98EAQ8ELwRfBH8EzwQFBAAvXXFxxF0yOS8zMTABFAcHIyc2NjU0IyIHNTYzIP53pgpvDkpYhjUtJUwBAgXXjCZtrg0uMFIIagwAAf0x/pj+Bv99AAsADrYDQAlQCQIJAC9dMzEwBTQ2MzIWFRQGIyIm/TE/LCs/Oy8wO/Y8Nzc8NT08AAD//wDHAAAD+AdzAiYAKAAAAQcAQ//QAVIAFbQBDQUmAbj/wrQRFQILJQErNQArNQD//wDJAAAFYAdzAiYBsgAAAQcAQwBmAVIAFbQBEwUmAbj/o7QXGxEJJQErNQArNQD//wBx/+wEGwYhAiYASAAAAQYAQ7cAAA65AAL/wrQhJQMKJQErNQAA//8ArgAABHUGIQImAdIAAAEGAEPiAAAOuQAB/6K0ExcNBiUBKzUAAAABAIP/7AeiBckAMgBQQCgEKxsoIhYrKAkwMCgWAzM0EBkpKRMZAB8ZH2lZBhkELSUTJWlZDBMTAD8zKxEAMxg/MysRADMREjkYLxE5ERIBFzkRMxEzETMRMxEzMTABIgYHJzYzMgAREAAhIiYnIwYGIyAAERAAMzIXByYmIyICERASMzI2NxEzERYzMhIREAIFrDxfLEl+mukBBf7h/vx0rksGSapz/vr+4wED6Zx8SixePJKhy7o+fDG5You5zKMFJSsdmFT+iv6r/o3+YTIyMjIBnQF1AVMBeFSYHSv+3P77/tn+ticlAcP+PUwBRQEsAQgBIQAAAAEAAAAABi0ESgAiACtAFgUcDxADIyQPAAAKFwMEGxAFDyAVBBUAPz8/MzMSFzkRMxESARc5MjEwAQYHByMBMxcSEhczNjc2NwMzEx4DFzM2EhEzEAIHIwMmAy0idTbf/n+6WGRqFggdS2EcpsPJDCAgGwcIppS0xdm+fR0BwWnpbwRK+P7o/rVsVpzKQAHL/a4lYWNZHrQBrwFP/pH+BuEBUEsAAgASAAAE/AW2ABEAGgByQEAGFgQIEhIBDwsWFg8RAxscBxEAEWtZBA8AAQ8GAAAIAggaaVnYCAE6CAEJCAEPAAigCAISAwgIDwIDDxJrWQ8SAD8rABg/EjkvX15dXl1dXSsREgA5GC9fXl0zKxEAMxESARc5ETMRMzMRMzMRMzEwEyE1MxUhFSEVMyARFAQhIREhATMyNjU0JiMjEgE6ugGe/mLBAjX+8P75/mf+xgH0y8CsuNOsBNHl5Zzp/mDU2AQ1/GeHiYl1AAAAAgASAAAEpgUnABEAGgB6QEgABBMTDwsHFxcCCw0EGxwQDgQSXVmEBJQEAgZFBAEDHwQBDQTdBO0EAxAFYARwBAIPBAEUAwQECw4DDQ4NXVkADg8LE15ZCxUAPysAGD8zKxEAMxESORgvX15dcV9eXV1fXV9dKwAYEMYREgEXOREzETMzETMzMTABIRUhESEgERQGIyERIzUzNTMRESEyNjU0JiMBtAFa/qYBNQG94eD+HfDwsgEpiI+AmwRKlv7R/sulqwO0lt38yf6hXFlZUQAAAAABAMf/7AclBcsAIQBjQDoYFBQVBhkeDAUZEhUGIiMGExgTaVkD2BgBOhgBCRgBDwAYoBgCEgMYGBUWAxUSHABpWRwEDwlpWQ8TAD8rABg/KwAYPz8SOS9fXl1eXV1dMysRADMREgEXOREzETMRMzEwASIGByEVIRIAMzI3FQYGIyAAAyERIxEzESESACUyFwcmJgWP3f8dArT9RwoBBuycxV2tcf7B/qUK/rC4uAFWIAFvATLatUppnQUp9Omg/vX+7DqgIhkBbQFR/VYFtv2WAS8BTgJenDMlAAAAAQCu/+wFqAReACEAf0BMCQUFBhUYGAoDDx8fFwMGBCIjGAQJBF1ZFYQJlAkCBkUJAQMfCQENCd0J7QkDEAWgCbAJAg8JARQDCQkGBw8GFQ0SYVkNEAAbYVkAFgA/KwAYPysAGD8/EjkvX15dcV9eXV1fXV9dMysRADMREgEXOREzETMzETMRMxEzMTAFIgAnIREjETMRITYkMzIXByYjIgYHIRUhFhYzMjY3FQYGBH/t/vYN/ue0tAEbGAEL4aOENYBynZ8QAg398Qmkn1mGPD2AFAEI9f4XBEr+N+j1O5Q0nqSYtq4lGZwfHAACAAAAAAV3BbYACwASAFRALgsUCA0DDAMEBBMUDRABCwQQCAkCBgwGa1k4DAGaDAFpDAHfDAEMDAkABAgSCQMAPz8zMxI5L3FdXXErEQAzERI5X15dERIBOREzMxEzMhEzMTAhASMRIxEjASMBMwEBIScmJwYGBLT+6pCoj/7nvgJisgJj/KABSVA6HAouAqT9XAKk/VwFtvpKA0LGlmQniQAAAAIACAAABIkESgALABIAdkBNDQUMBQYBBgoDExQQCgsECAwIXVkEDAH0DAEGtQwBA48MAU0MXQwCfQwBBf8MAQ8MjwyfDM8M3wwFLww/DL8M7wz/DAUMDAsGAgoVCw8APz8zMxI5L11xcl9dcV1fXV9dcSsRADMREjkREgEXOREzMxEzMTABASMDIxEjESMDIwEDISYnIwYGArYB07jLbKJxxrkB0RgBDmMgCAsYBEr7tgHh/h8B4f4fBEr+K/VnI0QAAAACAMcAAAdvBbYAEwAaAGdAORIcFQIUAwcOCgoLAgMDCAsDGxwYCxABBQkOCWlZFDgOAZoOAWkOATAOAZAOAQ4OEAMHEwMLEgwQAwA/Mz8XMxI5L11xXV1xxSsAEBjEMhESORESARc5ETMRMxEzMxEzETMRMzEwASMRIxEjASMBIREjETMRIQEzASMBIScmJwYHBZGLpo3+6cQBHP5ruLgB2QECtAJhyf1sAT5KPBgXQQKq/VYCqv1WAqr9VgW2/ZYCavpKA0rEmFxeogACAK4AAAYxBEoAEwAbAIFATxkFGAYKEQ0NDgUGAQYLDgQcHRQOEwgEDBEMXVkYBBEB9BEBBrURAQOPEQFNEV0RAn0RAQX/EQGPEZ8RAi8RPxG/EQMRERMCBgoDDhUPEw8APzM/FzMSOS9dcXJfXXFdX11fXXEzKxEAMzMREjkREgEXOREzETMRMzMRMxEzMTABASMDIxEjESMDIxMhESMRMxEhExcjBgcHIScmBGAB0bbNbKJrzbjP/t+wsAFjwnMIKCQ4AQs6IwRK+7YB6f4XAen+FwHp/hcESv41Act5gFOBkVkAAAAAAgAXAAAFvAW2AB8AIgBnQDwPEAIgBwEhIBAdIh4YCSMkIB0fDhIdEmxZAsgd2B0COh0BCR0BDw8dASYDHR0fEAgYEgEeIh8fImpZHwMAPysREgA5ORg/MzMSOS9fXl1eXV1dMysRADMREjkREgEXOREzETMxMAEVAR4CEhMjAy4CIyMRIxEjIgYGBwMjEz4CNwE1AQEhBTf+X3OZZl1XvIcgQmNSHLkaUWE/IoXEhy5il3L+ZwJLAW/9IwW2h/4VB02V/sj+3QHBaWEr/UoCtildb/4/AcWbj00IAeuH/aIBuAAAAgAMAAAFDgRKACAAIwCBQE8hAiIeEBECIwgBIxEeHxkHJCUPEx4TYFkCI9Qe5B4CBpUeAQNvHgEtHj0eAl0eAQVvHn8eAg8eHx6fHgMLAx4eIBEJGRUBHyIgICJdWSAPAD8rERIAOTkYPzMzEjkvX15dcV9dcV1fXV9dMzMrEQAzERIBFzkRMxEzETMRMzEwARUBHgMXEyMDLgIjIxEjESMiBgYHAyMTPgI3ATUFIQEEi/60U2xILxiBtIEhN09GC6YOQk83I4SygTZOcVn+tAMZ/csBGgRKaf6eCDJOaT7+sAFMVUQd/f4CAhxCWP60AVCKYjkKAWJplP7LAAAAAAIAxwAAB9UFtgAkACcAekBJJiIXIR0dHg8QAicHASUnECIbIx4JKCkOEiESa1khHGlZAifYIQE6IQEJIQEPACGgIQISAyEhJAgQFwMeEh8jAQMmJCQmalkkAwA/KxESABc5GD8XMxI5L19eXV5dXV0zMysrEQAzERIBFzkRMxEzETMRMzMRMzEwARUBHgIXEyMDLgIjIxEjESMiBgcDIzcSNjchESMRMxEhATUFIQEHTv5gc5lkLom2iSRCZVYXuhV+cyuFwSdbUSP+XLi4Asf+bgO7/SQBbwW2h/4TB02Om/47AcFuXSj9TAK0YpH+P4ABO8ol/VYFtv2WAeOHpv5GAAAAAgCuAAAGvgRKACUAKACLQFQmAicjGSIeHh8QEQIoCAEoESMcJB8IKSoOEyITYFkiHV1ZAiiEIpQiAgZFIgEDHyIBDSLdIu0iAxAFDyIBFAMiIiUJERkDHxUgJAEDJyUlJ11ZJQ8APysREgAXORg/FzMSOS9fXl1fXl1dX11fXTMzKysRADMREgEXOREzETMRMxEzMxEzETMxMAEVAR4DFxMjAy4CIyMRIxEjIgYGBwMjEzY3IREjETMRIQE1BSEBBjv+tVNrSC8YgbSBIThRQwulC0RROCKDs4EtJf7PsLACK/62Axn9ywEaBEpp/pwIMU5oPv6wAUxURBz+AAIAHEJW/rQBUHQo/hQESv43AWBplP7RAAAAAAEAPf5KBEIG0wBLAJlAVSgTCj42GUFCQjssHBwAABM7MD4hRRkZITAqEwVMTTAqLi44DzMfMy8zAwkDMypBHB0dHGtZDx0BOh0BAw8d3x0CDwYdHSoWSCokbFk7KgQQA2lZECMAPysAGD8zKwAuMxI5GC9fXl1fXXErERIAORgQxF9eXTIyLxI5ERIBFzkRMxEzETMRMxEzETMRMxEzETMRMxEzMTAXFBYzMjc2MzIXFSYjIgcGIyImNTQ2NzY2NRAhIzUzMjY1NCYjIgYHJzY3Jic1MxYXNjYzMhcVJiMiBgcWFhUUBgcVFhYVFAQFDgL6VFhgeHhBmURGoUJsbWi2t9zpxbr+PdDI19efiG3DY1qnwTOsg1yDXYNBNzAfJyxvMK3Ev6i7y/7f/uVgdjaHNTIHBieuMwUFgoaDgwoGg44BBpqRe2p7PEF9chw6tRs7iHVWDnUMUkcXvo6OtxkIGLSOz9gHAxsuAAABABn+cwOPBVAARgCdQFo8KQMgIAsNDg43Mz5AFxcpCEQLNxEvLzdEPikFR0gNMzQ0M11ZlTQBaTQBODQBWDQBbzQBDzQfNJ80AwsDNDQ+LBQmGl1ZJkQ+QkIFCABACQxIAD4+OV5ZPhAAPysAGBDEKzMyMi8SOS8rAC4zEjkYL19eXXFdcV1dKxESADkREgEXOREzETMRMxEzETMRMxEzETMRMxEzETMxMAEyFxUmIyIGBxYWFRQHFRYWFRQGBwYGFRQWMzI2MzIXFSYmIwcGIyImNTQ2NzY2NTQmIyM1MyA1NCMiByc2NyYnNTMWFzY2AwQ6KhgrL2UteozSgnX74oNzTVduvkt7KRtaK7FyapWgwbynoJyhkHcBN/mNqT+EdmtWg0iNWYgFUA51Ck08HIpruzgIJYZkmaYCAy48MioKKZcXFAUGe3V6gAQCXVtgV5OonEaPNhCDUhsyi3BVAAAA//8AbQAABgIFtgIGAXUAAP//AKL+FAWaBhICBgGVAAAAAwB9/+wFwwXNAAsAEgAZAFlANBYQEAYAFw8GDxobFhBpWRgWAXoWAUkWAW8WfxYCDxavFgILAxYWAwkJE2lZCQQDDGlZAxMAPysAGD8rERIAORgvX15dcV1dcSsREgE5OREzMxEzETMxMAEQACEgABEQACEgAAEyEhMhEhITIgYHISYmBcP+nf7B/r3+nwFfAUcBPgFi/V7e8wz8RA3z4dr0EwO6E+8C3f6h/m4BiwFoAWUBif5x/E0BCwEE/vz+9QSg+/f4+gADAHH/7ARoBF4ADAASABgAbUBDFhERBwAXEAcQGRoWEV1Z1BbkFgIGlRYBA28WAS0WPRYCXRYBBW8WfxYCDxYfFp8WAwsDFhYDCgoTXVkKEAMNXVkDFgA/KwAYPysREgA5GC9fXl1xX11xXV9dX10rERIBOTkRMzMRMxEzMTABEAAjIiYCNRAAMzIAATI2NyESASIGByECBGj+8PCV5nwBDPLoARH+BZyYC/1/EgEtmJcOAn8eAif+8/7SiwEErAEMASv+z/1UuLD+mANGpqIBSAAAAAEAAAAABVIFwwAVACJAEAYWFBcKBQYDBRIRAGtZEQQAPysAGD8/EjkRATMRMzEwASIGBwEjATMBFhc2NxM+AjMyFxUmBOw9TjT+tNP98sEBTUYhHUWkO1RuWSpXOAUraKr75wW2/FjBkYveAga+mEIVlxQAAQAAAAAETARUABYAIkAQAhcQGAYAAg8NEmRZDRAAFQA/PysAGD8SOREBMxEzMTAhAgEzARYXMzY3EzY2MzIXFSYjIgYHAwGYjP70vAEFQgkIHyGPNm9wJi4dKS45HPwBcgLY/Sm/NaVfAb+nawyMC1JW/OEAAAD//wAAAAAFUgdzAiYCfQAAAQcDcwTdAVIAGbYCASEFJgIBuP9/tCUfBhQlASs1NQArNTUA//8AAAAABEwGIQImAn4AAAEHA3MEbwAAABCxAgG4/5S0JiACECUBKzU1AAAAAwB9/hQJuAXNAAsAFwAuAEhAJwwGABIYJyEuJxIGBS8wHBguFSAYDwkVaVkJBAMPaVkDEyUqXVklGwA/KwAYPysAGD8rABg/Mz8SORESARc5ETMRMxEzMTABEAAhIAAREAAhIAABEBIzMhIREAIjIgIlMxMWFzM2NxMzAQYGIyInNRYzMjY3NwVa/rj+2v7W/rsBRQEsAScBRfvj29TU2NjS094Ebb70RBgJEE7bvv4rRb+LTko3QlV3KjkC3f6d/nIBhwFsAWoBhP5z/p3+3P7SASoBKAEnASf+2Ef9i7F4UdoCc/setp4RjwxcZpL//wBx/hQIiwReACYAUgAAAQcAXAR3AAAAC7YCCBgiADElASs1AAAAAAIAff+HBhQGLQATACgAUUAqFAomDQciHAAfHxwHFwoFKSoPBQcRIg0kJg0malkNAxocAxcHBxdpWQcSAD8rERIAOTkyGD8rEQAzEjk5ETMyERIBFzkRMxEzETMzETMxMAEQAAUGIyInJAAREAAlNjMyFwQAARQSFzY2MzIXNhI1NAInBiMiJwYCBhT+0P73Gnd8FP71/s4BLQEQFHx2GQENAS77KcO6EUk2aSS7wMK5H25xH7rDAt3+z/51K29vKAGIATkBNQGCK2xsLP51/tPu/tUqMCZWKgEr7vABKChYVij+1gAAAAACAHH/kwTVBLQAFwArAE1AKBgMKQ8aIxUDACEhAx8aDAUsLSYjKQ8pXVkSFQ8QHR8aCRpdWQYDCRUAPzMzKxEAMzMYPzMzKxEAMzMREgEXOREzETMzETMzETMxMAEUAgcGBiMiJicmAjU0Ejc2NjMyFhcWEgUQFzY2MzIXNhEQJwYGIyImJwYGBNXe0glAODk/Cc3l49EIPjk2QgnN4/xW/Aw8NWYZ+PgOPTQ2PQyDdwIn6P7dJjUuLTgkASjj6AEhJDYqKjgm/trf/qE7KiJKPAFcAVU+KiEiKx/MAAAAAAMAe//sB5EIRAATAEUAVwCPQBdSSU1DNkZNKh0dASVNOwo2B1hZkEkBVbj/wLMTF0hVuP/AQDUKDkhJVUlVOQcAAQEACRAJIAmQCaAJBQoJCQcPDh8OAgsDDiBAOUBpWSc5BBoUMxRpWS0zEwA/MysRADMYPzMrEQAzGC9fXl0zMy9eXTMRMxESOTkvLysrXRESARc5ETMRMxEzETMzMTABFSMiLgIjIhUjNTQ2MzIeAjMBMjY3FhYzMhIREAIjIgYHJic2MzIAERAAISImJwYGIyAAERAAMzIXBgcmJiMiAhEQEgEUBgc1NjY1NC4CNTQ2MzIWBawQV5B4Yypqg3xtOnF3hE79I16qOz6tWbnOo5A8YSotG3mc6gED/tv++nGmSUuncP73/uABBeiceRstKV88k6POAoR7eDo+HyYfNS05RAfNfyMqI3Qebm4lLSX4vko/QEkBSgEnAQgBIS0dWj5W/of+rv6M/mIvLzAuAZwBdgFVAXZWPlodLf7e/vn+2f62BlpQdRxKEjIaFBIRGhwmJ0YAAAMAb//sBhcHDAAqAD8ATgClQBtKQ0YUCEBGKB0dLCJGDjYIB09QGSAJDEgZFky4/8CzExZITLj/wEBDCg1IQ0xDTAsyKywsADUQNSA1kDWgNQU1NTIAOhA6AvA6AQ86HzrfOgMKAzpADRBIOh8RCxFhWSULEBsWBRZhWQAFFgA/MysRADMYPzMrEQAzGC8rX15dXXEzMy9dMxEzERI5OS8vKysROSsREgEXOREzETMRMxEzMzEwBSInBgYjIgIREBIzMhYXByYjIgYVECEyNjcWMyARECMiByc2NjMyEhEQAgMVIyIuAiMiBhUjNTQ2MzIeAjMFFAYHNTY1NCcmNTQzMhYEN5RhL3BT5vvRwj94KjtbRXJtASk5ckd0fQEn3UdbOyl7P8HR/FIRV5B4Yio1NoN6cDpwd4NN/vB9d3cxMWI5RBRFICUBJwEMARQBKyAZlDTR0/5kJS9UAZwBpDSUGSD+1P7t/vP+2gasgSQqJDc+H25rJCwk6FF0HEgoOh0RECxORgD//wCD/+wHogcKAiYCaQAAAQcJaAHBAWYAFbQBPgUmAbj//bRANBYJJQErNQArNQD//wAAAAAGLQWkAiYCagAAAQcJaADTAAAAC7YBCjAkBRwlASs1AAAAAAEAe/4UBOkFywAWAC9AGAMOCQoUCg4DFxgKGxIAaVkSBAsGalkLEwA/KwAYPysAGD8REgEXOREzETMxMAEiABEQADMyNxEjESAAETQSJDMyFwcmA0jz/uoBBP9vR7n+pv6WsAFH2ua3S6wFJ/7D/u/+3/7ZGf1qAdgBgQFu4QFYt1aeUAAAAAABAHH+FAOqBF4AGAAxQBgJFg8DFhcDFxkaFxsGDGFZBhAAEmFZABYAPysAGD8rABg/ERIBOTkRMxEzETMxMAUmABEQADMyFhcHJiMiBhUUFjMyNjcRIxECc/7+/AES/k2fPTWWZKqmqKRCWSm0FAIBHwESARUBKh8cljTK1tfFGRL9ZAHYAAAAAQBo//wEdQUGABMAN0AeEg0IAAMRBhAHDQoODAoHBgMCBAgUFQkPBRMECwESAD/NFzkREgEXOREzETMRMxEzMxEzMTABAycTJTcFEyU3BRMXAwUHJQMFBwICtnu2/uFEASHL/t9FAR+4ebgBIUb+48wBHkMBN/7FQwFApnWoAWKmd6gBPUX+wqZ1pv6gqHUAAQDJBI8DrgW2ABMAMEAJAAYQCgYKFBUDuP/oQA4JD0gDADAPCS8JXwkDCQAvXRrJMisREgE5OREzETMxMAEGBiMiJjU0NjMhNjYzMhYVFAYjAYcGKjA1KSo2AcMGLDAzLSw2BO4tMjQ1NSsvLzE1OCoAAAABAPgE4wPfBd0AEwA4QA0SCBQVExISoAmwCQIJuP/AQBIJDEgJCQwPBB8ELwRfBM8EBQQAL10zMy8rXTMvMxESATk5MTABMjc2MzIWFRUjNTQjIg4CIyM1AQR4lpVRbXqBaitkeI9WEAVoOzprbiETZCQrJIEAAAABAd0E1QLTBjkADwAaQAoGDgsLAAAQEQ4DAC/EERIBOREzETMzMTABNDYzMhYVFA4CFRQXFSYB3UY5LzMfJB939gW4OUgpJxsZEBIUOiRMOgAAAAABAd8E1QLTBjkADwAYQAkKAgAFBRARAg0AL8QREgE5ETMzMzEwARQHNTY1NC4CNTQ2MzIWAtP0dx8kHzQuOUQFuKk6TCU5FBIQGRsnKUgAAAgAKf7BB8EFkQAMABoAKAA2AEQAUgBfAG0AsUBpUDRILAsYAxBCJjoeVh5eJhAYLGM0awpuby0mHwMQNAE0KSIwMBspZF5XAxBrAWtgWmdnU2BJQjsDEFABUEU+TEw3RSlgRUVgKQMAERAYARgUUA2ADQIPDQENBBALAQuABw8APwBvAAMAAC9dMhrNcTIvXV0zzXEyEhc5Ly8vETMzETMQzXEXMhEzMxEzEM1xFzIRMzMRMxDNcRcyERIBFzkRMxEzETMRMxEzETMxMAEyFhcjJiYjIgYHIzYTMhYXIyYmIyIGByM2NgEyFhcjJiYjIgYHIzY2ITIWFyMmJiMiBgcjNjYBMhYXIyYmIyIGByM2NiEyFhcjJiYjIgYHIzY2ATIWFyMmJiMiBgcjNiEyFhcjJiYjIgYHIzY2A+ldcQdPBTxFTjIFSwvFXHMGTwU8RU4yBUsFZAKrXHMGUAU8RE4yBUwFZfvmXHMGUAU8RE4yBUwFZQToXHMGUAU8RE4yBUwFZfvmXHMGUAU8RE4yBUwFZQWnXHMGUAU8RE4zBUsL+tRccwZQBTxETjIFTAVlBZFlXSwsKS/C+fJmXCwsKS9ZaQEXZl0tKycxWmlmXS0rJzFaaQPbZl0tKycxWmlmXS0rJzFaaf4YaFosLCgwwmZcLSsnMVpoAAgAKf5/B30F0wAHAA8AFwAfACYALQA1AD0AaUBECQUNARUkOhc9IAEFJzUYMiscDj4/IyYqLU87XzuvO787BDs2QDNQM6AzsDMEMy42LRcfJi4HCAgHLiYfFy02CAwFDAQAPy8SFzkvLy8vLy8vLxDNXRDNXRDNEM0REgEXOREzETMxMAUXBgYHIzY3Ayc2NjczBgcBNxYWFxUmJwUHJiYnNRYXATQ2NxcGBwEUBgcnNjcDIiYmJzcWFwEXFhYXByYnBDcLEUYkYTUROwsTSR9hNBICIw5HyEHdgftoDkK/T92BA6aumEXqP/zou4tFvWsoEThQD0N7TANoEyZaF0OQNyMOQr9P3YEEmA5HyEHcgv4WCxNJH2E1ETsLEUYkYTURAagXWzhEmC78lRdeM0R1TwLgV8AuRsZj/OkEQsI9Rt5LAAIAx/5/BiUHYgAUACIAXUAzDA4CBQUUCREOCg0NDiAYFAUjJB8PGO8YAgkYGBwPFQEiAxUGEhQHAAMUEgwiDglpWQ4SAD8rABg/Pz8zEjk5xl9eXTIyL15dMxESARc5ETMRMzMRMxEzETMxMBMzERQHBzMBMxEzAyMTIxE0NyMBIwEiJiczFhYzMjY3MwYGx6wLBAkDJMvJlNOeqhMJ/NfMAkm+rQulCl1uaWMJqg2/Bbb825WxUQS8+uz93QGBAxup+vtCBiuPqGxOXV2jlAACAK7+hQUxBhAAEQAfAGJANwkLAQMDEAYMDgsHCgodCxUQBSAhCSIcDxUBFRUZoBIBDxJfEgIJAxIDDhARDwQPEBULBl1ZCxUAPysAGD8/PxI5OS9fXl1dMzMvXTM/ERIBFzkRMxEzMzMRMxEzETMxMAERFAcBMxEzAyMTIxE3NwEjESUiJiczFhYzMjY3MwYGAVgMAkzdvIG4fagDBf223QH0vq4LpghccWljCaoMvgRK/YFfugOY/E797QF7AomPgPxoBEqPkKdnU11doJcAAgAvAAAEgwW2ABEAGgB6QEcRDwQIEhIBDwsWFgYPAxscBxEAEWlZBAgAAQAAEAAgAAMPAwAACAIIGmlZ2AgBOggBCQgBDwAIoAgCEgMICA8CAw8Sa1kPEgA/KwAYPxI5L19eXV5dXV0rERIAORgvX15dXTMrEQAzERIBFzkRMxEzMxEzMxEzMTATMzUzFSEVIREzIBEUBCEhESMBMzI2NTQmIyMvmLgBUP6wuAJM/ur+7f5tmAFQ0b22t8TJBP64uKD+7v5g0dsEXvw+h4mDewAAAAACABIAAARUBhQAEQAaAIxAVg0LAAQTEw8LBxcXAgsDGxwDDQ4NX1kADg4EEAQSXVnUBOQEAgaVBAEDbwQBLQQ9BAJdBAEFAAQBAAQwBAJvBAEPBJ8EzwTfBAQLAwQECxAACxNeWQsVAD8rABg/EjkvX15dcXFyX11xXV9dX10rERIAORgvMysRADMREgEXOREzETMzETMzETMxMAEhFSERISARFAYjIREjNTM1MxERITI2NTQmIwFiAS/+0QE0Ab7i4f4dnJy0ASeIj4CbBSGJ/e/+yaSsBJiJ8/vc/qFbWllRAAACAMcAAAR9BbYADwAcAFlALQQDGBQTEAoKCxYTABgYEwsDHR4DBgwJFhMcEBUJDAkQa1kJCQwLEgwca1kMAwA/KwAYPxI5LysREgA5ERI5ORESOTkREgEXOREzETMRMxEzETMRMzMxMAEUBgcXBycGIyMRIxEhIAQBMzI3JzcXNjU0JiMjBH1yaXVpkWKKsrgBkQEPARb9AqJfOGZxhXa7wcMECIHIOJlYvhv9xwW22P35CIdWqkaojYwAAAIArv4UBHsEXgAXACgAXUA0FBEcCgMDBgYHJCETFhEmJhYhIgcFKSohJCMDGB8LAhYTBAAOCA8HGw4YXVkOEAAfXVkAFgA/KwAYPysAGD8/ERIXORESFzkREgEXOREzETMRMxEzERczETMxMAUiJyMWFREjETMXMzY2MzISERAHFwcnBgMiBgcVFBYzMjcnNxc2NTQmArbddwwMtJQaCECmbtbtsnBqgURyo5EClKYsJndwfV2RFJ+UIP49BjaWWVH+1/7y/q+QmlSsGAPbuMUj38cMmlSiZ+nQzgABAC8AAAQOBbYADQBFQCQKCAMHBwwIAQUIAw4PBgoLCmlZAw8LAQsDCwsNCBINAmlZDQMAPysAGD8SOS9fXl0zKxEAMxESARc5ETMzETMRMzEwARUhESEVIREjESM1MxEEDv1xAab+WriYmAW2pP4RoP19AoOgApMAAAAAAQAQAAADTgRKAA0AR0AmCQcCBgYLBwAEBwMODwUJCglkWQIPCh8KAg4DCgoMBxUMAV1ZDA8APysAGD8SOS9fXl0zKxEAMxESARc5ETMzETMRMzEwASERIRUhESMRIzUzESEDTv4SAVj+qLScnAKiA7L+tov+IwHdiwHiAAEAx/4ABOwFtgAbAExAKAcZFAkDAwQOGQQZHB0LAGlZDwsBCwMLCwQFERdpWREmBBIFCGlZBQMAPysAGD8/KxESADkYL19eXSsREgE5OREzETMRMzMRMzEwASIHESMRIRUhETYzIAAREAAhIiYnNRYzIBE0JAI3Xlq4A1L9Zl94AT4BWP7f/wBVgEZ7iQF3/v8Cgwz9iQW2pP4JCv6q/sf+xf6lFRykMQHy7/4AAAAAAQCu/goECARKABoASkAnEQcCEw0NDhgHDgcbHBUKYVkPFQEUAxUVDw4VDxJdWQ8PAAVhWQAcAD8rABg/KwAYPxI5L19eXSsREgE5OREzETMRMzMRMzEwASInNRYzIBE0JiMiBxEjESEVIRE2MyAAERACAk6Mam5+AQqttU48tAKq/gpSPAENAQvq/go8nz0BldfLDv4vBEqY/r8M/uH+3f70/tsAAAAAAQAC/n8HFAW2ABUATEAqCAwBFQYREQMSDA0NCRIAFQUWFwAJAxMGEAYVBwQBAxIVEg0iDwppWQ8SAD8rABg/PzM/MzMSFzkREgEXOREzETMzETMRMxEzMTABATMBETMRATMBATMRIxEjAREjEQEjAk79ycwCL7ECL8z9yQHLwrBm/cWx/cPTAvACxv08AsT9PALE/Tz9sv3bAYEC5f0bAuX9GwABAAL+hQY9BEoAFQBLQCoECQINDRUOCAkJBQ4SExEGFhcSBRUPDAIGERMJIgMAEw8OERULBl1ZCxUAPysAGD8zPzMzPxESFzkREgEXOREzETMzETMRMzEwATMRATMBATMRIxEjAREjEQEjAQEzAQKqqAG+w/47AWfIrmL+Jaj+Jc0B7P47xQG8BEr96wIV/ev+Yf3vAXsCLf3TAi390wI1AhX96wAAAP//AE7+PQRGBcsCJgGxAAABBwN8AV4AAAAOuQAB/+O0My0NByUBKzX//wBE/j0DjwReAiYB0QAAAQcDfAEMAAAADrkAAf/ytC8pGRMlASs1AAEAx/5/BT0FtgARAEVAJA8DDAgICREGAgMDBgkDEhMGEQwDBwcJDgoDCRIDIgUAaVkFEgA/KwAYPz8/MxI5ERczERIBFzkRMxEzETMRMxEzMTAlMxEjESMBBxEjETMRNwEzAAEEgbywcP33lbi4fgIJ1/7R/uyk/dsBgQK6g/3JBbb9L4sCRv6w/tMAAQCu/oMEVgRKAA4AP0AhAQYOCgoLBQYGAgsDDxAOCQIDCwwGIgAMDwsVCANdWQgVAD8rABg/PzM/ERIXORESARc5ETMRMxEzETMxMAEzAQEzESMRIwERIxEzEQM3xf4rAXe4rFj+ELS0BEr97/5f/esBfQIt/dMESv3rAAEAxwAABPQFtgATAFBAKAwPDxUGAgIDChISBxMDExQVExEDAAcKCAMLBgYADgMBAQMLBAMQAxIAPzM/MxI5ERczERIXORESOTkREgE5OREzMxEzETMRMxEzETMxMAEHESMRMxE3ETMVATMAAQEjAREjAfx9uLh9fQGN1/7R/uwCWtn+Xn0Comv9yQW2/SuOAV7TAbz+sP7T/McCRv7ZAAABAK4AAARIBEoAFABJQCYHAwMECw8TEwgUEA0UBAQVFgASFAMRAggLCQMMBwIHBAwFDxEEFQA/Mz8zEjk5ERIXORESFzkREgEXOREzMxEzMxEzETMxMAEmJxEjETMRNxEzFQEzARUBIwEVIwHVTiW0tHODAQTF/jcB8NH+4YMBtE4p/dUESv3peQFKxQEZ/h5s/gQBM9cAAAEALwAABPQFtgAWAFhALAoNDRgGERYUBAgTEwEUDBEUERcYBwQWAAAWaVkAAAIIEQwDEhIUCQIDDhQSAD8zPzMSOREXMxE5LysREgA5ORESATk5ETMRMzMRMzMRMxEzETMRMzEwEzM1MxUzFSMRATMAAQEjJgInBxEjESMvmLjV1QKH1/7R/uwCWt2D/YOVuJgFCK6uov57AtX+sP7T/MeuAV6ugf3HBGYAAAABABIAAAQxBhQAGABQQCkJCAQVFRgBFhATEQ8GExYFGRoTEAoUFA4SFhUHGAAYX1kEAAACDg8CAAA/PxI5LzMrEQAzGD8zEjkRMzMzERIBFzkRMxEzMzMRMzMzMTATMzUzFSEVIREHMzc2NwEzAQEjAQcRIxEjEpyyAXf+iQYIE0AsAV7V/kQB2df+g32ynAVcuLiF/fyeGVsuAXP+K/2LAf5r/m0E1wABAA4AAAWLBbYADQBEQCMEBgYPAgoKCwUICAsNAw4PAggFAwkJCwADAwcLEgANaVkAAwA/KwAYPzM/ERI5ERczERIBFzkRMxEzETMRMxEzMTATIREBMwEBIwEHESMRIQ4CCQKH2f28AljX/fqXt/6uBbb9KwLV/YP8xwK6g/3JBRIAAAABACUAAAT0BEoADAA2QB0CCQkKBgQFCgwFDQ4CCAUDCgADDwcKFQAMXVkADwA/KwAYPzM/ERIXORESARc5ETMRMzEwEyERATMBASMBESMRISUCDgHXw/4rAfzP/g6w/qIESv3rAhX97f3JAi390wO2AAAAAAEAx/5/BdUFtgAPAFpANAwICAkADQUCAwMFCQMQEQwHaVnYDAE6DAEJDAEPAAygDAISAwwMBQ4KAwkSAyIFAGlZBRIAPysAGD8/PzMSOS9fXl1eXV1dKxESARc5ETMRMzMRMxEzMTAlMxEjESMRIREjETMRIREzBSWwsLb9ELi4AvC2pP3bAYECqv1WBbb9lgJqAAABAK7+hQUMBEoADwBqQD4BDQ0OBQIKBwgICg4DEBEIIgEMXVmEAZQBAgZFAQEDHwEBDQHdAe0BAxAFDwEBFAMBAQoDDw8OFQoFXVkKFQA/KwAYPz8zEjkvX15dX15dXV9dX10rABg/ERIBFzkRMxEzMxEzETMxMAERIREzETMRIxEjESERIxEBYgJUtKKypP2stARK/jcByfxO/e0BewHp/hcESgAAAAABAMcAAAZ1BbYADQBVQDEKBgYHAgsDAAMHAw4PCgVpWdgKAToKAQkKAQ8ACqAKAhIDCgoHDAgDAwcSDAFpWQwDAD8rABg/Mz8REjkvX15dXl1dXSsREgEXOREzMxEzETMxMAEhESMRIREjETMRIREhBnX+sLj9Eri4Au4CCAUS+u4Cqv1WBbb9lgJqAAEArgAABckESgANAGNAOwELCwwHAggFCAwDDg8BCl1ZhAGUAQIGRQEBAx8BAQ0B3QHtAQMQBQ8BARQDAQEMAw0PCAwVAwZdWQMPAD8rABg/Mz8REjkvX15dX15dXV9dX10rERIBFzkRMzMRMxEzMTABESERIRUhESMRIREjEQFiAlQCE/6htP2stARK/jcByZT8SgHp/hcESgAAAAEAx/4ACCkFtgAgAFJALBQABAUIAAABDRoaAQUDISIKHWlZDwoBCwMKCgUGERdpWREmAQUSBgNqWQYDAD8rABg/Mz8rERIAORgvX15dKxESARc5ETMRMxEzETMRMzEwISMRIREjESERNjMgABEUAgYjIiYnNRYzMjY1NCYjIgYHBN22/Vi4BBZMfwExAVCB9KhPh0aGfre9790rfRcFEPrwBbb9YQz+pP7Kzf7XmxUcpDH98vb4BwcAAAAAAQCu/goGrARKABwAUkAtBRAUFRgQEBEACgoRFQMdHhoNYVkPGp8aAgsDGhoWERUVFhNdWRYPAwhhWQMcAD8rABg/KwAYPzMSOS9fXl0rERIBFzkRMxEzETMRMxEzMTAlEAIjIic1FjMyETQmIyIHESMRIREjESERNjMyAAas18OEY2pv8KSqRzi0/e+0A3lNOvYBCDv+8v7dPJ89AZXXyxL+MwOy/E4ESv4nDP7VAAACAH3/rAXjBc0AKQA1AG1AOxYzCAAcETAkAzMAKiozISQRBTY3AzMtBQwnCicta1kAJxAnAgkDJycOFAoFaVkKFBppWRQEDh9pWQ4TAD8rABg/KwAYLysREgA5GC9fXl0rERIAORESOTkREgEXOREzETMRMxEzETMRMzEwARQCBxYzMjcVBiMiJwYjIAAREAAhMhcHJiYjIBEQEjMyNyYCNTQSMzISBzQmIyIGFRQWFzY2BbqHckJVTj04XbKUZpD+yv6hAUgBO4FcMRZmMv4/+uYyKlBex7C1w7pkWFlkWk5hcAKmr/7OVh8WoRlkJAGJAVYBeQGJI5wJFP2o/ub+0gtfARqf8gEG/vn/r7+/q4r3UkH5AAAAAgBx/8UE1wReAAoANAB5QCgYADMrHhMAJS4DKwYGAyMlEwU1NiMDIQgNLg8oKAhkWQAoECgCEwMouP/AQBgJDEgoKA8WCzBeWQsWG11ZFhAPIV1ZDxYAPysAGD8rABgvKxESADkYLytfXl0rERIAOTkREjk5ERIBFzkRMxEzETMRMxEzETMxMAEUFhc2NjU0IyIGASInBiMiJgI1EBIzMhcHJiMiBhUUFjMyNyY1NDYzMhYVFAYHFjMyNxUGAvZDOkJRg0VIAV6WfGh2l+J6/ONfTSdGQZeOpZ8+HIWnmpWeaVsyPkIzLAHyXJ4vKphr4Xj9Zk8ojAECoQEUAS8YkhPQ4MjODJDbrMC8sH7OPRkOjxD//wB9/j0EzwXLAiYAJgAAAQcDfAInAAAAC7YBUCMdDxUlASs1AAAA//8Acf49A5MEXgImAEYAAAEHA3wBhQAAAAu2AVIiHAMJJQErNQAAAAABABL+fwRcBbYACwA0QBsGCwgJBAkLAQQMDQkiBQECAWpZAgMLBmlZCxIAPysAGD8rEQAzGD8REgEXOREzETMxMAEhNSEVIREzESMRIwHb/jcESv43sbG4BRCmpvuU/dsBgQAAAQAp/oUDogRKAAsANEAbBgsICQQJCwEEDA0JIgUBAgFdWQIPCwZdWQsVAD8rABg/KxEAMxg/ERIBFzkRMxEzMTABITUhFSERMxEjESMBi/6eA3n+m6KwpAO0lpb84v3vAXsA//8AAAAABIcFtgIGADwAAAABAAD+FAQQBEoADgAmQBENEAMAAQEPEAcDDgIMAw8BGwA/PzMvMxI5ERIBOREzMhEzMTABIxEBMxMWFzM2NjcTMwECYrT+UrzmTxMKDj0Y47z+Uv4UAegETv2m3V85w0ACWvuyAAAAAAEAAAAABIcFtgAQAEJAIgISDwQICA0JBgkLAxESBwsMC2lZBAwMAAAOAwMJAQ8DCRIAPz8zEhc5ETkvMysRADMREgEXOREzMxEzMhEzMTABATMBFSEVIREjESE1ITUBMwJEAX3G/hkBLf7Tuf7RAS/+GckC5wLP/IE5ov6kAVyiMQOHAAEAAP4UBBAESgAVADxAHhEXBxMBAQYCFQIEAxYXDAUQBw8CGwAEBQRfWRMFFQA/MysRADMYPz8zEjkREgEXOREzMxEzMhEzMTAFESMRITUhATMXEhYXMzYTNzMDAyEVAmK0/ugBFv5UvDynUxIIJ8VcvNXXAROJ/p0BY4kESp7+VflUrAH38/3a/dyJAAAAAAEACP5/BO4FtgAPAENAIw4DCggMBgIDAw8GCQgFEBEPBgwGDAgNCgMIEgMiBQBpWQUSAD8rABg/Pz8zEjk5ERI5ERIBFzkRMxEzETMRMzEwJTMRIxEjAQEjAQEzAQEzAQQ9sbFm/n3+d8MB5v45zQFmAWnC/jyk/dsBgQJ7/YUC+gK8/cMCPf1IAAAAAQAl/oMETARKAA8ARUAkBQkBDwMNCQoKBg0ADwUQEQYNAw0DDwEKIgQBDw8VDAddWQwVAD8rABg/PzM/ERI5ORESORESARc5ETMRMxEzETMxMAEBMwEBMwEBMxEjESMBASMBsv6FzQEbARjL/oUBJaCwUv7V/tHLAjECGf5iAZ795/5n/esBfQG2/koAAAAAAQAS/n8GtgW2AA8AQEAiDAUADQIDAw0KBQcFEBEOAwMiCwcIB2pZCAMADAUMaVkFEgA/KxEAMxg/KxEAMxg/PxESARc5ETMRMxEzMTAlMxEjESERITUhFSERIREzBgC2rvuu/lwEMP4tAtm4pP3bAYEFEKam+5QFEgAAAAEAKf6FBaYESgAPAEBAIgILBgMICQkDAAsNBRARCSIEDwENDg1dWQ4PBgILAl1ZCxUAPysRADMYPysRADMYPz8REgEXOREzETMRMzEwASERIREzETMRIxEhESE1IQN5/p4CNbSmsPxu/sUDUAO0/OQDsvxM/e8BewO0lgABAKT+fwV/BbYAFwA7QB8PDAAVBQIDAwUMAxgZCRJpWQkJBRYNAwMiBQBpWQUSAD8rABg/PzMSOS8rERIBFzkRMxEzMxEzMTAlMxEjESMRBgYjIiY1ETMRFBYzMjY3ETMEz7CwuJXIaNDeuHyMX7GjuKT92wGBAlg1J8GyAkf903Z1HjYCxAAAAQCY/oME2wRKABYAPUAfARUJBg4LDAwOFQMXGAwiEgNdWRISDgcWDw4JXVkOFQA/KwAYPzMSOS8rABg/ERIBFzkRMxEzMxEzMTABERQzMjY3ETMRMxEjESMRBgYjIiY1EQFM01ylZbSisqRusWykvgRK/nC8Nz4B1/xO/esBfQHpRzismAGcAAEApAAABM8FtgAWAE9AJwUCCxUVCBYQDRERFgIDFxgJCQMAFhYRFAALCAAIaVkAABEOAwMREgA/PzMSOS8rEQAzETMSORgvERI5LxESARc5ETMzETMzETMRMzEwASARETMRFBYzETMRNjcRMxEjEQYHESMCdf4vuImQfYuXu7uudH0B/AFzAkf903pxAVr+rg46Asj6SgJUQBD+zQAAAAABAJgAAAQpBEoAGABRQCgBFwYQEAMRCwgMDBEXAxkaBAQYFBERDBQGAw8UFANdWRQUDAkYDwwVAD8/MxI5LysRADMRMxESORgvERI5LxESARc5ETMzETMzETMRMzEwAREUFxEzETY3ETMRIxEGBxUjNQcjIiY1EQFMwnlwfrS0gG55DA6kuARK/m62BgEp/uMXVAHX+7YB6VcZ+OkCrZcBngAAAAEAxwAABPIFtgASAC1AFgIRERIICRIJExQEDWlZBAQSAAMJEhIAPzM/ETkvKxESATk5ETMRMxEzMTATMxEkMzIWFREjETQmIyIGBxEjx7gBAsPP37l8jGa1l7gFtv2oXMGx/bgCLXZ2IjL9OwAAAAABAK4AAAROBEoAEgAtQBYLBwcIEgAIABMUDgNdWQ4OCAkPAAgVAD8zPxI5LysREgE5OREzETMRMzEwIRE0IyIGBxEjETMRNjYzMhYVEQOa0V+gaLS0Y7tsp7sBj7s1QP4rBEr+FEQ7q5j+ZgACADf/7AZQBc0AHwAlAHNAQBcPBQAjEBAIHQ8kJB0AAyYnDwIBDgMCAiMdECMQaVkHGCMBeiMBSSMBDyOvIwILAyMjGgsLIGlZCwQaE2lZGhMAPysAGD8rERIAORgvX15dXV1xMysRADMRMxgvX15dERIBFzkRMxEzMxEzETMRMzEwEzQ3MwYVFDMzEgAhIAARFSESADMyNjcVBgYjIAADIiYBIgYHIRA3HZoVbyIpAUgBGQEoATT72w4BAfGK4F9x3Y/+xv6gFZKfA7vJ7BIDYAOFTTotQ2UBRwFP/pL+oWj+//72MiCoKSIBYQFLdgIb//MB8gAAAAIALf/sBOkEXAAdACQAZkA3GxQKBRUNFCIiDQMFBCUmDwcBDgMHByEDFSEVZFkMGSEBAw8hARAGISEAEBAeXVkQEAAXYVkAFgA/KwAYPysREgA5GC9fXl1fXTMrEQAzETMYL19eXRESARc5ETMRMxEzETMxMAUiACckNTQ3MwYVFDMzNjYzMhIVFSESITI2NxUGBgMiBgchNCYDUPP+6Aj+8BuTFGgVGvzJ0Pb9DwgBUGSgZFugnoCXDAIxihQBF/8E3Uc0JUVl3fD+9uNt/oMgKpwnIAPdn5uYogACADf+fwZQBc0AIQAoAH9ARx0VCwYlFhYOAyAhFSYmIQMGBCkqDwgBDgMICCUDFiUWaVkNGCUBeiUBSSUBDyWvJQILAyUlABEhIhEiaVkRBAAfHxlrWR8TAD8rEQAzGD8rABg/ERI5L19eXV1dcTMrEQAzETMYL19eXRESARc5ETMRMxEzMxEzETMRMzEwBSQAAyImNTQ3MwYVFDMzEgAhIAARFSESADMyNjcVBgcRIxMiBgchNCYDov77/t0Skp8dmhVvIh8BUAEbAScBNfvbDgEB8YrgX7bqslDJ7BIDYMcKHQFdASh2d006LUNlAT8BV/6U/qVu/v/+9jIgqEIF/o8Gqv/z//MAAAAAAgAt/oUE6QRcACAAJwByQD4bFAoFFQ0fIBQlJSANAwUFKCkgIg8HAQ4DBwckAxUkFWRZDBkkAQMPJAEQBiQkABAQIV1ZEBAAHh4XXVkeFgA/KxEAMxg/KxESADkYL19eXV9dMysRADMRMxgvX15dPxESARc5ETMRMxEzETMRMzEwBSYCJyQ1NDczBhUUMzM2NjMyEhUVIRIhMjY3FQYGBxEjEyIGByE0JgLVwdEG/vAbkxRoFR77xtD2/Q8IAVBkoGRIkFuwSoCXDAIxiggfARLZBN1HNCVFZdzx/vbjbf6DICqcIh4D/pUFRJ+bmKL//wBSAAACYgW2AgYALAAA//8AAgAABtEHYgImAbAAAAEHAjYBGQFSABa5AAH//EAJFR0EBSUBEgUmACs1ASs1//8AAgAABfoGEAImAdAAAAEHAjYAsAAAAAu2AQAWHgABJQErNQAAAAABAMf+AAUnBbYAHgBGQCQKDxYHAwMEDxwcCwQDHyAHDAwAa1kMDAQFExlpWRMmCQUDBBIAPz8zPysREgA5GC8rEQAzERIBFzkRMxEzETMzETMxMAEiBxEjETMRNwEzATcgABEUAgYjIiYnNRYzMhI1NCQCZIZfuLjRAazb/YsZAUkBY4f8rFN+SnuYssj+7wJxH/2uBbb9POcB3f1SAv68/s3P/tebFB2kMQED7OX5AAEArv4KBDUESgAcAERAIxkABhcTExQADQ0aFAMdHhcaGhBdWRoaFBgVDxQVBAphWQQcAD8rABg/PzMSOS8rEQAzERIBFzkRMxEzETMzETMxMCUUBgYjIic1FhYzMjY1NCYjIgcRIxEzEQEzARYABDVvy4iHYy9sRIaVv7RRX7KyAd/H/jX6AQA7sP2EPJsYJdXC1MIZ/kgESv38AgT+HAT+6AAAAQAC/n8FqgW2ABYAO0AfAwAFAQQEBQ4DFxgDIhUHaVkVAwUAaVkFFQwRa1kMEwA/KwAYPysAGD8rABg/ERIBFzkRMxEzMzEwJTMDIxMjESEHAgIGIyInNRYzMjYSEyEE48eT05+4/jEfP16Xgko7ND1PXW03AyCk/dsBgQUU7v4U/lanGZoZxwK+Aa4AAAAAAQAO/oUErARKABQAO0AfAwAFAQQEBQ0DFRYDIhMHXVkTDwUAXVkFFQsQXlkLFgA/KwAYPysAGD8rABg/ERIBFzkRMxEzMzEwJTMDIxMjESECAgYjIic1FjMyEhMhA/C8g7Z9tf67GmCZdj0iGR9shSMClpj97QF7A7T+nv5hvwyJBgHMAfsAAAEAx/4ABSUFtgAVAFVAMAYSDg4PABMLDwsWFxINaVnYEgE6EgEJEgEPABKgEgISAxISDxADCWlZAyYUEAMPEgA/PzM/KxESADkYL19eXV5dXV0rERIBOTkRMzMRMxEzMzEwJRAAISImJzUWMyARESERIxEzESERMwUl/uT+/VR9THuMAX/9ELi4AvC2j/7D/q4UHaIxAekCH/1WBbb9lgJqAAAAAQCu/goEagRKABUAYUA5Ag8LCwwTEAgMCBYXDwpdWYQPlA8CBkUPAQMfDwEND90P7Q8DEAUPDwEUAw8PDBENDwwVAAVhWQAcAD8rABg/PzMSOS9fXl1fXl1dX11fXSsREgE5OREzMxEzETMzMTABIic1FjMyNjURIREjETMRIREzERACAtGGX25pfXT9rrS0AlK21v4KOp87xMUBuP4XBEr+NwHJ++f+9P7lAAEAx/5/Be4FtgAPAF5ANgMFDAgICQANBQEEBAUJAxARDAdpWdgMAToMAQkMAQ8ADKAMAhIDDAwFDgoDCRIDIgUAaVkFEgA/KwAYPz8/MxI5L19eXV5dXV0rERIBFzkRMxEzMxEzETMRMzEwJTMDIxMjESERIxEzESERMwUlyZbToLb9ELi4AvC2pP3bAYECqv1WBbb9lgJqAAAAAAEArv6FBScESgAPAG5AQAgKAQ0NDgUCCgYJCQoOAxARCCIBDF1ZhAGUAQIGRQEBAx8BAQ0B3QHtAQMQBQ8BARQDAQEKAw8PDhUKBV1ZChUAPysAGD8/MxI5L19eXV9eXV1fXV9dKwAYPxESARc5ETMRMzMRMxEzETMxMAERIREzETMDIxMjESERIxEBYgJUtL2DuH60/ay0BEr+NwHJ/E797QF7Aen+FwRKAAABAKT+fwTPBbYAFwA7QB8PDAIDABUFBQMMAxgZCRJpWQkJARYNAwMiAQRpWQESAD8rABg/PzMSOS8rERIBFzkRMzMRMxEzMTAhIxEjETMRBgYjIiY1ETMRFBYzMjY3ETMEz7KwqpXIaNDeuHyMX7GjuP5/AiUBtDUnwbICR/3TdnUeNgLEAAAAAQCY/oMEOQRKABYAPUAfARULDAkGDg4MFQMXGAwiEgNdWRISCgcWDwoNXVkKFQA/KwAYPzMSOS8rABg/ERIBFzkRMzMRMxEzMTABERQzMjY3ETMRIxEjETMRBgYjIiY1EQFM01ylZbShsZ5usWykvgRK/nC8Nz4B1/u2/oMCFQFRRzismAGcAAEAx/5/B0IFtgAYAEVAJBETAgYGBw4WEw8SEhMHAxkaAhcLAxMMCAMABxIRIhMOaVkTEgA/KwAYPz8zPzMSFzkREgEXOREzETMzETMRMxEzMTAhASMXFhURIxEhATMBIREzAyMTIxE0NyMBA0z+HggHCKoBEAHFCAHJAQ7HlNWitg4I/hgFAoSYXfx3Bbb7UgSu+u792wGBA5aD5/sAAAABAK7+hQYEBEoAGQBCQCIIChQVBQoGCQkKFQMaGxMLAAMKFggiAxYPFQ8KCgVdWQoVAD8rEQAzMxg/Mz8REhc5ERIBFzkRMxEzETMRMzEwJTY3ATMRMwMjEyMRBwcBIwEmJicRIxEzARYC9h4uAR7ovIO4f6ITP/7ukv7uEzQHouEBHyWsbXQCvfxO/e0BewOJO6z9XgKmLZoc/HcESv1DXQAA//8AUgAAAmIFtgIGACwAAP//AAAAAAUbB2ICJgAkAAABBwI2AD0BUgAWuQAC//xACREZBQYlAg4FJgArNQErNf//AF7/7APXBhACJgBEAAABBgI28QAADrkAAv/ntCkxExklASs1AAD//wAAAAAFGwcpAiYAJAAAAQcAagA/AVIAF0ANAwIADiAFBiUDAiMFJgArNTUBKzU1AAAA//8AXv/sA9cF1wImAEQAAAEGAGr1AAAQsQMCuP/vtCY4ExklASs1Nf////4AAAaRBbYCBgCIAAD//wBe/+wGgQReAgYAqAAA//8AxwAAA/gHYgImACgAAAEHAjYADgFSABW0AQwFJgG4//y0DxcCCyUBKzUAKzUA//8Acf/sBBsGEAImAEgAAAEGAjYMAAALtgITHycDCiUBKzUAAAIAef/sBWoFzQATABoARUAlAhgPCREXDxcbHBAYaVkAEBAQAhADEBAMBgYAaVkGBAwUaVkMEwA/KwAYPysREgA5GC9fXl0rERIBOTkRMzMRMzMxMAEiBzU2NjMgABEQACEgABE1IQIAAzISNyEUFgKo4fJ92oABTAFy/qX+yP7T/s8ELxH+/r7N9BD8lckFK1SoLCL+cP6c/qL+cQF5AXZGAQMBB/tiAQXt//MAAAD//wBo/+wEEgReAgYESAAA//8Aef/sBWoHKQImAt4AAAEHAGoAcwFSABqxAwK4/6VAChstAwklAwIwBSYAKzU1ASs1Nf//AGj/7AQSBdcCJgRIAAABBgBqxAAAELEDArj/v7QcLhIDJQErNTX//wACAAAG0QcpAiYBsAAAAQcAagEXAVIAF0ANAgEAEiQEBSUCAScFJgArNTUBKzU1AAAA//8AAgAABfoF1wImAdAAAAEHAGoArAAAAA23AgEAEyUAASUBKzU1AP//AE7/7ARGBykCJgGxAAABBwBq//kBUgAXQA0CAT0FJgIBACg6DQclASs1NQArNTUAAAD//wBE/+wDjwXXAiYB0QAAAQYAao4AABCxAgG4//a0JDYZEyUBKzU1AAEASP/sBDsFtgAYAEhAJhQPGAMDDw8AEhUIBRkaEwAAEmtZAAAGGBUWFhVpWRYDBgxrWQYTAD8rABg/KxESADkSORgvKxEAMxESARc5ETMRMxEzMTABBAQVFAQhICc1FhYzMjY1ECEjNQEhNSEVAggBFAEf/sr+6f79o2TiYsfE/kGJAeH9XQONA0IL1MHP50+oMDCWiwEIlgHQpJEAAAABAB3+FAO2BEoAGQBLQCcVDxkDFgkDDw8AEwkEGhsUAAATXlkAAAcZFhcXFl1ZFw8HDF1ZBxsAPysAGD8rERIAORI5GC8rEQAzERIBFzkRMxEzETMRMzEwARYEFRQGBiMiJzUWMzI2NTQmIyM1ASE1IRUBvusBDYb5n++Mt8yiwNDOeAHA/Y0DRgHTEPjJkOJ8SKRWuZudqX0B8ZiDAAD//wDJAAAFYAa8AiYBsgAAAQcBTQC2AVIAIEAOAX8VjxWfFa8VBBUFJgG4//20FRQRCSUBKzUAK101AAD//wCuAAAEdQVqAiYB0gAAAQYBTTcAAAu2AQAREA0GJQErNQD//wDJAAAFYAcpAiYBsgAAAQcAagDBAVIAGbYCAScFJgIBuP/+tBIkEQklASs1NQArNTUA//8ArgAABHUF1wImAdIAAAEGAGpCAAANtwIBAQ4gDQYlASs1NQAAAP//AH3/7AXDBykCJgAyAAABBwBqAM8BUgAXQA0DAi0FJgMCABgqBgAlASs1NQArNTUAAAD//wBx/+wEaAXXAiYAUgAAAQYAahsAAA23AwIAGCoHACUBKzU1AAAA//8Aff/sBcMFzQIGAnsAAP//AHH/7ARoBF4CBgJ8AAD//wB9/+wFwwcpAiYCewAAAQcAagDPAVIAF0ANBAMvBSYEAwAaLAYAJQErNTUAKzU1AAAA//8Acf/sBGgF1wImAnwAAAEGAGoZAAAQsQQDuP/+tBkrBwAlASs1Nf//AD3/7ASRBykCJgHHAAABBwBq/8oBUgAZtgIBMQUmAgG4/7S0HC4DCSUBKzU1ACs1NQD//wA7/+wDgwXXAiYB5wAAAQcAav9TAAAAELECAbj/xbQZKwMWJQErNTUAAP//ABf/7AT+BrwCJgG9AAABBwFNAC8BUgAdQBQBfxqPGp8arxoEGgUmAQAaGQkSJQErNQArXTUA//8AAv4UBBQFagImAFwAAAEGAU2zAAALtgEDGxoACiUBKzUA//8AF//sBP4HKQImAb0AAAEHAGoAOQFSABdADQIBLAUmAgEAFykJEiUBKzU1ACs1NQAAAP//AAL+FAQUBdcCJgBcAAABBgBquwAADbcCAQEYKgAKJQErNTUAAAD//wAX/+wE/gdzAiYBvQAAAQcBUwCRAVIAF0ANAgEqBSYCAVUgJgkSJQErNTUAKzU1AAAA//8AAv4UBBQGIQImAFwAAAEGAVMKAAANtwIBTSEnAAolASs1NQAAAP//AKQAAATPBykCJgHBAAABBwBqAGgBUgAXQA0CASkFJgIBABQmCRMlASs1NQArNTUAAAD//wCYAAAEOQXXAiYB4QAAAQYAahcAAA23AgEAEyUSCSUBKzU1AAAAAAEAx/5/BA4FtgAJAC9AGAQJBgcCBwkDCgsHIgADaVkAAwkEaVkJEgA/KwAYPysAGD8REgEXOREzETMxMBMhFSERMxEjESPHA0f9ca6uuAW2pPuS/dsBgQABAK7+hQNKBEoACQAvQBgECQYHAgcJAwoLByIAA11ZAA8JBF1ZCRUAPysAGD8rABg/ERIBFzkRMxEzMTATIRUhETMRIxEjrgKc/hiisqQESpb84v3vAXv//wDHAAAGFwcpAiYBxQAAAQcAagEdAVIAGbYEAy0FJgQDuP//tBgqBRclASs1NQArNTUA//8ArgAABYsF1wImAeUAAAEHAGoAyQAAABCxBAO4//60FykJFiUBKzU1AAD//wAv/moEDgW2AiYCmAAAAQcDfQCcAAAAC7YBABYWBwclASs1AAAAAAEAEP5qA04ESgAZAGFANQILCRIWFg0JGAcQFAcJBBobFQsMC2RZEg8MHwwCDgMMDAkODhFdWQ4PCRZkWQkVAAVhWQAjAD8rABg/KwAYPysREgA5GC9fXl0zKxEAMxESARc5ETMRMzMRMxEzMzEwASInNRYzMjU1IxEjNTMRIRUhESEVIREzERABDjw/LjlisJycAqL+EgFY/qie/moZlhNrjwHdiwHimP62i/6u/vD+7wAAAAEACP5qBN8FtgAXAEtAKBIHDgwQChYHBwITCg0MBhgZEwoQEAoJEQ4DDBIJFGlZCRIABWtZACMAPysAGD8rABg/PzMSOTkREjkREgEXOREzETMRMxEzMTABIic1FjMyNTUjAQEjAQEzAQEzAQEzERAD8Dw/LjhiZv59/nfDAeb+Oc0BZgFpwv48AX6g/moZlhNrjwJ7/YUC+gK8/cMCPf1I/ab+1/7vAAEAJf5qBEgESgAXAEtAKBIWDgwQChYHBwITCg0MBhgZEwoQChAJEQ4PDBUJFGRZCRUABWFZACMAPysAGD8rABg/PzMSOTkREjkREgEXOREzETMRMxEzMTABIic1FjMyNTUjAQEjAQEzAQEzAQEzERADWD0+LjliXP7V/tHLAY3+hc0BGwEYy/6FASuW/moZlhNrjwG2/koCMQIZ/mIBnv3n/lr+8P7vAAEABgAABKgFtgARAF9AOAIPCg0HBAYLCwkEDRABEQ8IEhMNEQQAChEAEWlZBzkAAZoAAWgAAQAAMAACkAABAAAPBQIDDA8SAD8zPzMSOS9dcV1dcTMrEQAzETMRMxESARc5ETMRMxEzETMxMBMhATMBATMBIRUhASMBASMBIX0BM/53zQFmAWfE/nUBOf69AbjR/n3+d8UBuP6/A1YCYP3BAj/9oKL9TAJ7/YUCtAAAAQAlAAAEFwRKABEAXUA5Ag8HBAoNBgsLCQ0EEAERDwgSEw0KEQARZFkEB7UAxQDlAAOIAAGfAAEvAD8AvwADAAAPBQIPDA8VAD8zPzMSOS9dcV1dMzMrEQAzMxESARc5ETMRMxEzETMxMBMhATMBATMBIRUhASMBASMBIXMBDP64zQEbARjL/rYBE/7pAWPN/tX+0csBYP7uAnsBz/5iAZ7+MYv+EAG2/koB8AAAAAACAH8AAAQ7BbYACQASADpAHw4ABgMSABITFAILaVkAAqACAhIDAgIHBAMHEWtZBxIAPysAGD8SOS9fXl0rERIBOTkRMzMRMzEwExAhMxEzESEgJAEjIgYVFBYzM38CRr64/mH+9f7uAwSw2b23ws0BqAGkAmr6StQB2HmJjX8A//8Acf/sBD0GFAIGAEcAAAACAH//7AZ9BbYAGAAiAFpAMR0DCgciEg8PIgMDIyQXAAgAEHAQAhwDEAYaaVkABqAGAhIDBgYjCAMMHwAfa1kVABMAPzIrEQAzGD8SOS9fXl0rABgvX15dERI5ERIBFzkRMxEzMxEzMTAFIiY1NCQhMxEzERQzMjY1ETMRFAYjIicGEyMiBhUQITI2NQJM4+oBKAEijbjgZnO21rnoYnAij8+7ARd6iBLT0tngAmr7t+J7bQHd/hqyzKelAryHkv76dGkAAgBv/+wGjQYUACAALABPQCkqEh4bGSQGAwMkEgMtLg8EARUDBAwYDxUcABUoXVkVEAAhDyFdWQkPFgA/MysRADMYPysAGD8REjk5L19eXRESARc5ETMRMzMzETMxMCUyNjURMxEUBiMiJicGBiMiAhEQEjMyFhczJycRMxEUFiEyNjc1NCYjIBEUFgT8cmm2zcCCnC5TtX3V6+fNaaA8DQcEs2n9vJqOA4+f/uqIgYKGATP+vcnCV2tuVgEpAQwBDQEwTVVOVAG2+4iXhLDNI+LE/ljQzgAAAQBI/+wGgwXLACoAXEAyFhcXEwYiHx8GAQ0EKywAIHAgAhwDIBYBAgIBa1kPAgEVAwICKxAQCWtZEAQlHGlZJRMAPysAGD8rERIAORgvX15dKxESADkYL19eXRESARc5ETMRMzMRMzEwASM1MzI2NTQmIyIGByc2NjMyFhUUBgcVBBMWFjMyNjURMxEUBiMiJicmJgGox7+90pOBY7NhXGHzg9n2sZsBYgYCaHp0bbTVwMrWAgLPAqiVj4JqezlCe0pOxKWNuhkIM/7TkH14hAHH/inIxdHMk4wAAAEATv/sBdMEXgAlAFJAKyAhIR4SBQICEg4YBCYnDwMBFQMDIA4PDw5dWQ8PJhsbFF1ZGxAIAF1ZCBYAPysAGD8rERIAORgvKxESADkYL19eXRESARc5ETMRMzMRMzEwJTIRETMRFAYjIAMmJiMjNTMgNTQjIgYHJzY2MzIWFRQHFRYWFxYESteywMf+fg4Fj5KOcwEh6k6MTztSqm6718NqdgYEgwEGATP+vcrDAUlkWZOonCQijyclm4a6OQgVfWTJAAABAEj+fwThBcsAJABXQC8DBAQAFwgNCgsLDRcSHwUlJgMSExMSa1kPEwEVAxMTDSILIiIaa1kiBA0IaVkNEgA/KwAYPysAGD8REjkvX15dKxESADkREgEXOREzETMRMzMRMzEwARQGBxUWFhUVMxEjESMRNCYjIzUzMjY1NCYjIgYHJyc2NjMyFgQMuqK3wriwuubny8/J36CFZ7trLS9l+4ff/gRijbcaCBmzkvr92wGBAZ6Dh5WPgmp7OEM/PEtNxAAAAQBO/oUEKwRcAB4AUEAqFBUVEgcZHhscHB4HAw0FHyAUAwQcIgQDXVkEBB4PDwpdWQ8QHhldWR4VAD8rABg/KxESADkYLysAGD8REjkREgEXOREzETMRMzMRMzEwATQhIzUzIDU0JiMiByc2MzIWFRQHFRYWFRUzESMRIwLX/suYeQE5gnOZn0Gqz8DZyoBtqLCkAS/BlaZOUEiPTJuItjkLJoljl/3vAXsAAAEAAP/pBysFtgAgAEJAIwgSIBoXIBchIgAYcBgCHAMYEAFpWRADHRVpWR0TBgtrWQYTAD8rABg/KwAYPysAGC9fXl0REgE5OREzETMyMTABIQICBgYjIic1FjMyNhISEyERFBYzMjURMxEUBiMiJjUEBv5UOU5RjW5FQjQ9O1E+VDQC+2xw3bTPwsfNBRL+Nv4S+ncZmhttARcCIgGP+82Dc/wBx/4pwM3LxAABAA7/7AY7BEoAHQBAQCIADggFBQ4WAx4fDwYBFQMGHBBdWRwPCwNdWQsWFBleWRQWAD8rABg/KwAYPysAGC9fXl0REgEXOREzETMxMAEUFjMyEREzERQGIyImNREhAgIGIyInNRYzMhITIQPfZnPPtMHAxcr+yxpgmXY9IhkfbIUjAoUBgYJ8AQQBNf69ysPIxwI5/p7+Yb8MiQYBzAH7AAAAAAEAx//sB2YFtgAZAGVAOgoGBgcOCwMXFBQDBwMaGwAVcBUCHAMVCgVpWdgKAToKAQkKAQ8ACqAKAhIDCgoHDAgDBxIAEWlZABMAPysAGD8/MxI5L19eXV5dXV0rABgvX15dERIBFzkRMxEzMxEzETMxMAUiJjURIREjETMRIREzERQWMzI2NREzERQGBdfHyv05uLgCx7Zsb25ttM0Uy8YBLf1WBbb9lgJq+8+DdXiEAcf+KcDNAAABAK7/7AawBEoAGABxQEIBFhYXBQITDQoKExcDGRoPCwEVAwsBFV1ZhAGUAQIGRQEBAx8BAQ0B3QHtAQMQBQ8BARQDAQEXAxgPFxUQCF1ZEBYAPysAGD8/MxI5L19eXV9eXV1fXV9dKwAYL19eXRESARc5ETMRMzMRMxEzMTABESERMxEUFjMyEREzERQGIyImNTUhESMRAWICPrRoc8+ywMHEy/3CtARK/jcByf05g30BBgEz/r3Kw83Cbv4XBEoAAAAAAQB9/+wFogXLAB0APUAgDxwWCAIcHB0IAx4fAB1pWQAABQwME2lZDAQFGWlZBRMAPysAGD8rERIAORgvKxESARc5ETMRMxEzMTABIRUQACEgABE0EiQzMhYXByYmIyAAERAAMzI2NSEDYgJA/sz+xf65/pGzAVTpeehdRlnPYf79/uQBCvDS2P6BAvZY/qP+qwGQAWHlAVW0MCqeJy/+yP7q/uT+zOTlAAAAAAEAcf/sBLYEXgAZAEVAJQwCEgcCGBgZBwMaGwAZXVkPAAETAwAABAoKD11ZChAEFV1ZBBYAPysAGD8rERIAORgvX15dKxESARc5ETMRMxEzMTABIRUQISAAERAAITIXByYjIgYVFBYzMjY1IQKwAgb9+P7s/tcBQgEi3ao9qKbM2sa9pqz+sAJIRv3qASkBDgEPASxQjUrdy87WnJcAAAABABL/7AT+BbYAFABAQCIDCgUTDQoKEwADFRYAC3ALAhwDCwQAAQBqWQEDEAhpWRATAD8rABg/KxEAMxgvX15dERIBFzkRMxEzETMxMBM1IRUhERQWMzI1ETMRFAYjIiY1ERIEPv41c3DgttPDydIFEKam/HODdfwByf4pwM3LxAOVAAABACn/7ASaBEoAFAA+QCAIEAoDExAQAwUDFRYPEQEVAxEJBQYFXVkGDwANXVkAFgA/KwAYPysRADMYL19eXRESARc5ETMRMxEzMTAFIiY1ESE1IRUhERQWMzI2NREzERADFMPO/qYDav6kanNoa7MUysUCO5SU/c2DfXqGATn+vf5zAAABAG3/7ARkBcsAJwBdQDMlJCQNFiENAAYcHBIAIQQoKSUTEBATa1k6EAEDDxDfEAIPBhAQHgMDCmtZAwQeGWtZHhMAPysAGD8rERIAORgvX15dX10rERIAORESARc5ETMRMxEzETMRMzEwEzQkMzIWFwcmJiMiBhUUFjMzFSMiBhUUFjMyNxUGISAkNTQ2NzUmJpoBDOOJ4nBiZ7RqjZrQzs/N3+rHtu3Hr/71/vP+1s+6qrIEXKnGRUuDQjV5bHuNmIuFi4hcqk3cx5a/FggZsgAA//8AWP/sA5gEXgIGAYIAAAABAAL+agWDBbYAHgBAQCMCGwkdBwcJEgMfIAkbaVkJEhkLaVkZAxAVa1kQEwAFa1kAIwA/KwAYPysAGD8rABg/KxESARc5ETMRMzMxMAEiJzUWMzI1NSMRIQcCAgYjIic1FjMyNhITIREzERAEkzs/Ljhitv4xHz9el4JKOzQ9T11tNwMgoP5qGZYTa48FFO7+FP5WpxmaGccCvgGu+u7+1/7vAAEADv5qBJEESgAcAEBAIwIZCRsHBwkRAx0eCRlkWQkVFwtdWRcPDxRkWQ8WAAVhWQAjAD8rABg/KwAYPysAGD8rERIBFzkRMxEzMzEwASInNRYzMjU1IxEhAgIGIyInNRYzMhITIREzERADojw/Ljhjtf69HF+Zd0EeFSNugyUClp/+ahmWE2uPA7T+mP5lvw6HCAHQAfv8Qf7w/u8AAP//AAD+mAUbBbwCJgAkAAABBwJkBPIAAAALtgIADhQEByUBKzUAAAD//wBe/pgD1wRcAiYARAAAAQcCZARgAAAADrkAAv/htCYsCBolASs1//8AAAAABRsH4wImACQAAAEHAmME/gFSABlAEAIAEhEFBiUCABIQEgISBSYAK101ASs1AP//AF7/7APXBpECJgBEAAABBwJjBKgAAAALtgIOMiYTGSUBKzUAAAD//wAAAAAFGwfRAiYAJAAAAQcDdATpAVIAHrEDArj//EANFA4FBiUDAiAUARQFJgArcTU1ASs1Nf//AF7/7ARUBn8CJgBEAAABBwN0BJgAAAAQsQMCuP/itCwmExklASs1NQAA//8AAAAABRsH0QImACQAAAEHA3UE4wFSAB6xAwK4//xADRQOBQYlAwIgFAEUBSYAK3E1NQErNTX//wAl/+wD1wZ/AiYARAAAAQcDdQSYAAAAELEDArj/6LQsJhMZJQErNTUAAP//AAAAAAUbCEoCJgAkAAABBwN2BN8BUgAesQMCuP/yQA0UDgUGJQMCIBQBFAUmACtxNTUBKzU1//8AXv/sBCUG+AImAEQAAAEHA3YEoAAAABCxAwK4/+q0LCYTGSUBKzU1AAAABAAAAAAFGwhiAAcADQAlADQAeEAlBA4HCA0DNjUnKSwKMBowAgswDg8sARssBRYfAA6QDqAOAyADDrj/wLMUF0gOuP/AQBgLDkgOIhMOAxoKBAUNAmlZDQ0EBQMABBIAPzM/EjkvKxESADkYLxczLysrX15dMzMQxl5dEMZeXRE5ORESARc5ETMxMCEDIQMjATMBAQMnBgcDASIuAiMiBgcjNjYzMh4CMzI2NzMGBhMjJicGByM1NzY3MxYWFwRcsP28rroCO6YCOv5apEYeIaYBcCRHQ0AcKCoOXQ1kTCVJRT4bKCoMXAtlZGJmb1x5YjZvNrgwdzQBxf47Bbz6RAJoAbvbeGP+RQUdHSQdLjJqcx0kHS8xanP+pkJiU1EXPHlPRYU6AP//AF7/7APXBxACJgBEAAABBwN3BJYAAAAQsQMCuP/ltEQ+ExklASs1NQAA//8AAP6YBRsHcwImACQAAAAnAmQE8gAAAQcBSwAvAVIAHkAMAyYFJgIADhQoByUDuP//tCchBQYlKzUrNQArNf//AF7+mAPXBiECJgBEAAAAJwJkBIEAAAEGAUvYAAAWtwICJiwIGiUDuP/ftD85ExklKzUrNQAA//8AAAAABRsIEwImACQAAAEHA3gE8gFSABtAEAMCAxkhBQYlAwIgFgEWBSYAK3E1NQErNTUAAAD//wBe/+wD1wbBAiYARAAAAQcDeASeAAAAELEDArj/5rQxORMZJQErNTUAAP//AAAAAAUbCBMCJgAkAAABBwN5BPABUgAbQBADAgAYIAUGJQMCIBYBFgUmACtxNTUBKzU1AAAA//8AXv/sA9cGwQImAEQAAAEHA3kEnAAAABCxAwK4/+S0MDgTGSUBKzU1AAD//wAAAAAFGwhYAiYAJAAAAQcDegTwAVIAH0ATAwIAISkFBiUDAiAfAfAfAR8FJgArXXE1NQErNTUAAAD//wBe/+wD1wcGAiYARAAAAQcDegSkAAAAELEDArj/7LQ5QRMZJQErNTUAAAAEAAAAAAUbCF4ABwANACUAMgCLQBgEDgcIDQM0My8VKAEMKAEoKCwQJiAmAia4/8BAPAoQSCYFpx+3HwIXH+AO8A4CIA4wDgIDDhMOAgsOCBMYEwIVEyIOAwAaARsDGgoEBQ0CaVkNDQQFAwAEEgA/Mz8SOS8rERIAORgvX15dFzNeXS9eXV1xMzNdEMYrcTIyL11dMxESARc5ETMxMCEDIQMjATMBAQMnBgcDASIuAiMiBgcjNjYzMh4CMzI2NzMGBgMgAzMWFjMyNjczBgYEXLD9vK66AjumAjr+WqRGHiGmAXAkR0NAHCgqDl0NZEwlSUU+GygqDFwLZd3+6BNsB09rYlgIbQ2cAcX+OwW8+kQCaAG723hj/kUFHR0kHS4yaHEdJB0vMWdy/qYBCEU8QEGChgAAAP//AF7/7APXBwwCJgBEAAABBwN7BJwAAAAQsQMCuP/ktEBIExklASs1NQAA//8AAP6YBRsHTgImACQAAAAnAU4AMQFiAQcCZATyAAAAG0ASAg4FJgMAHCIoByUCABEZBQYlKzUrNQArNQAAAP//AF7+mAPXBewCJgBEAAAAJgFO3AABBwJkBH8AAAAWtwMANDoIGiUCuP/itCkxExklKzUrNQAA//8Ax/6YA/gFtgImACgAAAEHAmQEwwAAAA65AAH//7QMEgILJQErNf//AHH+mAQbBF4CJgBIAAABBwJkBLoAAAALtgIPHCIDCiUBKzUAAAD//wDHAAAD+AfjAiYAKAAAAQcCYwTPAVIAGUAQAQAQEBACEAUmAS0ZDAILJQErNQArXTUA//8Acf/sBBsGkQImAEgAAAEHAmMEyQAAAAu2AkApHAMKJQErNQAAAP//AMcAAAP4BzMCJgAoAAABBwFS/+YBUgATQAsBDAUmAQAVIQILJQErNQArNQAAAP//AHH/7AQbBeECJgBIAAABBgFS1gAAC7YCCiUxAwolASs1AP//AMcAAAR6B9ECJgAoAAABBwN0BL4BUgAbQBACAQASDAIDJQIBIBIBEgUmACtxNTUBKzU1AAAA//8Acf/sBGwGfwImAEgAAAEHA3QEsAAAAA23AwILIhwDCiUBKzU1AP//AE4AAAP4B9ECJgAoAAABBwN1BMEBUgAbQBACAQkSDAIDJQIBIBIBEgUmACtxNTUBKzU1AAAA//8AP//sBBsGfwImAEgAAAEHA3UEsgAAABdADQMCEyIcAwklAwIiESYAKzU1ASs1NQAAAP//AMcAAARBCEoCJgAoAAABBwN2BLwBUgAbQBACAQASDAIDJQIBIBIBEgUmACtxNTUBKzU1AAAA//8Acf/sBCsG+AImAEgAAAEHA3YEpgAAAA23AwIAIhwDCiUBKzU1AAADAMcAAAP4CGIACwAjADIApEApBgoKAQQAAAgBAzQzJScqKi46LgIuDDAqQCoCACoBCSoCFR0ADAEgAwy4/8CzFBdIDLj/wEAyCw5IDCARDAMYBglpWcgG2AYCOgYBCQYBDwAGkAagBgMSAwYGAQICBWlZAgMBCmlZARIAPysAGD8rERIAORgvX15dXl1dXSsAGC8XMy8rK19eXTMzEMZeXXIQxl0ROTkREgEXOREzETMRMzEwISERIRUhESEVIREhASIuAiMiBgcjNjYzMh4CMzI2NzMGBhMjJicGByM1NzY3MxYWFwP4/M8DMf2HAlT9rAJ5/u0kR0NAHCgqDl0NZEwlSUU+GygqDFwLZWRiZm9ceWI2bza4MHc0Bbai/jig/fYG4x0kHS4yanMdJB0vMWpz/qZCYlNRFzx5T0WFOgAAAP//AHH/7AQbBxACJgBIAAABBwN3BKYAAAANtwMCByUxAwolASs1NQD//wDH/pgD+AdzAiYAKAAAACcCZATBAAABBwFLAAABUgAgtAIkBSYBuP/9tQwSAQAlArj//7QlHwIDJSs1KzUAKzUAAP//AHH+mAQbBiECJgBIAAAAJwJkBLQAAAEGAUvzAAAUQA4CIhwiAxIlAws1LwMKJSs1KzX//wBSAAACYgfjAiYALAAAAQcCYwPLAVIAHUAUAQAQEBAgEDAQBBAFJgEuGQwGCyUBKzUAK101AP//AHsAAAHuBpECJgDzAAABBwJjA3cAAAALtgEACAcCAyUBKzUAAAD//wBS/pgCYgW2AiYALAAAAQcCZAO8AAAADrkAAf/+tAwSBgslASs1//8Am/6YAXMF5QImAEwAAAEHAmQDagAAAA65AAL//LQQFgQKJQErNf//AH3+mAXDBc0CJgAyAAABBwJkBYMAAAAOuQAC//+0GB4GACUBKzX//wBx/pgEaAReAiYAUgAAAQcCZATNAAAADrkAAv/8tBgeBwAlASs1//8Aff/sBcMH4wImADIAAAEHAmMFjwFSABlAEAIAHBAcAhwFJgIsJRgGACUBKzUAK101AP//AHH/7ARoBpECJgBSAAABBwJjBNkAAAALtgIqJRgHACUBKzUAAAD//wB9/+wFwwfRAiYAMgAAAQcDdAV9AVIAG0AQAwIAHhgGACUDAiAeAR4FJgArcTU1ASs1NQAAAP//AHH/7ASDBn8CJgBSAAABBwN0BMcAAAAQsQMCuP/8tB4YBwAlASs1NQAA//8Aff/sBcMH0QImADIAAAEHA3UFfQFSABtAEAMCBB4YBgAlAwIgHgEeBSYAK3E1NQErNTUAAAD//wBU/+wEaAZ/AiYAUgAAAQcDdQTHAAAADbcDAgAeGAcAJQErNTUA//8Aff/sBcMISgImADIAAAEHA3YFewFSAB6xAwK4//xADR4YBgAlAwIgHgEeBSYAK3E1NQErNTX//wBx/+wEaAb4AiYAUgAAAQcDdgTHAAAAELEDArj//LQeGAcAJQErNTUAAAAEAH3/7AXDCGIACwAXAC8APgB5QCoMBgASBhJAPzEzNho6KjoCOhgPNgEKNkAuMkg2QBsgSDYJKSEAGAEgAxi4/8CzFBdIGLj/wEAVCw5IGB0sGAMkCRVpWQkEAw9pWQMTAD8rABg/KwAYLxczLysrX15dMzMQxisrXl0Qxl0ROTkREgE5OREzETMxMAEQACEgABEQACEgAAEQEjMyEhEQAiMiAgEiLgIjIgYHIzY2MzIeAjMyNjczBgYTIyYnBgcjNTc2NzMWFhcFw/6d/sH+vf6fAV8BRwE+AWL7fPbs6/Ty6+72AmckR0NAHCgqDl0NZEwlSUU+GygqDFwLZWRiZm9ceWI2bza4MHc0At3+of5uAYsBaAFlAYn+cf6f/t7+0AEsASYBJQEp/tMDhx0kHS4yanMdJB0vMWpz/qZCYlNRFzx5T0WFOgAA//8Acf/sBGgHEAImAFIAAAEHA3cExQAAAA23AwIAIS0HACUBKzU1AP//AH3+mAXDB3MCJgAyAAAAJwJkBYMAAAEHAUsAwQFSACC0AzAFJgK4//61GB4GACUDuP//tDErBgAlKzUrNQArNQAA//8Acf6YBGgGIQImAFIAAAAnAmQE0QAAAQYBSwwAABa3AgAYHgcAJQO4//60MSsHACUrNSs1AAD//wB9/+wGcQdzAiYCXAAAAQcAdgEpAVIAE0ALAlsqJQYAJQIqBSYAKzUBKzUAAAD//wBx/+wFKwYhAiYCXQAAAQYAdm0AAAu2AlItKAcAJQErNQD//wB9/+wGcQdzAiYCXAAAAQcAQwCDAVIAFbQCIgUmArj/XbQmKgYQJQErNQArNQD//wBx/+wFKwYhAiYCXQAAAQYAQ9AAAA65AAL/tbQoJAcAJQErNQAA//8Aff/sBnEH4wImAlwAAAEHAmMFkQFSABlAEAIAJRAlAiUFJgIvLSEGACUBKzUAK101AP//AHH/7AUrBpECJgJdAAABBwJjBNkAAAALtgIqMCQHACUBKzUAAAD//wB9/+wGcQczAiYCXAAAAQcBUgCkAVIAE0ALAgAqNgYAJQIhBSYAKzUBKzUAAAD//wBx/+wFKwXhAiYCXQAAAQYBUvkAAAu2AgctOQcAJQErNQD//wB9/pgGcQYUAiYCXAAAAQcCZAV/AAAADrkAAv/7tCEnBgAlASs1//8Acf6YBSsE8gImAl0AAAEHAmQEzQAAAA65AAL//LQkKgcAJQErNf//ALj+mAUfBbYCJgA4AAABBwJkBVAAAAALtgEAEhgIASUBKzUAAAD//wCi/pgERARKAiYAWAAAAQcCZATDAAAADrkAAf/stBUbFAolASs1//8AuP/sBR8H4wImADgAAAEHAmMFVAFSABlAEAEAFtAWAhYFJgEmHxIIASUBKzUAK101AP//AKL/7AREBpECJgBYAAABBwJjBNcAAAATQAsBISIVFAolARkRJgArNQErNQAAAP//ALj/7AaLB3MCJgJeAAABBwB2APIBUgATQAsBWCQfEQAlASQFJgArNQErNQAAAP//AKL/7AWqBiECJgJfAAABBgB2fQAAC7YBWyciHQklASs1AP//ALj/7AaLB3MCJgJeAAABBwBDAFIBUgAWuQAB/7hACR8bEQAlARwFJgArNQErNf//AKL/7AWqBiECJgJfAAABBgBDuQAADrkAAf+XtCIeHQklASs1AAD//wC4/+wGiwfjAiYCXgAAAQcCYwVgAVIAGUAQAQAfEB8CHwUmATInGxEAJQErNQArXTUA//8Aov/sBaoGkQImAl8AAAEHAmME3QAAAAu2AScqHh0JJQErNQAAAP//ALj/7AaLBzMCJgJeAAABBwFSAIEBUgATQAsBECQwEQAlARsFJgArNQErNQAAAP//AKL/7AWqBeECJgJfAAABBgFSAgAAC7YBCSczHQklASs1AP//ALj+mAaLBhQCJgJeAAABBwJkBVAAAAALtgEAGyERACUBKzUAAAD//wCi/pgFqgT0AiYCXwAAAQcCZAS6AAAADrkAAf/itB4kHBMlASs1//8AAP6YBIcFtgImADwAAAEHAmQEpAAAAA65AAH//LQJDwUEJQErNf//AAL+FAQUBEoCJgBcAAABBwJkBbD//wALtgEkGB4LJSUBKzUAAAD//wAAAAAEhwfjAiYAPAAAAQcCYwSuAVIAGUAQAQANEA0CDQUmASgWCQcCJQErNQArXTUA//8AAv4UBBQGkQImAFwAAAEHAmMEbwAAAAu2ASElGAAKJQErNQAAAP//AAAAAASHBzMCJgA8AAABBwFS/8oBUgATQAsBCQUmAQASHgcCJQErNQArNQAAAP//AAL+FAQUBeECJgBcAAABBgFSlAAAC7YBAyEtAAolASs1AP//AHH+xQTZBhQCJgDTAAABBwBC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Ny0QHSUBKzU1AAAA//8AWP/sA5gGMQImAYIAAAEGB6bQAAANtwIBVzA5EB0lASs1NQAAAP//AFj/7AOYBjECJgGCAAABBgezuwAADbcCAUIvORAdJQErNTUAAAD//wABAAAEqgXMACcAKACyAAABBweW/3D/mwAUswEPBAG4/720DBIbAiUBKzUAPzX//wABAAAEngXMACcAKACmAAABBwfQ/2P/mwAUswEYBAG4/7S0FQwbAiUBKzUAPzX//wABAAAF2wXMACcAKAHjAAABBwel/sL/mwAYtQIBFwQCAbj/xbQUDCMBJQErNTUAPzU1//8AAQAABdEFzAAnACgB2QAAAQcHsv7W/5sAGLUCASAEAgG4/720HQwjAiUBKzU1AD81Nf//AAEAAAWoBcwAJwAoAbAAAAEHB6b+mf+bABi1AgEPBAIBuP/ktAwfIwIlASs1NQA/NTX//wABAAAFqAXMACcAKAGwAAABBwez/pn/mwAYtQIBGAQCAbj/3bQVHyMCJQErNTUAPzU1//8Arv4UBEwGMQImAYQAAAEHB5YBtAAAAAu2ATkfGwoUJQErNQAAAP//AK7+FARMBjECJgGEAAABBwfQAX8AAAALtgEQHhsKFCUBKzUAAAD//wCu/hQETAYxAiYBhAAAAQYHpScAABCxAgG4//u0JxwKFCUBKzU1//8Arv4UBEwGMQImAYQAAAEGB7JCAAAQsQIBuP/9tCYcChQlASs1Nf//AK7+FARMBjECJgGEAAABBgemNwAADbcCATkfKAoUJQErNTUAAAD//wCu/hQETAYxAiYBhAAAAQYHsyUAAA23AgEnHigKFCUBKzU1AAAA//8Arv4UBEwG4QImAYQAAAEGB6dCAAANtwIBJB4qChQlASs1NQAAAP//AK7+FARMBuECJgGEAAABBge0QgAADbcCASQeKgoUJQErNTUAAAD//wABAAAF1wXMACcAKwCyAAABBweW/3D/mwAUswEPBAG4/720DBIbBiUBKzUAPzX//wABAAAFywXMACcAKwCmAAABBwfQ/2P/mwAUswEYBAG4/7S0FQwbBiUBKzUAPzX//wABAAAHCAXMACcAKwHjAAABBwel/sL/mwAYtQIBFwQCAbj/xbQUDCMGJQErNTUAPzU1//8AAQAABv4FzAAnACsB2QAAAQcHsv7W/5sAGLUCASAEAgG4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NRMjFTMnIxUzJyMVMycjFTMBIxUzJyMVMycjFTMnIxUzARUzNTMVMzUXIxUzJyMVMwcVMzUzFTM1ByMVMzcVMzUFFTM1FzUjFRc1IxUjNSMVBxUzNTMVMzUTNSMVIzUjFQcVMzUzFTM1EyMVMycjFTMTIxUzJyMVMwXV+itqampqampqampqamtqa2pram1ramtqamtra2tra2tra2tra2v6lWtqa2pram1ra2vYamrVamrVamrVa2pramtqbf3pampramtqbWv8qWsBP2vVawGqbWtrbWpramtqa2tqa2pram1ra21qa2pramtramtqa2pta2tr2Gpq1Wpq1WpqAhdtbddra9Vra9VrawLsamtqampq1Gtr1Wprampra2pq/ldq1WrUampramprampqamtqamtqampq1Gtrampq1Wpq/hQBIWMBIGMBImEBIGMBIWIBIWBgYGBgYGBgYGBgYGDDYv7fXv7bXv7bXv7bXP7dYAZoXl5eXl5eXl6+Y2NjY2NjY2JeXl5eXl5eXr5hYWFhYWFhYcVeXl5eXl5eXl5e/t9jY2NjY2NjY2JcXFxcXFxcXP7fY2NjY2NjY2NeYGBgYGBgYGAGzWJiYmJiYmIBIGJiYmJiYmL+315eXl6+Y2NjYl5eXl6+YWFhYcVeXl5eXsNjY2NjYlxcXFz+32NjY2NeYGBgYAbNYmJiASBiYmIAAAABAHsA9gRaBNUAAwARtQMCBAUDAAAvLxESATk5MTATIREhewPf/CEE1fwhAAIABgAABM8EyQADAAcAHkAMBQMCBgMGCAkFAwQAAC8yLzMREgE5OREzETMxMBMhESETESERBgTJ+zdMBDEEyfs3BH37zwQxAAEAbQF/AmgDewADABG1AQAEBQECAC8vERIBOTkxMAEhESECaP4FAfsBfwH8AAAAAAIAbQF/AmgDewADAAcAHkAMBwEABAEECAkHAQYCAC8zLzMREgE5OREzETMxMAEhESEDESERAmj+BQH7S/6bAX8B/P5QAWL+ngAAAAABAAAAgQgAAukAAwARtQIFAwQDAAAvLxEBMxEzMTARIREhCAD4AALp/ZgAAAEBngAABkwErgACABG1AAIDBAABAC8vERIBOTkxMCEBAQGeAlgCVgSu+1IAAQGR/+UGWgSsAAIAE7cBAgADAwQCAAAvLxESARc5MTAJAgGRBMn7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A3MDfQADA34EpwABBKgErwADBLAEswABBLQExAADBMUE0AABBNEFLQADBS4FLgABBS8FUQADBVIGRwABBkgGVAADBlUHlQABB5YHmgADB5sHpAABB6UHpwADB6gHsQABB7IHtAADB7UHwQABB8IHxAADB8UHzgABB88H0AADB9EIhwABCIgIjgADCI8JPgABCT8JVwADCVgJaQABCW4JbwADCXAJlQABCZYJlgACCZcJnQABCZ4JngACCZ8JoAABCaEJoQACCaIJpgABCacJpwADCagJqwABCawJswADCbQJtwABCbgJuAADCboJvQADCb4JxQACCcYJxwABCcgJyQADCdgJ3QABCd8J4AADCeEJ4QABCeIKDgACCg8KEAADChELAwACCwQLDwADCxALGwACCxwLHAADCx0LXwACC2ALYAABC2ELZAACC2ULZQABC2YLaAACC2kLdAABC3ULmAACC5kLmQADC5oLnAABC50LpQACC6YLtQADC7YLvQABC74LxgACC8cL3AADC90L3gACC98L4wADC+QL6wACAYQAwASYBIADcAMsA1wEBgL2AyADNgL6A7oDAAMGAwwDDAMwAz4DJgRMBEwDEgMSBGgEbARwBHQDGASYBJwEoASAAxwDHAMcAyAESARIAyYDqgQOBAYEDgPYAywDXAQGA6oEDgOWA5IDeANIAzADNgNCAzoDlgOSA3gDSARIBEgDTAM+A1IDWANCA5YDkgN4A0gESARIA0wDUgNYA1wDYANcA2ADZAPiA5oDaANsA2QD4gQKA2gDbARoA3gDfANwBHQDdAOIA3gDfAQ8A4ADhAOIA4wERAOSA5YDmgOeA6IEYAOmA6oDrgO0A7oDwAPGA8wD0gP+A9gD3gPiA+YD6gPuA/QD9AP0A/gD/gP+BFQEWARYBJAEkAQCBAIEBgQKBAoEEgQOBBIEbASEBBYEHAQiBCgELgSEBEgERAQ0BEwEOAQ8BEAERARIBEwETARMBFAEUARQBFQEVARUBFgEXARgBGQEZARkBGgEbARwBHQEeAR8BIAEhASKBJAElASYBJwEoAACAD0JbglvAAAJdgl5AAIJfAl8AAYJggmCAAcJhAmEAAgJhgmGAAkJiAmIAAoJjAmPAAsJkwmTAA8JlQmVABAJmAmYABEJnQmeABIJoAmhABQJrAmzABYJuAm4AB4Jugm6AB8JvAm+ACAJwgnEACMJxgnJACYJ3AncACoJ6AnqACsJ8wn0AC4J9wn3ADAJ+Qn5ADEJ/An9ADIKAQoBADQKDwoPADUKVwpXADYKWQpZADcKWwpbADgKXQpdADkKYQpkADoKaApoAD4KagpqAD8KbApsAEAKcwpzAEEKewp7AEIKfwp/AEMKgQqBAEQKhQqIAEUKjAqMAEkKkAqQAEoKlwqXAEsK7wrwAEwK8wr0AE4K9wsAAFALBAsPAFoLHAscAGYLMQsxAGcLNAs3AGgLOQs5AGwLOws9AG0LPws/AHALQwtIAHELSgtKAHcLVgtWAHgLWAtYAHkLWgtbAHoLXgtkAHwLpgvFAIMLxwvjAKMAAQA2AAIARABFAAIAIgAjAAIAJwAoAAIANAA1AAIAQgBDAAEADgABAAQAAgAZADoAAgAAADIAAQA8AAIANQA2AAEAFQABABkAAQAGAAIAGQA5AAEAJwACADIAMwACAAAAMwABAEIAAQBJAAEAWwABADcAAQBUAAEATQABACoAAQA1AAEAIgABAC4AAQAyAAEAPgABACEAAgAZADMAAQBMAAEARAABAFEAAQBFAAEAXQABAGYAAQBcAAIAWQBaAAIAPgA/AAIATABNAAIAXQBeAAIARwBIAAIAZwBoAAIARQBGAAIANgA3AAEAUAABAEYAAQBYAAEAUwACADoAOwABADoAAgBeAF8AAQAtAAEAOQABAE4AAQBeAAEAYQABAHEAAgBKAEsAAgAkACUAAgAlACYAAgAzADQAAgAxADIAAQBKAAEAJQABADMAAQAxAAEAQwABADQAAQAkAAEAJgABAB0AAQAwAAEARwABAFkAAQAXAAEAHwABACAAAQAYAAEAKQABAEAAAQBSAAEAEAACAEMARAACAFoAWwABACgAAQA9AAEAGgABABEAAQAjAAQAAAABAAAAAgAfAmACYgACAmQCZAADAowCjwACA3MDcwACBOQE9AACBPYE+QADBPoE+gACBPwFAAADBQMFBQADBQgFEgADBRgFGwADBRwFHgACBSEFIwACBSQFJAADBSUFJQACBSYFKAADBSkFKwACBSwFLQADBS8FMQACBTIFNQADBTYFNgACBTgFOQADBToFOgACBUIFTgACBkgGSQACBkoGSgADBksGUQACBlIGUgADBlMGUwACBlQGVAADCIsIjgABAAEAAAAKAHAA+gAFY3lybAAgZGV2MgAwZGV2YQAwZ3JlawBGbGF0bgBWAAQAAAAA//8AAwAAAAMABgAKAAFNQVIgAAoAAP//AAMACQAKAAsABAAAAAD//wADAAEABAAHAAQAAAAA//8AAwACAAUACAAMa2VybgBKa2VybgBKa2VybgBKbWFyawBQbWFyawBWbWFyawBWbWttawBsbWttawBsbWttawBsYWJ2bQB0Ymx3bQB6ZGlzdACAAAAAAQALAAAAAQABAAAACQAAAAEAAgADAAQABQAGAAcACAAAAAIACQAKAAAAAQAMAAAAAQANAAAAAwAOAA8AEQAUACojTiSWJ04pSDy2TNZN3lqWW3peZmCGnqSjtKeGqaKq5qr8rFysbAAEAAAAAQAIAAEADABkAAMBRgNqAAIADgJgAmIAAAJkAmQAAwKMAo8ABANzA3MACATkBPoACQT8BQAAIAUDBQUAJQUIBRIAKAUYBR4AMwUhBS0AOgUvBTYARwU4BToATwVCBU4AUgZIBlQAXwACACUAJAA9AAAARABdABoAggCYADQAmgC4AEsAugFCAGoB+gIBAPMCNwI3APsCTgJOAPwCUwJWAP0CXAJfAQEDGgMdAQUDMgM3AQkDQgNJAQ8DXgNhARcDbANxARsDfgOzASEDtwO4AVcDugO6AVkDvwPPAVoD2APYAWsD3QQZAWwEHgQfAakEIgSeAasF9AYiAigGVQZaAlcGXQZmAl0GawZuAmcGcQaAAmsGgwacAnsGpQasApUGrwbIAp0GzQbmArcIAwgDAtEIUQhRAtIIYghoAtMIcwhzAtoIdgh2AtsAbAAAAcQAAAHEAAABxAACAbIAAAG4AAATbAAAAb4AAAG+AAABxAAAW+IAAFviAABclAAAW+IAAAHKAABb4gAAAdAAAFviAAAB1gAAW+IAAAHcAAAB3AAAW+IAAFviAAAB4gAAAeIAAAHiAAEB6AACAh4AAgIeAAICHgACAh4AAFviAAIB7gACAh4AAgIeAAICHgACAh4AAgIeAAICHgACAh4AAgIeAAICHgACAh4AAgIeAAICHgACAh4AAgIeAAICHgACAh4AAgIeAAICAAACAfQAAgIeAAICHgACAh4AAFusAABbrAAAXJQAAFviAAAB+gAAW8QAAgIeAABbxAACAgAAAgIeAAICHgAAW+IAAAIYAABb4gACAh4AAgIeAAACGAAAAhgAAFviAAICHgACAh4AAgIeAAICHgAAW+gAAgIeAAICHgAAAhgAAAIGAAACBgAAAgYAAAIGAAACBgAAAgYAAAIGAAACBgAAAgYAAAIGAAACBgAAAgYAAAIGAAACDAAAAgwAAgIeAABb4gAAW+IAAFviAABb4gAAW+IAAFviAABb4gACAhIAAAIYAAICHgAB/YsAAAABAjoEfgABAk8EsAAB/YsEsAABAAAEzgAB/ZQEsAAB/7AEsAABAAAEiAABAAADhAAB/2YDmQABABQAAAAB/+wAAAABAAAETAABAAAAZAABAAAB4AABAAAE4gAB/+IAAAABAAAEfgABAAAAAALcIGodeEnuFIQfcBSWNMo21FImHKw33FXOHFIdeFbuHFIdeFbuEjIcEFaUHEAzYhwuHEwcUhb6H44fWBEqHHw21BxqH2Qfako2HOIc6BzQIHYdHh0MIIIZ4lMEIHAfcFboIIIZ4hEwIIgfcFXyH2QdeEo2HfYfcB3YHiAzYhhQHkQ21B4+Hmgebh5cHFIepFbuH2QepEo2Hs45Ah62IPQRNlJEG6QfalLUG1YZIhvIG+Ab5hSWIKwRNko2Gxob/koeG1YRThFUHuwgmlboG24fWB9eG24fWBn6HIgcjlJEETwfWB9eHPQRQhzcIL4RtFboIL4RqFboHiwRtB1IEUgRThFUHWAZKB1UHX4RWh2WGsARYB1UHiwRtFLUIMQfTFasHnoRZh8QIMQfTFasIMQfTB8iHtoZIhrMFqYfcEnuFqYfcEnuFqYfcEnuF5YfcEnuF5YfcEnuEWwfcEnuFMYUolZYNMo21BFyFsQdeFbuFsQdeFbuFsQdeFbuHowdeFbuFugcUhb6FugcrBb6Fugg7hb6FvQg7hb6HKw33FXOHQYdHh0MFbwZ4lMEFbwZ4lMEFbwZ4lMEF7QZ4lMEF7QZ4lMEIIIZ4lMEFcIzYhhQFcIzYhhQFcIzYhhQFzwzYhhQFsQepFbuIHAfglboEXgRflbuFtASklJEFtASklJEFtASklJEEYQfoFJEWlgSkh3YEYoRkEo2GSIRllIsG1YZIh+IFaoSkko2FaoSkko2FaoSkko2E+gSkko2Fu4RnB9eFu4ZTB9eFu4ZTB9eGIAZTB9eFnwRolQYHTYfglLUFpobDlboFpobDlboFpobDlboFpobDlboF7obDlboIL4RqFboHTYfglLUHTYfglLUHTYfglLUEa4fglLUEbofoBHAG6QRtB1IEbofoBHAEcYUkEnuEcwSkko2F5YUkEnuFwwSkko2IGodeBHSIKwfLhIOEdgcEFImGLAflBvIEeQcEBHqEd4bSh3YF7QcEFMEHFIbSlbuEeQcEBHqFwwflEo2EfA33FXOG+AzYhSWHKw33FXOG+AfcBSWEfYdeFbuEfwSkkoSHowdeFbuEgIflEoSHTwdeFboEiYflFbuHFIdeBIIIKwfLhIOEhQdtFZkEiYfoFbuEhocEFRmEiYcHBeoEiAcEFIsEiYcHBeoEiwcEFIgFGAcHBeuEjIcEBI4Ej4cHBoMF2AzYhwuEkQSSlboHEAzYhwuHuwgmlboElAgghb6G24Yqh9eElYgghb6ElwYqh9eFvQgghb6G24Yqh9eHEwcUhJiG24fWBKqFvQcUhb6GvYa/B9eHFIUkBJoEm4YdBJ0EnoUZhKAG24Tjhn6HHw21BKGHIgcjhKMIPQSklJEHYofako2EpgSnh9eH2QfahKkH1IfWBKqH2Qfako2H1IgcB9eH2Qfako2H1IYIB9eH2Qfako2H1IfWB9eFpQdHh0MHRIfglboIHYdHhKwIL4fghK2FpQdHh0MHRIfglboErwaeBLCIHYdHhLIIL4fghw6FhAZ4lMEFhwbDlboF7QZ4lMEHRIbDlboFbwZ4lMEHRISzlboH3AS1FWwEtoS4FbEEuYfcFXyFyQZoB1UIIgfcBLsHWAdZhLyEvgfcBSWFyQZxB1UHYodeEo2Ev4fLh2WHYodeEo2HcAewkn6H2QdeBoMHX4dxhlkHYodtEo2EwQW4lSWHfYTChMQGsATFhMcEyIfcB3YGsAdZh1UHfYfcB3YGsAeCB1UEygzYhhQHUIfglLUEy4zYhhQEzQfglLUFzwzYhhQHUIfglLUEzoUMBhQE0ATRlLUFcIzYhhQHUIfglLUHiAzYhNMHiwfghNSE3webh5cE1gegB8QHYoepEo2IOgfoB8iHp4epEo2E2Q5Ah62E2ofLhrME145Ah62E2oe4BrME2Q5Ah62E2oTcBrME3YgiB7+IIIWCheoE3webh5cE4IegB8QE3webh5cE4IegB8QHlYebh5cHmIegB8QHYoepEo2FrIfoB8iGvYTiBn6Fu4Tjhn6E5Qc6BzQE5oZFhOgIGodeBOmIKwfLhOsIIITslMEIL4aeFboHiATuBhQHiwTvlLUIGodeBPEIKwfLhyaE8oT1knuE9wT4ko2HFIdeByyIKwfLhyaE9AT1lbuE9wT4ko2HowfcFbuE+gbDko2E+4T9Bb6E/oUAB9eHEwcUhQGHF4fWBy4IIIZ4hQMIL4bDh7yFBIUGFMEFB4UJFboHiAzYh4UHiwfgh4aFCoUMBhQFDYUPFLUH2QepByaIMQfoB8iFEIUSEo2FE4UVB8iHp4epEo2HqofoB8iHfYfcBTeGsAeCBRaHUIfalLUNMo21FImFMwdtFLUFGAdeFZkHKw19lXOHUIaqFLUIJQUkFasFGYUbFUOGegUchvIHKw33FXOFHgUflRmFIQ5AhSWFIoUkBSWHRI19lboGtggmlNkIO4ZNFa+FMw19lLUIHAdeBw6IIIUnFMEG4YU9hoMHI4UolbEFKgUrhrGFLQcUhb6HHwW3BxqFLoaqFJEG24fWB9eFyQcUhvIHbQUwFeWIHYdHh0MIL4fghw6IIIZ4lMEHXgV1FHqGSgZplMENMoUxlImHUIfcB1IFMwfcFLUG4Yb5lJEHX4U0h2WG4YdtFJEFNgYPhleGsAeCBl8FPwYJlZkGhIg7h9GHfYfcBTeFOQU6hTwHkQU9h4+H2QU9ko2IMQbDh8iHs45Ah62Htoe4BrMH3wdeBvIFPwfcFZkFQIVCFX4FQ4VFBUaIKwWuBoMFSAVJhUsFTIVOBZYFT4VRFOIFUoVUBVWFVwVYhVoFW4VdBYWFXoVgBWGFYwaJBWSFZgX/BWeFqYVpEnuFaofoEo2FugVsBb6Fu4Vth9eFbwzYlMEFpoflFboFcIYMhhQHTYfoFLUFcgfLlJEFc4V1BXaFeAV5hXsFfIZ4hX4G1YWgh1IFf4Z4hp+FpoWghw6HGQZNBxqFgQWCkoSIIIzYhYWIL4fLhw6FhAzYhYWFhwbShw6FiIYblX4FigWLhY0FjoWQBZGFkwWUhZYFl4WZBZqFnAWdlaUFnwWgh1IHKwWiEowIO4zdBaOFpQ2LB0MFpoWoFboFqYWrEnuFrIWuEo2F5YzdEnuFwwWvko2FsQWylbuFtAW1lbuHowW3FbuHFIW4lbuFugguBb6Fu4ZRh9eFvQgiBb6G24ZRh9eFwAXBlMEFwwflFboF7QZ4lMEHRIfLlboHTAXElXOFxgaBh7+IGQXHlXOFyQaBh1UFyoXMBhQFzYYnlLUFzwXZhhQHUIYnlLUF0IgjhdIF04XVBdaF2AXZhwuHGQdeBxqF2wYYhdyG+AdeB1IF3gaqBd+HuwXhFboHs4XiheQHtofLhtQF5YzdEnuF5wfLheiHFI21BeoIKwfLheuF7QZ4lMEF7ofLlboF8AzYko2F8YflB8iH1IYhhfMIL4flBfSGsAX2BfeF+QX6hfwF/YX/BgCIGoYCBgONMoYFBgaGCAYJhgsH2Qfako2HfYYMhg4HX4dxhlkHtoe4BnKGD4aqB9GH0AYRB9GGEodeFa+HiAzYhhQGFYfcBhcHFIYYhhoH3wYbhvsH44YdBh6GIAYhhn6GIwYkhiYG1YYnhvIIIg21FXOHWAYpB1UH2QzYko2IMQflB8iINwflFbuIPQcHEo2HiwfoFLUHUIfoFLUINAYqkokG1YZ3BvIGLAb5hoMH3w2FEo2G1YbShvIG1YbShvIG1YaNhvIGMIdZhjIG2IYth7+G2IbFB7+IKwflEo2GvYa/Bn6H3w2FB+IG1YcHB+IIKwfLko2GegflBi8GMIfoBjIHiwfgh1IHRIflFboHRIflBw6HF4fWB9eGM4Y1BjaGOAY5hjsGPIaQhj4GPIaQhj4G24fWBn6GP4ZBBkKHPQZEBzcHPQZEBzcHPQZFhzcIL4fghw6IL4fghw6GrQfghkcIL4foFboGSIc+lIsGSgZplMEGS4ZNBk6H0AZRh9GGUAgiB9GH0AZRhl8HWAdZhpIHWAdZhpIINAZTEokGVIZWBleIPQfLlJEINwflFbuHX4dxhlkGhIcUhl8GhIcUhl8GWoZcBl2GhIfZBl8GYIZiBmOGsAeCBmUHiwfglLUIL4ZmlboINwbDlbuGegZoFSWHnoZph8QGawaqFasGbIewhm4Htoe4BnKHtoe4Bm+HtoZxBnKHtoZxBnKGhIggh9GGxoggkoeGdAg7hnWG1YZ3B+IIIIZ4lMEIPQfLlJEG1Ye4BvIGegZ7lboGfQcHBueHF4fWBn6G5IfLhoAHWAaBh1UH3w33BoMGhIggh9GGxoggkoeGh4aJFJQGh4aJBoYGh4aJBoqGjAaNho8GkI19hpIGk4aVBpaGmAaZhpsGnIaeBp+GoQaihqQGpYaqBqcGqIaqBquGrQfghq6GrQfghq6HuwfalboG+Ab5lLUGxob/koeHPQbLBzcIL4fglboIL4fghw6HWAdZh1UINAeCEokHX4dxh2WGsAeCBrGHtoe4BrMIKwa0h1IGtgfahreG1Ya5B+IGuoa8FHqGvYa/B9eGvYa/BsCIL4fghw6GwgbDlZkHiwbFFLUHuwfah0qG+Ab5h4yGxob/hsgG1YcHBw6HIgcjhsmH1IfWBzEHPQbLB0AIL4fgh0qIL4fghw6HWAdZlHMHX4dxhsyGzgbhhs+IMQflBtEG1YbShvsHtoe4BtQIKwfLhyaG1Yfgh7yG+AbXBw6IKwfLh4yIMQdxlHGG2IdxhtoIMQfLhyyG24fWBt0H0AdZht6G4AbhhuMHiwfgh4yG5Ie4FPEG5gfcBueG6QfalLUG6ofcBuwG6QbvB4aG6ofcBuwG7YbvB4aG8I33Fa+G+Ab5hvIHKw33BvOG+Ab5h26HKw33BvOG+Ab5h26HKw33BvUG+Ab5hvaHKw33FVQG+Ab5hvsHFIdeBy+IKwfLlHGHFIdeBy+IKwfLlHGG/IdeFZkG/gb/hwEHAocEFRmHBYcHBwiHCgzYhwuHTwgmlboHEAzYhxGHuwgmh7yHCgzYhwuHTwfcFboHEAzYhw0Huwgmhw6HEAzYhxGHuwgmh7yHEwcUhxYHF4fWBzEHGQ21BxqHHAcdlJEHHw21ByCHIgcjhyUHHw21ByCHIgcjhyUH2QfahyaH1IfWBy4HKAfahyyHKYcrBy4H2QfahyyH1IfWBy4H2Qfahy+H1IfWBzEHMoc6BzQHNYc+hzcHOIc6BzuHPQc+h0AHQYdHh0MHRIfglboIHYdHh0YIL4fgh7yIHYdHh0YIL4fgh7yIHYdHh0kIL4fgh0qHTAfcFboHTYfgh1IHTwfcFboHUIfgh1IIGQfcFXyHU4dZh1UIIgfcB1aHWAdZh1sIIgfcB1aHWAdZh1sHp4deEo2HXIdxh2WH2QdeB26HX4dxh2EHYodtEo2HZAewh2WHZwdoko2HagdrlSWHp4dtB26HcAdxh3MHdIfcB3YHd4d5B7+HfYfcB3qHgIeCB3wHfYfcB3qHgIeCB3wHfYfcB38HgIeCB4OHiAzYh4UHiwfgh4aHiAzYh4mHiwfgh4yHiAzYh4mHiwfgh4yHjg21B4+HqofoFasHkQ21B5KIMQfoB5QHlYebh5cHmIegB8QHmgebh50HnoegB6GHowepFbu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AZIBmAGeAZ4BngGeAaQBpAGkAaQBtgG2AbYBtgG2AbYB/gH+Af4B/gGqAaoB5gGwAc4BtgG8AcIByAHOAdQB2gHyAeAB5gHsAfIB+AH+AgQCCgABAuEFvAABBGAEfgABBGQFdQABBO0EfgABA9MFtgABBLwFdQABBNoFtgABAVQFtgABBAYFtgABAVcFtgABBlMFtgABBSYFtgABBW4EfgABBEIEfgABA4QFeAABBD4FtgABBPYFtgABBH4FtgABBvQFtgABA/wFtgABA+gFtgABA0gELQABBBUGFAABAt0GDgABAUAESgABAToGFAABBpoDhAABBBUESgABAu4ESgABAv4EFAABAoYESgABBcgESgABA2YESgABAzwESgABA5gDhAABBjYDhAABA9QDhAABAToESgABA6wESgABBuADhAABA+gDhAABA/wESgABBBADhAABAwwDhAABA6wDhAABAyAEEAABAyoDhAABA6wEXgABATsESgABAeAESgABBAYDhAABBlQESgABBBwESgABBHIESgABAmIESgAEAAAAAQAIAAEADAASAAEAWABkAAEAAQT7AAIACwAkAD0AAABEAF0AGgCUAJUANACXAJcANgCbAJwANwC0ALUAOQC3ALcAOwC7ALwAPAEqASsAPgNGA0kAQANeA2EARAABAAAABgAB/y0E8ABIAJIAmACeAKQAqgCqALAAtgC8AMIAyADOANQA2gF2AOABdgDgAOYA7AGCAPIA+AD+AP4BBAEKAUABEAEWARwBIgFGAUABKAEoAS4BNAE6AUABfAFAAUYBTAFSAVgBiAFqAV4BZAFqAXABdgF2AXYBggGCAXwBfAF8AYgBiAGCAYgBdgF8AXYBfAGCAYgBggGIAAEC3QW8AAED6AV4AAEEfgWCAAEDyAV4AAED9AW2AAEE2AWCAAEFIQW2AAEB4AW2AAEBcQW2AAEETAW2AAEBewW2AAEGdwW2AAEFSgW2AAEDmAV4AAEDqAV5AAEEWAW2AAEEiAW2AAEHEgW2AAEEEAW2AAEEKgW2AAEDXAPoAAEDXAQjAAEEOQYUAAEDcAPoAAEC6QX6AAEBXgRKAAEDXARKAAEBXgYUAAEGQAPoAAED1APoAAEEOARKAAEDFgRSAAEDJQQUAAEBbQVGAAEF8ARKAAEDhARKAAEDwARKAAEDYARKAAEEeAVkAAEDjgPoAAEFGwW2AAEEQARKAAQAAAABAAgAAQAMJKgAAgAWAEwAAgABBTsFQQAAAAcAAQAeAAAAMAAAACQAAQAqAAAAMAAAADAAASRIAAECaQA8AAEBqARKAAEBqAAoAAECaQRKAzEO+g8AEUAu5A+oEUYRWBFeEgwSEg2kEXAOahF8EYIRiBGOEZQPKg8wEZoRoBLGEswRshG4EcoR0BAOEdYS0hLYEA4R1hLeEuQR9BH6EgwSEhIYEh4SJBIqEjASNhDUEkIPQhJUEmASZhLqEvAR3BHiEUwRUhHcEeIRZBFqEP4RdhHcEeISeBJ+EroSwBK6EsARphGsEroSwBG+EcQSeBJ+EtIS2BHcEeIR3BHiEegR7hIAEgYS9hL8EngSfhMCEwgSPBKKEkgSThJaEpYSbBJyISQPACEkDwAhJA8AISQPACEkDwAhJA8AINYOZCI+EUYisBISIrASEiKwEhIisBISIm4RlCJuEZQibhGUIm4RlCJKEV4ijBHQIpIR1iKSEdYikhHWIpIR1iKSEdYikhHWIrYSHiK2Eh4ithIeIrYSHiLUElQS0hLYDMYMzBLqEvAS6hLwEuoS8BLqEvAS6hLwEuoS8A+uD7QRTBFSEWQRahFkEWoRZBFqEWQRahK6EsASuhLAEroSwBK6EsAS0hLYEngSfhLSEtgS0hLYEtIS2BLSEtgS0hLYEtIS2BJ4En4SeBJ+EngSfhJ4En4SWhKWEdwR4hJaEpYhJA8AEuoS8CEkDwAS6hLwDvoPABLqEvAiPhFGEUwRUiI+EUYRTBFSIj4RRhFMEVIiPhFGEUwRUiJKEV4R3BHiEVgRXgzSDNgisBISEWQRaiKwEhIRZBFqIrASEhFkEWoSDBISEWQRaiKwEhIRZBFqImIRfBHcEeIiYhF8EdwR4iJiEXwR3BHiDmoRfBHcEeIiaBGIIuYSfhGCEYgSeBJ+Im4RlBK6EsAibhGUEroSwCJuEZQSuhLAEY4RlBK6EsAibhGUEroSwA1oDW4M3gzkITwPMBK6EsARmhGgEaYRrBGmEawjEBLMIwoSwBLGEswSuhLAEsYSzBK6EsASxhLMDOoM8BLGEswSuhLAIowR0BJ4En4RyhHQEngSfiKMEdASeBJ+DPYM/BHKEdASeBJ+IpIR1hLSEtgikhHWEtIS2CKSEdYS0hLYDQINCA0ODRQjHBLkEegR7hLeEuQR6BHuIxwS5BHoEe4ipBH6EgASBiKkEfoSABIGEfQR+hIAEgYipBH6EgASBhIMEhIS9hL8IrASEhL2EvwSDBISEvYS/CK2Eh4SeBJ+IrYSHhJ4En4ithIeEngSfiK2Eh4SeBJ+IrYSHhJ4En4SGBIeEngSfiLCEjYSPBKKItQSVBJaEpYi1BJUItoSZhJsEnIi2hJmEmwSciLaEmYSbBJyDRoSnBJgEmYiwhI2EjwSiiLCEjYSPBKKIsISNhI8Eooi1BJUEloSlhK6EsASuhLAIoARuBG+EcQO+g8AEuoS8A0+DUQNSg1QEQQRCg0gDSYO+g8AEuoS8CEkDwANLBLwISQPAA0sEvAhJA8ADSwS8CEkDwANLBLwISQPACMiEvAhJA8AEuoS8CEkDwAjIhLwISQPACMiEvAhJA8AIyIS8CEkDwAjIhLwISQPABLqEvASDBISEWQRaiKwEhIRZBFqIrASEhFkEWoisBISDTIRaiKwEhINMhFqIrASEg0yEWoisBISIlARaiKwEhIRZBFqIm4RlBK6EsARjhGUEroSwBAOEdYS0hLYIpIR1hLSEtgikhHWDTgS2CKSEdYNOBLYIpIR1g04EtgikhHWIxYS2CKSEdYS0hLYIEYNRA1KDVAgRg1EDUoNUCBGDUQNSg1QIEYNRA1KDVANPg1EDUoNUBIYEh4SeBJ+IrYSHhJ4En4iGhEKDVYNXCIaEQoNVg1cIhoRCg1WDVwiGhEKDVYNXBEEEQoNVg1cD0ISVBJaEpYi1BJUEloSliLUElQSWhKWEgwSEhL2EvwR3BHiEVgRXhHcEeIR3BHiDWIu/A1oDW4PqBFGIj4RRg10DXoRWBFeDYANhhHcEeIR3BHiDYwNkhIMEhINmA2eDrIOuA2kEXAiYhF8DfIN+A2qDbANtg28EY4RlBGaEaARphGsEroSwA/qD/ANwg3IEcoR0BJ4En4Pug/ADc4N1A3aDeAN5g3sEdwR4hLeEuQR9BH6EgASBg3yDfgN/g4EEvYS/BAUEBoS9hL8EgwSEg+6D8ASJBIqD0ISVA/qD/ASYBJmEmwScg4KDnAOCg5wDgoOcBE0EToOEA4WDhwOIiDiDnwOgg6IDo4OlA4oDi4Pug/ADjQOOg5ADkYOTA5SDlgOXiEkDwAS6hLwIm4RlBK6EsAikhHWEtIS2CK2Eh4SeBJ+EWQRaiDWDmQPrg+0DmoRfBHcEeIiYhF8EdwR4iJ0EaAiehGsEA4R1hLSEtgikhHWEtIS2CDcDnAP9g/8DnYOfA6CDogOjg6UImIRfBHcEeIOmg6gDqYOrCKMEdASeBJ+ISQPABLqEvAhJA8AEuoS8CKwEhIRZBFqIrASEhFkEWoibhGUEroSwCJuEZQSuhLAIpIR1hLSEtgikhHWEtIS2CMcEuQR6BHuIxwS5BHoEe4ithIeEngSfiK2Eh4SeBJ+DrIOuA/2D/wiaBGIIuYSfhGCEYgR3BHiDr4OxBLSEtgSYBJmEmwSciEkDwAS6hLwEgwSEhFkEWoikhHWEtIS2CLUElQSWhKWDsoO0A7WDtwO4g7oDu4O9A7uDvQO+g8AD6gRRhFMEVISxhLMEgwSEhIAEgYSbBJyDwYPDA8SDxgRQC7kEhgSHg8eDyQSDBISEWQRag8qDzASuhLADzYPPBHcEeIS3hLkEegR7g9CElQSWhKWEuoS8BHcEeIR3BHiEdwR4hFMEVIPSA9OEdwR4hHcEeIRZBFqEWQRag9UD1oPYA9mETQROhFALuQSJBIqEroSwBHcEeIR3BHiECAQJhMCEwgTAhMIEngSfhJ4En4SeBJ+EroSwBDmEOwPbA9yD3gPfg+ED4oPkA+WD5wPohG+EcQRvhHEEb4RxBJ4En4SeBJ+D6gRRhLSEtgPrg+0D7oPwBMOExQR6BHuEegR7hHoEe4R6BHuEegR7hDmEOwQ5hDsD8YPzA/GD8wSABIGEroSwBK6EsASuhLAEroSwBL2EvwS9hL8EngSfhLSEtgP0g/YEwITCBI8EooSWhKWD94P5BJsEnIP6g/wD/YP/A/2D/wQRBBKEEQQShBEEEoQAhAIEA4R1hAUEBoSJBIqECAQJhAsEDISuhLAEaYRrBA4ED4R3BHiEEQQShBEEEoQUBBWEFwQYhBoEG4QdBB6EKQQqhCAEIYQgBCGEIwQkhCYEJ4QpBCqELAQthC8EMIQvBDCEdwR4hHcEeIQ/hF2Eb4RxBJ4En4R3BHiEegR7hDmEOwSABIGEvYS/BJsEnIR3BHiEMgQzhDUEkIQ2hDgEroSwBDmEOwR3BHiEPIQ+BLSEtgR3BHiEdwR4hD+EXYRBBEKEaYRrBK6EsARvhHEEngSfhHcEeIR6BHuEgASBhEQERYTAhMIEkgSThJsEnIS6hLw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D9IhJg4oISYO9CEmDwAhJg8AISYPACEmDwAhJg+6ISYPuiEmD7ohJg+6ISYPxiEmD5YhJg/GISYPxiEmD8YhJg/GISYPxiEmD8YhJg+WISYPliEmD5YhJg+WISYPoiEmD0ghJg+iISYN1CEmD9IhJg3UISYP0iEmDdQhJg/SISYO7iEmDvQhJg7uISYO9CEmDu4hJg70ISYO7iEmDvQhJg76ISYPSCEmDvohJgzMISYPYCEmDwAhJg9gISYPACEmD2AhJg8AISYPYCEmDwAhJg9gISYPACEmDxIhJg9IISYPEiEmD0ghJg8SISYPSCEmDxIhJg9IISYPGCEmD5YhJg8YISYPliEmDx4hJg+6ISYPHiEmD7ohJg8eISYPuiEmDx4hJg+6ISYPHiEmD7ohJg0OISYM0iEmDewhJg+6ISYPJCEmDyohJg8qISYPwCEmD7ohJg/AISYPuiEmD8AhJg+6ISYPwCEmDNghJg/AISYPuiEmDzwhJg+WISYPPCEmD5YhJg88ISYPliEmDN4hJg88ISYPliEmD0IhJg/GISYPQiEmD8YhJg9CISYPxiEmDOQhJgzqISYPzCEmD04hJg/MISYPTiEmD8whJg9OISYPVCEmD1ohJg9UISYPWiEmD1QhJg9aISYPVCEmD1ohJg9gISYP2CEmD2AhJg/YISYPYCEmD9ghJg9mISYPliEmD2YhJg+WISYPZiEmD5YhJg9mISYPliEmD2YhJg+WISYPZiEmD5YhJg9yISYPnCEmD4QhJg+iISYPhCEmD4ohJg+QISYPiiEmD5AhJg+KISYPkCEmD6ghJg+KISYPciEmD5whJg9yISYPnCEmD3IhJg+cISYPhCEmD6IhJg+6ISYPuiEmDzAhJg82ISYN1CEmD9IhJgz2ISYM/CEmDsohJgzwISYN1CEmD9IhJg3UISYP0iEmDdQhJg/SISYN1CEmD9IhJg3UISYP0iEmDdQhJg/SISYN1CEmD9IhJg3UISYP0iEmDdQhJg/SISYN1CEmD9IhJg3UISYP0iEmDdQhJg/SISYPYCEmDwAhJg9gISYPACEmD2AhJg8AISYPYCEmDwAhJg9gISYPACEmD2AhJg8AISYPYCEmDwAhJg9gISYPACEmDx4hJg+6ISYPHiEmD7ohJg9CISYPxiEmD0IhJg/GISYPQiEmD8YhJg9CISYPxiEmD0IhJg/GISYPQiEmD8YhJg9CISYPxiEmDPYhJgz8ISYM9iEmDPwhJgz2ISYM/CEmDPYhJgz8ISYM9iEmDPwhJg9mISYPliEmD2YhJg+WISYOyiEmDQIhJg7KISYNAiEmDsohJg0CISYOyiEmDQIhJg7KISYNAiEmD4QhJg+iISYPhCEmD6IhJg+EISYPoiEmD2AhJg/YISYPSCEmDvohJg9IISYPSCEmDQghJg0OISYO7iEmDu4hJg0UISYO+iEmDRohJg9IISYPSCEmDSAhJg9gISYNJiEmDbAhJg8GISYPEiEmDVAhJg0sISYNMiEmDx4hJg8kISYPKiEmD7ohJg5GISYNOCEmDzwhJg+WISYOLiEmDT4hJg1EISYNSiEmD0ghJg/MISYPVCEmD1ohJg1QISYNViEmD9ghJg5YISYP2CEmD2AhJg4uISYPbCEmD4QhJg5GISYPiiEmD5AhJg2MISYNjCEmDYwhJg7iISYNXCEmDWIhJg2SISYNmCEmDZ4hJg1oISYOLiEmDW4hJg10ISYNeiEmDYAhJg3UISYP0iEmDx4hJg+6ISYPQiEmD8YhJg9mISYPliEmDwAhJg2GISYOKCEmDxIhJg9IISYPEiEmD0ghJg8kISYPKiEmD0IhJg/GISYPQiEmD8YhJg2MISYOTCEmDZIhJg2YISYNniEmDxIhJg9IISYNpCEmDaohJg88ISYPliEmDdQhJg/SISYN1CEmD9IhJg9gISYPACEmD2AhJg8AISYPHiEmD7ohJg8eISYPuiEmD0IhJg/GISYPQiEmD8YhJg/MISYPTiEmD8whJg9OISYPZiEmD5YhJg9mISYPliEmDbAhJg5MISYPGCEmD5YhJg8YISYPSCEmDbYhJg/GISYPiiEmD5AhJg3UISYP0iEmD2AhJg8AISYPQiEmD8YhJg+EISYPoiEmDbwhJg3CISYNyCEmDc4hJg3OISYN1CEmDu4hJg70ISYPwCEmD2AhJg9aISYPkCEmDdohJg3gISYO6CEmD2YhJg3mISYPYCEmDwAhJg3sISYPuiEmDfIhJg9IISYPzCEmD04hJg+EISYPoiEmD9IhJg9IISYPSCEmD0ghJg70ISYN+CEmD0ghJg9IISYPACEmDwAhJg3+ISYOBCEmDuIhJg7oISYPbCEmD7ohJg9IISYPSCEmDl4hJg/eISYP3iEmD5YhJg+WISYPliEmD7ohJg6+ISYOCiEmDhAhJg4WISYOHCEmDiIhJg82ISYPNiEmDzYhJg+WISYPliEmDu4hJg/GISYOKCEmDi4hJg/kISYPTiEmD04hJg9OISYPTiEmD04hJg6+ISYOviEmDjQhJg40ISYPWiEmD7ohJg+6ISYPuiEmD7ohJg/YISYP2CEmD5YhJg/GISYOOiEmD94hJg+cISYPoiEmDkAhJg+QISYORiEmDkwhJg5MISYOcCEmDnAhJg5wISYOUiEmD0IhJg5YISYPbCEmDl4hJg5kISYPuiEmDyohJg5qISYPSCEmDnAhJg5wISYOdiEmDnwhJg6CISYOiCEmDqAhJg6OISYOjiEmDpQhJg6aISYOoCEmDqYhJg6sISYOrCEmD0ghJg9IISYPDCEmDzYhJg+WISYPSCEmD04hJg6+ISYPWiEmD9ghJg+QISYPSCEmDrIhJg94ISYOuCEmD7ohJg6+ISYPSCEmDsQhJg/GISYPSCEmD0ghJg8MISYOyiEmDyohJg+6ISYPNiEmD5YhJg9IISYPTiEmD1ohJg7QISYP3iEmD34hJg+QISYP0iEmD0ghJg9IISYPACEmDtYhJg7iISYO3CEmD7ohJg70ISYPuiEmD5YhJg7iISYO6CEmD0ghJg7oISYPSCEmDughJg9IISYO7iEmDvQhJg76ISYPSCEmDvohJg9IISYO+iEmD0ghJg76ISYPSCEmDvohJg9IISYPYCEmDwAhJg9gISYPACEmD2AhJg8AISYPYCEmDwAhJg9gISYPACEmDwYhJg8MISYPEiEmD0ghJg8YISYPliEmDxghJg+WISYPGCEmD5YhJg8YISYPliEmDxghJg+WISYPHiEmD7ohJg8eISYPuiEmDyQhJg8qISYPJCEmDyohJg8kISYPKiEmD8AhJg+6ISYPwCEmD7ohJg/AISYPuiEmD8AhJg+6ISYPMCEmDzYhJg8wISYPNiEmDzwhJg+WISYPPCEmD5YhJg88ISYPliEmDzwhJg+WISYPQiEmD8YhJg9CISYPxiEmD0IhJg/GISYPQiEmD8YhJg/GISYPSCEmD8YhJg9IISYPzCEmD04hJg/MISYPTiEmD8whJg9OISYPzCEmD04hJg9UISYPWiEmD1QhJg9aISYPVCEmD1ohJg9UISYPWiEmD1QhJg9aISYPYCEm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BrgKeAvIBpQJiAhWCbIKhAp+DJQLzgvIBzYLyAvOC8gLzgvIC84LyAqoCq4KqAquCqgKrgaaBqAGpgrGCswKxgrMCsYKzAasBrIL2gvIC9oGuAvaC8gK8Ar2CvAK9gpOBr4K8Ar2BsQGygsIC6oK8AuqC1YI4AtWCOALVgjgC1YI4AtWCOAG0AbWC3QLsAuAC7YLgAuMC4YLjAuGC4wLhgvICQQLdAuwC3QLsAt0C7ALgAu2CbIJsgqcCqIIOAc2CsYKzAbcCOAIOAc2CDgHNgg4BzYIOAc2CDgHNgg4BzYIOAc2CDgHNgg4BzYIOAc2CDgHNgg4BzYG6AbiC84KzAvOCswLzgrMC84KzAvOCswLzgrMBugG7gp4C8gG9Ab6BwAHBgrGCswKxgrMCsYKzArGCswKxgrMBwAHBgrGCswKxgrMCsYKzArGCswHAAcGBwwI4AtWCOALVgjgC1YI4AtWCOALVgjgBwwI4AcSC7YLgAu2C4ALtgcYCQQKDAvOCgYKDAoGCgwK8Ag+CvAKKgceByQK8ArwCAIHKgoGBzAHNgpOBzwHQgp4CoQKfgvIB0gHTgqoCXAKxgdUB1oHYArSB2YK8Ar2C4wI/gj+CwgLqgdsB3ILvAd4C7YLjAuGB34LvAeECvYHigrSB5AHlgecB6IHqAmIB64HtAhcCDgL4Ap4C8gKxgrMC1YI4Ae6B8AHxgvCCzgLwgs4CoQKfgfMCkIHzApCB9II/gfYB94H5AvCCzgH6gfwCqgKrgg4C+AIOAvgC84KzAvOCswKeAvICngLyArGCswKxgrMC9oLyAvaC8gLVgjgC1YI4ArwB/YKZgquB/wJdggCCgwICAgOCDgL4AgUCBoKxgrMC4ALtgggCCYLqggsCDIIOAg+CswLzgsICEQISgp4CS4KBgtWCFALzgoMCFYJsghcCGgL2gvIC4ALtgrwCkgKDAoMCGIL5ghoCkgK8ArwCvAK8Ar2CvYKDAmyCzgLOAoMCG4L5gh0Cq4IegvICZQKeAjaCNoIzgiACIYIjAiSCq4ImAieCswIpAiqCLALgAuACh4K0gi2C8gIvAquC+YIwgjICMgIzgjUCNoJBAjgCgwKzAuMCOYI7AjyCPgJZAj+CQQJLgkKCRAJiArGCRYJFgrwC3oJsgkcCSIJKAkuCTQJOglACUYJTAlSCVgJXgvCCn4JZAlqCXAJdgoMCvAKYAqiCq4K0gvIC8gK9guqC4YJfAmCCYgJjgvICZQK0gu8C7wJvgncCe4LOAmaCaYJoAncCtIJpgmsCbIJuAm+CcQJygn6CdAJ1gncCgAJ4gnoCe4J9An6CgAKBgoMChIKGAukC6QKHgokCioK8AowCkgKNgsaCjwKSApCCkgLzgrMC84KzApOCk4KVApUCloLUAvUCmALwgs4CmYKrgpmCq4KZgquCmYKrgpmCq4KbApyCngLyAqECn4KhAp+CoQKigvCCpALwgqQC5gK5AtQCpYKnAqiCpwKogqoCq4KqAquCrQLpAq6CsAKxgrMCsYKzArGCswKxgrMC9QK0gvUCtIL2gvIC9oK2AvaCtgK3grkCvAK9grqCwIK8Ar2CvAK9gr8CwILCAuqCw4LFAsaCyALJgssCzILOAs+C0QLSgtQC1YLXAtWC1wLvAtiC2gLbgt0C7ALdAuwC3oMlAt6DJQLgAu2C4wLhguMC5ILmAueC6QLqguwC7YL4AvIC7wLwgvOC8gLzgvUC9oL4AvmC+wL8gABBkUAAAABBVAAAAABApT+SAABBNj+cAABA9T+cAABA5j+cAABBQAAKAABAbb+cAABA1z+rAABBNgAAAABBGD+rAABA9T+SAABBnUAAAABBhgAAAABAWL+cAABAk7+cAABAkT+cAABAeD+cAABA1z+cAABBET+cAABAz4AAAABA9QAMgABA+gAAAABA94AKAABAbb+ygABAWL+ygABA5j+ygABAqj+ygABA1L+ygABAqD+ygABAor+SAABA2YAAAABAwwAAAABA1wAAAABAPD+SAABA9QAAAABBWQAAAABAjAAAAABBBAAAAABBYwAAAABB+EAAAABBh3+FAABAiMAAAABBMT/MwABAw3+SAABA4QAAAABAqoAAAABAmgAAAABAl0AAAABAjEAAAABCVIAAAABCNoAAAABB9MAAAABBMz+rAABBO/+SAABBrj+rAABBsz+SAABAn4AAAABBhwAAAABBV8AAAABA6z+cAABAnYAAAABCX8AAAABCMQAAAABB9AAAAABBcwAAAABAX/+FAABAjD+SAABBTP+FAABAyAAAAABAgIAAAABAYoAAAABAs3+SAABAqn+SAABAgwAAAABBQcAAAABBdwAAAABBfoAAAABBRsAAAABA3oAAAABAu3+SAABAxL+SAABBMQAAAABAJb+rAABBbT+SAABAhIAAAABBMT+SAABAmz+SAABBET+FAABA+j+SAABA1z+SAABBs0AAAABBtX+FAABBmj+SAABBOz+SAABBGAAAAABBXUAAAABBJIAAAABAz3+FAABAeX+SAABAh3+FAABAcz+SAABAMz+SAABAfT+SAABAK/+SAABAd0AAAABBEQAAAABBikAAAABBAgAAAABAkIAAAABBBD+SAABAkT+SAABAgj+SAABAmoAAAABAecAAAABAooAAAABA5j+FAABAtcAAAABBD3+FAABAbIAAAABAkwAAAABBuAAAAABBgn+SAABBzMAAAABBHQAAAABAur+SAABBYMAAAABBan+SAABA0gAAAABBAoAAAABBEz+FAABBNj+SAABAuT+SAABA4ECtAABAtD+SAABBy8AAAABAfQAAAABA5j+hAABBqT+hAABAUD+hAABAqD+hAABAMj+SAABA0j+hAABA4T+hAABAuT+hAABBKL+hAABBM7+SAABBIj+hAABBBr+hAABBTP+hAABAkT+hAABAXz+hAABAMj+hAABBRT+hAABAUr+hAABAvgAAAABAtAAAAABAuT+ygABAtD+ygABAzT+SAABAoD+SAABAp4AAAABAvj+ygABAuT+6AABAuT+cAABAwz+cAABBD0AAAABAqj+cAABAqj+ogABAtr+cAABAZMAAAABBSUAAAABAbb+ogABAWL+ogABAbYAAAABBCQAAAABBPQAAAABAp7+6AABAXL+ygABAWj+cAABBnsAAAABBtUAAAABBU4AAAABBEwAAAABA3D+6AABA4T+cAABAu7+cAABA5gAAAABAqgAAAABAWL+FAABAUr+ygABAwz+6AABAWj+6AABAoD+ygABApQAAAABAiYAAAABAmz+ygABAjD+ygABApYAAAABApT+ygABAgj+ygABApT+6AABAgj+6AABAq3+cAABAf7+cAABA1L+XAABArz+SAABAy/+ogABAtD+ogABAzT+cAABArz+cAABA1IAAAABBDoAAAABAmIAAAABArz+ygABAmL+ygABBcgAAAABBGoAAAABAqAAAAABAuQAAAABBAYAAAABAzQAAAABArz+6AABAkT+6AABAtD+6AABAggAAAABBPYAAAABAUD+SAABArwAAAABA8AAAAABAWIAAAAB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M4403ghANN40kgcwCEAJxjTeB7o03jnCNfoR7hHuCEA2tgxWB7Q0gDSYPOw0mAeiNIAI1jSANIA0mDSYCwA87DmcOZwI1jmcB7QzjjOOM44zjjOOM440kghANJI0kjSSNJI03jTeNN403jTeNN403jX6Nfo1+jX6NrYHujSANIA0gDSANIA0gDSYNJg0mDSYNJg0mDSYNJgPujSYOZw0mDmcM440gDOONIAzjjSACEAIQAhACEA03gmoNN40kjSYNJI0mDSSNJg0kjSYNJI0mDSACEAI1gjWCcYJxgnGCagJxgnGNIA03jTeNN40kgsACwALADnCPOw5wjzsOcI1+jX6Nfo1+jX6NfoR7jmcNrY5nDa2DFYMVgxWM440gDSSNN4NGDz+GuIavg+6DRgM7A1sDRgNDjz+DlANGDz+DUoNbBriDiIOUA5mGr4a4g6QNIAO3g6QD1QOljSADswO3g74DxYPVA/MD8wPag/MD3wPug/MERQpEiBOD+YRFBEUERQvjCeqJLwP8CXwKRIzfC+MJfAvjCY6HdIgTikSJ6oQNi+MM3wzfBEUERQmOiY6NIAmGiuINIA0gCV6JholeicwJzAriCjkJzAlejSANIAdNB00JzAnMDSAJiAriB00HTQmICV6KOQpEiuIEe45nBHuOZwR7jmcNrY5nBTEFMQUxBUSFRIYoBUSGKAavhrQGuIzjjSANbA2bDSAJjoo5By6HTQgTiS8G3gkvBt4HE4cTiXwHFgnMCY6JzAnqijkJ6oo5CjkKOQmOicwJjonMCY6KOQgTjN8NIAcuh00HdInMCweLnAeWB7uLvYwGiXwJhou9jAaL4wlei+MJXovjCV6M3w0gCweK4gfCB+uIBQnMCBOKRIriCDAIs4gwCLOLvYwGjN8NIAzfDSAIvwjpiP0NIAj9DSAL4wleiQWM2YzfDSAM3w0gDN8NIAkvDSAJLw0gDSANIAmOicwJjonMC+MJXol8CYaJiAmOicwJjonMCY6JzAmOicwJ6oo5CeqKOQnqijkKRIriCweLnAu9jAaL4wwGjCQMuwwkDLsM3w0gDCQMuwwkDLsMZIx8DI+MuwzZjN8NIAzjjSAM440gDOONIAzjjSAM440gDOONIAzjjSAM440gDOONIAzjjSAM440gDOONIA0kjSYNJI0mDSSNJg0kjSYNJI0mDSSNJg0kjSYNJI0mDTeNJg03jSYNN403jTeNN403jWwNbA1sDWwNbA1+jZsNmw2bDZsNmw2tjmcNrY5nDa2OZw5wjzsPP4AAgCOAAUABQAAAAoACwABAA8AEQADACQAKQAGAC4ALwAMADIANAAOADcAPgARAEQARgAZAEgASQAcAEsASwAeAE4ATgAfAFAAUwAgAFUAVQAkAFcAVwAlAFkAXAAmAF4AXgAqAIIAjQArAJIAkgA3AJQAmAA4AJoAoAA9AKIApwBEAKoArQBKALIAsgBOALQAtgBPALgAuABSALoAugBTAL8AyABUAMoAygBeAMwAzABfAM4AzgBgANAA0gBhANQA3QBkAOcA5wBuAPgA+wBvAP0A/QBzAP8BAQB0AQMBAwB3AQgBCAB4AQ4BDgB5ARABEAB6ARIBEgB7ARQBFAB8ARcBFwB9ARkBGQB+ARsBGwB/ASQBKACAASoBKgCFASwBLACGAS4BLgCHATABMACIATIBMgCJATQBNACKATYBOwCLAT0BPQCRAT8BPwCSAUMBRQCTAUcBRwCWAVYBVgCXAVsBYgCYAWQBZACgAWYBZgChAWgBaQCiAW0BbQCkAW8BbwClAXEBdgCmAXgBeQCsAXsBfACuAX4BfgCwAYABgACxAYMBiACyAYoBigC4AYwBjAC5AY4BjgC6AZABkAC7AZMBlAC8AZcBlwC+AZkBmQC/AZ0BoADAAaQBqADEAaoBrgDJAbABsQDOAbQBtADQAbgBuADRAboBwADSAcMBxADZAcYByADbAcoBygDeAcwB0QDfAdQB1ADlAdgB2ADmAdoB2gDnAdwB4ADoAeMB5ADtAeYB6ADvAeoB7ADyAfIB9gD1AfgCBAD6AgYCCAEHAgoCCgEKAgwCDAELAiECIQEMAlACUQENAlUCVgEPAl0CXQERAl8CXwESAmcCZwETAmkCbQEUAm8CcwEZAnUCdQEeAncCdwEfAnkCiQEgApICsAExArICvQFQAsACxQFcAscCzAFiAs8C0AFoAtMC1AFqAtYC2QFsAtsC2wFwAt0C5gFxAuwC+QF7AvwC/QGJAwADBQGLAwgDFgGRAxgDQQGgA0YDSgHKA0wDTAHPA04DTgHQA1ADUAHRA1IDUgHSA1UDVQHTA1cDVwHUA1kDWQHVA1sDWwHWA10DXgHXA2MDYwHZA2UDZQHaA2cDZwHbA2kDaQHcA2sDcQHdA34DfwHkBy8HLwHmABwAD//EABH/xAAk/+wAgv/sAIP/7ACE/+wAhf/sAIb/7ACH/+wAwv/sAMT/7ADG/+wBQ//sAgj/xAIM/8QCVf/sAxr/7AMc/+wDHv/sAyD/7AMi/+wDJP/sAyb/7AMo/+wDKv/sAyz/7AMu/+wDMP/sAAQABQA8AAoAPAIHADwCCwA8AAEALQBaACEAD/9+ABH/fgAk/84AO//sAD3/9gCC/84Ag//OAIT/zgCF/84Ahv/OAIf/zgDC/84AxP/OAMb/zgE7//YBPf/2AT//9gFD/84CCP9+Agz/fgJV/84DGv/OAxz/zgMe/84DIP/OAyL/zgMk/84DJv/OAyj/zgMq/84DLP/OAy7/zgMw/84AJQAm/+wAKv/sADL/7AA0/+wAif/sAJT/7ACV/+wAlv/sAJf/7ACY/+wAmv/sAMj/7ADK/+wAzP/sAM7/7ADe/+wA4P/sAOL/7ADk/+wBDv/sARD/7AES/+wBFP/sAUf/7AJc/+wDRv/sA0j/7ANK/+wDTP/sA07/7ANQ/+wDUv/sA1T/7ANW/+wDWP/sA1r/7ANc/+wANABG/+wAR//sAEj/7ABS/+wAVP/sAKL/7ACp/+wAqv/sAKv/7ACs/+wArf/sALT/7AC1/+wAtv/sALf/7AC4/+wAuv/sAMn/7ADL/+wAzf/sAM//7ADR/+wA0//sANX/7ADX/+wA2f/sANv/7ADd/+wBD//sARH/7AET/+wBFf/sAUj/7AJd/+wDM//sAzX/7AM3/+wDOf/sAz3/7AM//+wDQf/sA0f/7ANJ/+wDS//sA0//7ANR/+wDU//sA1X/7ANX/+wDWf/sA1v/7ANd/+wABwAFACgACgAoAAwARgBAAEYAYABGAgcAKAILACgATgAF/7AACv+wACb/7AAq/+wAMv/sADT/7AA3/+wAOP/2ADn/7AA6/+wAPP/iAIn/7ACU/+wAlf/sAJb/7ACX/+wAmP/sAJr/7ACb//YAnP/2AJ3/9gCe//YAn//iAMj/7ADK/+wAzP/sAM7/7ADe/+wA4P/sAOL/7ADk/+wBDv/sARD/7AES/+wBFP/sAST/7AEm/+wBKv/2ASz/9gEu//YBMP/2ATL/9gE0//YBNv/sATj/4gE6/+IBR//sAfr/7AH8/+wB/v/sAgD/4gIH/7ACC/+wAlz/7AJe//YDRv/sA0j/7ANK/+wDTP/sA07/7ANQ/+wDUv/sA1T/7ANW/+wDWP/sA1r/7ANc/+wDXv/2A2D/9gNi//YDZP/2A2b/9gNo//YDav/2A2z/4gNu/+IDcP/iA37/7ABVAAUAKAAKACgARP/sAEb/7ABH/+wASP/sAEr/9gBS/+wAVP/sAKL/7ACj/+wApP/sAKX/7ACm/+wAp//sAKj/7ACp/+wAqv/sAKv/7ACs/+wArf/sALT/7AC1/+wAtv/sALf/7AC4/+wAuv/sAMP/7ADF/+wAx//sAMn/7ADL/+wAzf/sAM//7ADR/+wA0//sANX/7ADX/+wA2f/sANv/7ADd/+wA3//2AOH/9gDj//YA5f/2AQ//7AER/+wBE//sARX/7AFE/+wBRv/sAUj/7AIHACgCCwAoAlb/7AJd/+wDG//sAx3/7AMf/+wDI//sAyX/7AMn/+wDKf/sAyv/7AMt/+wDL//sAzH/7AMz/+wDNf/sAzf/7AM5/+wDPf/sAz//7ANB/+wDR//sA0n/7ANL/+wDT//sA1H/7ANT/+wDVf/sA1f/7ANZ/+wDW//sA13/7AAlACb/9gAq//YAMv/2ADT/9gCJ//YAlP/2AJX/9gCW//YAl//2AJj/9gCa//YAyP/2AMr/9gDM//YAzv/2AN7/9gDg//YA4v/2AOT/9gEO//YBEP/2ARL/9gEU//YBR//2Alz/9gNG//YDSP/2A0r/9gNM//YDTv/2A1D/9gNS//YDVP/2A1b/9gNY//YDWv/2A1z/9gAIAA//2AAR/9gBVv/sAV//7AFi/+wBaf/sAgj/2AIM/9gAAgFm//YBbf/2AAwABf+6AAr/ugFm/+wBbf/sAXH/ugFy/8QBc//sAXX/2AF4/8QCB/+6Agv/ugJR/8QACAAP/34AEf9+AVb/zgFf/84BYv/OAWn/zgII/34CDP9+AC0AD//EABD/2AAR/8QBVv+wAV//sAFi/7ABZv/iAWn/sAFt/+IBc//OAXb/4gF5/7oBev/OAXv/zgF8/9gBff/OAX7/ugGA/+wBgf/iAYL/zgGE/84Bhv/YAYf/zgGJ/84Biv/sAYz/ugGO/84Bj/+6AZD/ugGS/84Bk/+6AZT/7AGV/84Blv/OAZj/zgGZ/7oBmv/OAZv/zgIC/9gCA//YAgT/2AII/8QCDP/EAiH/4gJQ/+wACwAP/84AEf/OAVb/7AFf/+wBYv/sAWn/7AFy/+IBeP/iAgj/zgIM/84CUf/iAAUBZv/sAW3/7AFz/+IBjf/2AZH/9gAKAA//xAAR/8QBVv/YAV//2AFi/9gBZv/2AWn/2AFt//YCCP/EAgz/xAABAYgAFAANABD/zgF5/+wBfv/sAYz/7AGN/+wBj//sAZD/7AGR/+wBk//sAZn/7AIC/84CA//OAgT/zgAEAA//7AAR/+wCCP/sAgz/7AAGAAX/2AAK/9gBjf/2AZH/9gIH/9gCC//YAAcBef/sAX7/7AGM/+wBj//sAZD/7AGT/+wBmf/sAA8ABf/EAAr/xAF5//YBfv/2AYD/7AGK/+wBjP/2AY3/7AGP//YBkP/2AZH/7AGT//YBmf/2Agf/xAIL/8QABQAP/9gAEf/YAYj/9gII/9gCDP/YAAQAD//2ABH/9gII//YCDP/2AA8AD//iABD/7AAR/+IBef/sAX7/7AGM/+wBj//sAZD/7AGT/+wBmf/sAgL/7AID/+wCBP/sAgj/4gIM/+IABAAF/+wACv/sAgf/7AIL/+wABgAF//YACv/2AYD/7AGK/+wCB//2Agv/9gACAwv/7AMN/+wAEQAF/+wACv/sAar/9gHB/+wCB//sAgv/7AJv//YCef/sArz/7AK+/+wCwv/sAsT/7ALR/+wC1v/2Atj/9gLa//YC+v/sADcAD//YABH/2AGd/+wBpP/sAab/7AGo/+IBqv/sAa7/7AGw/+wBsf/sAbX/7AG8/+IBvf/iAb//7AHE/+wBx//sAc7/9gHV//YB8v/2Agj/2AIM/9gCb//sAnD/9gJ3/+wCff/2An//9gKc/+wCnv/sAqb/7AKy/+ICtP/iArb/4gK4/+wCuv/sAsf/7ALL/+wCzP/2Atb/7ALY/+wC2v/sAuL/7ALk/+wC8v/sAvT/4gL2/+IC+P/iAwL/7AME/+wDCv/sAwz/7AMO/+wDD//2AxT/7AMY/+wDGf/2ADYABf/YAAr/2AGd/8QBpv/EAaj/7AG8/84Bvf/sAcH/zgHE/8QB3P/sAd3/7AHh/+wB5P/sAfb/7AIH/9gCC//YAmv/2AJ5/84Cff/YAn//2AKU/9gCmP/YAqT/2AKm/8QCp//sArL/zgKz/+wCtP/OArX/7AK2/84Ct//sArr/xAK7/+wCvP/OAr3/7AK+/84Cv//sAtH/zgLS/+wC9P/sAvX/7AL2/+wC9//sAvj/7AL5/+wC+v/OAvv/7AMA/9gDCv/OAwv/4gMM/84DDf/iAxT/xAMV/+wAtQAP/84AEf/OACIAFAAk/9gAJv/2ACr/9gAy//YANP/2AET/7ABG/+wAR//sAEj/7ABK//YAUP/2AFH/9gBS/+wAU//2AFT/7ABV//YAVv/2AFj/9gCC/9gAg//YAIT/2ACF/9gAhv/YAIf/2ACJ//YAlP/2AJX/9gCW//YAl//2AJj/9gCa//YAov/sAKP/7ACk/+wApf/sAKb/7ACn/+wAqP/sAKn/7ACq/+wAq//sAKz/7ACt/+wAtP/sALX/7AC2/+wAt//sALj/7AC6/+wAu//2ALz/9gC9//YAvv/2AML/2ADD/+wAxP/YAMX/7ADG/9gAx//sAMj/9gDJ/+wAyv/2AMv/7ADM//YAzf/sAM7/9gDP/+wA0f/sANP/7ADV/+wA1//sANn/7ADb/+wA3f/sAN7/9gDf//YA4P/2AOH/9gDi//YA4//2AOT/9gDl//YA+v/2AQb/9gEI//YBDf/2AQ7/9gEP/+wBEP/2ARH/7AES//YBE//sART/9gEV/+wBF//2ARn/9gEd//YBIf/2ASv/9gEt//YBL//2ATH/9gEz//YBNf/2AUP/2AFE/+wBRv/sAUf/9gFI/+wBSv/2Agj/zgIM/84CVP/2AlX/2AJW/+wCXP/2Al3/7AJf//YDGv/YAxv/7AMc/9gDHf/sAx7/2AMf/+wDIP/YAyL/2AMj/+wDJP/YAyX/7AMm/9gDJ//sAyj/2AMp/+wDKv/YAyv/7AMs/9gDLf/sAy7/2AMv/+wDMP/YAzH/7AMz/+wDNf/sAzf/7AM5/+wDPf/sAz//7ANB/+wDRv/2A0f/7ANI//YDSf/sA0r/9gNL/+wDTP/2A07/9gNP/+wDUP/2A1H/7ANS//YDU//sA1T/9gNV/+wDVv/2A1f/7ANY//YDWf/sA1r/9gNb/+wDXP/2A13/7ANf//YDYf/2A2P/9gNl//YDZ//2A2n/9gNr//YAEwA3/9gBJP/YASb/2AFx/9gBnf/YAab/2AG8/9gBxP/YAdz/7AHk/+wCpv/YAqf/7AKy/9gCs//sArr/2AK7/+wDFP/YAxX/7AN+/9gA4wAk/7oANwAUADkAFAA6ABQAPAAKAET/2ABG/8QAR//EAEj/xABK/+IAUP/iAFH/4gBS/8QAU//iAFT/xABV/+IAVv/iAFj/4gCC/7oAg/+6AIT/ugCF/7oAhv+6AIf/ugCfAAoAov/EAKP/2ACk/9gApf/YAKb/2ACn/9gAqP/YAKn/xACq/8QAq//EAKz/xACt/8QAtP/EALX/xAC2/8QAt//EALj/xAC6/8QAu//iALz/4gC9/+IAvv/iAML/ugDD/9gAxP+6AMX/2ADG/7oAx//YAMn/xADL/8QAzf/EAM//xADR/8QA0//EANX/xADX/8QA2f/EANv/xADd/8QA3//iAOH/4gDj/+IA5f/iAPr/4gEG/+IBCP/iAQ3/4gEP/8QBEf/EARP/xAEV/8QBF//iARn/4gEd/+IBIf/iASQAFAEmABQBK//iAS3/4gEv/+IBMf/iATP/4gE1/+IBNgAUATgACgE6AAoBQ/+6AUT/2AFG/9gBSP/EAUr/4gFW/7oBX/+6AWL/ugFp/7oBef/YAXr/7AF7/+wBfv/YAYH/4gGC/+wBg//sAYT/7AGH/+wBif/sAYz/2AGO/+IBj//YAZD/2AGT/9gBmf/YAaT/xAGq/7oBrv/EAbX/xAHK/+wBzv+6Ac//xAHV/7oB2P/EAdv/xAHe/8QB6v/EAe3/xAHu/+IB8v+6AfoAFAH8ABQB/gAUAgAACgJU/+ICVf+6Alb/2AJd/8QCX//iAmf/xAJv/7oCcP+6Anr/9gJ8/8QCgv/EAoT/xAKG/8QCiv/EAq//xAKx/8QCy//EAsz/ugLW/7oC1//sAtj/ugLZ/+wC2v+6Atv/7ALd/8QC3//sAuH/7ALt/8QC7//EAvH/xAMG/7oDB//EAwj/ugMJ/8QDDv/EAw//ugMT/8QDF//EAxj/xAMZ/7oDGv+6Axv/2AMc/7oDHf/YAx7/ugMf/9gDIP+6AyL/ugMj/9gDJP+6AyX/2AMm/7oDJ//YAyj/ugMp/9gDKv+6Ayv/2AMs/7oDLf/YAy7/ugMv/9gDMP+6AzH/2AMz/8QDNf/EAzf/xAM5/8QDPf/EAz//xANB/8QDR//EA0n/xANL/8QDT//EA1H/xANT/8QDVf/EA1f/xANZ/8QDW//EA13/xANf/+IDYf/iA2P/4gNl/+IDZ//iA2n/4gNr/+IDbAAKA24ACgNwAAoDfgAUAIcAJv/OACr/zgAy/84ANP/OADf/ugA4/+wAOf/EADr/xAA8/8QAif/OAJT/zgCV/84Alv/OAJf/zgCY/84Amv/OAJv/7ACc/+wAnf/sAJ7/7ACf/8QAyP/OAMr/zgDM/84Azv/OAN7/zgDg/84A4v/OAOT/zgEO/84BEP/OARL/zgEU/84BJP+6ASb/ugEq/+wBLP/sAS7/7AEw/+wBMv/sATT/7AE2/8QBOP/EATr/xAFH/84BZv/YAW3/2AFx/7oBcv/EAXP/zgF1/8QBeP/EAYX/7AGd/7oBn//OAab/ugG4/84Bu//OAbz/ugG+/9gBwf+wAcT/ugHc/84B4f/EAeT/zgH6/8QB/P/EAf7/xAIA/8QCUf/EAlz/zgJe/+wCaf/OAnn/sAJ7/84Cff/EAn//xAKB/84Cg//OAoX/zgKH/84Cif/OAqb/ugKn/84Crv/OArD/zgKy/7oCs//OArT/xAK2/8QCuv+6Arv/zgK8/7ACvf/EAr7/sAK//8QCwv/EAsT/xALR/7AC0v/EAuz/zgLu/84C8P/OAvr/sAL7/8QDCv/EAwv/zgMM/8QDDf/OAxL/zgMU/7oDFf/OA0b/zgNI/84DSv/OA0z/zgNO/84DUP/OA1L/zgNU/84DVv/OA1j/zgNa/84DXP/OA17/7ANg/+wDYv/sA2T/7ANm/+wDaP/sA2r/7ANs/8QDbv/EA3D/xAN+/7oABAFx/+wBcv/2AXj/9gJR//YABAAP/+IAEf/iAgj/4gIM/+IAJQAP/8QAEf/EAVb/xAFf/8QBYv/EAWb/7AFp/8QBbf/sAXP/4gF2//YBef/OAXr/2AF7/+IBfP/iAX3/4gF+/84Bgf/iAYL/2AGE/+IBhv/iAYf/4gGJ/+IBjP/OAY7/zgGP/84BkP/OAZL/4gGT/84Blf/iAZb/4gGY/+IBmf/OAZr/4gGb/+ICCP/EAgz/xAIh//YANQAF/7oACv+6Ac//7AHY/+wB2//sAdz/zgHd/+IB3v/sAeH/4gHk/84B6v/sAe3/7AH2/+ICB/+6Agv/ugJn/+wCav/sAnr/9gJ8/+wCfv/sAoD/7AKC/+wChP/sAob/7AKI/+wCiv/sAqf/zgKv/+wCsf/sArP/zgK1/+wCt//sArv/zgK9/+ICv//iAsP/7ALF/+wC0v/iAt3/7ALt/+wC7//sAvH/7AL1/+IC9//iAvn/4gL7/+IDB//sAwn/7AML/8QDDf/EAxP/7AMV/84DF//sAAIDCv/2Awz/9gAYAA//ugAR/7oBpP/iAar/2AGu/+IBtf/iAc7/7AHV/+wB8v/sAgj/ugIM/7oCb//YAnD/7ALL/+ICzP/sAtb/2ALY/9gC2v/YAwb/2AMI/9gDDv/iAw//7AMY/+IDGf/sAB4ABf/YAAr/2AGd/+wBpv/sAbz/2AHB/9gBxP/sAdz/7AHk/+wCB//YAgv/2AJ5/9gCff/iAn//4gKm/+wCp//sArL/2AKz/+wCtP/iArb/4gK6/+wCu//sArz/2AK+/9gC0f/YAvr/2AMK/84DDP/OAxT/7AMV/+wAJwAF/8QACv/EAdD/7AHc/84B3f/iAd//7AHh/9gB5P/OAfb/4gIH/8QCC//EAmr/7AJ+/+wCgP/sAoj/7AKd/+wCp//OArP/zgK1/+ICt//iArn/7AK7/84Cvf/YAr//2ALD/+wCxf/sAsj/7ALS/9gC4//sAuf/7AL1/+IC9//iAvn/4gL7/9gDA//sAwX/7AML/84DDf/OAxX/zgAhAA//fgAR/34BpP/EAar/zgGu/8QBsP/sAbX/xAG//+wBzv/OAdX/zgHy/84CCP9+Agz/fgJv/84CcP/OAnP/9gKc/+wCuP/sAsf/7ALL/8QCzP/OAtb/zgLY/84C2v/OAuL/7AMC/+wDBP/sAwb/2AMI/9gDDv/EAw//zgMY/8QDGf/OACUABf/iAAr/4gGd/+IBpv/iAbz/4gHB/9gBxP/iAdz/7AHh/+wB5P/sAgf/4gIL/+ICef/YAn3/4gJ//+ICpv/iAqf/7AKy/+ICs//sArT/7AK2/+wCuv/iArv/7AK8/9gCvf/sAr7/2AK//+wC0f/YAtL/7AL6/9gC+//sAwr/7AML/+IDDP/sAw3/4gMU/+IDFf/sAAYABf/iAAr/4gIH/+ICC//iAwv/7AMN/+wAKQAF/7AACv+wAZ3/zgGm/84BvP+mAcH/xAHE/84B3P/YAeH/7AHk/9gCB/+wAgv/sAJ5/8QCff+6An//ugKm/84Cp//YArL/pgKz/9gCtP/OArb/zgK6/84Cu//YArz/xAK9/+wCvv/EAr//7ALC/+ICw//sAsT/4gLF/+wC0f/EAtL/7AL6/8QC+//sAwr/pgML/9gDDP+mAw3/2AMU/84DFf/YABkABf+6AAr/ugHc/84B4f/sAeT/zgIH/7oCC/+6Amr/7AJ+/+wCgP/sAoj/7AKn/84Cs//OArX/7AK3/+wCu//OAr3/7AK//+wCw//sAsX/7ALS/+wC+//sAwv/ugMN/7oDFf/OAA4Bnf/sAab/7AG8/+IBxP/sAn3/9gJ///YCpv/sArL/4gK0//YCtv/2Arr/7AMK/+wDDP/sAxT/7AAcAZ//7AG4/+wBu//sAb7/7AHh/+wCaf/sAnv/7AKB/+wCg//sAoX/7AKH/+wCif/sAq7/7AKw/+wCvf/sAr//7ALC/+wCxP/sAtL/7ALs/+wC7v/sAvD/7AL7/+wDBv/sAwj/7AML/+wDDf/sAxL/7ACDAA//xAAR/8QBn//sAaT/2AGq/8QBrv/YAbX/2AG4/+wBu//sAb7/4gHK/9gBzP/iAc3/4gHO/84Bz//OAdL/4gHT/+IB1P/iAdX/zgHW/+IB1//iAdj/zgHZ/+IB2v/iAdv/zgHe/84B4P/iAeH/2AHi/+IB4//iAeX/4gHm/+IB6P/iAen/7AHq/84B6wAUAez/4gHt/84B7v/YAfL/zgHz/+IB9AAUAfX/4gH3/+IB+f/iAgj/xAIM/8QCZ//OAmj/4gJp/+wCbv/iAm//xAJw/84Ccv/iAnT/7AJ2/+ICev/sAnv/7AJ8/84Cgf/sAoL/zgKD/+wChP/OAoX/7AKG/84Ch//sAon/7AKK/84Ck//iApUAFAKX/+ICm//iAqH/4gKj/+ICpQAUAqn/4gKr/+ICrf/iAq7/7AKv/84CsP/sArH/zgK9/9gCv//YAsH/4gLD/9gCxf/YAsr/4gLL/9gCzP/OAs7/4gLQ/+IC0v/YAtT/4gLW/8QC1//YAtj/xALZ/9gC2v/EAtv/2ALd/84C3v/2At//2ALg//YC4f/YAun/4gLr/+IC7P/sAu3/zgLu/+wC7//OAvD/7ALx/84C+//YAv3/4gL//+IDBv/YAwf/zgMI/9gDCf/OAwv/7AMN/+wDDv/YAw//zgMR/+IDEv/sAxP/zgMW//YDF//OAxj/2AMZ/84ACwAP/9gAEf/YAc7/9gHV//YB8v/2Agj/2AIM/9gCcP/2Asz/9gMP//YDGf/2ACoABf/iAAr/4gGd/+wBpv/sAbz/xAHB/9gBxP/sAdz/7AHd//YB4f/2AeT/7AH2//YCB//iAgv/4gJ5/9gCff/iAn//4gKm/+wCp//sArL/xAKz/+wCtP/OArb/zgK6/+wCu//sArz/2AK9//YCvv/YAr//9gLR/9gC0v/2AvX/9gL3//YC+f/2Avr/2AL7//YDCv/YAwv/7AMM/9gDDf/sAxT/7AMV/+wAEwAF/84ACv/OAdz/7AHd/+wB5P/sAfb/7AIH/84CC//OAqf/7AKz/+wCtf/sArf/7AK7/+wC9f/sAvf/7AL5/+wDC//YAw3/2AMV/+wACAG8/+wCff/2An//9gKy/+wCtP/2Arb/9gMK//YDDP/2ACkABf/iAAr/4gG8/+wBwf/YAdz/4gHh/+wB5P/iAgf/4gIL/+ICav/2Ann/2AJ9/+wCfv/2An//7AKA//YCiP/2Aqf/4gKy/+wCs//iArT/7AK1//YCtv/sArf/9gK7/+ICvP/YAr3/7AK+/9gCv//sAsL/4gLD/+wCxP/iAsX/7ALR/9gC0v/sAvr/2AL7/+wDCv/sAwv/4gMM/+wDDf/iAxX/4gAvAAX/ugAK/7oBnf/OAab/zgG8/7oBvv/sAcH/zgHE/84B3P/sAeH/7AHk/+wCB/+6Agv/ugJr/+wCef/OAn3/2AJ//9gClP/sApj/7AKk/+wCpv/OAqf/7AKy/7oCs//sArT/xAK2/8QCuv/OArv/7AK8/84Cvf/sAr7/zgK//+wCwv/OAsT/zgLR/84C0v/sAt7/7ALg/+wC+v/OAvv/7AMA/+wDCv+6Awv/7AMM/7oDDf/sAxT/zgMV/+wAHQHP/+wB2P/sAdv/7AHe/+wB4f/sAer/7AHt/+wCZ//sAnz/7AKC/+wChP/sAob/7AKK/+wCr//sArH/7AK9/+wCv//sAsP/7ALF/+wC0v/sAt3/7ALt/+wC7//sAvH/7AL7/+wDB//sAwn/7AMT/+wDF//sAAoAD//YABH/2AII/9gCDP/YAn3/9gJ///YCtP/2Arb/9gMK/+wDDP/sAAEB6QAUAAYABf/2AAr/9gIH//YCC//2Awv/7AMN/+wAPQAP/9gAEf/YAZ3/9gGk/+wBpv/2Aaj/7AGq/+wBrv/sAbD/7AGx//YBtf/sAbz/4gG9/+wBv//sAcH/7AHE//YBx//2Ac7/9gHV//YB8v/2Agj/2AIM/9gCb//sAnD/9gJ3//YCef/sAn3/9gJ///YCnP/sAp7/9gKm//YCsv/iArT/9gK2//YCuP/sArr/9gK8/+wCvv/sAsf/7ALL/+wCzP/2AtH/7ALW/+wC2P/sAtr/7ALi/+wC5P/2AvL/9gL0/+wC9v/sAvj/7AL6/+wDAv/sAwT/7AMK/+wDDP/sAw7/7AMP//YDFP/2Axj/7AMZ//YAHgAF//YACv/2AdD/7AHc//YB3f/2Ad//7AHh//YB5P/2Afb/9gIH//YCC//2Ap3/7AKn//YCs//2Arn/7AK7//YCvf/2Ar//9gLI/+wC0v/2AuP/7AL1//YC9//2Avn/9gL7//YDA//sAwX/7AML//YDDf/2AxX/9gBOAA//xAAR/8QBn//2AaT/zgGq/7oBrv/OAbX/zgG4//YBu//2Ab7/4gHJ//YBzv/YAc//7AHV/9gB2P/sAdv/7AHe/+wB4f/sAer/7AHrADIB7f/sAe7/9gHy/9gB9AAyAgj/xAIM/8QCZ//sAmn/9gJv/7oCcP/YAnv/9gJ8/+wCgf/2AoL/7AKD//YChP/sAoX/9gKG/+wCh//2Aon/9gKK/+wClQAyAqUAMgKu//YCr//sArD/9gKx/+wCvf/sAr//7ALC/+wCw//iAsT/7ALF/+ICy//OAsz/2ALS/+wC1v+6Atj/ugLa/7oC3f/sAuz/9gLt/+wC7v/2Au//7ALw//YC8f/sAvv/7AMG/7oDB//sAwj/ugMJ/+wDDv/OAw//2AMS//YDE//sAxf/7AMY/84DGf/YAAsAD//YABH/2AHO/+wB1f/sAfL/7AII/9gCDP/YAnD/7ALM/+wDD//sAxn/7ACdAA//xAAQ/9gAEf/EAZ//7AGk/84Bqv+6Aa7/zgG1/84BuP/sAbv/7AG8ABQBvv/YAcz/zgHN/84Bzv/EAc//ugHQ/+wB0f/sAdL/zgHT/84B1P/OAdX/xAHW/84B1//OAdj/ugHZ/84B2v/OAdv/ugHc/9gB3f/YAd7/ugHf/+wB4P/OAeH/zgHi/84B4//OAeT/2AHl/84B5v/OAef/7AHo/84B6f/iAer/ugHs/84B7f+6Ae7/xAHy/8QB8//OAfX/zgH2/9gB9//OAfn/zgIC/9gCA//YAgT/2AII/8QCDP/EAmf/ugJo/84Caf/sAmr/7AJu/84Cb/+6AnD/xAJy/84CdP/OAnb/zgJ6/84Ce//sAnz/ugJ+/+wCgP/sAoH/7AKC/7oCg//sAoT/ugKF/+wChv+6Aof/7AKI/+wCif/sAor/ugKT/84Cl//OApv/zgKd/+wCn//sAqH/zgKj/84Cp//YAqn/zgKr/84Crf/OAq7/7AKv/7oCsP/sArH/ugKyABQCs//YArX/2AK3/9gCuf/sArv/2AK9/84Cv//OAsH/zgLC/84Cw/+6AsT/zgLF/7oCyP/sAsr/zgLL/84CzP/EAs7/zgLQ/84C0v/OAtT/zgLW/7oC2P+6Atr/ugLd/7oC4//sAuX/7ALn/+IC6f/OAuv/zgLs/+wC7f+6Au7/7ALv/7oC8P/sAvH/ugLz/+wC9f/YAvf/2AL5/9gC+//OAv3/zgL//84DA//sAwX/7AMG/7oDB/+6Awj/ugMJ/7oDC//OAw3/zgMO/84DD//EAxH/zgMS/+wDE/+6AxX/2AMX/7oDGP/OAxn/xAAlAA//zgAQ/+wAEf/OAc7/4gHP//YB1f/iAdj/9gHb//YB3v/2Aer/9gHt//YB8v/iAgL/7AID/+wCBP/sAgj/zgIM/84CZ//2AnD/4gJ8//YCgv/2AoT/9gKG//YCiv/2Aq//9gKx//YCzP/iAt3/9gLt//YC7//2AvH/9gMH//YDCf/2Aw//4gMT//YDF//2Axn/4gCUAA//zgAQ/+wAEf/OAZ0AFAGf/+wBpP/YAaYAFAGq/8QBrv/YAbX/2AG4/+wBu//sAbwAFAG+/+IBxAAUAcz/4gHN/+IBzv/OAc//2AHQ/+wB0f/sAdL/4gHT/+IB1P/iAdX/zgHW/+IB1//iAdj/2AHZ/+IB2v/iAdv/2AHe/9gB3//sAeD/4gHh/84B4v/iAeP/4gHl/+IB5v/iAef/7AHo/+IB6v/YAesAFAHs/+IB7f/YAe7/4gHy/84B8//iAfQAFAH1/+IB9//iAfn/4gIC/+wCA//sAgT/7AII/84CDP/OAmf/2AJo/+ICaf/sAm7/4gJv/8QCcP/OAnL/4gJ0/+wCdv/iAnr/4gJ7/+wCfP/YAoH/7AKC/9gCg//sAoT/2AKF/+wChv/YAof/7AKJ/+wCiv/YApP/4gKVABQCl//iApv/4gKd/+wCn//sAqH/4gKj/+ICpQAUAqYAFAKp/+ICq//iAq3/4gKu/+wCr//YArD/7AKx/9gCsgAUArn/7AK6ABQCvf/OAr//zgLB/+ICwv/sAsP/4gLE/+wCxf/iAsj/7ALK/+ICy//YAsz/zgLO/+IC0P/iAtL/zgLU/+IC1v/EAtj/xALa/8QC3f/YAuP/7ALl/+wC6f/iAuv/4gLs/+wC7f/YAu7/7ALv/9gC8P/sAvH/2ALz/+wC+//OAv3/4gL//+IDA//sAwX/7AMG/84DB//YAwj/zgMJ/9gDC//sAw3/7AMO/9gDD//OAxH/4gMS/+wDE//YAxQAFAMX/9gDGP/YAxn/zgAhAA//4gAR/+IBzv/iAc//7AHV/+IB2P/sAdv/7AHe/+wB6v/sAe3/7AHy/+ICCP/iAgz/4gJn/+wCcP/iAnz/7AKC/+wChP/sAob/7AKK/+wCr//sArH/7ALM/+IC3f/sAu3/7ALv/+wC8f/sAwf/7AMJ/+wDD//iAxP/7AMX/+wDGf/iACUBn//sAaMAbgG4/+wBu//sAb7/4gHc/+wB4f/YAeT/7AJp/+wCeAAeAnr/9gJ7/+wCgf/sAoP/7AKF/+wCh//sAon/7AKn/+wCrv/sArD/7AKz/+wCu//sAr3/2AK//9gCwv/iAsP/7ALE/+ICxf/sAtL/2ALs/+wC7v/sAvD/7AL7/9gDC//sAw3/7AMS/+wDFf/sACMBn//sAbj/7AG7/+wBvv/sAcH/7AHh/+wCaf/sAnn/7AJ7/+wCgf/sAoP/7AKF/+wCh//sAon/7AKu/+wCsP/sArz/7AK9/+wCvv/sAr//7ALC/84CxP/OAtH/7ALS/+wC7P/sAu7/7ALw/+wC+v/sAvv/7AMG/+wDCP/sAwv/7AMN/+wDEv/sAxb/9gAdAc//9gHY//YB2//2Ad7/9gHh//YB6v/2Ae3/9gJn//YCfP/2AoL/9gKE//YChv/2Aor/9gKv//YCsf/2Ar3/9gK///YC0v/2At3/9gLt//YC7//2AvH/9gL7//YDB//2Awn/9gML/+wDDf/sAxP/9gMX//YAQAAF/84ACv/OAZ3/2AGm/9gBqP/iAar/4gGw/+IBvP+6Ab3/4gG//+IBwf/iAcT/2AHQ/+wB3P/iAd//7AHh/+wB5P/iAgf/zgIL/84Cb//iAnP/7AJ5/+ICff/iAn//4gKc/+ICnf/sAqb/2AKn/+ICsv+6ArP/4gK0/+ICtv/iArj/4gK5/+wCuv/YArv/4gK8/+ICvf/sAr7/4gK//+wCx//iAsj/7ALR/+IC0v/sAtb/4gLY/+IC2v/iAuL/4gLj/+wC9P/iAvb/4gL4/+IC+v/iAvv/7AMC/+IDA//sAwT/4gMF/+wDCv/sAwv/7AMM/+wDDf/sAxT/2AMV/+IAFwAP/9gAEf/YAar/9gGw/+wBvP/sAb//7AII/9gCDP/YAm//9gJ9//YCf//2Apz/7AKy/+wCtP/2Arb/9gK4/+wCx//sAtb/9gLY//YC2v/2AuL/7AMC/+wDBP/sABMABf/sAAr/7AHQ//YB3f/2Ad//9gH2//YCB//sAgv/7AKd//YCuf/2Asj/9gLj//YC9f/2Avf/9gL5//YDA//2AwX/9gML/+wDDf/sACsABf/YAAr/2AGd/+IBpv/iAar/7AGw/+wBvP/iAb//7AHB/+wBxP/iAdz/7AHk/+wCB//YAgv/2AJv/+wCef/sAn3/7AJ//+wCnP/sAqb/4gKn/+wCsv/iArP/7AK0/+wCtv/sArj/7AK6/+ICu//sArz/7AK+/+wCx//sAtH/7ALW/+wC2P/sAtr/7ALi/+wC+v/sAwL/7AME/+wDCv/sAwz/7AMU/+IDFf/sAB4ABf/OAAr/zgHQ/+wB3P/iAd3/7AHf/+wB4f/sAeT/4gH2/+wCB//OAgv/zgKd/+wCp//iArP/4gK5/+wCu//iAr3/7AK//+wCyP/sAtL/7ALj/+wC9f/sAvf/7AL5/+wC+//sAwP/7AMF/+wDC//OAw3/zgMV/+IABQHh/+wCvf/sAr//7ALS/+wC+//sAAQBowBuAucAFAML/+wDDf/sADwABf+6AAr/ugAm/+wAKv/sAC0AggAy/+wANP/sADf/ugA5/9gAOv/YADz/xACJ/+wAlP/sAJX/7ACW/+wAl//sAJj/7ACa/+wAn//EAMj/7ADK/+wAzP/sAM7/7ADe/+wA4P/sAOL/7ADk/+wBDv/sARD/7AES/+wBFP/sAST/ugEm/7oBNv/YATj/xAE6/8QBR//sAfr/2AH8/9gB/v/YAgD/xAIH/7oCC/+6Alz/7ANG/+wDSP/sA0r/7ANM/+wDTv/sA1D/7ANS/+wDVP/sA1b/7ANY/+wDWv/sA1z/7ANs/8QDbv/EA3D/xAN+/7oABAAF//YACv/2Agf/9gIL//YAAQAtADwAEQAF//YACv/2AFn/7ABa/+wAW//sAFz/7ABd//YAv//sATf/7AE8//YBPv/2AUD/9gH7/+wB/f/sAgf/9gIL//YDbf/sADQAD//YABH/2AAk/+wAN//iADn/9gA6//YAO//sADz/9gA9//YAgv/sAIP/7ACE/+wAhf/sAIb/7ACH/+wAn//2AML/7ADE/+wAxv/sAST/4gEm/+IBNv/2ATj/9gE6//YBO//2AT3/9gE///YBQ//sAaD/9gH6//YB/P/2Af7/9gIA//YCCP/YAgz/2AJV/+wDGv/sAxz/7AMe/+wDIP/sAyL/7AMk/+wDJv/sAyj/7AMq/+wDLP/sAy7/7AMw/+wDbP/2A27/9gNw//YDfv/iABIASQAoAFcAKABZADIAWgAyAFsAMgBcADIAvwAyASUAKAEnACgBNwAyAfsAMgH9ADICNAAoAjUAKAJaACgCWwAoA20AMgN/ACgAHAAP/+wAEf/sACT/9gCC//YAg//2AIT/9gCF//YAhv/2AIf/9gDC//YAxP/2AMb/9gFD//YCCP/sAgz/7AJV//YDGv/2Axz/9gMe//YDIP/2AyL/9gMk//YDJv/2Ayj/9gMq//YDLP/2Ay7/9gMw//YAEgBJADIAVwAyAFkAMgBaADIAWwAyAFwAMgC/ADIBJQAyAScAMgE3ADIB+wAyAf0AMgI0ADICNQAyAloAMgJbADIDbQAyA38AMgC5AA//xAAR/8QAIgAUACT/xAAm/+wAKv/sADL/7AA0/+wARP/OAEb/zgBH/84ASP/OAEr/7ABQ/+IAUf/iAFL/zgBT/+IAVP/OAFX/4gBW/9gAWP/iAF3/7ACC/8QAg//EAIT/xACF/8QAhv/EAIf/xACJ/+wAlP/sAJX/7ACW/+wAl//sAJj/7ACa/+wAov/OAKP/zgCk/84Apf/OAKb/zgCn/84AqP/OAKn/zgCq/84Aq//OAKz/zgCt/84AtP/OALX/zgC2/84At//OALj/zgC6/84Au//iALz/4gC9/+IAvv/iAML/xADD/84AxP/EAMX/zgDG/8QAx//OAMj/7ADJ/84Ayv/sAMv/zgDM/+wAzf/OAM7/7ADP/84A0f/OANP/zgDV/84A1//OANn/zgDb/84A3f/OAN7/7ADf/+wA4P/sAOH/7ADi/+wA4//sAOT/7ADl/+wA+v/iAQb/4gEI/+IBDf/iAQ7/7AEP/84BEP/sARH/zgES/+wBE//OART/7AEV/84BF//iARn/4gEd/9gBIf/YASv/4gEt/+IBL//iATH/4gEz/+IBNf/iATz/7AE+/+wBQP/sAUP/xAFE/84BRv/OAUf/7AFI/84BSv/YAgj/xAIM/8QCVP/iAlX/xAJW/84CXP/sAl3/zgJf/+IDGv/EAxv/zgMc/8QDHf/OAx7/xAMf/84DIP/EAyL/xAMj/84DJP/EAyX/zgMm/8QDJ//OAyj/xAMp/84DKv/EAyv/zgMs/8QDLf/OAy7/xAMv/84DMP/EAzH/zgMz/84DNf/OAzf/zgM5/84DPf/OAz//zgNB/84DRv/sA0f/zgNI/+wDSf/OA0r/7ANL/84DTP/sA07/7ANP/84DUP/sA1H/zgNS/+wDU//OA1T/7ANV/84DVv/sA1f/zgNY/+wDWf/OA1r/7ANb/84DXP/sA13/zgNf/+IDYf/iA2P/4gNl/+IDZ//iA2n/4gNr/+IACQAFACgACgAoAA//2AAR/9gAIgAUAgcAKAII/9gCCwAoAgz/2ADKAA//xAAQ/9gAEf/EACIAFAAk/7oAJv/sACr/7AAy/+wANP/sADcAFABE/7AARv+6AEf/ugBI/7oASv+6AFD/zgBR/84AUv+6AFP/zgBU/7oAVf/OAFb/xABY/84AWf/sAFr/7ABb/+wAXP/sAF3/2ACC/7oAg/+6AIT/ugCF/7oAhv+6AIf/ugCJ/+wAlP/sAJX/7ACW/+wAl//sAJj/7ACa/+wAov+6AKP/sACk/7AApf+wAKb/sACn/7AAqP+wAKn/ugCq/7oAq/+6AKz/ugCt/7oAtP+6ALX/ugC2/7oAt/+6ALj/ugC6/7oAu//OALz/zgC9/84Avv/OAL//7ADC/7oAw/+wAMT/ugDF/7AAxv+6AMf/sADI/+wAyf+6AMr/7ADL/7oAzP/sAM3/ugDO/+wAz/+6ANH/ugDT/7oA1f+6ANf/ugDZ/7oA2/+6AN3/ugDe/+wA3/+6AOD/7ADh/7oA4v/sAOP/ugDk/+wA5f+6APr/zgEG/84BCP/OAQ3/zgEO/+wBD/+6ARD/7AER/7oBEv/sARP/ugEU/+wBFf+6ARf/zgEZ/84BHf/EASH/xAEkABQBJgAUASv/zgEt/84BL//OATH/zgEz/84BNf/OATf/7AE8/9gBPv/YAUD/2AFD/7oBRP+wAUb/sAFH/+wBSP+6AUr/xAH7/+wB/f/sAgL/2AID/9gCBP/YAgj/xAIM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AAQKCQACCacAAQAECgoAAgmnAAEABAoLAAIJpwABAAQKDAACCacAAQAECfMAAgmnAAEABAn0AAIJpwABAAQJ9QACCacAAQAEC2EAAgmnAAEABAtnAAIJpwACAAgJcQmVAAAJlwmdACUJnwmgACwJogmmAC4JxgnHADMJ2AnYADULYAtgADYLZQtlADcABAAAAAEACAABACIAAgAKABYAAQAECg0AAwm4CaQAAQAECg4AAwm4CYsAAQACCYIJiQAEAAAAAQAIAAEELgABAAgAAQAECg8AAgm4AAQAAAABAAgAAQ80AEoAmgCmALIAvgDKANYA4gDuAPoBBgESAR4BKgE2AUIBTgFaAWYBcgF+AYoBlgGiAa4BugHGAdIB3gHqAfYCAgIOAhoD4gImAjICPgJKAlYCYgJuAnoChgKSAp4CqgK2AsICzgLaAuYC8gL+AwoDFgMiAy4DOgNGA1IDXgNqA3YDggPuA44DmgOmA7IDvgPKA9YD4gPuAAEABApXAAMJuAmdAAEABApYAAMJuAmdAAEABApZAAMJuAmdAAEABApaAAMJuAmdAAEABApbAAMJuAmdAAEABApcAAMJuAmdAAEABApdAAMJuAmdAAEABApeAAMJuAmdAAEABApfAAMJuAmdAAEABApgAAMJuAmdAAEABAphAAMJuAmdAAEABApiAAMJuAmdAAEABApjAAMJuAmdAAEABApkAAMJuAmdAAEABAplAAMJuAmdAAEABApmAAMJuAmdAAEABApnAAMJuAmdAAEABApoAAMJuAmdAAEABAppAAMJuAmdAAEABApqAAMJuAmdAAEABAqOAAMJuAmdAAEABAprAAMJuAmdAAEABApsAAMJuAmdAAEABAptAAMJuAmdAAEABApuAAMJuAmdAAEABApvAAMJuAmdAAEABApwAAMJuAmdAAEABApxAAMJuAmdAAEABAqVAAMJuAmdAAEABApyAAMJuAmdAAEABApzAAMJuAmdAAEABAqXAAMJuAmdAAEABAp0AAMJuAmdAAEABAp2AAMJuAmdAAEABAp3AAMJuAmdAAEABAp4AAMJuAmdAAEABAp7AAMJuAmdAAEABAp8AAMJuAmdAAEABAp9AAMJuAmdAAEABAqCAAMJuAmdAAEABAqHAAMJuAmdAAEABAqIAAMJuAmdAAEABAqQAAMJuAmdAAEABAqUAAMJuAmdAAEABAp+AAMJuAmdAAEABAp/AAMJuAmdAAEABAqAAAMJuAmdAAEABAqBAAMJuAmdAAEABAqDAAMJuAmdAAEABAqEAAMJuAmdAAEABAqFAAMJuAmdAAEABAqGAAMJuAmdAAEABAqJAAMJuAmdAAEABAqKAAMJuAmdAAEABAqLAAMJuAmdAAEABAqMAAMJuAmdAAEABAqNAAMJuAmdAAEABAqPAAMJuAmdAAEABAqRAAMJuAmdAAEABAqSAAMJuAmdAAEABAqTAAMJuAmdAAEABAqWAAMJuAmdAAEABAqYAAMJuAmdAAEABAqaAAMJuAmdAAEABAqbAAMJuAmdAAEABAqcAAMJuAmdAAEABAp5AAMJuAmdAAEABAp6AAMJuAmdAAEABAtiAAMJuAmdAAEABAtjAAMJuAmdAAEABAp1AAMJuAmdAAEABAqZAAMJuAmdAAQAAAABAAgAAQASAAEACAABAAQKEAACCbgAAQABCZ0ABQAAAAEACAABAA4ABAAaABoAGgAaAAEABAnZCdoJ3AndAAEABAADAAEJuAmdAAEACwAEAAAAAQAIAAEAEgABAAgAAQAEChAAAgmdAAEAAQm4AAQAAAABAAgAAQrAAEoBggGMAZYBoACaAbYBwAHKAdQB3gCkAK4AuADCAhgCIgIsAMwCQgJMAlYCYAJqAnQCfgKIApICnAKoBEQCsgK8AsYC0ALaAuQC7gL4AwIDDAMWajpqRAM4A0IDTGpOA2IDbAN2A4BqWGpiA6IDrAO2ANYDzAPWA+AD6gP0BE4D/gQIBBIEHAQmBDAEOgREBE4EWARiAAEABAoVAAIJuAABAAQKGwACCbgAAQAEChwAAgm4AAEABAodAAIJuAABAAQKHgACCbgAAQAECiIAAgm4AAEABApGAAIJuAAEAAAAAQAIAAEJ2ABKAJoApACuALgAwgDOANgA4gDsAPYBAAEMARgBJAEwAToBRAFOAVoBZAFuAXgBggGMAZYBoAGqAbQBwANcAcoB1AHeAegB8gH8AgYCEAIaAiQCLgI4AkQCUAJaAmQCbgJ6AoQCjgKYAqICrgK6AsQCzgLYAuQC7gL4AwIDDANmAxYDIAMqAzQDPgNIA1IDXANmA3ADegABAAQKEQACCbgAAQAEChIAAgm4AAEABAoTAAIJuAABAAQKFAACCbgAAQAEChUAAwm4CeAAAQAEChYAAgm4AAEABAoXAAIJuAABAAQKGAACCbgAAQAEChkAAgm4AAEABAoaAAIJuAABAAQKGwADCbgJ4AABAAQKHAADCbgJ4AABAAQKHQADCbgJ4AABAAQKHgADCbgJ4AABAAQKHwACCbgAAQAECiAAAgm4AAEABAohAAIJuAABAAQKIgADCbgJ4AABAAQKIwACCbgAAQAECiQAAgm4AAEABApIAAIJuAABAAQKJQACCbgAAQAECiYAAgm4AAEABAonAAIJuAABAAQKKAACCbgAAQAECikAAgm4AAEABAoqAAIJuAABAAQKKwADCbgJ4AABAAQKKwACCbgAAQAECi0AAgm4AAEABApRAAIJuAABAAQKLgACCbgAAQAECi8AAgm4AAEABAowAAIJuAABAAQKMQACCbgAAQAECjIAAgm4AAEABAo1AAIJuAABAAQKNgACCbgAAQAECjcAAgm4AAEABAo8AAIJuAABAAQKQQADCbgJ4AABAAQKQgADCbgJ4AABAAQKSgACCbgAAQAECk4AAgm4AAEABAo4AAIJuAABAAQKOQADCbgJ4AABAAQKOgACCbgAAQAECjsAAgm4AAEABAo9AAIJuAABAAQKPgACCbgAAQAECj8AAwm4CeAAAQAECkAAAwm4CeAAAQAECkMAAgm4AAEABApEAAIJuAABAAQKRQACCbgAAQAECkYAAwm4CeAAAQAECkcAAgm4AAEABApJAAIJuAABAAQKSwACCbgAAQAECkwAAgm4AAEABApNAAIJuAABAAQKUgACCbgAAQAEClMAAgm4AAEABApUAAIJuAABAAQKVQACCbgAAQAEClYAAgm4AAEABAozAAIJuAABAAQKNAACCbgAAQAECiwAAgm4AAEABApQAAIJuAABAAQLZgACCbgAAQAEC2gAAgm4AAQAAAABAAgAAQKgADoAegCEAI4AmACiAKwAtgDAAMoA1ADeAOgA8gD8AQYBEAEaASQBLgE4AlACjAFCAUwBVgFgAWoBdAF+AYgBkgGcAaYBsAG6AcQBzgHYAeIB7AH2AgACCgIUAh4CKAIyAjwCRgJQApYCWgJkAm4CeAKCAowClgABAAQKnQACCbgAAQAECp4AAgm4AAEABAqfAAIJuAABAAQKoAACCbgAAQAECqMAAgm4AAEABAqkAAIJuAABAAQKpQACCbgAAQAECqYAAgm4AAEABAqnAAIJuAABAAQKsAACCbgAAQAECrEAAgm4AAEABAqyAAIJuAABAAQKtAACCbgAAQAECrUAAgm4AAEABAq2AAIJuAABAAQKtwACCbgAAQAECrgAAgm4AAEABAq5AAIJuAABAAQKugACCbgAAQAECrsAAgm4AAEABAq+AAIJuAABAAQKvwACCbgAAQAECsAAAgm4AAEABArBAAIJuAABAAQKwgACCbgAAQAECsQAAgm4AAEABArFAAIJuAABAAQKxgACCbgAAQAECscAAgm4AAEABArIAAIJuAABAAQKyQACCbgAAQAECswAAgm4AAEABArNAAIJuAABAAQKzgACCbgAAQAECs8AAgm4AAEABArQAAIJuAABAAQK2QACCbgAAQAECtoAAgm4AAEABArbAAIJuAABAAQK3QACCbgAAQAECt4AAgm4AAEABArfAAIJuAABAAQK4AACCbgAAQAECuEAAgm4AAEABAriAAIJuAABAAQK4wACCbgAAQAECuQAAgm4AAEABAq8AAIJuAABAAQK5wACCbgAAQAECugAAgm4AAEABArpAAIJuAABAAQK6gACCbgAAQAECusAAgm4AAEABAq9AAIJuAABAAQK5gACCbgAAgAJClcKWgAAClwKYAAECmUKZwAJCmkKdwAMCnkKfgAbCoAKhAAhCokKiwAmCo0KmwApC2ILYwA4AAQAAAABAAgAAQNqAEoAmgCkAK4AuADCAMwA1gDgAOoA9AD+AQgBEgEcASYBMAE6AUQBTgFYAWIBbAF2AYABigGUAZ4BqAGyAbwBxgHQAdoDVgHkAe4B+AICAgwCFgIgAioCNAI+AkgCUgJcAmYCcAJ6AoQCjgKYAqICrAK2AsACygLUAt4C6ALyAvwDBgNgAxADGgMkAy4DOANCA0wDVgNgAAEABApXAAIKEAABAAQKWAACChAAAQAEClkAAgoQAAEABApaAAIKEAABAAQKWwACChAAAQAEClwAAgoQAAEABApdAAIKEAABAAQKXgACChAAAQAECl8AAgoQAAEABApgAAIKEAABAAQKYQACChAAAQAECmIAAgoQAAEABApjAAIKEAABAAQKZAACChAAAQAECmUAAgoQAAEABApmAAIKEAABAAQKZwACChAAAQAECmgAAgoQAAEABAppAAIKEAABAAQKagACChAAAQAECo4AAgoQAAEABAprAAIKEAABAAQKbAACChAAAQAECm0AAgoQAAEABApuAAIKEAABAAQKbwACChAAAQAECnAAAgoQAAEABApxAAIKEAABAAQKlQACChAAAQAECnIAAgoQAAEABApzAAIKEAABAAQKlwACChAAAQAECnQAAgoQAAEABAp2AAIKEAABAAQKdwACChAAAQAECngAAgoQAAEABAp7AAIKEAABAAQKfAACChAAAQAECn0AAgoQAAEABAqCAAIKEAABAAQKhwACChAAAQAECogAAgoQAAEABAqQAAIKEAABAAQKlAACChAAAQAECn4AAgoQAAEABAp/AAIKEAABAAQKgAACChAAAQAECoEAAgoQAAEABAqDAAIKEAABAAQKhAACChAAAQAECoUAAgoQAAEABAqGAAIKEAABAAQKiQACChAAAQAECooAAgoQAAEABAqLAAIKEAABAAQKjAACChAAAQAECo0AAgoQAAEABAqPAAIKEAABAAQKkQACChAAAQAECpIAAgoQAAEABAqTAAIKEAABAAQKlgACChAAAQAECpgAAgoQAAEABAqaAAIKEAABAAQKmwACChAAAQAECpwAAgoQAAEABAp5AAIKEAABAAQKegACChAAAQAEC2IAAgoQAAEABAtjAAIKEAABAAQKdQACChAAAQAECpkAAgoQAAIABgmCCaYAAAm+CcUAJQn2Cg4ALQtgC2EARgtlC2UASAtnC2cASQAEAAAAAQAIAAEDRgBHAJQAngCoALIAvADGANAA2gDkAO4A+AECAQwBFgEgASoBNAE+AUgBUgFcAWYBcAF6AYQBjgGYAaIBrAG2AzIBwAHKAdQB3gHoAfIB/AIGAhACGgIkAi4COAJCAkwCVgJgAmoCdAJ+AogCkgKcAqYCsAK6AsQCzgLYAuIC7AL2AwADCgM8AxQDHgMoAzIDPAABAAQKnQACChAAAQAECp4AAgoQAAEABAqfAAIKEAABAAQKoAACChAAAQAECqEAAgoQAAEABAqjAAIKEAABAAQKpAACChAAAQAECqUAAgoQAAEABAqmAAIKEAABAAQKpwACChAAAQAECqgAAgoQAAEABAqqAAIKEAABAAQKrAACChAAAQAECq4AAgoQAAEABAqwAAIKEAABAAQKsQACChAAAQAECrIAAgoQAAEABAqzAAIKEAABAAQKtAACChAAAQAECrUAAgoQAAEABAq2AAIKEAABAAQKtwACChAAAQAECrgAAgoQAAEABAq5AAIKEAABAAQKugACChAAAQAECrsAAgoQAAEABAq8AAIKEAABAAQKvQACChAAAQAECr4AAgoQAAEABAq/AAIKEAABAAQKwQACChAAAQAECsIAAgoQAAEABArDAAIKEAABAAQKxAACChAAAQAECsUAAgoQAAEABArGAAIKEAABAAQKxwACChAAAQAECsgAAgoQAAEABArJAAIKEAABAAQKygACChAAAQAECswAAgoQAAEABArNAAIKEAABAAQKzgACChAAAQAECs8AAgoQAAEABArQAAIKEAABAAQK0QACChAAAQAECtMAAgoQAAEABArVAAIKEAABAAQK1wACChAAAQAECtkAAgoQAAEABAraAAIKEAABAAQK2wACChAAAQAECtwAAgoQAAEABArdAAIKEAABAAQK3gACChAAAQAECt8AAgoQAAEABArgAAIKEAABAAQK4QACChAAAQAECuIAAgoQAAEABArjAAIKEAABAAQK5AACChAAAQAECuYAAgoQAAEABArnAAIKEAABAAQK6AACChAAAQAECuoAAgoQAAEABArrAAIKEAABAAQK7AACChAAAQAECsAAAgoQAAEABArpAAIKEAACAAQKEQpOAAAKUApWAD4LZgtmAEULaAtoAEYABgAAAAEACAACAFBa4gAQABgAAgAAAC4AAQpxAAEAAQACAAMJrgmvAAEJuAm4AAEJyAnJAAEAAQAEAAAAAQABAAEAAQAAABIAAgAAAAEACAABAAgAAQAOAAEAAQpxAAIKKwmdAAQAAAABAAgAAQEWAAYAEgAeAFYAbACYAK4AAQAECzIAAwm4CZwABQAMABYAHgAoADALNQAECbgJjAmtCzQAAwm4CYwLNwAECbgJjQmtCzYAAwm4CY0LOAADCbgJnAACAAYADgs5AAMJuAmNCzoAAwm4CZwABAAKABQAHAAkCz0ABAm4CY4JrQs8AAMJuAmOCzsAAwm4CY8LPgADCbgJnAACAAYADgs/AAMJuAmPC0AAAwm4CZwACgAWAB4AJgAuADYAPgBGAE4AVgBeC0QAAwm4CYQLQwADCbgJhQtKAAMJuAmTC0gAAwm4CZQLRQADCbgJmQtGAAMJuAmaC0sAAwm4CZsLTAADCbgJnAtH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AAIJ3AndAAMJ4AngACcJ9gn5AAMJ+gn7AAIJ/An9AAMJ/gn+AAIJ/woMAAMKDQoNAAIKDgoOAAMKEwoTAAQKFAoUAAkKFgoWABIKGAoYACYKGQoZABwKGgoaACUKGwobACEKHgoeACUKHwofABoKIAogAAwKIQohABAKIgoiACIKIwojAAsKJAokAAoKJQolAAUKJwonABQKKAooABgKKQopAAYKKgoqAAcKKworABMKLAosACAKLgouABEKLwovABUKMAowAA0KMQoxABAKMgoyAB0KMwozABkKNAo0ABYKNwo3AAQKOAo4AAkKOgo6ABIKPAo8ACYKPQo9ABwKPgo+ACUKPwo/ACEKQgpCACUKQwpDABoKRApEAAwKRQpFABAKRgpGACIKRwpHAAsKSApIAAoKSQpJAAUKSwpLABQKTApMABgKTQpNAAYKTgpOAAcKTwpPABMKUApQACAKUgpSABEKUwpTABUKVApUAA0KVQpVABAKVgpWAB0KVwpXAAMKWApYAAIKWQpdAAMKXgpgAAIKYQpkAAMKZQplAAIKZgp2AAMKdwp3AAIKeAp4AAMKeQp6AAIKewp7AAMKfAp8AAIKfQqBAAMKggqEAAIKhQqIAAMKiQqJAAIKigqaAAMKmwqbAAIKnAqcAAMKnwqfAAgKoAqgAAkKowqjABIKqAqoACEKrgquACUKsAqwAB8KsQqxAA4KsgqyABAKswqzACIKtAq0AAsKtQq1AA8Ktgq2AAUKuAq4ABQKuQq5ACUKugq6ABcKuwq7AAcKvAq8ABMKvQq9ACAKvwq/ABEKwArAACQKwQrBAA0KwgrDACUKxQrFACUKyArIAAgKyQrJAAkKzArMABIK0QrRACEK1wrXACUK2QrZAB8K2graAA4K2wrbABAK3ArcACIK3QrdAAsK3greAA8K3wrfAAUK4QrhABQK4griACUK4wrjABcK5ArkAAcK5QrlABMK5grmACAK6AroABEK6QrpACQK6grqAA0K6wrsACULMQsxAAMLMgszAAILNAs0AAMLNgs2AAMLOAs4AAILOQs5AAMLOgs6AAILOws8AAMLPgs+AAILPws/AAMLQAtAAAILQQtBAAMLQgtCABsLQwtFAAMLRgtGAAILRwtHAAMLSAtJAAILSgtKAAMLSwtLAAILTAtMAAMLTQtNAB4LTgtOAAMLTwtP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CY8ABQmRCZEABgmSCZIACAmTCZMABAmUCZQABwmVCZgABgmZCZkABwmbCZwABwmdCZ4AAwmiCaIABgmkCaQABwmlCaUACAmmCaYABgm+Cb4ABgnACcAABgnCCcIABwnDCcMABQnECcQABgnFCcUABwnZCdkABgncCd0ABwngCeAACQn2CfcABwn4CfkACAn8Cf0ABAn/Cf8ABgoACgAACAoBCgEABAoCCgIABwoDCgMABgoECgQABwoGCgYABwoICggABgoKCgoABwoLCgsACAoMCgwABgoOCg4ACAopCikAAgpNCk0AAgpXClcABgpZClkABgpaClsABwpcCl0ACAphCmIABApjCmMABwpkCmQABQpmCmYABgpnCmcACApoCmgABAppCmkABwpqCmwABgptCm0ABwpvCnAABwpxCnEAAwp0CnQABgp2CnYABwp4CngABgp7CnsABgp9Cn0ABgp+Cn8ABwqACoEACAqFCoYABAqHCocABwqICogABQqKCooABgqLCosACAqMCowABAqNCo0ABwqOCpAABgqRCpEABwqTCpQABwqVCpUAAwqYCpgABgqaCpoABwqcCpwABgsxCzEACAs0CzQABAs2CzYABAs5CzkABAs7CzwABws/Cz8ABQtDC0MACAtEC0QABAtFC0UABwtHC0cABgtKC0oABQtOC04ABgtWC1kABwtbC1sABwtgC2MACAtlC2UACAtnC2cACAtpC2kAAQAMABoAJgAyAD4ASgBWAGIAcAB+AIwAmgCoAAMAAQACAAMAAABGAAMAAQACAAQAAABHAAMAAQACAAUAAABIAAMAAQACAAYAAABJAAMAAQACAAcAAABKAAMAAQACAAgAAABLAAQAAQACAAkAAwAAAEYABAABAAIACQAEAAAARwAEAAEAAgAJAAUAAABIAAQAAQACAAkABgAAAEkABAABAAIACQAHAAAASgAEAAEAAgAJAAgAAABLAAUAAAABAAgAAjBqABoACQAAAnYAAAAAAAAAAAAAAAAAAAACAGQJggmCAAUJhAmFAAYJhgmIAAcJjAmNAAMJjgmOAAcJjwmPAAQJkQmRAAYJkgmSAAcJkwmTAAQJlAmUAAcJlQmWAAUJlwmXAAYJmAmYAAUJmQmZAAYJmwmcAAYJnQmeAAMJogmiAAUJpAmkAAYJpgmmAAUJvgm+AAUJwAnA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AAQKZApkAAUKZgpmAAYKaApoAAUKaQppAAcKagpqAAYKawprAAcKbApsAAYKbQptAAcKbwpwAAcKcQpxAAMKdAp0AAYKdgp2AAcKeAp4AAYKewp7AAYKfQp9AAYKfgp+AAcKhQqGAAQKiAqIAAUKigqKAAYKjAqMAAUKjQqNAAcKjgqOAAYKjwqPAAcKkAqQAAYKkQqRAAcKkwqUAAcKlQqVAAMKmAqYAAYKmgqaAAcKnAqcAAYKwQrBAAIK6grqAAILNAs0AAQLNgs2AAQLOQs5AAQLPws/AAULRAtEAAULRQtFAAcLRwtHAAYLSgtKAAULTgtOAAcLVgtZAAcLWwtbAAcLaQtpAAEACgAWACIALgA6AEYAUgBgAG4AfACKAAMAAQACAAMAAABHAAMAAQACAAQAAABIAAMAAQACAAUAAABJAAMAAQACAAYAAABKAAMAAQACAAcAAABLAAQAAQACAAgAAwAAAEcABAABAAIACAAEAAAASAAEAAEAAgAIAAUAAABJAAQAAQACAAgABgAAAEoABAABAAIACAAHAAAASwAFAAAAAQAIAAIgFgAYAAgAAAHeAAAAAAAAAAAAAAAAAAIASwmCCYIABQmECYUABgmMCY0ABAmPCY8ABAmRCZEABgmTCZMABAmVCZYABQmXCZcABgmYCZgABQmZCZkABgmbCZwABgmdCZ4AAwmiCaIABQmkCaQABgmmCaYABQm+Cb4ABQnACcAABgnDCcMABAnECcQABQnFCcUABgnZCdkABgndCd0ABgngCeAABwn2CfYABgn8Cf0ABAn/Cf8ABgoBCgEABAoDCgQABgoGCgYABgoICggABQoKCgoABgoMCgwABQpXClcABQpZCloABgphCmIABApkCmQABApmCmYABQpoCmgABApqCmoABQprCmsABgpsCmwABQptCm0ABgpvCnAABgpxCnEAAwp0CnQABQp2CnYABgp4CngABQp7CnsABQp9Cn4ABgqFCoYABAqICogABAqKCooABQqMCowABAqOCo4ABQqPCo8ABgqQCpAABQqRCpEABgqTCpQABgqVCpUAAwqYCpgABQqaCpoABgqcCpwABQqxCrEAAgraCtoAAgs0CzQABAs2CzYABAs5CzkABAs/Cz8ABAtEC0QABAtFC0UABgtHC0cABgtKC0oABAtOC04ABgtWC1kABgtp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CowKjAADCo0KjQAFCo4KkQAECpIKkgAHCpMKkwAFCpQKlAAECpUKlQACCpYKlgAHCpcKlwAGCpgKmAAECpkKmQAHCpoKmgAECpsKmwAHCpwKnAAECzELMQAGCzQLNAADCzYLNgADCzkLOQADCzsLPAAFCz8LPwADC0ELQQAGC0MLQwAGC0QLRAADC0ULRQAEC0YLRgAIC0cLRwAEC0gLSAAIC0oLSgADC0sLSwAHC0wLTAAGC04LTgAEC08LTwAKC1ALUAAHC1ILUgAHC1ULVQAHC1YLVwAEC1gLWQAFC1oLWgAHC1sLWwAFC1wLXAAJC10LXQAIC14LXwAHC2ALYwAGC2ULZQAGC2cLZwAGC2kLaQABAAkAFAAeACgAMgA8AEYAUABaAGQAAgABAAIAAABBAAIAAQADAAAAQgACAAEABAAAAEMAAgABAAUAAABEAAIAAQAGAAAARQACAAEABwAAAEYAAgABAAgAAABHAAIAAQAJAAAASAACAAEACgAAAEoAAQAAAAEACAABAJIAAQABAAAAAQAIAAEAhAACAAEAAAABAAgAAQB2AAMAAQAAAAEACAABAGgABAABAAAAAQAIAAEAWgAFAAEAAAABAAgAAQBMAAYAAQAAAAEACAABAD4ABwABAAAAAQAIAAEAMAAIAAEAAAABAAgAAQAiAAkAAQAAAAEACAABABQACgABAAAAAQAIAAEABgALAAEAAQtpAAUAAAABAAgAAgEcABQABgAAAJwAAAAAAAAAAAACABYJbwlvAAMJggmmAAIJvgnFAAIJ9goOAAIKDwoPAAQKVwqcAAILHAscAAULMQsxAAILNAs0AAILNgs2AAILOAs5AAILOws8AAILPws/AAILQQtBAAILQwtIAAILSgtMAAILTgtQAAILUgtSAAILVQtjAAILZQtlAAILZwtnAAILaQt0AAEAAwAIABgAKAADAAIAAgADAAAATgACAE0AAwACAAIABAAAAE8AAgBNAAMAAgACAAUAAABQAAIATQABAAAAAQAIAAIADAADC5kLmQuZAAEAAwlvCg8LHAABAAAAAQAIAAEAIgAMAAEAAAABAAgAAQAUABgAAQAAAAEACAABAAYAJAACAAELaQt0AAAABgAAAAEACAACAYgAEAFeD9AAAgAAAWYAAgA3CYIJggACCYQJhAACCYwJjQACCY8JjwACCZEJkQACCZMJkwACCZUJmQACCZwJngACCaIJogACCaQJpAACCaYJpgACCb4JvgACCcAJwAACCcMJxQACCdkJ2QACCd0J3QACCfwJ/QACCf8J/wACCgEKAQACCgMKBAACCggKCAACCgoKCgACCgwKDAACClcKVwACClkKWQACCmEKYgACCmQKZAACCmYKZgACCmgKaAACCmoKbQACCnAKcQACCnQKdAACCnYKdgACCngKeAACCnsKewACCn0KfQACCoUKhgACCogKiAACCooKigACCowKjAACCo4KkQACCpQKlQACCpgKmAACCpoKmgACCpwKnAACCzQLNAACCzYLNgACCzkLOQACCz8LPwACC0QLRQACC0cLRwACC0oLSgACC04LTgACC1YLVwACC2oLbQABAAEJbgABAAEAAQAEAAIAAgABAAEAAAABAAAAUgABAAAAAQAIAAEABgJxAAEAAQluAAUAAAABAAgAAgAYAHQACAAAARoBKgE6AVQAAAAAAAAAAQAsCdkJ2gncCd0KWwpdCmEKYgpjCmQKcwp/CoEKhQqGCocKiAqXCzQLNgs5CzsLPAs/C0MLRAtFC0YLRwtIC0kLSgtQC1ILVwtZC1oLWwtcC10LXgtfC2ILYwACABsJrAmuAAUJrwmvAAYJuAm4AAUJyAnJAAYJ2QnaAAQJ3AndAAQKEAoQAAcKWwpbAAMKXQpdAAMKYQpkAAMKcwpzAAMKfwp/AAMKgQqBAAMKhQqIAAMKlwqXAAMLNAs0AAELNgs2AAELOQs5AAELOws8AAELPws/AAELQwtKAAILUAtQAAILUgtSAAILVwtXAAMLWQtZAAMLWgtfAAILYgtjAAMAAgAGADAAAgABAAUAAABXAAIABgAgAAIAAQAFAAEAVgACAAYAEAACAAEABQAAAFUAAgABAAYAAABXAAEABAADAAEABwAFAAEAVAACAAAAAQAIAAEACAABAA4AAQABChAAAQuZAAIAAAABAAgAAQAqABIAUgBYAF4AZABqAHAAdgB8AIQAjACUAJwApACsALQAugDAAMYAAQASClsKXQphCmIKYwpkCnMKfwqBCoUKhgqHCogKlwtXC1kLYgtjAAIJhguZAAIJiAuZAAIJjAuZAAIJjQuZAAIJjguZAAIJjwuZAAIJoAuZAAMJhgmnC5kAAwmICacLmQADCYwJpwuZ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";

                                    doc.addFileToVFS("NotoSans.ttf", NotoSans);
                                    doc.addFont('NotoSans.ttf', 'NotoSans', 'normal');
                                    doc.setFont('NotoSans'); // set font

                                    var count = 15; // initial header space
                                    const contentRightMargin = 35;
                                    const contentLeftMargin = 15;
                                    const contentTopBottomMargin = 27;
                                    const a4height = 280; // in mm.
                                    const a4width = 210; // in mm.

                                    // Get data to process.
                                    var data = data['newdata'];
                                    var title = data['documentname'];
                                    data = data['posts'];

                                    // Print title.
                                    doc.setFontSize(16);            
                                    doc.setTextColor(0,84,159);
                                    doc.text(35, count, title);

                                    doc.setTextColor(0,0,51);
                                    doc.setFontSize(10);

                                    if (data === null) {
                                        doc.text(35, 27, M.util.get_string('emptypdf', 'pdfannotator') + " " + page);
                                        data = 0;
                                    }

                                    var count = 27;
                                    var page = '0';

                                    for (var i = 0; i < data.length; i++) {
                                        (function (innerI){
                                            var post = data[innerI];
                                            // Add page number each time it changes.
                                            if (page !== post['page']) {
                                                page = post['page'];
                                                doc.setFont(undefined, "bold");
                                                doc.setTextColor(0,84,159);
                                                if (count >= a4height) {
                                                    doc.addPage();
                                                    count = contentTopBottomMargin;
                                                }
                                                doc.text(15, count, M.util.get_string('page', 'pdfannotator') + " " + page);
                                                doc.setFont(undefined, "normal");
                                                count += 5;
                                            };
                                            // Add icon to each question depending on its annotation type and increment count by 5 or 7.
                                            addIcon(post['annotationtypeid']);

                                            // Add question in RWTH dark blue.
                                            var question = post['answeredquestion'];
                                            var author = post['author'];
                                            var timeasked = post['timemodified'];
                                            doc.setTextColor(0,84,159);
                                            breakLines(author, timeasked, question);                                            
                                            // Add answers to the question in black (extremely dark blue which looks better).
                                            doc.setTextColor(0,0,51);
                                            var answers = post['answers'];
                                            var answer;
                                            for (var z = 0; z < answers.length; z++) {
                                                (function (innerZ){
                                                    answer = answers[innerZ];
                                                    count+= 5;
                                                    breakLines(answer['author'], answer['timemodified'], answer['answer']);
                                                })(z);
                                            }
                                        })(i);
                                        count += 10;
                                    }
                                    var printtitle = title + "_" + M.util.get_string('comments', 'pdfannotator');
                                    doc.save(printtitle + ".pdf");
                                    /**
                                     * Take a user's post (i.e. an individual question or answer), determine whether
                                     * it contains latex formulae images or not and place its text and/or images on the pdf
                                     */
                                    function breakLines(author=null, timemodified=null, post, characters = 130) {
                                        // 1. print the author right away
                                        printAuthor(author, timemodified);
                                        post.forEach(function(subContent) {
                                            // Answer contains text only or any object such as array.
                                            if (typeof subContent === "string") { 
                                                printTextblock(author, timemodified, subContent, characters);
                                            } else if (typeof subContent === "object") {
                                                printItem(subContent);
                                            }
                                        });
                                    }
                                    /**
                                     * Take a text block, split it into pieces no larger than 130 characters
                                     * and print one piece per line                                      
                                     */
                                    function printTextblock(author=null, timemodified=null, text, characters = 130) {
                                        // In the comments linebreaks are represented by <br \>-Tags. Sometimes there is an additional \n
                                        // jsPDF needs \n-linebreaks so we replace <br \> with \n. But first we remove all \n that already exist.
                                        text = text.replace(/\n/g, "");
                                        text = text.replace(/<br \/>/g, "\n");
                                        // Remove all other HTML-Tags.
                                        text = $("<div>").html(text).text();

                                        var stringarray = doc.splitTextToSize(text, characters);
                                        var textbit;
                                        for (var j = 0; j < stringarray.length; j++) {
                                            //doc.setFont('NotoSans');
                                            doc.setFont(undefined, "normal");
                                            textbit = stringarray[j];
                                            if (count >= a4height) {
                                                doc.addPage();
                                                count = contentTopBottomMargin;
                                            }
                                            doc.text(contentRightMargin, count, textbit);
                                            count += 5;
                                        }
                                    }
                                    function printItem(item, index) {
                                        if (typeof item === "object") { //item.includes('data:image/png;base64,')) {
                                            if (item['mathform']) {
                                                printMathFrom(item);
                                            } else if (item['image']) {
                                                printImage(item);
                                            }
                                        } else if (typeof item === "string"){
                                            printTextblock(null, null, item);
                                        } else {
                                            console.error(M.util.get_string('error:printlatex', 'pdfannotator'));
                                            notification.addNotification({
                                                message: M.util.get_string('error:printlatex','pdfannotator'),
                                                type: "error"
                                            });
                                        }
                                    }
                                    function printImage(data) {
                                        var url;
                                        var image;
                                        
                                        if (data['image'] !== 'error') {
                                            image = data['image'];
                                            var height = data['imageheight'] * 0.264583333333334; // Convert pixel into mm.
                                            // Reduce height and witdh if its size more than a4height.
                                            while ( height > (a4height-(2*contentTopBottomMargin) )) {
                                                height = height - (height*0.1);
                                            }
                                            var width = data['imagewidth'] * 0.264583333333334;
                                            while ( width > (a4width-(contentLeftMargin+contentRightMargin)) ) {
                                                width = width - (width*0.1);
                                            }
                                            if ( (count+height) >= a4height ) {
                                                doc.addPage();
                                                count = contentTopBottomMargin;
                                            }
                                            doc.addImage(image, data['format'], contentRightMargin, count, width, height); // image data, format, offset to the left, offset to the top, width, height
                                            count += (5 + height);
                                        } else {
                                            let item = `<p>${data['message']}</p>`;
                                            printTextblock(null, null, item);
                                        }
                                    }
                                    /**
                                     * Take an image, calculate its height in millimeters and print it on the pdf
                                     */
                                    function printMathFrom(data) {
                                        var img = data['mathform'];
                                        var height = data['mathformheight'] * 0.264583333333334; // Convert pixel into mm.
                                        if ( (count+height) >= a4height ) {
                                            doc.addPage();
                                            count = contentTopBottomMargin;
                                        }
                                        doc.addImage(img, data['format'], contentRightMargin, count, 0, 0); // image data, format, offset to the left, offset to the top, width, height
                                        count += (5 + height);
                                    }
                                    /**
                                     * Print the author in bold
                                     * @param {type} author
                                     * @returns {undefined}
                                     */
                                    function printAuthor(author, timemodified=null) {
                                        doc.setFont(undefined, "bold");
                                        if (timemodified !== null) {
                                            doc.text(120, count, timemodified);
                                        }
                                        if (author.length > 37) {
                                            count += 5;
                                        }
                                        doc.text(contentRightMargin, count, author);
                                        doc.setFont(undefined, "normal");
                                        count += 5;
                                    }
                                    /**
                                     * Place an icon before each question, depending on the question's type of annotation
                                     * Increment the height variable so that the next line does not overlap with the currnet one
                                     */
                                    function addIcon(annotationtype) {
                                        if (count >= a4height) {
                                            doc.addPage();
                                            count = contentTopBottomMargin;
                                        }
                                        var height = 5;
                                        switch(annotationtype) {
                                            case '1':
                                                doc.addImage(myarea, 'PNG', 15, count, 5, 5);
                                                break;
                                            case '3':
                                                doc.addImage(myhighlight, 'PNG', 15, count, 5, 5);
                                                break;
                                            case '4':
                                                doc.addImage(mypin, 'PNG', 15, count, 5, 7);
                                                height = 7;
                                                break;
                                            case '5':
                                                doc.addImage(mystrikeout, 'PNG', 15, count, 5, 5);
                                                break;
                                            default:
                                                doc.addImage(mypin, 'PNG', 15, count, 5, 7);
                                                height = 7;
                                        } 
                                        count+= height;
                                    }

                                } else if (data.status === 'empty') {
                                    notification.addNotification({
                                        message: M.util.get_string('infonocomments','pdfannotator'),
                                        type: "info"
                                    });
                                } else if(data.status === 'error') {
                                    notification.addNotification({
                                        message: M.util.get_string('error:printcomments','pdfannotator'),
                                        type: "error"
                                    });
                                } 
                            });  
                    } // end of function openCommentsCallback
                }
  
        })();

        /**
         * First function to render the pdf document. Renders only the first page 
         * and triggers the function to sync the annotations.
         * @returns {undefined}
         */       
	function render() {

            return pdfjsLib.getDocument(RENDER_OPTIONS.documentPath).promise.then(function fulfilled(pdf) {
	    RENDER_OPTIONS.pdfDocument = pdf;
            pdf.getPage(1).then(function(result){
               let rotate = result._pageInfo.rotate;
               RENDER_OPTIONS.rotate = parseInt(localStorage.getItem(documentId + '/rotate'), 10) || rotate;
   
	    var viewer = document.getElementById('viewer');
	    viewer.innerHTML = '';
	    NUM_PAGES = pdf._pdfInfo.numPages;
	    for (var i = 0; i < NUM_PAGES; i++) {
	      var page = UI.createPage(i + 1);
	      viewer.appendChild(page);
	    }
	    return UI.renderPage(_page, RENDER_OPTIONS, true).then(function (_ref) {
	      var _ref2 = _slicedToArray(_ref, 2);

	      var pdfPage = _ref2[0];
	      var annotations = _ref2[1];
	      var viewport = pdfPage.getViewport({scale:RENDER_OPTIONS.scale, rotation:RENDER_OPTIONS.rotate});
	      PAGE_HEIGHT = viewport.height;

              //Set the right page height to every nonseen page to calculate the current seen page better during scrolling
              document.querySelectorAll('#viewer .page').forEach(function(elem){
                  elem.style.height = PAGE_HEIGHT+'px';
              });

              if (! $('.path-mod-pdfannotator').first().hasClass('fullscreenWrapper')) {
                  var pageheight100 = pdfPage.getViewport({scale:1, rotation:0}).height;
                  $('#body-wrapper').css('height',pageheight100+40);
              }              
              document.getElementById('currentPage').value = _page;
              document.getElementById('currentPage').max = NUM_PAGES;
              document.getElementById('sumPages').innerHTML = NUM_PAGES;

              //pick annotation, if the annotation id has been passed
              if(_annoid !== null){
                  UI.pickAnnotation(_page,_annoid,_commid);
              }else{
                  UI.renderAllQuestions(documentId, _page);
              }
              
              setTimeout(UI.loadNewAnnotations, 5000);
            });
            });
	  },function rejected(err){
              let child = document.createElement('div');
              child.innerHTML = M.util.get_string('error:openingPDF', 'pdfannotator');
              document.getElementById('viewer').appendChild(child);
          }).catch(function (err) {
              let child = document.createElement('div');
              child.innerHTML = M.util.get_string('error:openingPDF', 'pdfannotator');
              document.getElementById('viewer').appendChild(child);
          });
	}
        
	render();
        
        //initialize button allQuestions
        (function (){
            document.querySelector('#allQuestions').addEventListener('click', function(){
                UI.renderAllQuestions(documentId);
            });
        })();

        //initialize button questionsOnThisPage
        (function (){
            document.querySelector('#questionsOnThisPage').addEventListener('click', function(){
                var pageNumber = document.getElementById('currentPage').value;
                UI.renderQuestions(documentId, pageNumber, 1);
                
            });
        })();

        /**
         * Function for a fixed toolbar, when scrolliing down. But only as long as the document is visible.
         * @returns {undefined}
         */
        (function () {
            var top = $('#pdftoolbar').offset().top - parseFloat($('#pdftoolbar').css('marginTop').replace(/auto/, 0));
            var width = $('#pdftoolbar').width();
            var fixedTop = 0; // Height of moodle-navbar.
            if ($('.fixed-top').length > 0) {
                fixedTop = $('.fixed-top').outerHeight();
            } else if ($('.navbar-static-top').length > 0) {
                fixedTop = $('.navbar-static-top').outerHeight();
            }
            var toolbarHeight = $('#pdftoolbar').outerHeight();
            var contentTop = $('#content-wrapper').offset().top;

            var oldTop = $('#pdftoolbar').css('top');
            var contentHeight = $('#content-wrapper').height();
            var bottom = contentTop+contentHeight-fixedTop - toolbarHeight;

            $(window).scroll(function (event) {
                var y = $(this).scrollTop();
                
                // Calculate again in case contentHeight was 1 (because content wasn't loaded yet?)
                contentHeight = $('#content-wrapper').height();
                bottom = contentTop + contentHeight - fixedTop - toolbarHeight;
                var notifications = $('#user-notifications').children();
       
                if (y >= top + 1 - fixedTop && y < bottom - 50 && !notifications) {
                    $('#pdftoolbar').addClass('fixtool');
                    $('#pdftoolbar').width(width);
                    document.getElementById("pdftoolbar").style.top = fixedTop + "px";
                } else {
                    $('#pdftoolbar').removeClass('fixtool');
                    document.getElementById("pdftoolbar").style.top = oldTop;
                }
            });
            
            // adjust width of toolbar
            $(window).resize( function() {
                width = $('#pdftoolbar').parent().width();
                $('#pdftoolbar').width(width);
            })
        })();

        //initialize searchForm
        (function(){
            // hide form and show/hide it after click on the search icon
            $('#searchForm').hide();
            $('#searchButton').click( function (e) {
                $('#searchForm').toggle();
                $('#searchPattern').val('');
                $('#searchClear').hide();
                $('#searchPattern').focus();
            });
            // Search if the user typed
            $('#searchPattern').keyup( function(e) {
                if($('#searchPattern').val().length > 0){
                    $('#searchClear').show();
                } else {
                    $('#searchClear').hide();
                }
                let pageNumber = document.getElementById('currentPage').value;
                UI.renderQuestions(documentId, pageNumber, 1);            
            });
            
            //Clear-Button
            $('#searchForm').submit (function(e) {
                $('#searchPattern').val('');
                $('#searchClear').hide();
                let pageNumber = document.getElementById('currentPage').value;
                UI.renderQuestions(documentId, pageNumber, 1); 
                return false;
            });
            
        })();
        

        
        if(_toolbarSettings.use_studenttextbox === "1"|| _capabilities.usetextbox){
            // initialize the textbox
            (function () {
            var textSize = void 0;
            var textColor = void 0;

            function initText() {
              var size = document.querySelector('.toolbar .text-size');
              [8, 9, 10, 11, 12, 14, 18, 24, 30, 36, 48, 60, 72, 96].forEach(function (s) {
                size.appendChild(new Option(s, s));
              });

              setText(localStorage.getItem(RENDER_OPTIONS.documentId + '/text/size') || 10, localStorage.getItem(RENDER_OPTIONS.documentId + '/text/color') || '#000000');

              (0, _initColorPicker2.default)(document.querySelector('.text-color'), textColor, function (value) {
                setText(textSize, value);
              });
            }

            function setText(size, color) {
              var modified = false;

              if (textSize !== size) {
                modified = true;
                textSize = size;
                localStorage.setItem(RENDER_OPTIONS.documentId + '/text/size', textSize);
                document.querySelector('.toolbar .text-size').value = textSize;
              }

              if (textColor !== color) {
                modified = true;
                textColor = color;
                localStorage.setItem(RENDER_OPTIONS.documentId + '/text/color', textColor);

                var selected = document.querySelector('.toolbar .text-color.color-selected');
                if (selected) {
                  selected.classList.remove('color-selected');
                  selected.removeAttribute('aria-selected');
                }

                selected = document.querySelector('.toolbar .text-color[data-color="' + color + '"]');
                if (selected) {
                  selected.classList.add('color-selected');
                  selected.setAttribute('aria-selected', true);
                }
              }

              if (modified) {
                UI.setText(textSize, textColor);
              }
            }

            function handleTextSizeChange(e) {
              setText(e.target.value, textColor);
              document.querySelector('#pdftoolbar button.text').click(); // Select text.
            }
            
            document.querySelector('.toolbar .text-size').addEventListener('change', handleTextSizeChange);

            initText();
          })(); // Initialize textbox end.
        }
 
        if(_toolbarSettings.use_studentdrawing === "1"|| _capabilities.usedrawing){
            // Initialize pen.
            (function () {
              var penSize = void 0;
              var penColor = void 0;

              function initPen() {
                var size = document.querySelector('.toolbar .pen-size');
                for (var i = 0; i < 20; i++) {
                  size.appendChild(new Option(i + 1, i + 1));
                }

                setPen(localStorage.getItem(RENDER_OPTIONS.documentId + '/pen/size') || 1, localStorage.getItem(RENDER_OPTIONS.documentId + '/pen/color') || '#000000');

                (0, _initColorPicker2.default)(document.querySelector('.pen-color'), penColor, function (value) {
                  setPen(penSize, value);
                });
              }

              function setPen(size, color) {
                var modified = false;

                if (penSize !== size) {
                  modified = true;
                  penSize = size;
                  localStorage.setItem(RENDER_OPTIONS.documentId + '/pen/size', penSize);
                  document.querySelector('.toolbar .pen-size').value = penSize;
                }

                if (penColor !== color) {
                  modified = true;
                  penColor = color;
                  localStorage.setItem(RENDER_OPTIONS.documentId + '/pen/color', penColor);

                  var selected = document.querySelector('.toolbar .pen-color.color-selected');
                  if (selected) {
                    selected.classList.remove('color-selected');
                    selected.removeAttribute('aria-selected');
                  }

                  selected = document.querySelector('.toolbar .pen-color[data-color="' + color + '"]');
                  if (selected) {
                    selected.classList.add('color-selected');
                    selected.setAttribute('aria-selected', true);
                  }
                }

                if (modified) {
                  UI.setPen(penSize, penColor);
                }
              }

              function handlePenSizeChange(e) {
                setPen(e.target.value, penColor);
                document.querySelector('#pdftoolbar button.pen').click(); // Select pen.
              }

              document.querySelector('.toolbar .pen-size').addEventListener('change', handlePenSizeChange);

              initPen();
            })(); // End initialize pen.
        }
	// Toolbar buttons (defined in index.mustache) are given event listeners:
	(function () {
	  //Cursor should always be default selected
	  var tooltype = 'cursor';
	  if (tooltype) {
	    setActiveToolbarItem(tooltype, document.querySelector('.toolbar button[data-tooltype=' + tooltype + ']'));
	  }
          
	  function setActiveToolbarItem(type, button) {
	    var active = document.querySelector('.toolbar button.active');
	    if (active) {
	      active.classList.remove('active');

	      switch (tooltype) {
	        case 'cursor':
	          UI.disableEdit();
	          break;
	        case 'draw':
	          UI.disablePen();
	          break;
	        case 'text':
	          UI.disableText();
	          break;
	        case 'point':
	          UI.disablePoint();
	          break;
	        case 'area':
	        case 'highlight':
	        case 'strikeout':
	          UI.disableRect();
	          break;
	      }
	    }

	    if (button) {
	      button.classList.add('active');
	    }
	    if (tooltype !== type) {
	      localStorage.setItem(RENDER_OPTIONS.documentId + '/tooltype', type);
	    }
	    tooltype = type;

	    switch (type) {
	      case 'cursor':
	        UI.enableEdit();
	        break;
	      case 'draw':
	        UI.enablePen();
	        break;
	      case 'text':
	        UI.enableText();
	        break;
	      case 'point':
	        UI.enablePoint();
	        break;
	      case 'area':
	      case 'highlight':
	      case 'strikeout':
	        UI.enableRect(type);
	        break;
	    }
	  }

	  function handleToolbarClick(e) {
        var target = e.target;
        //The content of some buttons are img-tags. 
        //Then the nodeName of the clicked target will be IMG, but we need the outer button element 
        if((target.nodeName === 'IMG' || target.nodeName === 'I')   && target.parentElement.nodeName === 'BUTTON'){
            target = e.target.parentElement;
        }
	    if (target.nodeName === 'BUTTON') {
                //Only setActiveToolbarItem if the button is not disabled! (It is disables, if the annotations are hidden)
                if(!target.disabled){
                    setActiveToolbarItem(target.getAttribute('data-tooltype'), target);
                }
	    }
            //clear right side (comment-wrapper), if there are comments of an annotation
            var form = document.querySelector('.comment-list-form');
            if(form.style.display !== 'none'){
                form.style.display = 'none';
                document.querySelector('.comment-list-container').innerHTML = '';
            }
	    }
	    document.querySelector('.toolbar').addEventListener('click', handleToolbarClick);
	})(); //end Toolbar buttons

	// Scale
        
	(function () {
	  function setScaleRotate(scale, rotate) {
	    scale = parseFloat(scale, 10);
	    rotate = parseInt(rotate, 10);

	    if (RENDER_OPTIONS.scale !== scale || RENDER_OPTIONS.rotate !== rotate) {
	      RENDER_OPTIONS.scale = scale;
	      RENDER_OPTIONS.rotate = rotate;

	      localStorage.setItem(RENDER_OPTIONS.documentId + '/scale', RENDER_OPTIONS.scale);
	      localStorage.setItem(RENDER_OPTIONS.documentId + '/rotate', RENDER_OPTIONS.rotate % 360);
              _page = parseInt(document.getElementById('currentPage').value);
              
              let pagecontainer = document.getElementById('pageContainer'+_page);
              let loader = document.createElement('div');
              loader.id = "loader";
              
              document.body.appendChild(loader);
	      render().then(function(){
                  $('#content-wrapper').scrollTop(document.getElementById('pageContainer'+_page).offsetTop);
                  document.body.removeChild(loader);
              });
	    }
	  }

	  function handleScaleChange(e) {
	    setScaleRotate(e.target.value, RENDER_OPTIONS.rotate);
	  }

	  function handleRotateCWClick() {
	    setScaleRotate(RENDER_OPTIONS.scale, RENDER_OPTIONS.rotate + 90);
	  }

	  function handleRotateCCWClick() {
	    setScaleRotate(RENDER_OPTIONS.scale, RENDER_OPTIONS.rotate - 90);
	  }

	  document.querySelector('.toolbar select.scale').value = RENDER_OPTIONS.scale;

          // Add eventHandlers to select and +/- Button for scaling
          $('.toolbar select.scale').change(handleScaleChange);

          let options = document.querySelector('.toolbar select.scale').options.length;
           document.querySelector('.toolbar #scalePlus').addEventListener('click', function() {
               handleScaleButton(1);
           });
           document.querySelector('.toolbar #scaleMinus').addEventListener('click', function() {
               handleScaleButton(-1);
           });

           function handleScaleButton(value){
              let index = document.querySelector('.toolbar select.scale').selectedIndex + value;
              if (index >= 0 && index < options) {
                  document.querySelector('.toolbar select.scale').selectedIndex = index;
              }
              $('.toolbar select.scale').change();
              setTimeout(function(){
                  document.getElementById('pdfannotator_cursor').click(); 
              }, 100);
          }

	})(); //end scale rotate
        
        // Hide/Show annotations Button
        (function(){
            //hide is 'block' for display annotations and 'none' for hide annotations
            var hide = localStorage.getItem(RENDER_OPTIONS.documentId + '/hide') || 'block';
                        
            function handleHideClick(e) {
                toggleHide(e);
                
                for (var i = 0; i < NUM_PAGES; i++) {                    
                  document.querySelector('div#pageContainer' + (i + 1) + ' svg.annotationLayer').style.display = hide;
                }
            }
            
            function toggleHide(e){
                let img;
                let a;
                if(e.target.tagName === 'A'){
                    a = e.target;
                    img = e.target.childNodes[0];
                }else{
                    img = e.target;
                    a = img.parentNode;
                }
                hide = hide === 'block'? 'none' :'block';
                img.src = img.src.indexOf('accessibility_checker') !== -1 ? M.util.image_url('/i/hide') /*'/moodle/theme/image.php/clean/core/1504678549/i/hide' */ : M.util.image_url('/e/accessibility_checker'); // '/moodle/theme/image.php/clean/core/1504678549/e/accessibility_checker';
                if(hide === 'block'){
                    document.querySelectorAll('.toolbar button[data-tooltype]').forEach(function(elem){
                        elem.disabled = false;
                    });
                    img.alt = M.util.get_string('hideAnnotations','pdfannotator');
                    img.title = M.util.get_string('hideAnnotations','pdfannotator');
                    a.title = M.util.get_string('hideAnnotations','pdfannotator');
                }else{
                    document.querySelectorAll('.toolbar button[data-tooltype]').forEach(function(elem){
                        elem.disabled = true;
                    });
                    img.alt = M.util.get_string('showAnnotations','pdfannotator');
                    img.title = M.util.get_string('showAnnotations','pdfannotator');
                    a.title = M.util.get_string('showAnnotations','pdfannotator');
                }
            }
            document.querySelector('a.hideComments').addEventListener('click', handleHideClick);
        })(); //end hide/show annotations button

        // Jump to Page
        (function(){
            var currentPageInput = $('#currentPage');
            var oldPage = currentPageInput.val();

            function jumpToPage(){
                var numPages = parseInt($('#sumPages').html(), 10); 
                var newPage = parseInt(currentPageInput.val(), 10);

                var inputValid = false;
                if (Number.isInteger(newPage)){
                    if (newPage >= 1 && newPage <= numPages ){
                        inputValid = true;
                    }
                }

                if(!inputValid){
                   currentPageInput.val(oldPage);
                   return;
                }
                
                oldPage = newPage;
                $('#content-wrapper').scrollTop(document.getElementById('pageContainer'+newPage).offsetTop);
            }
            
            // Add eventListener for inputfield and buttons
            currentPageInput.change(jumpToPage);
           
            $('#nextPage').click(function(){
                currentPageInput.val(parseInt(currentPageInput.val(), 10) + 1);
                currentPageInput.change();
                setTimeout(function(){
                    document.getElementById('pdfannotator_cursor').click();
                }, 100);
            });
            $('#prevPage').click(function(){
                currentPageInput.val(parseInt(currentPageInput.val(), 10) - 1);
                currentPageInput.change();
                setTimeout(function(){
                    document.getElementById('pdfannotator_cursor').click();
                }, 100);
            });
        })();
        
        // Make toolbar responsive: Elements that can't be displayed in one row, are in a dropdown.
        (function () {
            // Set width as attribute because width of elements in dropdown is 0 if dropdown is collapsed.
            $('#toolbarContent').children().each(function(){
                $(this).attr('visibleWidth', $(this).outerWidth());
            });
            
            function responsiveToolbar() {
                let changed = false;
                do {
                    changed = false;
                    let lastElement = $('#toolbarContent').children(':not(.pdf-annotator-hidden)').last(); // Last visible element in toolbar.
                    let firstDropdownElement = $('#toolbar-dropdown-content').children().first(); // First element in dropdown.
                    let firstWidth = parseInt(firstDropdownElement.attr('visibleWidth')); // Width of first element in dropdown.
                    // If lastElem is displayed in a second row because screen isn't wide enough.
                    if (lastElement.offset().top > $('#toolbarContent').offset().top + 10) {
                        // Move last element (not dropdown-button) into dropdown and display button.
                        let toolbarElements = $('#toolbarContent').children(':not(#toolbar-dropdown-button)');
                        if(toolbarElements.length > 0) {
                            lastElement = toolbarElements.last();
                            $('#toolbar-dropdown-content').prepend(lastElement);
                            $('#toolbar-dropdown-button').removeClass('pdf-annotator-hidden');
                            changed = true;
                        }
                    // If there is enough space to display the next hidden element.
                    } else if ((firstDropdownElement.length !== 0) && 
                            (lastElement.offset().left + lastElement.outerWidth() + firstWidth + 20 < $('#toolbarContent').offset().left + $('#toolbarContent').width())){
                        firstDropdownElement.insertBefore('#toolbar-dropdown-button'); // Move element from dropdown to toolbar.
                        // Hide button if all elements are shown.
                        if ($('#toolbar-dropdown-content').children().length === 0){
                            $('#toolbar-dropdown-button').addClass('pdf-annotator-hidden');
                        }
                        changed = true;
                    }
                } while (changed);
            }
            responsiveToolbar();
            $(window).resize(responsiveToolbar);
            $('#toolbar-dropdown-button').click(function(){
                setTimeout(function(){
                    document.getElementById('pdfannotator_cursor').click();
                }, 100);
            });
            
        })();
        
        // Comment annotations.
        (function (window, document) {
            var commentList = document.querySelector('#comment-wrapper .comment-list-container'); // to be found in index.php
	        var commentForm = document.querySelector('#comment-wrapper .comment-list-form'); // to be found in index.php

            // Function checks whether the target annotation type allows comments.
            function supportsComments(target) {
                var type = target.getAttribute('data-pdf-annotate-type');
                return ['point', 'highlight', 'area', 'strikeout'].indexOf(type) > -1;
            }
            
                    
            /*
            * Function inserts a comment into the HTML DOM for display.
            * A comment consists of its content as well as a delete button, wrapped up in a shared div.
            * 
            * @return {Element}
            */
            function insertComments(comments, markCommentid = undefined) {
                if(!comments) {
                    return false;
                }
                if(!comments.comments){
                   comments = {comments: [comments]};
                }
    
                (function(templates, data) {
                    if(data.comments[0] !== false) {
                        templates.render('mod_pdfannotator/comment', data)
                        .then(function(html,js){
                            if(data.comments.length === 1 && !data.comments[0].isquestion) {
                                $('.comment-list-container').append(html);
                            } else {
                                templates.replaceNodeContents('.comment-list-container', html, js);
                            }                                
                        }).then(function() {
                            data.comments.forEach(function(comment) {
                                createVoteHandler(comment);
                                createEditFormHandler(comment);
                                createSubscriptionHandler(comment);
                                createHideHandler(comment);
                                createDeleteHandler(comment);
                                createSolvedHandler(comment);                           
                                let pattern = $('#searchPattern').val();
                                if(pattern !== '' && comment.content.search(new RegExp(pattern, "i")) !== -1){
                                    $('#comment_'+comment.uuid).addClass('mark');
                                }
                                
                                let selector = '#comment_' + comment.uuid + ' .chat-message-text p';
                                let element = document.querySelector(selector);
                                renderMathJax(element);
                            });

                            //if the target has the attribute markCommentid a specific comment should be marked with an red border.
                            //after 3 sec the border should disappear.
                            if(markCommentid !== undefined && markCommentid !== null){
                                $('#comment_'+markCommentid).addClass('mark');
                                markCommentid = undefined;
                                setTimeout(function(){
                                    if(document.querySelector('#comment_'+markCommentid)){
                                        document.querySelector('#comment_'+markCommentid).style.border = "none";
                                    }
                                },3000);
                            }
                        }).catch(notification.exception);
                    }
                })(templates, comments);
                return true;
            }
            
            function createSolvedHandler(comment){
            var button = $('#comment_'+comment.uuid+' .comment-solve-a');
            var i = $('#comment_'+comment.uuid+' .comment-solve-a i');
            var span = $('#comment_'+comment.uuid+' .comment-solve-a span.menu-action-text');
            var img = $('#comment_'+comment.uuid+' .comment-solve-a img');
            button.click(function(e) {
                _2.default.getStoreAdapter().markSolved(RENDER_OPTIONS.documentId, comment)
            });
            }
            
            function createSubscriptionHandler(comment){

            var button = $('#comment_'+comment.uuid+' .comment-subscribe-a');
            var i = $('#comment_'+comment.uuid+' .comment-subscribe-a i');
            var span = $('#comment_'+comment.uuid+' .comment-subscribe-a span.menu-action-text')
            button.click(function(e) {
                if(comment.issubscribed){
                    _2.default.getStoreAdapter().unsubscribeQuestion(RENDER_OPTIONS.documentId, comment.annotation)
                        .then(function(data){
                            if(data.status === "success") {
                                notification.addNotification({
                                        message: M.util.get_string('successfullyUnsubscribed', 'pdfannotator'),
                                        type: "success"
                                });
                                setTimeoutNotification()
                            } else if(data.status == 'error') {
                                notification.addNotification({
                                    message: M.util.get_string('error:unsubscribe','pdfannotator'),
                                    type: "error"
                                });
                                console.error(M.util.get_string('error:unsubscribe', 'pdfannotator'));
                            } 
                            span.text(M.util.get_string('subscribeQuestion', 'pdfannotator'));
                        });
                } else {
                    _2.default.getStoreAdapter().subscribeQuestion(RENDER_OPTIONS.documentId, comment.annotation)
                        .then(function(data){
                            if(data.status === "success") {
                                notification.addNotification({
                                        message: M.util.get_string('successfullySubscribed', 'pdfannotator'),
                                        type: "success"
                                });
                                setTimeoutNotification();
                            } else if(data.status == 'error') {
                                notification.addNotification({
                                    message: M.util.get_string('error:subscribe','pdfannotator'),
                                    type: "error"
                                });
                                console.error(M.util.get_string('error:subscribe', 'pdfannotator'));
                            } 
                            span.text(M.util.get_string('unsubscribeQuestion', 'pdfannotator'));                            
                        });                
                }
                comment.issubscribed = !comment.issubscribed;
                i.toggleClass("fa-bell");
                i.toggleClass("fa-bell-slash");
            });            
            }
            
            function createVoteHandler(comment){
                // Create an element for click.
                var likeButton = $('#comment_'+comment.uuid+' .comment-like-a');
                if (comment.isdeleted == 1 || !comment.usevotes) {
                    likeButton.attr("disabled","disabled");
                    likeButton.css("visibility", "hidden");
                } else if ((comment.userid == _userid) || (comment.isvoted)) {
                    likeButton.attr("disabled","disabled");                    
                }              
                                
                likeButton.click(function(e) { 
                    _2.default.getStoreAdapter().voteComment(RENDER_OPTIONS.documentId, comment.uuid)
                        .then(function(data){
                            if(data.status == 'error') {
                                notification.addNotification({
                                    message: M.util.get_string('error:voteComment','pdfannotator'),
                                    type: "error"
                                });
                                console.error(M.util.get_string('error:voteComment', 'pdfannotator'));                                
                            } else {
                                // Update number of votes and disable button.
                                var voteDiv = document.querySelector("div#comment_"+comment.uuid+" div.wrappervotessolved");
                                var button = voteDiv.querySelector("button");
                                var img = button.querySelector("i");
                                var div = voteDiv.querySelector(".countVotes");
                                
                                button.disabled = true;                 
                                div.innerHTML = data.numberVotes;
                                if (comment.isquestion==1) {
                                    button.title = M.util.get_string('likeQuestionForbidden', 'pdfannotator');  //button
                                    img.title = M.util.get_string('likeQuestionForbidden', 'pdfannotator');  //img
                                    img.alt = M.util.get_string('likeQuestionForbidden', 'pdfannotator');  //img
                                    div.title = data.numberVotes + " " + M.util.get_string('likeCountQuestion', 'pdfannotator');
                                } else {
                                    button.title = M.util.get_string('likeAnswerForbidden', 'pdfannotator');
                                    img.title = M.util.get_string('likeAnswerForbidden', 'pdfannotator');  //img
                                    img.alt = M.util.get_string('likeAnswerForbidden', 'pdfannotator');  //img
                                    div.title = data.numberVotes + " " + M.util.get_string('likeCountAnswer', 'pdfannotator');                                    
                                }
                            }
                        });
                });
            }

            /**
             * Function enables managers to hide a comment from participants
             * or to display it to participants once more.
             * 
             * @param {type} comment
             * @returns {undefined}
             */
            function createHideHandler(comment){

                var button = $('#hideButton'+comment.uuid);

                button.click(function(e) {
                    var icon = button.children().first();
                    var menutext = button.children().last();
                    if(comment.ishidden){
                        _2.default.getStoreAdapter().redisplayComment(RENDER_OPTIONS.documentId, comment.uuid);
                        menutext.html(M.util.get_string('markhidden', 'pdfannotator'));
                    } else {
                        _2.default.getStoreAdapter().hideComment(RENDER_OPTIONS.documentId, comment.uuid);
                        menutext.html(M.util.get_string('removehidden', 'pdfannotator'));
                    }
                    comment.ishidden = !comment.ishidden;
                    icon.toggleClass("fa-eye");
                    icon.toggleClass("fa-eye-slash");
                });            
            }

            /**
             * Function handles opening/closing and submitting the edit comment form.
             * @param {type} comment
             * @returns {undefined}
             */
            function createEditFormHandler(comment) {
                // Create an element for click.
                var editButton = $('#editButton'+comment.uuid);
                // Add an event handler to the click element that opens a textarea and fills it with the current comment.
                editButton.click(function(e) {
                    UI.loadEditor('edit', comment.uuid, handleClickIfEditorExists);
                    function handleClickIfEditorExists() { 
                        // Add an event handler to the form for submitting any changes to the database.
                        let editForm = document.getElementById(`edit${comment.uuid}`);
                        editForm.onsubmit = function (e) {
                            let editTextarea =  document.getElementById(`editarea${comment.uuid}`);
                            let editAreaEditable = document.getElementById(`editarea${comment.uuid}editable`);
                            let chatMessage = document.getElementById(`chatmessage${comment.uuid}`);
                            
                            let newContent = editTextarea.value.trim();
                            let imgContents = editAreaEditable.querySelectorAll('img');
                            let isEmptyContent = editAreaEditable.innerText.replace('/\n/g', '').trim() === '';
                            let defaultPTag = editAreaEditable.querySelector('p');
                            isEmptyContent = (defaultPTag && defaultPTag.innerText.replace('/\n/g', '').trim() === '' && imgContents.length === 0) && editAreaEditable.childNodes.length === 0;
                            if(isEmptyContent && imgContents.length === 0){
                                // Should be more than one character, otherwise it should not be saved.
                                notification.addNotification({
                                    message: M.util.get_string('min0Chars','pdfannotator'),
                                    type: "error"
                                });
                            } else if(newContent === comment.displaycontent) { // No changes.
                                editForm.style.display = "none";
                                chatMessage.innerHTML = comment.displaycontent;
                                renderMathJax(chatMessage);
                            } else { // Save changes.
                                _2.default.getStoreAdapter().editComment(documentId, comment.uuid, newContent, editForm)
                                    .then(function(data){
                                        if (data.status === "success") {
                                            editForm.style.display = "none";
                                            $(`edit_comment_editor_wrapper_${comment.uuid}`).remove();
                                            if (data.modifiedby) {
                                                $('#comment_' + comment.uuid + ' .edited').html(M.util.get_string('editedComment', 'pdfannotator') + " " + data.timemodified + " " + M.util.get_string('modifiedby', 'pdfannotator') + " " + data.modifiedby);
                                            } else {
                                                $('#comment_' + comment.uuid + ' .edited').html( M.util.get_string('editedComment', 'pdfannotator') + " " + data.timemodified);
                                            }
                                            newContent = data.newContent;
                                            chatMessage.innerHTML = newContent;
                                            comment.content = newContent;
                                            comment.displaycontent = newContent;
                                            editTextarea = newContent;
                                            renderMathJax(chatMessage);
                                            notification.addNotification({
                                                message: M.util.get_string('successfullyEdited', 'pdfannotator'),
                                                type: "success"
                                            });
                                            setTimeoutNotification();
                                        } else {
                                            notification.addNotification({
                                                message: M.util.get_string('error:editComment','pdfannotator'),
                                                type: "error"
                                            });                                        
                                        }
                                    });
                            }
                            setTimeout(function(){
                                let notificationpanel = document.getElementById("user-notifications");
                                while (notificationpanel.hasChildNodes()) {
                                    notificationpanel.removeChild(notificationpanel.firstChild);
                                }
                            }, 4000);
    
                            return false; // Prevents normal POST and page reload in favour of an asynchronous load.
                        };
    
                        let cancelBtn = $('#comment_' + comment.uuid + ' #commentCancel');
                        cancelBtn.click(function(e){
                            let editTextarea =  document.getElementById(`editarea${comment.uuid}`);
                            let editAreaEditable = document.getElementById(`editarea${comment.uuid}editable`);
                            let chatMessage = document.getElementById(`chatmessage${comment.uuid}`);
                            editForm.style.display = "none";
                            editTextarea.innerHTML = '';
                            editTextarea.innerHTML = comment.displaycontent;
                            editAreaEditable.innerHTML = '';
                            editAreaEditable.innerHTML = comment.displaycontent;
                            chatMessage.innerHTML = comment.displaycontent;
                            renderMathJax(chatMessage);
                        });
                    }
                });               
            }
            
            /**
             * This function creates an Node-Element for deleting the comment.
             * @param {type} comment The comment-object for which the deletebutton is.
             * @returns {Element|startIndex.indexstartIndex#L#26.indexstartIndex#L#26#L#72.indexstartIndex#L#26#L#72#L#743.createDeleteButton.deleteSpan}
             */
            function createDeleteHandler(comment) {
                var button = $('#comment_'+comment.uuid+' .comment-delete-a');
                button.click(function(e) {
                    var confirmDelete = '';
                    if(comment.isquestion==1){                            
                        if (_capabilities.deleteany) {
                            confirmDelete = M.util.get_string('deletingQuestion_manager', 'pdfannotator');
                        } else {
                            confirmDelete = M.util.get_string('deletingQuestion_student', 'pdfannotator');
                        }
                    } else {
                        confirmDelete = M.util.get_string('deletingComment', 'pdfannotator');
                    }
                    var deleteCallback = function() {
                        dialogCallbackForDelete.call(this, comment);
                    };
                    notification.confirm(M.util.get_string('deletingCommentTitle', 'pdfannotator'), confirmDelete, M.util.get_string('yesButton', 'pdfannotator'), M.util.get_string('cancelButton', 'pdfannotator'), deleteCallback, null);                     
                });

                function dialogCallbackForDelete(args = comment){
                    if(args.type === "textbox" || args.type === "drawing"){
                        _2.default.getStoreAdapter().deleteAnnotation(documentId, args.annotation).then(function(data){
                            if(data.status === "success"){
                                var node = document.querySelector('[data-pdf-annotate-id="'+args.annotation+'"]');
                                var visiblePageNum = node.parentNode.getAttribute('data-pdf-annotate-page');
                                node.parentNode.removeChild(node);
                                // Not possible to enter new comments.
                                document.querySelector('.comment-list-container').innerHTML = '';
                                document.querySelector('.comment-list-form').setAttribute('style','display:none');
                                UI.renderQuestions(documentId,visiblePageNum);
                            }
                        },function(err){
                            notification.addNotification({
                                message: M.util.get_string('error:deleteAnnotation', 'pdfannotator'),
                                type: "error"
                            });
                            console.error(M.util.get_string('error:deleteAnnotation', 'pdfannotator'));
                        });
                    } else {  
                        _2.default.getStoreAdapter().deleteComment(RENDER_OPTIONS.documentId, args.uuid).then(function(data) {
                            // If comment was answered so that it is not completly deleted but displayed as deleted.
                            // If question: Close   If answer: Remove marking as correct
                            if(data.wasanswered && ((comment.isquestion && !comment.solved) || (!comment.isquestion && comment.solved))){
                                _2.default.getStoreAdapter().markSolved(RENDER_OPTIONS.documentId, args);
                            }
                        });
                    } 
                
                }
            }
                
            /**
             * This function is called, when an annotation is clicked. The corresponding comments are rendered and a form to submit a comment.
             * @param {type} target
             * @returns {undefined}
             */    
            function handleAnnotationClick(target) {
                if (supportsComments(target)) {
                    (function () {
                        var documentId = target.parentNode.getAttribute('data-pdf-annotate-document');
                        var annotationId = target.getAttribute('data-pdf-annotate-id');

                        _2.default.getStoreAdapter().getComments(documentId, annotationId)
                        .then(function (comments) {
                            var title;
                            if(comments.comments[0].visibility == "protected") {
                                title = M.util.get_string('protected_comments','pdfannotator');
                                $("#protectedDiv").hide();                    
                                $("#anonymousDiv").hide();
                                $("#privateDiv").hide();
                                $("#id_pdfannotator_contenteditable").attr("placeholder", M.util.get_string('add_protected_comment', 'pdfannotator'));
                            } else if (comments.comments[0].visibility == "private") {
                                title = M.util.get_string('private_comments','pdfannotator');
                                $("#privateDiv").hide();
                                $("#protectedDiv").hide();                    
                                $("#anonymousDiv").hide();
                                $("#id_pdfannotator_contenteditable").attr("placeholder", M.util.get_string('add_private_comment', 'pdfannotator'));
                            } else {
                                title = M.util.get_string('public_comments','pdfannotator');
                                $("#privateDiv").hide();
                                $("#protectedDiv").hide();
                                $("#anonymousDiv").show();
                                $("#id_pdfannotator_contenteditable").attr("placeholder", M.util.get_string('addAComment', 'pdfannotator'));
                            }
                            
                            $('#comment-wrapper h4')[0].innerHTML = title;
                            commentList.innerHTML = '';
                            commentForm.style.display = 'inherit';
                                
                            var button1 = document.getElementById('allQuestions'); // to be found in index template
                            button1.style.display = 'inline';
                            var button2 = document.getElementById('questionsOnThisPage'); // to be found in index template
                            button2.style.display = 'inline';
                            
                            commentForm.onsubmit = function (e) {
                                document.querySelector('#commentSubmit').disabled = true;
                                var commentVisibility= read_visibility_of_checkbox();
                                var isquestion = 0; // this is a normal comment, so it is not a question
                                var commentContentElements = document.querySelectorAll('#id_pdfannotator_contenteditable')[0];
                                var imgContents = commentContentElements.querySelectorAll('img');

                                var innerContent = commentContentElements.innerText.replace('/\n/g', '').trim();
                                var temp = commentContentElements.querySelectorAll('p')[0];
                                let isEmptyContent = (temp && temp.innerText.replace('/\n/g', '').trim() === '' && imgContents.length === 0) && innerContent === '';
                                if(isEmptyContent && imgContents.length === 0){
                                    //should be more than one character, otherwise it should not be saved.
                                    notification.addNotification({
                                        message: M.util.get_string('min0Chars','pdfannotator'),
                                        type: "error"
                                    });
                                    document.querySelector('#commentSubmit').disabled = false;
                                    return false;
                                }

                                _2.default.getStoreAdapter().addComment(documentId, annotationId, commentContentElements.innerHTML, commentVisibility, isquestion)
                                .then(function (response) {
                                    var fn = (response) => insertComments(response);
                                    UI.loadEditor('add', 0, fn, response);
                                })
                                .then(function (success) {
                                    if (!success) {
                                        return false;
                                    }
                                    document.querySelector('#commentSubmit').disabled = false;
                                })
                                .catch(function(err){
                                    notification.addNotification({
                                        message: M.util.get_string('error:addComment','pdfannotator'),
                                        type: "error"
                                    });
                                    console.error(M.util.get_string('error:addComment', 'pdfannotator'));
                                });

                                return false; // Prevents page reload via POST to enable asynchronous loading
                            };
                            
                            var params = {'comments':comments, 'markCommentid':target.markCommentid};
                            var fn = (params) => {
                                var comments = params.comments;
                                var markCommentid = params.markCommentid;
                                //render comments   
                                insertComments(comments, markCommentid);
                            }
                            UI.loadEditor('add', 0, fn, params);
                                
                        })
                        .catch(function (err){
                            commentList.innerHTML = '';
                            commentForm.style.display = 'none';
                            commentForm.onsubmit = null;

                            insertComments({ content: M.util.get_string('error:getComments', 'pdfannotator')});
                            
                            notification.addNotification({
                                message: M.util.get_string('error:getComments','pdfannotator'),
                                type: "error"
                            });
                        });
                    })();
                }else{      
                    // Drawing or textbox                        
                    (function () {
                        var documentId = target.parentNode.getAttribute('data-pdf-annotate-document');
                        var annotationId = target.getAttribute('data-pdf-annotate-id');
            
                        _2.default.getStoreAdapter().getInformation(documentId, annotationId)
                        .then(function (annotation) {
                            UI.hideLoader();
                            commentList.innerHTML = '';
                            commentForm.style.display = 'none';
                            commentForm.onsubmit = null;
                            
                            var button1 = document.getElementById('allQuestions'); // to be found in index template
                            button1.style.display = 'inline';
                            var button2 = document.getElementById('questionsOnThisPage'); // to be found in index template
                            button2.style.display = 'inline';
                            
                            //render comments  
                            insertComments(annotation);

                        }).catch(function (err){
                            commentList.innerHTML = '';
                            commentForm.style.display = 'none';
                            commentForm.onsubmit = null;

                            insertComments({ content: M.util.get_string('error:getComments', 'pdfannotator')});

                            notification.addNotification({
                                message: M.util.get_string('error:getComments','pdfannotator'),
                                type: "error"
                            });
                        });
                    })();
                }
            }

            function handleAnnotationBlur(target) {
                if (supportsComments(target)) {
                    commentList.innerHTML = '';
                    commentForm.style.display = 'none';
                    commentForm.onsubmit = null;
                }
                var visiblePageNum = document.getElementById('currentPage').value;
                UI.renderQuestions(documentId,visiblePageNum);
            }

            UI.addEventListener('annotation:click', handleAnnotationClick);
            UI.addEventListener('annotation:blur', handleAnnotationBlur);
        })(window, document); //end comment annotation.

/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;
        (function() {
	  if (typeof twttr === "undefined" || twttr === null) {
	    var twttr = {};
	  }

	  twttr.txt = {};
	  twttr.txt.regexen = {};

	  var HTML_ENTITIES = {
	    '&': '&amp;',
	    '>': '&gt;',
	    '<': '&lt;',
	    '"': '&quot;',
	    "'": '&#39;'
	  };

	  // HTML escaping
	  twttr.txt.htmlEscape = function(text) {
	    return text && text.replace(/[&"'><]/g, function(character) {
	      return HTML_ENTITIES[character];
	    });
	  };

	  // Builds a RegExp
	  function regexSupplant(regex, flags) {
	    flags = flags || "";
	    if (typeof regex !== "string") {
	      if (regex.global && flags.indexOf("g") < 0) {
	        flags += "g";
	      }
	      if (regex.ignoreCase && flags.indexOf("i") < 0) {
	        flags += "i";
	      }
	      if (regex.multiline && flags.indexOf("m") < 0) {
	        flags += "m";
	      }

	      regex = regex.source;
	    }

	    return new RegExp(regex.replace(/#\{(\w+)\}/g, function(match, name) {
	      var newRegex = twttr.txt.regexen[name] || "";
	      if (typeof newRegex !== "string") {
	        newRegex = newRegex.source;
	      }
	      return newRegex;
	    }), flags);
	  }

	  twttr.txt.regexSupplant = regexSupplant;

	  // simple string interpolation
	  function stringSupplant(str, values) {
	    return str.replace(/#\{(\w+)\}/g, function(match, name) {
	      return values[name] || "";
	    });
	  }

	  twttr.txt.stringSupplant = stringSupplant;

	  twttr.txt.regexen.spaces_group = /\x09-\x0D\x20\x85\xA0\u1680\u180E\u2000-\u200A\u2028\u2029\u202F\u205F\u3000/;
	  twttr.txt.regexen.spaces = regexSupplant(/[#{spaces_group}]/);
	  twttr.txt.regexen.invalid_chars_group = /\uFFFE\uFEFF\uFFFF\u202A-\u202E/;
	  twttr.txt.regexen.invalid_chars = regexSupplant(/[#{invalid_chars_group}]/);
	  twttr.txt.regexen.punct = /\!'#%&'\(\)*\+,\\\-\.\/:;<=>\?@\[\]\^_{|}~\$/;
	  twttr.txt.regexen.rtl_chars = /[\u0600-\u06FF]|[\u0750-\u077F]|[\u0590-\u05FF]|[\uFE70-\uFEFF]/mg;
	  twttr.txt.regexen.non_bmp_code_pairs = /[\uD800-\uDBFF][\uDC00-\uDFFF]/mg;

	  twttr.txt.regexen.latinAccentChars = /\xC0-\xD6\xD8-\xF6\xF8-\xFF\u0100-\u024F\u0253\u0254\u0256\u0257\u0259\u025B\u0263\u0268\u026F\u0272\u0289\u028B\u02BB\u0300-\u036F\u1E00-\u1EFF/;

	  // Generated from unicode_regex/unicode_regex_groups.scala, same as objective c's \p{L}\p{M}
	  twttr.txt.regexen.bmpLetterAndMarks = /A-Za-z\xaa\xb5\xba\xc0-\xd6\xd8-\xf6\xf8-\u02c1\u02c6-\u02d1\u02e0-\u02e4\u02ec\u02ee\u0300-\u0374\u0376\u0377\u037a-\u037d\u037f\u0386\u0388-\u038a\u038c\u038e-\u03a1\u03a3-\u03f5\u03f7-\u0481\u0483-\u052f\u0531-\u0556\u0559\u0561-\u0587\u0591-\u05bd\u05bf\u05c1\u05c2\u05c4\u05c5\u05c7\u05d0-\u05ea\u05f0-\u05f2\u0610-\u061a\u0620-\u065f\u066e-\u06d3\u06d5-\u06dc\u06df-\u06e8\u06ea-\u06ef\u06fa-\u06fc\u06ff\u0710-\u074a\u074d-\u07b1\u07ca-\u07f5\u07fa\u0800-\u082d\u0840-\u085b\u08a0-\u08b2\u08e4-\u0963\u0971-\u0983\u0985-\u098c\u098f\u0990\u0993-\u09a8\u09aa-\u09b0\u09b2\u09b6-\u09b9\u09bc-\u09c4\u09c7\u09c8\u09cb-\u09ce\u09d7\u09dc\u09dd\u09df-\u09e3\u09f0\u09f1\u0a01-\u0a03\u0a05-\u0a0a\u0a0f\u0a10\u0a13-\u0a28\u0a2a-\u0a30\u0a32\u0a33\u0a35\u0a36\u0a38\u0a39\u0a3c\u0a3e-\u0a42\u0a47\u0a48\u0a4b-\u0a4d\u0a51\u0a59-\u0a5c\u0a5e\u0a70-\u0a75\u0a81-\u0a83\u0a85-\u0a8d\u0a8f-\u0a91\u0a93-\u0aa8\u0aaa-\u0ab0\u0ab2\u0ab3\u0ab5-\u0ab9\u0abc-\u0ac5\u0ac7-\u0ac9\u0acb-\u0acd\u0ad0\u0ae0-\u0ae3\u0b01-\u0b03\u0b05-\u0b0c\u0b0f\u0b10\u0b13-\u0b28\u0b2a-\u0b30\u0b32\u0b33\u0b35-\u0b39\u0b3c-\u0b44\u0b47\u0b48\u0b4b-\u0b4d\u0b56\u0b57\u0b5c\u0b5d\u0b5f-\u0b63\u0b71\u0b82\u0b83\u0b85-\u0b8a\u0b8e-\u0b90\u0b92-\u0b95\u0b99\u0b9a\u0b9c\u0b9e\u0b9f\u0ba3\u0ba4\u0ba8-\u0baa\u0bae-\u0bb9\u0bbe-\u0bc2\u0bc6-\u0bc8\u0bca-\u0bcd\u0bd0\u0bd7\u0c00-\u0c03\u0c05-\u0c0c\u0c0e-\u0c10\u0c12-\u0c28\u0c2a-\u0c39\u0c3d-\u0c44\u0c46-\u0c48\u0c4a-\u0c4d\u0c55\u0c56\u0c58\u0c59\u0c60-\u0c63\u0c81-\u0c83\u0c85-\u0c8c\u0c8e-\u0c90\u0c92-\u0ca8\u0caa-\u0cb3\u0cb5-\u0cb9\u0cbc-\u0cc4\u0cc6-\u0cc8\u0cca-\u0ccd\u0cd5\u0cd6\u0cde\u0ce0-\u0ce3\u0cf1\u0cf2\u0d01-\u0d03\u0d05-\u0d0c\u0d0e-\u0d10\u0d12-\u0d3a\u0d3d-\u0d44\u0d46-\u0d48\u0d4a-\u0d4e\u0d57\u0d60-\u0d63\u0d7a-\u0d7f\u0d82\u0d83\u0d85-\u0d96\u0d9a-\u0db1\u0db3-\u0dbb\u0dbd\u0dc0-\u0dc6\u0dca\u0dcf-\u0dd4\u0dd6\u0dd8-\u0ddf\u0df2\u0df3\u0e01-\u0e3a\u0e40-\u0e4e\u0e81\u0e82\u0e84\u0e87\u0e88\u0e8a\u0e8d\u0e94-\u0e97\u0e99-\u0e9f\u0ea1-\u0ea3\u0ea5\u0ea7\u0eaa\u0eab\u0ead-\u0eb9\u0ebb-\u0ebd\u0ec0-\u0ec4\u0ec6\u0ec8-\u0ecd\u0edc-\u0edf\u0f00\u0f18\u0f19\u0f35\u0f37\u0f39\u0f3e-\u0f47\u0f49-\u0f6c\u0f71-\u0f84\u0f86-\u0f97\u0f99-\u0fbc\u0fc6\u1000-\u103f\u1050-\u108f\u109a-\u109d\u10a0-\u10c5\u10c7\u10cd\u10d0-\u10fa\u10fc-\u1248\u124a-\u124d\u1250-\u1256\u1258\u125a-\u125d\u1260-\u1288\u128a-\u128d\u1290-\u12b0\u12b2-\u12b5\u12b8-\u12be\u12c0\u12c2-\u12c5\u12c8-\u12d6\u12d8-\u1310\u1312-\u1315\u1318-\u135a\u135d-\u135f\u1380-\u138f\u13a0-\u13f4\u1401-\u166c\u166f-\u167f\u1681-\u169a\u16a0-\u16ea\u16f1-\u16f8\u1700-\u170c\u170e-\u1714\u1720-\u1734\u1740-\u1753\u1760-\u176c\u176e-\u1770\u1772\u1773\u1780-\u17d3\u17d7\u17dc\u17dd\u180b-\u180d\u1820-\u1877\u1880-\u18aa\u18b0-\u18f5\u1900-\u191e\u1920-\u192b\u1930-\u193b\u1950-\u196d\u1970-\u1974\u1980-\u19ab\u19b0-\u19c9\u1a00-\u1a1b\u1a20-\u1a5e\u1a60-\u1a7c\u1a7f\u1aa7\u1ab0-\u1abe\u1b00-\u1b4b\u1b6b-\u1b73\u1b80-\u1baf\u1bba-\u1bf3\u1c00-\u1c37\u1c4d-\u1c4f\u1c5a-\u1c7d\u1cd0-\u1cd2\u1cd4-\u1cf6\u1cf8\u1cf9\u1d00-\u1df5\u1dfc-\u1f15\u1f18-\u1f1d\u1f20-\u1f45\u1f48-\u1f4d\u1f50-\u1f57\u1f59\u1f5b\u1f5d\u1f5f-\u1f7d\u1f80-\u1fb4\u1fb6-\u1fbc\u1fbe\u1fc2-\u1fc4\u1fc6-\u1fcc\u1fd0-\u1fd3\u1fd6-\u1fdb\u1fe0-\u1fec\u1ff2-\u1ff4\u1ff6-\u1ffc\u2071\u207f\u2090-\u209c\u20d0-\u20f0\u2102\u2107\u210a-\u2113\u2115\u2119-\u211d\u2124\u2126\u2128\u212a-\u212d\u212f-\u2139\u213c-\u213f\u2145-\u2149\u214e\u2183\u2184\u2c00-\u2c2e\u2c30-\u2c5e\u2c60-\u2ce4\u2ceb-\u2cf3\u2d00-\u2d25\u2d27\u2d2d\u2d30-\u2d67\u2d6f\u2d7f-\u2d96\u2da0-\u2da6\u2da8-\u2dae\u2db0-\u2db6\u2db8-\u2dbe\u2dc0-\u2dc6\u2dc8-\u2dce\u2dd0-\u2dd6\u2dd8-\u2dde\u2de0-\u2dff\u2e2f\u3005\u3006\u302a-\u302f\u3031-\u3035\u303b\u303c\u3041-\u3096\u3099\u309a\u309d-\u309f\u30a1-\u30fa\u30fc-\u30ff\u3105-\u312d\u3131-\u318e\u31a0-\u31ba\u31f0-\u31ff\u3400-\u4db5\u4e00-\u9fcc\ua000-\ua48c\ua4d0-\ua4fd\ua500-\ua60c\ua610-\ua61f\ua62a\ua62b\ua640-\ua672\ua674-\ua67d\ua67f-\ua69d\ua69f-\ua6e5\ua6f0\ua6f1\ua717-\ua71f\ua722-\ua788\ua78b-\ua78e\ua790-\ua7ad\ua7b0\ua7b1\ua7f7-\ua827\ua840-\ua873\ua880-\ua8c4\ua8e0-\ua8f7\ua8fb\ua90a-\ua92d\ua930-\ua953\ua960-\ua97c\ua980-\ua9c0\ua9cf\ua9e0-\ua9ef\ua9fa-\ua9fe\uaa00-\uaa36\uaa40-\uaa4d\uaa60-\uaa76\uaa7a-\uaac2\uaadb-\uaadd\uaae0-\uaaef\uaaf2-\uaaf6\uab01-\uab06\uab09-\uab0e\uab11-\uab16\uab20-\uab26\uab28-\uab2e\uab30-\uab5a\uab5c-\uab5f\uab64\uab65\uabc0-\uabea\uabec\uabed\uac00-\ud7a3\ud7b0-\ud7c6\ud7cb-\ud7fb\uf870-\uf87f\uf882\uf884-\uf89f\uf8b8\uf8c1-\uf8d6\uf900-\ufa6d\ufa70-\ufad9\ufb00-\ufb06\ufb13-\ufb17\ufb1d-\ufb28\ufb2a-\ufb36\ufb38-\ufb3c\ufb3e\ufb40\ufb41\ufb43\ufb44\ufb46-\ufbb1\ufbd3-\ufd3d\ufd50-\ufd8f\ufd92-\ufdc7\ufdf0-\ufdfb\ufe00-\ufe0f\ufe20-\ufe2d\ufe70-\ufe74\ufe76-\ufefc\uff21-\uff3a\uff41-\uff5a\uff66-\uffbe\uffc2-\uffc7\uffca-\uffcf\uffd2-\uffd7\uffda-\uffdc/;
	  twttr.txt.regexen.astralLetterAndMarks = /\ud800[\udc00-\udc0b\udc0d-\udc26\udc28-\udc3a\udc3c\udc3d\udc3f-\udc4d\udc50-\udc5d\udc80-\udcfa\uddfd\ude80-\ude9c\udea0-\uded0\udee0\udf00-\udf1f\udf30-\udf40\udf42-\udf49\udf50-\udf7a\udf80-\udf9d\udfa0-\udfc3\udfc8-\udfcf]|\ud801[\udc00-\udc9d\udd00-\udd27\udd30-\udd63\ude00-\udf36\udf40-\udf55\udf60-\udf67]|\ud802[\udc00-\udc05\udc08\udc0a-\udc35\udc37\udc38\udc3c\udc3f-\udc55\udc60-\udc76\udc80-\udc9e\udd00-\udd15\udd20-\udd39\udd80-\uddb7\uddbe\uddbf\ude00-\ude03\ude05\ude06\ude0c-\ude13\ude15-\ude17\ude19-\ude33\ude38-\ude3a\ude3f\ude60-\ude7c\ude80-\ude9c\udec0-\udec7\udec9-\udee6\udf00-\udf35\udf40-\udf55\udf60-\udf72\udf80-\udf91]|\ud803[\udc00-\udc48]|\ud804[\udc00-\udc46\udc7f-\udcba\udcd0-\udce8\udd00-\udd34\udd50-\udd73\udd76\udd80-\uddc4\uddda\ude00-\ude11\ude13-\ude37\udeb0-\udeea\udf01-\udf03\udf05-\udf0c\udf0f\udf10\udf13-\udf28\udf2a-\udf30\udf32\udf33\udf35-\udf39\udf3c-\udf44\udf47\udf48\udf4b-\udf4d\udf57\udf5d-\udf63\udf66-\udf6c\udf70-\udf74]|\ud805[\udc80-\udcc5\udcc7\udd80-\uddb5\uddb8-\uddc0\ude00-\ude40\ude44\ude80-\udeb7]|\ud806[\udca0-\udcdf\udcff\udec0-\udef8]|\ud808[\udc00-\udf98]|\ud80c[\udc00-\udfff]|\ud80d[\udc00-\udc2e]|\ud81a[\udc00-\ude38\ude40-\ude5e\uded0-\udeed\udef0-\udef4\udf00-\udf36\udf40-\udf43\udf63-\udf77\udf7d-\udf8f]|\ud81b[\udf00-\udf44\udf50-\udf7e\udf8f-\udf9f]|\ud82c[\udc00\udc01]|\ud82f[\udc00-\udc6a\udc70-\udc7c\udc80-\udc88\udc90-\udc99\udc9d\udc9e]|\ud834[\udd65-\udd69\udd6d-\udd72\udd7b-\udd82\udd85-\udd8b\uddaa-\uddad\ude42-\ude44]|\ud835[\udc00-\udc54\udc56-\udc9c\udc9e\udc9f\udca2\udca5\udca6\udca9-\udcac\udcae-\udcb9\udcbb\udcbd-\udcc3\udcc5-\udd05\udd07-\udd0a\udd0d-\udd14\udd16-\udd1c\udd1e-\udd39\udd3b-\udd3e\udd40-\udd44\udd46\udd4a-\udd50\udd52-\udea5\udea8-\udec0\udec2-\udeda\udedc-\udefa\udefc-\udf14\udf16-\udf34\udf36-\udf4e\udf50-\udf6e\udf70-\udf88\udf8a-\udfa8\udfaa-\udfc2\udfc4-\udfcb]|\ud83a[\udc00-\udcc4\udcd0-\udcd6]|\ud83b[\ude00-\ude03\ude05-\ude1f\ude21\ude22\ude24\ude27\ude29-\ude32\ude34-\ude37\ude39\ude3b\ude42\ude47\ude49\ude4b\ude4d-\ude4f\ude51\ude52\ude54\ude57\ude59\ude5b\ude5d\ude5f\ude61\ude62\ude64\ude67-\ude6a\ude6c-\ude72\ude74-\ude77\ude79-\ude7c\ude7e\ude80-\ude89\ude8b-\ude9b\udea1-\udea3\udea5-\udea9\udeab-\udebb]|\ud840[\udc00-\udfff]|\ud841[\udc00-\udfff]|\ud842[\udc00-\udfff]|\ud843[\udc00-\udfff]|\ud844[\udc00-\udfff]|\ud845[\udc00-\udfff]|\ud846[\udc00-\udfff]|\ud847[\udc00-\udfff]|\ud848[\udc00-\udfff]|\ud849[\udc00-\udfff]|\ud84a[\udc00-\udfff]|\ud84b[\udc00-\udfff]|\ud84c[\udc00-\udfff]|\ud84d[\udc00-\udfff]|\ud84e[\udc00-\udfff]|\ud84f[\udc00-\udfff]|\ud850[\udc00-\udfff]|\ud851[\udc00-\udfff]|\ud852[\udc00-\udfff]|\ud853[\udc00-\udfff]|\ud854[\udc00-\udfff]|\ud855[\udc00-\udfff]|\ud856[\udc00-\udfff]|\ud857[\udc00-\udfff]|\ud858[\udc00-\udfff]|\ud859[\udc00-\udfff]|\ud85a[\udc00-\udfff]|\ud85b[\udc00-\udfff]|\ud85c[\udc00-\udfff]|\ud85d[\udc00-\udfff]|\ud85e[\udc00-\udfff]|\ud85f[\udc00-\udfff]|\ud860[\udc00-\udfff]|\ud861[\udc00-\udfff]|\ud862[\udc00-\udfff]|\ud863[\udc00-\udfff]|\ud864[\udc00-\udfff]|\ud865[\udc00-\udfff]|\ud866[\udc00-\udfff]|\ud867[\udc00-\udfff]|\ud868[\udc00-\udfff]|\ud869[\udc00-\uded6\udf00-\udfff]|\ud86a[\udc00-\udfff]|\ud86b[\udc00-\udfff]|\ud86c[\udc00-\udfff]|\ud86d[\udc00-\udf34\udf40-\udfff]|\ud86e[\udc00-\udc1d]|\ud87e[\udc00-\ude1d]|\udb40[\udd00-\uddef]/;

	  // Generated from unicode_regex/unicode_regex_groups.scala, same as objective c's \p{Nd}
	  twttr.txt.regexen.bmpNumerals = /0-9\u0660-\u0669\u06f0-\u06f9\u07c0-\u07c9\u0966-\u096f\u09e6-\u09ef\u0a66-\u0a6f\u0ae6-\u0aef\u0b66-\u0b6f\u0be6-\u0bef\u0c66-\u0c6f\u0ce6-\u0cef\u0d66-\u0d6f\u0de6-\u0def\u0e50-\u0e59\u0ed0-\u0ed9\u0f20-\u0f29\u1040-\u1049\u1090-\u1099\u17e0-\u17e9\u1810-\u1819\u1946-\u194f\u19d0-\u19d9\u1a80-\u1a89\u1a90-\u1a99\u1b50-\u1b59\u1bb0-\u1bb9\u1c40-\u1c49\u1c50-\u1c59\ua620-\ua629\ua8d0-\ua8d9\ua900-\ua909\ua9d0-\ua9d9\ua9f0-\ua9f9\uaa50-\uaa59\uabf0-\uabf9\uff10-\uff19/;
	  twttr.txt.regexen.astralNumerals = /\ud801[\udca0-\udca9]|\ud804[\udc66-\udc6f\udcf0-\udcf9\udd36-\udd3f\uddd0-\uddd9\udef0-\udef9]|\ud805[\udcd0-\udcd9\ude50-\ude59\udec0-\udec9]|\ud806[\udce0-\udce9]|\ud81a[\ude60-\ude69\udf50-\udf59]|\ud835[\udfce-\udfff]/;

	  twttr.txt.regexen.hashtagSpecialChars = /_\u200c\u200d\ua67e\u05be\u05f3\u05f4\uff5e\u301c\u309b\u309c\u30a0\u30fb\u3003\u0f0b\u0f0c\xb7/;

	  // A hashtag must contain at least one unicode letter or mark, as well as numbers, underscores, and select special characters.
	  twttr.txt.regexen.hashSigns = /[#＃]/;
	  twttr.txt.regexen.hashtagAlpha = regexSupplant(/(?:[#{bmpLetterAndMarks}]|(?=#{non_bmp_code_pairs})(?:#{astralLetterAndMarks}))/);
	  twttr.txt.regexen.hashtagAlphaNumeric = regexSupplant(/(?:[#{bmpLetterAndMarks}#{bmpNumerals}#{hashtagSpecialChars}]|(?=#{non_bmp_code_pairs})(?:#{astralLetterAndMarks}|#{astralNumerals}))/);
	  twttr.txt.regexen.endHashtagMatch = regexSupplant(/^(?:#{hashSigns}|:\/\/)/);
	  twttr.txt.regexen.codePoint = /(?:[^\uD800-\uDFFF]|[\uD800-\uDBFF][\uDC00-\uDFFF])/;
	  twttr.txt.regexen.hashtagBoundary = regexSupplant(/(?:^|$|(?!#{hashtagAlphaNumeric}|&)#{codePoint})/);
	  twttr.txt.regexen.validHashtag = regexSupplant(/(#{hashtagBoundary})(#{hashSigns})(?!\uFE0F|\u20E3)(#{hashtagAlphaNumeric}*#{hashtagAlpha}#{hashtagAlphaNumeric}*)/gi);

	  // Mention related regex collection
	  twttr.txt.regexen.validMentionPrecedingChars = /(?:^|[^a-zA-Z0-9_!#$%&*@＠]|(?:^|[^a-zA-Z0-9_+~.-])(?:rt|RT|rT|Rt):?)/;
	  twttr.txt.regexen.atSigns = /[@＠]/;
	  twttr.txt.regexen.validMentionOrList = regexSupplant(
	    '(#{validMentionPrecedingChars})' +  // $1: Preceding character
	    '(#{atSigns})' +                     // $2: At mark
	    '([a-zA-Z0-9_]{1,20})' +             // $3: Screen name
	    '(\/[a-zA-Z][a-zA-Z0-9_\-]{0,24})?'  // $4: List (optional)
	  , 'g');
	  twttr.txt.regexen.validReply = regexSupplant(/^(?:#{spaces})*#{atSigns}([a-zA-Z0-9_]{1,20})/);
	  twttr.txt.regexen.endMentionMatch = regexSupplant(/^(?:#{atSigns}|[#{latinAccentChars}]|:\/\/)/);

	  // URL related regex collection
	  twttr.txt.regexen.validUrlPrecedingChars = regexSupplant(/(?:[^A-Za-z0-9@＠$#＃#{invalid_chars_group}]|^)/);
	  twttr.txt.regexen.invalidUrlWithoutProtocolPrecedingChars = /[-_.\/]$/;
	  twttr.txt.regexen.invalidDomainChars = stringSupplant("#{punct}#{spaces_group}#{invalid_chars_group}", twttr.txt.regexen);
	  twttr.txt.regexen.validDomainChars = regexSupplant(/[^#{invalidDomainChars}]/);
	  twttr.txt.regexen.validSubdomain = regexSupplant(/(?:(?:#{validDomainChars}(?:[_-]|#{validDomainChars})*)?#{validDomainChars}\.)/);
	  twttr.txt.regexen.validDomainName = regexSupplant(/(?:(?:#{validDomainChars}(?:-|#{validDomainChars})*)?#{validDomainChars}\.)/);
	  twttr.txt.regexen.validGTLD = regexSupplant(RegExp(
	'(?:(?:' +
	    '삼성|닷컴|닷넷|香格里拉|餐厅|食品|飞利浦|電訊盈科|集团|购物|谷歌|诺基亚|联通|网络|网站|网店|网址|组织机构|移动|珠宝|点看|游戏|淡马锡|机构|書籍|时尚|新闻|政府|政务|' +
	    '手表|手机|我爱你|慈善|微博|广东|工行|家電|娱乐|大拿|在线|嘉里大酒店|嘉里|商标|商店|商城|公益|公司|八卦|健康|信息|佛山|企业|中文网|中信|世界|ポイント|ファッション|' +
	    'セール|ストア|コム|グーグル|クラウド|みんな|คอม|संगठन|नेट|कॉम|همراه|موقع|موبايلي|كوم|شبكة|بيتك|بازار|العليان|' +
	    'ارامكو|ابوظبي|קום|сайт|рус|орг|онлайн|москва|ком|дети|zuerich|zone|zippo|zip|zero|zara|zappos|' +
	    'yun|youtube|you|yokohama|yoga|yodobashi|yandex|yamaxun|yahoo|yachts|xyz|xxx|xperia|xin|xihuan|' +
	    'xfinity|xerox|xbox|wtf|wtc|world|works|work|woodside|wolterskluwer|wme|wine|windows|win|' +
	    'williamhill|wiki|wien|whoswho|weir|weibo|wedding|wed|website|weber|webcam|weatherchannel|' +
	    'weather|watches|watch|warman|wanggou|wang|walter|wales|vuelos|voyage|voto|voting|vote|' +
	    'volkswagen|vodka|vlaanderen|viva|vistaprint|vista|vision|virgin|vip|vin|villas|viking|vig|video|' +
	    'viajes|vet|versicherung|vermögensberatung|vermögensberater|verisign|ventures|vegas|vana|' +
	    'vacations|ups|uol|uno|university|unicom|ubs|tvs|tushu|tunes|tui|tube|trv|trust|' +
	    'travelersinsurance|travelers|travelchannel|travel|training|trading|trade|toys|toyota|town|tours|' +
	    'total|toshiba|toray|top|tools|tokyo|today|tmall|tirol|tires|tips|tiffany|tienda|tickets|theatre|' +
	    'theater|thd|teva|tennis|temasek|telefonica|telecity|tel|technology|tech|team|tdk|tci|taxi|tax|' +
	    'tattoo|tatar|tatamotors|taobao|talk|taipei|tab|systems|symantec|sydney|swiss|swatch|suzuki|' +
	    'surgery|surf|support|supply|supplies|sucks|style|study|studio|stream|store|storage|stockholm|' +
	    'stcgroup|stc|statoil|statefarm|statebank|starhub|star|stada|srl|spreadbetting|spot|spiegel|' +
	    'space|soy|sony|song|solutions|solar|sohu|software|softbank|social|soccer|sncf|smile|skype|sky|' +
	    'skin|ski|site|singles|sina|silk|shriram|show|shouji|shopping|shop|shoes|shiksha|shia|shell|shaw|' +
	    'sharp|shangrila|sfr|sexy|sex|sew|seven|services|sener|select|seek|security|seat|scot|scor|' +
	    'science|schwarz|schule|school|scholarships|schmidt|schaeffler|scb|sca|sbs|sbi|saxo|save|sas|' +
	    'sarl|sapo|sap|sanofi|sandvikcoromant|sandvik|samsung|salon|sale|sakura|safety|safe|saarland|' +
	    'ryukyu|rwe|run|ruhr|rsvp|room|rodeo|rocks|rocher|rip|rio|ricoh|richardli|rich|rexroth|reviews|' +
	    'review|restaurant|rest|republican|report|repair|rentals|rent|ren|reit|reisen|reise|rehab|' +
	    'redumbrella|redstone|red|recipes|realty|realtor|realestate|read|racing|quest|quebec|qpon|pwc|' +
	    'pub|protection|property|properties|promo|progressive|prof|productions|prod|pro|prime|press|' +
	    'praxi|post|porn|politie|poker|pohl|pnc|plus|plumbing|playstation|play|place|pizza|pioneer|pink|' +
	    'ping|pin|pid|pictures|pictet|pics|piaget|physio|photos|photography|photo|philips|pharmacy|pet|' +
	    'pccw|passagens|party|parts|partners|pars|paris|panerai|pamperedchef|page|ovh|ott|otsuka|osaka|' +
	    'origins|orientexpress|organic|org|orange|oracle|ooo|online|onl|ong|one|omega|ollo|olayangroup|' +
	    'olayan|okinawa|office|obi|nyc|ntt|nrw|nra|nowtv|nowruz|now|norton|northwesternmutual|nokia|' +
	    'nissay|nissan|ninja|nikon|nico|nhk|ngo|nfl|nexus|nextdirect|next|news|new|neustar|network|' +
	    'netflix|netbank|net|nec|navy|natura|name|nagoya|nadex|mutuelle|mutual|museum|mtr|mtpc|mtn|' +
	    'movistar|movie|mov|motorcycles|moscow|mortgage|mormon|montblanc|money|monash|mom|moi|moe|moda|' +
	    'mobily|mobi|mma|mls|mlb|mitsubishi|mit|mini|mil|microsoft|miami|metlife|meo|menu|men|memorial|' +
	    'meme|melbourne|meet|media|med|mba|mattel|marriott|markets|marketing|market|mango|management|man|' +
	    'makeup|maison|maif|madrid|luxury|luxe|lupin|ltda|ltd|love|lotto|lotte|london|lol|locus|locker|' +
	    'loans|loan|lixil|living|live|lipsy|link|linde|lincoln|limo|limited|like|lighting|lifestyle|' +
	    'lifeinsurance|life|lidl|liaison|lgbt|lexus|lego|legal|leclerc|lease|lds|lawyer|law|latrobe|lat|' +
	    'lasalle|lanxess|landrover|land|lancaster|lamer|lamborghini|lacaixa|kyoto|kuokgroup|kred|krd|kpn|' +
	    'kpmg|kosher|komatsu|koeln|kiwi|kitchen|kindle|kinder|kim|kia|kfh|kerryproperties|kerrylogistics|' +
	    'kerryhotels|kddi|kaufen|juegos|jprs|jpmorgan|joy|jot|joburg|jobs|jnj|jmp|jll|jlc|jewelry|jetzt|' +
	    'jcp|jcb|java|jaguar|iwc|itv|itau|istanbul|ist|ismaili|iselect|irish|ipiranga|investments|' +
	    'international|int|insure|insurance|institute|ink|ing|info|infiniti|industries|immobilien|immo|' +
	    'imdb|imamat|ikano|iinet|ifm|icu|ice|icbc|ibm|hyundai|htc|hsbc|how|house|hotmail|hoteles|hosting|' +
	    'host|horse|honda|homes|homedepot|holiday|holdings|hockey|hkt|hiv|hitachi|hisamitsu|hiphop|hgtv|' +
	    'hermes|here|helsinki|help|healthcare|health|hdfcbank|haus|hangout|hamburg|guru|guitars|guide|' +
	    'guge|gucci|guardian|group|gripe|green|gratis|graphics|grainger|gov|got|gop|google|goog|goodyear|' +
	    'goo|golf|goldpoint|gold|godaddy|gmx|gmo|gmbh|gmail|globo|global|gle|glass|giving|gives|gifts|' +
	    'gift|ggee|genting|gent|gea|gdn|gbiz|garden|games|game|gallup|gallo|gallery|gal|fyi|futbol|' +
	    'furniture|fund|fujitsu|ftr|frontier|frontdoor|frogans|frl|fresenius|fox|foundation|forum|' +
	    'forsale|forex|ford|football|foodnetwork|foo|fly|flsmidth|flowers|florist|flir|flights|flickr|' +
	    'fitness|fit|fishing|fish|firmdale|firestone|fire|financial|finance|final|film|ferrero|feedback|' +
	    'fedex|fast|fashion|farmers|farm|fans|fan|family|faith|fairwinds|fail|fage|extraspace|express|' +
	    'exposed|expert|exchange|everbank|events|eus|eurovision|estate|esq|erni|ericsson|equipment|epson|' +
	    'epost|enterprises|engineering|engineer|energy|emerck|email|education|edu|edeka|eat|earth|dvag|' +
	    'durban|dupont|dunlop|dubai|dtv|drive|download|dot|doosan|domains|doha|dog|docs|dnp|discount|' +
	    'directory|direct|digital|diet|diamonds|dhl|dev|design|desi|dentist|dental|democrat|delta|' +
	    'deloitte|dell|delivery|degree|deals|dealer|deal|dds|dclk|day|datsun|dating|date|dance|dad|dabur|' +
	    'cyou|cymru|cuisinella|csc|cruises|crs|crown|cricket|creditunion|creditcard|credit|courses|' +
	    'coupons|coupon|country|corsica|coop|cool|cookingchannel|cooking|contractors|contact|consulting|' +
	    'construction|condos|comsec|computer|compare|company|community|commbank|comcast|com|cologne|' +
	    'college|coffee|codes|coach|clubmed|club|cloud|clothing|clinique|clinic|click|cleaning|claims|' +
	    'cityeats|city|citic|cisco|circle|cipriani|church|chrome|christmas|chloe|chintai|cheap|chat|' +
	    'chase|channel|chanel|cfd|cfa|cern|ceo|center|ceb|cbre|cbn|cba|catering|cat|casino|cash|casa|' +
	    'cartier|cars|careers|career|care|cards|caravan|car|capital|capetown|canon|cancerresearch|camp|' +
	    'camera|cam|call|cal|cafe|cab|bzh|buzz|buy|business|builders|build|bugatti|budapest|brussels|' +
	    'brother|broker|broadway|bridgestone|bradesco|boutique|bot|bostik|bosch|boots|book|boo|bond|bom|' +
	    'boehringer|boats|bnpparibas|bnl|bmw|bms|blue|bloomberg|blog|blanco|blackfriday|black|biz|bio|' +
	    'bingo|bing|bike|bid|bible|bharti|bet|best|berlin|bentley|beer|beats|bcn|bcg|bbva|bbc|bayern|' +
	    'bauhaus|bargains|barefoot|barclays|barclaycard|barcelona|bar|bank|band|baidu|baby|azure|axa|aws|' +
	    'avianca|autos|auto|author|audio|audible|audi|auction|attorney|associates|asia|arte|art|arpa|' +
	    'army|archi|aramco|aquarelle|apple|app|apartments|anz|anquan|android|analytics|amsterdam|amica|' +
	    'alstom|alsace|ally|allfinanz|alipay|alibaba|akdn|airtel|airforce|airbus|aig|agency|agakhan|afl|' +
	    'aetna|aero|aeg|adult|ads|adac|actor|active|aco|accountants|accountant|accenture|academy|' +
	    'abudhabi|abogado|able|abbvie|abbott|abb|aarp|aaa|onion' +
	')(?=[^0-9a-zA-Z@]|$))'));
	  twttr.txt.regexen.validCCTLD = regexSupplant(RegExp(
	'(?:(?:' +
	    '한국|香港|澳門|新加坡|台灣|台湾|中國|中国|გე|ไทย|ලංකා|ഭാരതം|ಭಾರತ|భారత్|சிங்கப்பூர்|இலங்கை|இந்தியா|ଭାରତ|ભારત|ਭਾਰਤ|' +
	    'ভাৰত|ভারত|বাংলা|भारत|پاکستان|مليسيا|مصر|قطر|فلسطين|عمان|عراق|سورية|سودان|تونس|بھارت|ایران|' +
	    'امارات|المغرب|السعودية|الجزائر|الاردن|հայ|қаз|укр|срб|рф|мон|мкд|ею|бел|бг|ελ|zw|zm|za|yt|ye|ws|' +
	    'wf|vu|vn|vi|vg|ve|vc|va|uz|uy|us|um|uk|ug|ua|tz|tw|tv|tt|tr|tp|to|tn|tm|tl|tk|tj|th|tg|tf|td|tc|' +
	    'sz|sy|sx|sv|su|st|ss|sr|so|sn|sm|sl|sk|sj|si|sh|sg|se|sd|sc|sb|sa|rw|ru|rs|ro|re|qa|py|pw|pt|ps|' +
	    'pr|pn|pm|pl|pk|ph|pg|pf|pe|pa|om|nz|nu|nr|np|no|nl|ni|ng|nf|ne|nc|na|mz|my|mx|mw|mv|mu|mt|ms|mr|' +
	    'mq|mp|mo|mn|mm|ml|mk|mh|mg|mf|me|md|mc|ma|ly|lv|lu|lt|ls|lr|lk|li|lc|lb|la|kz|ky|kw|kr|kp|kn|km|' +
	    'ki|kh|kg|ke|jp|jo|jm|je|it|is|ir|iq|io|in|im|il|ie|id|hu|ht|hr|hn|hm|hk|gy|gw|gu|gt|gs|gr|gq|gp|' +
	    'gn|gm|gl|gi|gh|gg|gf|ge|gd|gb|ga|fr|fo|fm|fk|fj|fi|eu|et|es|er|eh|eg|ee|ec|dz|do|dm|dk|dj|de|cz|' +
	    'cy|cx|cw|cv|cu|cr|co|cn|cm|cl|ck|ci|ch|cg|cf|cd|cc|ca|bz|by|bw|bv|bt|bs|br|bq|bo|bn|bm|bl|bj|bi|' +
	    'bh|bg|bf|be|bd|bb|ba|az|ax|aw|au|at|as|ar|aq|ao|an|am|al|ai|ag|af|ae|ad|ac' +
	')(?=[^0-9a-zA-Z@]|$))'));
	  twttr.txt.regexen.validPunycode = /(?:xn--[0-9a-z]+)/;
	  twttr.txt.regexen.validSpecialCCTLD = /(?:(?:co|tv)(?=[^0-9a-zA-Z@]|$))/;
	  twttr.txt.regexen.validDomain = regexSupplant(/(?:#{validSubdomain}*#{validDomainName}(?:#{validGTLD}|#{validCCTLD}|#{validPunycode}))/);
	  twttr.txt.regexen.validAsciiDomain = regexSupplant(/(?:(?:[\-a-z0-9#{latinAccentChars}]+)\.)+(?:#{validGTLD}|#{validCCTLD}|#{validPunycode})/gi);
	  twttr.txt.regexen.invalidShortDomain = regexSupplant(/^#{validDomainName}#{validCCTLD}$/i);
	  twttr.txt.regexen.validSpecialShortDomain = regexSupplant(/^#{validDomainName}#{validSpecialCCTLD}$/i);
	  twttr.txt.regexen.validPortNumber = /[0-9]+/;
	  twttr.txt.regexen.cyrillicLettersAndMarks = /\u0400-\u04FF/;
	  twttr.txt.regexen.validGeneralUrlPathChars = regexSupplant(/[a-z#{cyrillicLettersAndMarks}0-9!\*';:=\+,\.\$\/%#\[\]\-_~@\|&#{latinAccentChars}]/i);
	  // Allow URL paths to contain up to two nested levels of balanced parens
	  //  1. Used in Wikipedia URLs like /Primer_(film)
	  //  2. Used in IIS sessions like /S(dfd346)/
	  //  3. Used in Rdio URLs like /track/We_Up_(Album_Version_(Edited))/
	  twttr.txt.regexen.validUrlBalancedParens = regexSupplant(
	    '\\('                                   +
	      '(?:'                                 +
	        '#{validGeneralUrlPathChars}+'      +
	        '|'                                 +
	        // allow one nested level of balanced parentheses
	        '(?:'                               +
	          '#{validGeneralUrlPathChars}*'    +
	          '\\('                             +
	            '#{validGeneralUrlPathChars}+'  +
	          '\\)'                             +
	          '#{validGeneralUrlPathChars}*'    +
	        ')'                                 +
	      ')'                                   +
	    '\\)'
	  , 'i');
	  // Valid end-of-path chracters (so /foo. does not gobble the period).
	  // 1. Allow =&# for empty URL parameters and other URL-join artifacts
	  twttr.txt.regexen.validUrlPathEndingChars = regexSupplant(/[\+\-a-z#{cyrillicLettersAndMarks}0-9=_#\/#{latinAccentChars}]|(?:#{validUrlBalancedParens})/i);
	  // Allow @ in a url, but only in the middle. Catch things like http://example.com/@user/
	  twttr.txt.regexen.validUrlPath = regexSupplant('(?:' +
	    '(?:' +
	      '#{validGeneralUrlPathChars}*' +
	        '(?:#{validUrlBalancedParens}#{validGeneralUrlPathChars}*)*' +
	        '#{validUrlPathEndingChars}'+
	      ')|(?:@#{validGeneralUrlPathChars}+\/)'+
	    ')', 'i');

	  twttr.txt.regexen.validUrlQueryChars = /[a-z0-9!?\*'@\(\);:&=\+\$\/%#\[\]\-_\.,~|]/i;
	  twttr.txt.regexen.validUrlQueryEndingChars = /[a-z0-9_&=#\/]/i;
	  twttr.txt.regexen.extractUrl = regexSupplant(
	    '('                                                            + // $1 total match
	      '(#{validUrlPrecedingChars})'                                + // $2 Preceeding chracter
	      '('                                                          + // $3 URL
	        '(https?:\\/\\/)?'                                         + // $4 Protocol (optional)
	        '(#{validDomain})'                                         + // $5 Domain(s)
	        '(?::(#{validPortNumber}))?'                               + // $6 Port number (optional)
	        '(\\/#{validUrlPath}*)?'                                   + // $7 URL Path
	        '(\\?#{validUrlQueryChars}*#{validUrlQueryEndingChars})?'  + // $8 Query String
	      ')'                                                          +
	    ')'
	  , 'gi');

	  twttr.txt.regexen.validTcoUrl = /^https?:\/\/t\.co\/[a-z0-9]+/i;
	  twttr.txt.regexen.urlHasProtocol = /^https?:\/\//i;
	  twttr.txt.regexen.urlHasHttps = /^https:\/\//i;

	  // cashtag related regex
	  twttr.txt.regexen.cashtag = /[a-z]{1,6}(?:[._][a-z]{1,2})?/i;
	  twttr.txt.regexen.validCashtag = regexSupplant('(^|#{spaces})(\\$)(#{cashtag})(?=$|\\s|[#{punct}])', 'gi');

	  // These URL validation pattern strings are based on the ABNF from RFC 3986
	  twttr.txt.regexen.validateUrlUnreserved = /[a-z\u0400-\u04FF0-9\-._~]/i;
	  twttr.txt.regexen.validateUrlPctEncoded = /(?:%[0-9a-f]{2})/i;
	  twttr.txt.regexen.validateUrlSubDelims = /[!$&'()*+,;=]/i;
	  twttr.txt.regexen.validateUrlPchar = regexSupplant('(?:' +
	    '#{validateUrlUnreserved}|' +
	    '#{validateUrlPctEncoded}|' +
	    '#{validateUrlSubDelims}|' +
	    '[:|@]' +
	  ')', 'i');

	  twttr.txt.regexen.validateUrlScheme = /(?:[a-z][a-z0-9+\-.]*)/i;
	  twttr.txt.regexen.validateUrlUserinfo = regexSupplant('(?:' +
	    '#{validateUrlUnreserved}|' +
	    '#{validateUrlPctEncoded}|' +
	    '#{validateUrlSubDelims}|' +
	    ':' +
	  ')*', 'i');

	  twttr.txt.regexen.validateUrlDecOctet = /(?:[0-9]|(?:[1-9][0-9])|(?:1[0-9]{2})|(?:2[0-4][0-9])|(?:25[0-5]))/i;
	  twttr.txt.regexen.validateUrlIpv4 = regexSupplant(/(?:#{validateUrlDecOctet}(?:\.#{validateUrlDecOctet}){3})/i);

	  // Punting on real IPv6 validation for now
	  twttr.txt.regexen.validateUrlIpv6 = /(?:\[[a-f0-9:\.]+\])/i;

	  // Also punting on IPvFuture for now
	  twttr.txt.regexen.validateUrlIp = regexSupplant('(?:' +
	    '#{validateUrlIpv4}|' +
	    '#{validateUrlIpv6}' +
	  ')', 'i');

	  // This is more strict than the rfc specifies
	  twttr.txt.regexen.validateUrlSubDomainSegment = /(?:[a-z0-9](?:[a-z0-9_\-]*[a-z0-9])?)/i;
	  twttr.txt.regexen.validateUrlDomainSegment = /(?:[a-z0-9](?:[a-z0-9\-]*[a-z0-9])?)/i;
	  twttr.txt.regexen.validateUrlDomainTld = /(?:[a-z](?:[a-z0-9\-]*[a-z0-9])?)/i;
	  twttr.txt.regexen.validateUrlDomain = regexSupplant(/(?:(?:#{validateUrlSubDomainSegment]}\.)*(?:#{validateUrlDomainSegment]}\.)#{validateUrlDomainTld})/i);

	  twttr.txt.regexen.validateUrlHost = regexSupplant('(?:' +
	    '#{validateUrlIp}|' +
	    '#{validateUrlDomain}' +
	  ')', 'i');

	  // Unencoded internationalized domains - this doesn't check for invalid UTF-8 sequences
	  twttr.txt.regexen.validateUrlUnicodeSubDomainSegment = /(?:(?:[a-z0-9]|[^\u0000-\u007f])(?:(?:[a-z0-9_\-]|[^\u0000-\u007f])*(?:[a-z0-9]|[^\u0000-\u007f]))?)/i;
	  twttr.txt.regexen.validateUrlUnicodeDomainSegment = /(?:(?:[a-z0-9]|[^\u0000-\u007f])(?:(?:[a-z0-9\-]|[^\u0000-\u007f])*(?:[a-z0-9]|[^\u0000-\u007f]))?)/i;
	  twttr.txt.regexen.validateUrlUnicodeDomainTld = /(?:(?:[a-z]|[^\u0000-\u007f])(?:(?:[a-z0-9\-]|[^\u0000-\u007f])*(?:[a-z0-9]|[^\u0000-\u007f]))?)/i;
	  twttr.txt.regexen.validateUrlUnicodeDomain = regexSupplant(/(?:(?:#{validateUrlUnicodeSubDomainSegment}\.)*(?:#{validateUrlUnicodeDomainSegment}\.)#{validateUrlUnicodeDomainTld})/i);

	  twttr.txt.regexen.validateUrlUnicodeHost = regexSupplant('(?:' +
	    '#{validateUrlIp}|' +
	    '#{validateUrlUnicodeDomain}' +
	  ')', 'i');

	  twttr.txt.regexen.validateUrlPort = /[0-9]{1,5}/;

	  twttr.txt.regexen.validateUrlUnicodeAuthority = regexSupplant(
	    '(?:(#{validateUrlUserinfo})@)?'  + // $1 userinfo
	    '(#{validateUrlUnicodeHost})'     + // $2 host
	    '(?::(#{validateUrlPort}))?'        //$3 port
	  , "i");

	  twttr.txt.regexen.validateUrlAuthority = regexSupplant(
	    '(?:(#{validateUrlUserinfo})@)?' + // $1 userinfo
	    '(#{validateUrlHost})'           + // $2 host
	    '(?::(#{validateUrlPort}))?'       // $3 port
	  , "i");

	  twttr.txt.regexen.validateUrlPath = regexSupplant(/(\/#{validateUrlPchar}*)*/i);
	  twttr.txt.regexen.validateUrlQuery = regexSupplant(/(#{validateUrlPchar}|\/|\?)*/i);
	  twttr.txt.regexen.validateUrlFragment = regexSupplant(/(#{validateUrlPchar}|\/|\?)*/i);

	  // Modified version of RFC 3986 Appendix B
	  twttr.txt.regexen.validateUrlUnencoded = regexSupplant(
	    '^'                               + // Full URL
	    '(?:'                             +
	      '([^:/?#]+):\\/\\/'             + // $1 Scheme
	    ')?'                              +
	    '([^/?#]*)'                       + // $2 Authority
	    '([^?#]*)'                        + // $3 Path
	    '(?:'                             +
	      '\\?([^#]*)'                    + // $4 Query
	    ')?'                              +
	    '(?:'                             +
	      '#(.*)'                         + // $5 Fragment
	    ')?$'
	  , "i");


	  // Default CSS class for auto-linked lists (along with the url class)
	  var DEFAULT_LIST_CLASS = "tweet-url list-slug";
	  // Default CSS class for auto-linked usernames (along with the url class)
	  var DEFAULT_USERNAME_CLASS = "tweet-url username";
	  // Default CSS class for auto-linked hashtags (along with the url class)
	  var DEFAULT_HASHTAG_CLASS = "tweet-url hashtag";
	  // Default CSS class for auto-linked cashtags (along with the url class)
	  var DEFAULT_CASHTAG_CLASS = "tweet-url cashtag";
	  // Options which should not be passed as HTML attributes
	  var OPTIONS_NOT_ATTRIBUTES = {'urlClass':true, 'listClass':true, 'usernameClass':true, 'hashtagClass':true, 'cashtagClass':true,
	                            'usernameUrlBase':true, 'listUrlBase':true, 'hashtagUrlBase':true, 'cashtagUrlBase':true,
	                            'usernameUrlBlock':true, 'listUrlBlock':true, 'hashtagUrlBlock':true, 'linkUrlBlock':true,
	                            'usernameIncludeSymbol':true, 'suppressLists':true, 'suppressNoFollow':true, 'targetBlank':true,
	                            'suppressDataScreenName':true, 'urlEntities':true, 'symbolTag':true, 'textWithSymbolTag':true, 'urlTarget':true,
	                            'invisibleTagAttrs':true, 'linkAttributeBlock':true, 'linkTextBlock': true, 'htmlEscapeNonEntities': true
	                            };

	  var BOOLEAN_ATTRIBUTES = {'disabled':true, 'readonly':true, 'multiple':true, 'checked':true};

	  // Simple object cloning function for simple objects
	  function clone(o) {
	    var r = {};
	    for (var k in o) {
	      if (o.hasOwnProperty(k)) {
	        r[k] = o[k];
	      }
	    }

	    return r;
	  }

	  twttr.txt.tagAttrs = function(attributes) {
	    var htmlAttrs = "";
	    for (var k in attributes) {
	      var v = attributes[k];
	      if (BOOLEAN_ATTRIBUTES[k]) {
	        v = v ? k : null;
	      }
	      if (v == null) continue;
	      htmlAttrs += " " + twttr.txt.htmlEscape(k) + "=\"" + twttr.txt.htmlEscape(v.toString()) + "\"";
	    }
	    return htmlAttrs;
	  };

	  twttr.txt.linkToText = function(entity, text, attributes, options) {
	    if (!options.suppressNoFollow) {
	      attributes.rel = "nofollow";
	    }
	    // if linkAttributeBlock is specified, call it to modify the attributes
	    if (options.linkAttributeBlock) {
	      options.linkAttributeBlock(entity, attributes);
	    }
	    // if linkTextBlock is specified, call it to get a new/modified link text
	    if (options.linkTextBlock) {
	      text = options.linkTextBlock(entity, text);
	    }
	    var d = {
	      text: text,
	      attr: twttr.txt.tagAttrs(attributes)
	    };
	    return stringSupplant("<a#{attr}>#{text}</a>", d);
	  };

	  twttr.txt.linkToTextWithSymbol = function(entity, symbol, text, attributes, options) {
	    var taggedSymbol = options.symbolTag ? "<" + options.symbolTag + ">" + symbol + "</"+ options.symbolTag + ">" : symbol;
	    text = twttr.txt.htmlEscape(text);
	    var taggedText = options.textWithSymbolTag ? "<" + options.textWithSymbolTag + ">" + text + "</"+ options.textWithSymbolTag + ">" : text;

	    if (options.usernameIncludeSymbol || !symbol.match(twttr.txt.regexen.atSigns)) {
	      return twttr.txt.linkToText(entity, taggedSymbol + taggedText, attributes, options);
	    } else {
	      return taggedSymbol + twttr.txt.linkToText(entity, taggedText, attributes, options);
	    }
	  };

	  twttr.txt.linkToHashtag = function(entity, text, options) {
	    var hash = text.substring(entity.indices[0], entity.indices[0] + 1);
	    var hashtag = twttr.txt.htmlEscape(entity.hashtag);
	    var attrs = clone(options.htmlAttrs || {});
	    attrs.href = options.hashtagUrlBase + hashtag;
	    attrs.title = "#" + hashtag;
	    attrs["class"] = options.hashtagClass;
	    if (hashtag.charAt(0).match(twttr.txt.regexen.rtl_chars)){
	      attrs["class"] += " rtl";
	    }
	    if (options.targetBlank) {
	      attrs.target = '_blank';
	    }

	    return twttr.txt.linkToTextWithSymbol(entity, hash, hashtag, attrs, options);
	  };

	  twttr.txt.linkToCashtag = function(entity, text, options) {
	    var cashtag = twttr.txt.htmlEscape(entity.cashtag);
	    var attrs = clone(options.htmlAttrs || {});
	    attrs.href = options.cashtagUrlBase + cashtag;
	    attrs.title = "$" + cashtag;
	    attrs["class"] =  options.cashtagClass;
	    if (options.targetBlank) {
	      attrs.target = '_blank';
	    }

	    return twttr.txt.linkToTextWithSymbol(entity, "$", cashtag, attrs, options);
	  };

	  twttr.txt.linkToMentionAndList = function(entity, text, options) {
	    var at = text.substring(entity.indices[0], entity.indices[0] + 1);
	    var user = twttr.txt.htmlEscape(entity.screenName);
	    var slashListname = twttr.txt.htmlEscape(entity.listSlug);
	    var isList = entity.listSlug && !options.suppressLists;
	    var attrs = clone(options.htmlAttrs || {});
	    attrs["class"] = (isList ? options.listClass : options.usernameClass);
	    attrs.href = isList ? options.listUrlBase + user + slashListname : options.usernameUrlBase + user;
	    if (!isList && !options.suppressDataScreenName) {
	      attrs['data-screen-name'] = user;
	    }
	    if (options.targetBlank) {
	      attrs.target = '_blank';
	    }

	    return twttr.txt.linkToTextWithSymbol(entity, at, isList ? user + slashListname : user, attrs, options);
	  };

	  twttr.txt.linkToUrl = function(entity, text, options) {
	    var url = entity.url;
	    var displayUrl = url;
	    var linkText = twttr.txt.htmlEscape(displayUrl);

	    // If the caller passed a urlEntities object (provided by a Twitter API
	    // response with include_entities=true), we use that to render the display_url
	    // for each URL instead of it's underlying t.co URL.
	    var urlEntity = (options.urlEntities && options.urlEntities[url]) || entity;
	    if (urlEntity.display_url) {
	      linkText = twttr.txt.linkTextWithEntity(urlEntity, options);
	    }

	    var attrs = clone(options.htmlAttrs || {});

	    if (!url.match(twttr.txt.regexen.urlHasProtocol)) {
	      url = "http://" + url;
	    }
	    attrs.href = url;

	    if (options.targetBlank) {
	      attrs.target = '_blank';
	    }

	    // set class only if urlClass is specified.
	    if (options.urlClass) {
	      attrs["class"] = options.urlClass;
	    }

	    // set target only if urlTarget is specified.
	    if (options.urlTarget) {
	      attrs.target = options.urlTarget;
	    }

	    if (!options.title && urlEntity.display_url) {
	      attrs.title = urlEntity.expanded_url;
	    }

	    return twttr.txt.linkToText(entity, linkText, attrs, options);
	  };

	  twttr.txt.linkTextWithEntity = function (entity, options) {
	    var displayUrl = entity.display_url;
	    var expandedUrl = entity.expanded_url;

	    // Goal: If a user copies and pastes a tweet containing t.co'ed link, the resulting paste
	    // should contain the full original URL (expanded_url), not the display URL.
	    //
	    // Method: Whenever possible, we actually emit HTML that contains expanded_url, and use
	    // font-size:0 to hide those parts that should not be displayed (because they are not part of display_url).
	    // Elements with font-size:0 get copied even though they are not visible.
	    // Note that display:none doesn't work here. Elements with display:none don't get copied.
	    //
	    // Additionally, we want to *display* ellipses, but we don't want them copied.  To make this happen we
	    // wrap the ellipses in a tco-ellipsis class and provide an onCopy handler that sets display:none on
	    // everything with the tco-ellipsis class.
	    //
	    // Exception: pic.twitter.com images, for which expandedUrl = "https://twitter.com/#!/username/status/1234/photo/1
	    // For those URLs, display_url is not a substring of expanded_url, so we don't do anything special to render the elided parts.
	    // For a pic.twitter.com URL, the only elided part will be the "https://", so this is fine.

	    var displayUrlSansEllipses = displayUrl.replace(/…/g, ""); // We have to disregard ellipses for matching
	    // Note: we currently only support eliding parts of the URL at the beginning or the end.
	    // Eventually we may want to elide parts of the URL in the *middle*.  If so, this code will
	    // become more complicated.  We will probably want to create a regexp out of display URL,
	    // replacing every ellipsis with a ".*".
	    if (expandedUrl.indexOf(displayUrlSansEllipses) != -1) {
	      var displayUrlIndex = expandedUrl.indexOf(displayUrlSansEllipses);
	      var v = {
	        displayUrlSansEllipses: displayUrlSansEllipses,
	        // Portion of expandedUrl that precedes the displayUrl substring
	        beforeDisplayUrl: expandedUrl.substr(0, displayUrlIndex),
	        // Portion of expandedUrl that comes after displayUrl
	        afterDisplayUrl: expandedUrl.substr(displayUrlIndex + displayUrlSansEllipses.length),
	        precedingEllipsis: displayUrl.match(/^…/) ? "…" : "",
	        followingEllipsis: displayUrl.match(/…$/) ? "…" : ""
	      };
	      for (var k in v) {
	        if (v.hasOwnProperty(k)) {
	          v[k] = twttr.txt.htmlEscape(v[k]);
	        }
	      }
	      // As an example: The user tweets "hi http://longdomainname.com/foo"
	      // This gets shortened to "hi http://t.co/xyzabc", with display_url = "…nname.com/foo"
	      // This will get rendered as:
	      // <span class='tco-ellipsis'> <!-- This stuff should get displayed but not copied -->
	      //   …
	      //   <!-- There's a chance the onCopy event handler might not fire. In case that happens,
	      //        we include an &nbsp; here so that the … doesn't bump up against the URL and ruin it.
	      //        The &nbsp; is inside the tco-ellipsis span so that when the onCopy handler *does*
	      //        fire, it doesn't get copied.  Otherwise the copied text would have two spaces in a row,
	      //        e.g. "hi  http://longdomainname.com/foo".
	      //   <span style='font-size:0'>&nbsp;</span>
	      // </span>
	      // <span style='font-size:0'>  <!-- This stuff should get copied but not displayed -->
	      //   http://longdomai
	      // </span>
	      // <span class='js-display-url'> <!-- This stuff should get displayed *and* copied -->
	      //   nname.com/foo
	      // </span>
	      // <span class='tco-ellipsis'> <!-- This stuff should get displayed but not copied -->
	      //   <span style='font-size:0'>&nbsp;</span>
	      //   …
	      // </span>
	      v['invisible'] = options.invisibleTagAttrs;
	      return stringSupplant("<span class='tco-ellipsis'>#{precedingEllipsis}<span #{invisible}>&nbsp;</span></span><span #{invisible}>#{beforeDisplayUrl}</span><span class='js-display-url'>#{displayUrlSansEllipses}</span><span #{invisible}>#{afterDisplayUrl}</span><span class='tco-ellipsis'><span #{invisible}>&nbsp;</span>#{followingEllipsis}</span>", v);
	    }
	    return displayUrl;
	  };

	  twttr.txt.autoLinkEntities = function(text, entities, options) {
	    options = clone(options || {});

	    options.hashtagClass = options.hashtagClass || DEFAULT_HASHTAG_CLASS;
	    options.hashtagUrlBase = options.hashtagUrlBase || "https://twitter.com/#!/search?q=%23";
	    options.cashtagClass = options.cashtagClass || DEFAULT_CASHTAG_CLASS;
	    options.cashtagUrlBase = options.cashtagUrlBase || "https://twitter.com/#!/search?q=%24";
	    options.listClass = options.listClass || DEFAULT_LIST_CLASS;
	    options.usernameClass = options.usernameClass || DEFAULT_USERNAME_CLASS;
	    options.usernameUrlBase = options.usernameUrlBase || "https://twitter.com/";
	    options.listUrlBase = options.listUrlBase || "https://twitter.com/";
	    options.htmlAttrs = twttr.txt.extractHtmlAttrsFromOptions(options);
	    options.invisibleTagAttrs = options.invisibleTagAttrs || "style='position:absolute;left:-9999px;'";

	    // remap url entities to hash
	    var urlEntities, i, len;
	    if(options.urlEntities) {
	      urlEntities = {};
	      for(i = 0, len = options.urlEntities.length; i < len; i++) {
	        urlEntities[options.urlEntities[i].url] = options.urlEntities[i];
	      }
	      options.urlEntities = urlEntities;
	    }

	    var result = "";
	    var beginIndex = 0;

	    // sort entities by start index
	    entities.sort(function(a,b){ return a.indices[0] - b.indices[0]; });

	    var nonEntity = options.htmlEscapeNonEntities ? twttr.txt.htmlEscape : function(text) {
	      return text;
	    };

	    for (var i = 0; i < entities.length; i++) {
	      var entity = entities[i];
	      result += nonEntity(text.substring(beginIndex, entity.indices[0]));

	      if (entity.url) {
	        result += twttr.txt.linkToUrl(entity, text, options);
	      } else if (entity.hashtag) {
	        result += text;//twttr.txt.linkToHashtag(entity, text, options);
	      } else if (entity.screenName) {
	        result += text;//twttr.txt.linkToMentionAndList(entity, text, options);
	      } else if (entity.cashtag) {
	        result += text;//twttr.txt.linkToCashtag(entity, text, options);
	      }
	      beginIndex = entity.indices[1];
	    }
	    result += nonEntity(text.substring(beginIndex, text.length));
	    return result;
	  };

	  twttr.txt.autoLinkWithJSON = function(text, json, options) {
	    // map JSON entity to twitter-text entity
	    if (json.user_mentions) {
	      for (var i = 0; i < json.user_mentions.length; i++) {
	        // this is a @mention
	        json.user_mentions[i].screenName = json.user_mentions[i].screen_name;
	      }
	    }

	    if (json.hashtags) {
	      for (var i = 0; i < json.hashtags.length; i++) {
	        // this is a #hashtag
	        json.hashtags[i].hashtag = json.hashtags[i].text;
	      }
	    }

	    if (json.symbols) {
	      for (var i = 0; i < json.symbols.length; i++) {
	        // this is a $CASH tag
	        json.symbols[i].cashtag = json.symbols[i].text;
	      }
	    }

	    // concatenate all entities
	    var entities = [];
	    for (var key in json) {
	      entities = entities.concat(json[key]);
	    }

	    // modify indices to UTF-16
	    twttr.txt.modifyIndicesFromUnicodeToUTF16(text, entities);

	    return twttr.txt.autoLinkEntities(text, entities, options);
	  };

	  twttr.txt.extractHtmlAttrsFromOptions = function(options) {
	    var htmlAttrs = {};
	    for (var k in options) {
	      var v = options[k];
	      if (OPTIONS_NOT_ATTRIBUTES[k]) continue;
	      if (BOOLEAN_ATTRIBUTES[k]) {
	        v = v ? k : null;
	      }
	      if (v == null) continue;
	      htmlAttrs[k] = v;
	    }
	    return htmlAttrs;
	  };

	  twttr.txt.autoLink = function(text, options) {
	    var entities = twttr.txt.extractEntitiesWithIndices(text, {extractUrlsWithoutProtocol: false});
	    return twttr.txt.autoLinkEntities(text, entities, options);
	  };

	  twttr.txt.autoLinkUsernamesOrLists = function(text, options) {
	    var entities = twttr.txt.extractMentionsOrListsWithIndices(text);
	    return twttr.txt.autoLinkEntities(text, entities, options);
	  };

	  twttr.txt.autoLinkHashtags = function(text, options) {
	    var entities = twttr.txt.extractHashtagsWithIndices(text);
	    return twttr.txt.autoLinkEntities(text, entities, options);
	  };

	  twttr.txt.autoLinkCashtags = function(text, options) {
	    var entities = twttr.txt.extractCashtagsWithIndices(text);
	    return twttr.txt.autoLinkEntities(text, entities, options);
	  };

	  twttr.txt.autoLinkUrlsCustom = function(text, options) {
	    var entities = twttr.txt.extractUrlsWithIndices(text, {extractUrlsWithoutProtocol: false});
	    return twttr.txt.autoLinkEntities(text, entities, options);
	  };

	  twttr.txt.removeOverlappingEntities = function(entities) {
	    entities.sort(function(a,b){ return a.indices[0] - b.indices[0]; });

	    var prev = entities[0];
	    for (var i = 1; i < entities.length; i++) {
	      if (prev.indices[1] > entities[i].indices[0]) {
	        entities.splice(i, 1);
	        i--;
	      } else {
	        prev = entities[i];
	      }
	    }
	  };

	  twttr.txt.extractEntitiesWithIndices = function(text, options) {
	    var entities = twttr.txt.extractUrlsWithIndices(text, options)
	                    .concat(twttr.txt.extractMentionsOrListsWithIndices(text))
	                    .concat(twttr.txt.extractHashtagsWithIndices(text, {checkUrlOverlap: false}))
	                    .concat(twttr.txt.extractCashtagsWithIndices(text));

	    if (entities.length == 0) {
	      return [];
	    }

	    twttr.txt.removeOverlappingEntities(entities);
	    return entities;
	  };

	  twttr.txt.extractMentions = function(text) {
	    var screenNamesOnly = [],
	        screenNamesWithIndices = twttr.txt.extractMentionsWithIndices(text);

	    for (var i = 0; i < screenNamesWithIndices.length; i++) {
	      var screenName = screenNamesWithIndices[i].screenName;
	      screenNamesOnly.push(screenName);
	    }

	    return screenNamesOnly;
	  };

	  twttr.txt.extractMentionsWithIndices = function(text) {
	    var mentions = [],
	        mentionOrList,
	        mentionsOrLists = twttr.txt.extractMentionsOrListsWithIndices(text);

	    for (var i = 0 ; i < mentionsOrLists.length; i++) {
	      mentionOrList = mentionsOrLists[i];
	      if (mentionOrList.listSlug == '') {
	        mentions.push({
	          screenName: mentionOrList.screenName,
	          indices: mentionOrList.indices
	        });
	      }
	    }

	    return mentions;
	  };

	  /**
	   * Extract list or user mentions.
	   * (Presence of listSlug indicates a list)
	   */
	  twttr.txt.extractMentionsOrListsWithIndices = function(text) {
	    if (!text || !text.match(twttr.txt.regexen.atSigns)) {
	      return [];
	    }

	    var possibleNames = [],
	        slashListname;

	    text.replace(twttr.txt.regexen.validMentionOrList, function(match, before, atSign, screenName, slashListname, offset, chunk) {
	      var after = chunk.slice(offset + match.length);
	      if (!after.match(twttr.txt.regexen.endMentionMatch)) {
	        slashListname = slashListname || '';
	        var startPosition = offset + before.length;
	        var endPosition = startPosition + screenName.length + slashListname.length + 1;
	        possibleNames.push({
	          screenName: screenName,
	          listSlug: slashListname,
	          indices: [startPosition, endPosition]
	        });
	      }
	    });

	    return possibleNames;
	  };


	  twttr.txt.extractReplies = function(text) {
	    if (!text) {
	      return null;
	    }

	    var possibleScreenName = text.match(twttr.txt.regexen.validReply);
	    if (!possibleScreenName ||
	        RegExp.rightContext.match(twttr.txt.regexen.endMentionMatch)) {
	      return null;
	    }

	    return possibleScreenName[1];
	  };

	  twttr.txt.extractUrls = function(text, options) {
	    var urlsOnly = [],
	        urlsWithIndices = twttr.txt.extractUrlsWithIndices(text, options);

	    for (var i = 0; i < urlsWithIndices.length; i++) {
	      urlsOnly.push(urlsWithIndices[i].url);
	    }

	    return urlsOnly;
	  };

	  twttr.txt.extractUrlsWithIndices = function(text, options) {
	    if (!options) {
	      options = {extractUrlsWithoutProtocol: true};
	    }
	    if (!text || (options.extractUrlsWithoutProtocol ? !text.match(/\./) : !text.match(/:/))) {
	      return [];
	    }

	    var urls = [];

	    while (twttr.txt.regexen.extractUrl.exec(text)) {
	      var before = RegExp.$2, url = RegExp.$3, protocol = RegExp.$4, domain = RegExp.$5, path = RegExp.$7;
	      var endPosition = twttr.txt.regexen.extractUrl.lastIndex,
	          startPosition = endPosition - url.length;

	      // if protocol is missing and domain contains non-ASCII characters,
	      // extract ASCII-only domains.
	      if (!protocol) {
	        if (!options.extractUrlsWithoutProtocol
	            || before.match(twttr.txt.regexen.invalidUrlWithoutProtocolPrecedingChars)) {
	          continue;
	        }
	        var lastUrl = null,
	            asciiEndPosition = 0;
	        domain.replace(twttr.txt.regexen.validAsciiDomain, function(asciiDomain) {
	          var asciiStartPosition = domain.indexOf(asciiDomain, asciiEndPosition);
	          asciiEndPosition = asciiStartPosition + asciiDomain.length;
	          lastUrl = {
	            url: asciiDomain,
	            indices: [startPosition + asciiStartPosition, startPosition + asciiEndPosition]
	          };
	          if (path
	              || asciiDomain.match(twttr.txt.regexen.validSpecialShortDomain)
	              || !asciiDomain.match(twttr.txt.regexen.invalidShortDomain)) {
	            urls.push(lastUrl);
	          }
	        });

	        // no ASCII-only domain found. Skip the entire URL.
	        if (lastUrl == null) {
	          continue;
	        }

	        // lastUrl only contains domain. Need to add path and query if they exist.
	        if (path) {
	          lastUrl.url = url.replace(domain, lastUrl.url);
	          lastUrl.indices[1] = endPosition;
	        }
	      } else {
	        // In the case of t.co URLs, don't allow additional path characters.
	        if (url.match(twttr.txt.regexen.validTcoUrl)) {
	          url = RegExp.lastMatch;
	          endPosition = startPosition + url.length;
	        }
	        urls.push({
	          url: url,
	          indices: [startPosition, endPosition]
	        });
	      }
	    }

	    return urls;
	  };

	  twttr.txt.extractHashtags = function(text) {
	    var hashtagsOnly = [],
	        hashtagsWithIndices = twttr.txt.extractHashtagsWithIndices(text);

	    for (var i = 0; i < hashtagsWithIndices.length; i++) {
	      hashtagsOnly.push(hashtagsWithIndices[i].hashtag);
	    }

	    return hashtagsOnly;
	  };

	  twttr.txt.extractHashtagsWithIndices = function(text, options) {
	    if (!options) {
	      options = {checkUrlOverlap: true};
	    }

	    if (!text || !text.match(twttr.txt.regexen.hashSigns)) {
	      return [];
	    }

	    var tags = [];

	    text.replace(twttr.txt.regexen.validHashtag, function(match, before, hash, hashText, offset, chunk) {
	      var after = chunk.slice(offset + match.length);
	      if (after.match(twttr.txt.regexen.endHashtagMatch))
	        return;
	      var startPosition = offset + before.length;
	      var endPosition = startPosition + hashText.length + 1;
	      tags.push({
	        hashtag: hashText,
	        indices: [startPosition, endPosition]
	      });
	    });

	    if (options.checkUrlOverlap) {
	      // also extract URL entities
	      var urls = twttr.txt.extractUrlsWithIndices(text);
	      if (urls.length > 0) {
	        var entities = tags.concat(urls);
	        // remove overlap
	        twttr.txt.removeOverlappingEntities(entities);
	        // only push back hashtags
	        tags = [];
	        for (var i = 0; i < entities.length; i++) {
	          if (entities[i].hashtag) {
	            tags.push(entities[i]);
	          }
	        }
	      }
	    }

	    return tags;
	  };

	  twttr.txt.extractCashtags = function(text) {
	    var cashtagsOnly = [],
	        cashtagsWithIndices = twttr.txt.extractCashtagsWithIndices(text);

	    for (var i = 0; i < cashtagsWithIndices.length; i++) {
	      cashtagsOnly.push(cashtagsWithIndices[i].cashtag);
	    }

	    return cashtagsOnly;
	  };

	  twttr.txt.extractCashtagsWithIndices = function(text) {
	    if (!text || text.indexOf("$") == -1) {
	      return [];
	    }

	    var tags = [];

	    text.replace(twttr.txt.regexen.validCashtag, function(match, before, dollar, cashtag, offset, chunk) {
	      var startPosition = offset + before.length;
	      var endPosition = startPosition + cashtag.length + 1;
	      tags.push({
	        cashtag: cashtag,
	        indices: [startPosition, endPosition]
	      });
	    });

	    return tags;
	  };

	  twttr.txt.modifyIndicesFromUnicodeToUTF16 = function(text, entities) {
	    twttr.txt.convertUnicodeIndices(text, entities, false);
	  };

	  twttr.txt.modifyIndicesFromUTF16ToUnicode = function(text, entities) {
	    twttr.txt.convertUnicodeIndices(text, entities, true);
	  };

	  twttr.txt.getUnicodeTextLength = function(text) {
	    return text.replace(twttr.txt.regexen.non_bmp_code_pairs, ' ').length;
	  };

	  twttr.txt.convertUnicodeIndices = function(text, entities, indicesInUTF16) {
	    if (entities.length == 0) {
	      return;
	    }

	    var charIndex = 0;
	    var codePointIndex = 0;

	    // sort entities by start index
	    entities.sort(function(a,b){ return a.indices[0] - b.indices[0]; });
	    var entityIndex = 0;
	    var entity = entities[0];

	    while (charIndex < text.length) {
	      if (entity.indices[0] == (indicesInUTF16 ? charIndex : codePointIndex)) {
	        var len = entity.indices[1] - entity.indices[0];
	        entity.indices[0] = indicesInUTF16 ? codePointIndex : charIndex;
	        entity.indices[1] = entity.indices[0] + len;

	        entityIndex++;
	        if (entityIndex == entities.length) {
	          // no more entity
	          break;
	        }
	        entity = entities[entityIndex];
	      }

	      var c = text.charCodeAt(charIndex);
	      if (0xD800 <= c && c <= 0xDBFF && charIndex < text.length - 1) {
	        // Found high surrogate char
	        c = text.charCodeAt(charIndex + 1);
	        if (0xDC00 <= c && c <= 0xDFFF) {
	          // Found surrogate pair
	          charIndex++;
	        }
	      }
	      codePointIndex++;
	      charIndex++;
	    }
	  };

	  // this essentially does text.split(/<|>/)
	  // except that won't work in IE, where empty strings are ommitted
	  // so "<>".split(/<|>/) => [] in IE, but is ["", "", ""] in all others
	  // but "<<".split("<") => ["", "", ""]
	  twttr.txt.splitTags = function(text) {
	    var firstSplits = text.split("<"),
	        secondSplits,
	        allSplits = [],
	        split;

	    for (var i = 0; i < firstSplits.length; i += 1) {
	      split = firstSplits[i];
	      if (!split) {
	        allSplits.push("");
	      } else {
	        secondSplits = split.split(">");
	        for (var j = 0; j < secondSplits.length; j += 1) {
	          allSplits.push(secondSplits[j]);
	        }
	      }
	    }

	    return allSplits;
	  };

	  twttr.txt.hitHighlight = function(text, hits, options) {
	    var defaultHighlightTag = "em";

	    hits = hits || [];
	    options = options || {};

	    if (hits.length === 0) {
	      return text;
	    }

	    var tagName = options.tag || defaultHighlightTag,
	        tags = ["<" + tagName + ">", "</" + tagName + ">"],
	        chunks = twttr.txt.splitTags(text),
	        i,
	        j,
	        result = "",
	        chunkIndex = 0,
	        chunk = chunks[0],
	        prevChunksLen = 0,
	        chunkCursor = 0,
	        startInChunk = false,
	        chunkChars = chunk,
	        flatHits = [],
	        index,
	        hit,
	        tag,
	        placed,
	        hitSpot;

	    for (i = 0; i < hits.length; i += 1) {
	      for (j = 0; j < hits[i].length; j += 1) {
	        flatHits.push(hits[i][j]);
	      }
	    }

	    for (index = 0; index < flatHits.length; index += 1) {
	      hit = flatHits[index];
	      tag = tags[index % 2];
	      placed = false;

	      while (chunk != null && hit >= prevChunksLen + chunk.length) {
	        result += chunkChars.slice(chunkCursor);
	        if (startInChunk && hit === prevChunksLen + chunkChars.length) {
	          result += tag;
	          placed = true;
	        }

	        if (chunks[chunkIndex + 1]) {
	          result += "<" + chunks[chunkIndex + 1] + ">";
	        }

	        prevChunksLen += chunkChars.length;
	        chunkCursor = 0;
	        chunkIndex += 2;
	        chunk = chunks[chunkIndex];
	        chunkChars = chunk;
	        startInChunk = false;
	      }

	      if (!placed && chunk != null) {
	        hitSpot = hit - prevChunksLen;
	        result += chunkChars.slice(chunkCursor, hitSpot) + tag;
	        chunkCursor = hitSpot;
	        if (index % 2 === 0) {
	          startInChunk = true;
	        } else {
	          startInChunk = false;
	        }
	      } else if(!placed) {
	        placed = true;
	        result += tag;
	      }
	    }

	    if (chunk != null) {
	      if (chunkCursor < chunkChars.length) {
	        result += chunkChars.slice(chunkCursor);
	      }
	      for (index = chunkIndex + 1; index < chunks.length; index += 1) {
	        result += (index % 2 === 0 ? chunks[index] : "<" + chunks[index] + ">");
	      }
	    }

	    return result;
	  };

	  var MAX_LENGTH = 140;

	  // Returns the length of Tweet text with consideration to t.co URL replacement
	  // and chars outside the basic multilingual plane that use 2 UTF16 code points
	  twttr.txt.getTweetLength = function(text, options) {
	    if (!options) {
	      options = {
	          // These come from https://api.twitter.com/1/help/configuration.json
	          // described by https://dev.twitter.com/docs/api/1/get/help/configuration
	          short_url_length: 23,
	          short_url_length_https: 23
	      };
	    }
	    var textLength = twttr.txt.getUnicodeTextLength(text),
	        urlsWithIndices = twttr.txt.extractUrlsWithIndices(text);
	    twttr.txt.modifyIndicesFromUTF16ToUnicode(text, urlsWithIndices);

	    for (var i = 0; i < urlsWithIndices.length; i++) {
	      // Subtract the length of the original URL
	      textLength += urlsWithIndices[i].indices[0] - urlsWithIndices[i].indices[1];

	      // Add 23 characters for URL starting with https://
	      // http:// URLs still use https://t.co so they are 23 characters as well
	      if (urlsWithIndices[i].url.toLowerCase().match(twttr.txt.regexen.urlHasHttps)) {
	         textLength += options.short_url_length_https;
	      } else {
	        textLength += options.short_url_length;
	      }
	    }

	    return textLength;
	  };

	  // Check the text for any reason that it may not be valid as a Tweet. This is meant as a pre-validation
	  // before posting to api.twitter.com. There are several server-side reasons for Tweets to fail but this pre-validation
	  // will allow quicker feedback.
	  //
	  // Returns false if this text is valid. Otherwise one of the following strings will be returned:
	  //
	  //   "too_long": if the text is too long
	  //   "empty": if the text is nil or empty
	  //   "invalid_characters": if the text contains non-Unicode or any of the disallowed Unicode characters
	  twttr.txt.isInvalidTweet = function(text) {
	    if (!text) {
	      return "empty";
	    }

	    // Determine max length independent of URL length
	    if (twttr.txt.getTweetLength(text) > MAX_LENGTH) {
	      return "too_long";
	    }

	    if (twttr.txt.hasInvalidCharacters(text)) {
	      return "invalid_characters";
	    }

	    return false;
	  };

	  twttr.txt.hasInvalidCharacters = function(text) {
	    return twttr.txt.regexen.invalid_chars.test(text);
	  };

	  twttr.txt.isValidTweetText = function(text) {
	    return !twttr.txt.isInvalidTweet(text);
	  };

	  twttr.txt.isValidUsername = function(username) {
	    if (!username) {
	      return false;
	    }

	    var extracted = twttr.txt.extractMentions(username);

	    // Should extract the username minus the @ sign, hence the .slice(1)
	    return extracted.length === 1 && extracted[0] === username.slice(1);
	  };

	  var VALID_LIST_RE = regexSupplant(/^#{validMentionOrList}$/);

	  twttr.txt.isValidList = function(usernameList) {
	    var match = usernameList.match(VALID_LIST_RE);

	    // Must have matched and had nothing before or after
	    return !!(match && match[1] == "" && match[4]);
	  };

	  twttr.txt.isValidHashtag = function(hashtag) {
	    if (!hashtag) {
	      return false;
	    }

	    var extracted = twttr.txt.extractHashtags(hashtag);

	    // Should extract the hashtag minus the # sign, hence the .slice(1)
	    return extracted.length === 1 && extracted[0] === hashtag.slice(1);
	  };

	  twttr.txt.isValidUrl = function(url, unicodeDomains, requireProtocol) {
	    if (unicodeDomains == null) {
	      unicodeDomains = true;
	    }

	    if (requireProtocol == null) {
	      requireProtocol = true;
	    }

	    if (!url) {
	      return false;
	    }

	    var urlParts = url.match(twttr.txt.regexen.validateUrlUnencoded);

	    if (!urlParts || urlParts[0] !== url) {
	      return false;
	    }

	    var scheme = urlParts[1],
	        authority = urlParts[2],
	        path = urlParts[3],
	        query = urlParts[4],
	        fragment = urlParts[5];

	    if (!(
	      (!requireProtocol || (isValidMatch(scheme, twttr.txt.regexen.validateUrlScheme) && scheme.match(/^https?$/i))) &&
	      isValidMatch(path, twttr.txt.regexen.validateUrlPath) &&
	      isValidMatch(query, twttr.txt.regexen.validateUrlQuery, true) &&
	      isValidMatch(fragment, twttr.txt.regexen.validateUrlFragment, true)
	    )) {
	      return false;
	    }

	    return (unicodeDomains && isValidMatch(authority, twttr.txt.regexen.validateUrlUnicodeAuthority)) ||
	           (!unicodeDomains && isValidMatch(authority, twttr.txt.regexen.validateUrlAuthority));
	  };

	  function isValidMatch(string, regex, optional) {
	    if (!optional) {
	      // RegExp["$&"] is the text of the last match
	      // blank strings are ok, but are falsy, so we check stringiness instead of truthiness
	      return ((typeof string === "string") && string.match(regex) && RegExp["$&"] === string);
	    }

	    // RegExp["$&"] is the text of the last match
	    return (!string || (string.match(regex) && RegExp["$&"] === string));
	  }

	  if (typeof module != 'undefined' && module.exports) {
	    module.exports = twttr.txt;
	  }

	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_FACTORY__ = (twttr.txt), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  }

	  if (typeof window != 'undefined') {
	    if (window.twttr) {
	      for (var prop in twttr) {
	        window.twttr[prop] = twttr[prop];
	      }
	    } else {
	      window.twttr = twttr;
	    }
	  }
	})();


/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/* WEBPACK VAR INJECTION */(function(module) {'use strict';var _typeof2=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol?"symbol":typeof obj;};(function webpackUniversalModuleDefinition(root,factory){if(( false?'undefined':_typeof2(exports))==='object'&&( false?'undefined':_typeof2(module))==='object')module.exports=factory();else if(true)!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));else if((typeof exports==='undefined'?'undefined':_typeof2(exports))==='object')exports["PDFAnnotate"]=factory();else root["PDFAnnotate"]=factory();})(undefined,function(){return(/******/function(modules){// webpackBootstrap
	/******/// The module cache
	/******/var installedModules={};/******//******/// The require function
	/******/function __webpack_require__(moduleId){/******//******/// Check if module is in cache
	/******/if(installedModules[moduleId])/******/return installedModules[moduleId].exports;/******//******/// Create a new module (and put it into the cache)
	/******/var module=installedModules[moduleId]={/******/exports:{},/******/id:moduleId,/******/loaded:false/******/};/******//******/// Execute the module function
	/******/modules[moduleId].call(module.exports,module,module.exports,__webpack_require__);/******//******/// Flag the module as loaded
	/******/module.loaded=true;/******//******/// Return the exports of the module
	/******/return module.exports;/******/}/******//******//******/// expose the modules object (__webpack_modules__)
	/******/__webpack_require__.m=modules;/******//******/// expose the module cache
	/******/__webpack_require__.c=installedModules;/******//******/// __webpack_public_path__
	/******/__webpack_require__.p="";/******//******/// Load entry module and return exports
	/******/return __webpack_require__(0);/******/}(/************************************************************************//******/
    [/* 0 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            var _PDFJSAnnotate=__webpack_require__(1);
            var _PDFJSAnnotate2=_interopRequireDefault(_PDFJSAnnotate);
            function _interopRequireDefault(obj){
                return obj&&obj.__esModule?obj:{default:obj};
            }
            exports.default=_PDFJSAnnotate2.default;
            module.exports=exports['default'];/***/},
    /* 1 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            var _StoreAdapter=__webpack_require__(2);
            var _StoreAdapter2=_interopRequireDefault(_StoreAdapter);
            var _LocalStoreAdapter=__webpack_require__(8);
            var _LocalStoreAdapter2=_interopRequireDefault(_LocalStoreAdapter);
            var _render=__webpack_require__(10);
            var _render2=_interopRequireDefault(_render);
            var _UI=__webpack_require__(28);
            var _UI2=_interopRequireDefault(_UI);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            exports.default={/**
		   * Abstract class that needs to be defined so PDFJSAnnotate
		   * knows how to communicate with your server.
		   */StoreAdapter:_StoreAdapter2.default,/**
		   * Implementation of StoreAdapter that stores annotation data to localStorage.
		   */LocalStoreAdapter:_LocalStoreAdapter2.default,/**
		   * Abstract instance of StoreAdapter
		   */__storeAdapter:new _StoreAdapter2.default(),/**
		   * Getter for the underlying StoreAdapter property
		   *
		   * @return {StoreAdapter}
		   */getStoreAdapter:function getStoreAdapter(){return this.__storeAdapter;},/**
		   * Setter for the underlying StoreAdapter property
		   *
		   * @param {StoreAdapter} adapter The StoreAdapter implementation to be used.
		   */setStoreAdapter:function setStoreAdapter(adapter){// TODO this throws an error when bundled
	// if (!(adapter instanceof StoreAdapter)) {
	//   throw new Error('adapter must be an instance of StoreAdapter');
	// }
	this.__storeAdapter=adapter;},/**
		   * UI is a helper for instrumenting UI interactions for creating,
		   * editing, and deleting annotations in the browser.
		   */UI:_UI2.default,/**
		   * Render the annotations for a page in the PDF Document
		   *
		   * @param {SVGElement} svg The SVG element that annotations should be rendered to
		   * @param {PageViewport} viewport The PDFPage.getViewport data
		   * @param {Object} data The StoreAdapter.getAnnotations data
		   * @return {Promise}
		   */render:_render2.default,/**
		   * Convenience method for getting annotation data
		   *
		   * @alias StoreAdapter.getAnnotations
		   * @param {String} documentId The ID of the document
		   * @param {String} pageNumber The page number
		   * @return {Promise}
		   */getAnnotations:function getAnnotations(documentId,pageNumber){
                                         var _getStoreAdapter;
                                         return(_getStoreAdapter=this.getStoreAdapter()).getAnnotations.apply(_getStoreAdapter,arguments);
                                     }
                                 };
            module.exports=exports['default'];/***/},
    /* 2 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            var _createClass=function(){
                function defineProperties(target,props){
                    for(var i=0;i<props.length;i++){
                        var descriptor=props[i];
                        descriptor.enumerable=descriptor.enumerable||false;
                        descriptor.configurable=true;
                        if("value"in descriptor)
                            descriptor.writable=true;
                        Object.defineProperty(target,descriptor.key,descriptor);
                    }
                }
                return function(Constructor,protoProps,staticProps){
                    if(protoProps)defineProperties(Constructor.prototype,protoProps);
                    if(staticProps)defineProperties(Constructor,staticProps);
                    return Constructor;
                };
            }();
            var _abstractFunction=__webpack_require__(3);
            var _abstractFunction2=_interopRequireDefault(_abstractFunction);
            var _event=__webpack_require__(4);
            function _interopRequireDefault(obj){
                return obj&&obj.__esModule?obj:{default:obj};
                }
            function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor)){throw new TypeError("Cannot call a class as a function");}}
            // Adapter should never be invoked publicly
            var StoreAdapter=function(){/**
		   * Create a new StoreAdapter instance
		   *
		   * @param {Object} [definition] The definition to use for overriding abstract methods
		   */function StoreAdapter(){var _this=this;var definition=arguments.length<=0||arguments[0]===undefined?{}:arguments[0];_classCallCheck(this,StoreAdapter);// Copy each function from definition if it is a function we know about
	Object.keys(definition).forEach(function(key){if(typeof definition[key]==='function'&&typeof _this[key]==='function'){_this[key]=definition[key];}});}
                _createClass(StoreAdapter,[
                        
                      /**
                        * Get all the annotations for a given document and page number.
                        *
                        * @param {String} documentId The ID for the document the annotations belong to
                        * @param {Number} pageNumber The number of the page the annotations belong to
                        * @return {Promise}
                        */
                        {key:'__getAnnotations',value:function __getAnnotations(documentId,pageNumber){
                                (0,_abstractFunction2.default)('getAnnotations');
                            }
                        },
                       /**
                         * Get the definition for a specific annotation.
                         *
                         * @param {String} documentId The ID for the document the annotation belongs to
                         * @param {String} annotationId The ID for the annotation
                         * @return {Promise}
                         */
                        {key:'getAnnotation',value:function getAnnotation(documentId,annotationId){
                                (0,_abstractFunction2.default)('getAnnotation');
                            }
                        },
                       /**
                        * Add an annotation
                        *
                        * @param {String} documentId The ID for the document to add the annotation to
                        * @param {String} pageNumber The page number to add the annotation to
                        * @param {Object} annotation The definition for the new annotation
                        * @return {Promise}
                        */
                        {key:'__addAnnotation',value:function __addAnnotation(documentId,pageNumber,annotation){
                                (0,_abstractFunction2.default)('addAnnotation');
                            }
                        },
                       /**
                        * Edit an annotation
                        *
                        * @param {String} documentId The ID for the document
                        * @param {String} pageNumber the page number of the annotation
                        * @param {Object} annotation The definition of the modified annotation
                        * @return {Promise}
                        */
                        {key:'__editAnnotation',value:function __editAnnotation(documentId,page,annotationId,annotation){
                                (0,_abstractFunction2.default)('editAnnotation');
                            }
                        },
                        // Original:
//                        {key:'__editAnnotation',value:function __editAnnotation(documentId,pageNumber,annotation){
//                                (0,_abstractFunction2.default)('editAnnotation');
//                            }
//                        },
                       /**
                        * Delete an annotation
                        *
                        * @param {String} documentId The ID for the document
                        * @param {String} annotationId The ID for the annotation
                        * @return {Promise}
                        */
                        {key:'__deleteAnnotation',value:function __deleteAnnotation(documentId,annotationId){
                                (0,_abstractFunction2.default)('deleteAnnotation');
                            }
                        },
                       /**
                        * Get all the comments for an annotation
                        *
                        * @param {String} documentId The ID for the document
                        * @param {String} annotationId The ID for the annotation
                        * @return {Promise}
                        */
                        {key:'getComments',value:function getComments(documentId,annotationId){
                                (0,_abstractFunction2.default)('getComments');
                            }
                        },
                        
                        /**
                        * Get all the questions of one page
                        *
                        * @param {String} documentId The ID for the document
                        * @param {String} pageNumber The number of the requested page
                        * @return {Promise}
                        */
                        {key:'getQuestions',value:function getQuestions(documentId,pageNumber,pattern){
                                (0,_abstractFunction2.default)('getQuestions');
                            }
                        },
                        /**
                        * Get all the questions of one page
                        *
                        * @param {String} documentId The ID for the document
                        * @param {String} pageNumber The number of the requested page
                        * @return {Promise}
                        */
                        {key:'__getQuestions',value:function getQuestions(documentId,pageNumber,pattern){
                                (0,_abstractFunction2.default)('getQuestions');
                            }
                        },
                       /**
                        * Add a new comment
                        *
                        * @param {String} documentId The ID for the document
                        * @param {String} annotationId The ID for the annotation
                        * @param {Object} content The definition of the comment
                        * @return {Promise}
                        * 
                        */
                        {key:'__addComment',value:function __addComment(documentId,annotationId,content){
                                (0,_abstractFunction2.default)('addComment');
                            }
                        },
                       /**
                        * Report a new comment
                        *
                        * @param {String} documentId The ID for the document
                        * @param {String} commentId The id of the comment that is to be reported
                        * @param {String} reason for reporting the comment: 'inaccurate', 'inappropriate' or 'other'
                        * @param {Object} content The definition of the complaint
                        * @return {Promise}
                        * 
                        */
                        {key:'__reportComment',value:function reportComment(documentId,commentId,reason,content){
                                (0,_abstractFunction2.default)('reportComment');
                            }
                        },
                       /**
                         * Delete a comment
                         *
                         * @param {String} documentId The ID for the document
                         * @param {String} commentId The ID for the comment
                         * @return {Promise}
                         */
                        {key:'__deleteComment',value:function __deleteComment(documentId,commentId){
                                (0,_abstractFunction2.default)('deleteComment');
                            }
                        },
                        /**
                         * Hide a comment from participants view (i.e. display it as deleted)
                         * 
                         * @param {String} documentId The ID for the document
                         * @param {String} commentId The ID for the comment
                         * @return {Promise}
                         */
                        {key:'__hideComment',value:function __hideComment(documentId,commentId){
                                (0,_abstractFunction2.default)('hideComment');
                            }
                        },
                        /**
                         * Redisplay a comment for participants
                         * 
                         * @param {String} documentId The ID for the document
                         * @param {String} commentId The ID for the comment
                         * @return {Promise}
                         */
                        {key:'__redisplayComment',value:function __redisplayComment(documentId,commentId){
                                (0,_abstractFunction2.default)('redisplayComment');
                            }
                        },
                        /**
                         * Vote for a comment
                         * @param {String} documentId The ID for the document
                         * @param {String} commentId The ID for the comment
                         * @return {Promise}
                         */
                        {key:'__getInformation',value:function __getInformation(documentId,annotationId){
                                (0,_abstractFunction2.default)('getInformation');
                            }
                        },
                        
                        /**
                         * Vote for a comment
                         * @param {String} documentId The ID for the document
                         * @param {String} commentId The ID for the comment
                         * @return {Promise}
                         */
                        {key:'__voteComment',value:function __voteComment(documentId,commentId){
                                (0,_abstractFunction2.default)('voteComment');
                            }
                        },
                        
                        /**
                         * Edit a comment
                         * @param {String} documentId The ID for the document
                         * @param {String} commentId The ID for the comment
                         * @return {Promise}
                         */
                        {key:'__editComment',value:function __editComment(documentId,commentId){
                                (0,_abstractFunction2.default)('editComment');
                            }
                        },
                        
                        /**
                         * Subscribe to a question
                         * @param {String} documentId The ID for the document
                         * @param {String} commentId The ID for the comment
                         * @return {Promise}
                         */
                        {key:'__subscribeQuestion',value:function __subscribeQuestion(documentId,annotationId){
                                (0,_abstractFunction2.default)('subscribeQuestion');
                            }
                        },
                        
                        {key:'__unsubscribeQuestion',value:function __unsubscribeQuestion(documentId,annotationId){
                                (0,_abstractFunction2.default)('unsubscribeQuestion');
                            }
                        },
                        
                        {key:'__markSolved',value:function __markSolved(documentId,comment){
                                (0,_abstractFunction2.default)('markSolved');
                            }
                        },
                        
                        {key:'__getCommentsToPrint',value:function __getCommentsToPrint(documentId){
                                (0,_abstractFunction2.default)('getCommentsToPrint');
                            }
                        },
                                
                        {key:'getAnnotations',get:function get(){
                                return this.__getAnnotations;
                         },
                         set:function set(fn){
                             this.__getAnnotations=function getAnnotations(documentId,pageNumber){
                                 return fn.apply(undefined,arguments).then(function(annotations){// TODO may be best to have this happen on the server
                                    if(annotations.annotations){
                                        annotations.annotations.forEach(function(a){
                                        a.documentId=documentId;
                                    });
                                 }
                                 return annotations;
                             });};}},
                        {key:'addAnnotation',get:function get(){
                                return this.__addAnnotation;
                        },
                        set:function set(fn){this.__addAnnotation=function addAnnotation(documentId,pageNumber,annotation){return fn.apply(undefined,arguments).then(function(annotation){(0,_event.fireEvent)('annotation:add',documentId,pageNumber,annotation);return annotation;});};}},
                        {key:'editAnnotation',get:function get(){
                                return this.__editAnnotation;
                            },set:function set(fn){
                                this.__editAnnotation=function editAnnotation(documentId,page,annotationId,annotation){
                                    return fn.apply(undefined,arguments).then(function(annotation){
                                        (0,_event.fireEvent)('annotation:edit',documentId,annotationId,annotation);
                                        return annotation;
                                    });
                                };
                            }
                        },
                        {key:'deleteAnnotation',get:function get(){return this.__deleteAnnotation;},set:function set(fn){this.__deleteAnnotation=function deleteAnnotation(documentId,annotationId){return fn.apply(undefined,arguments).then(function(success){if(success){(0,_event.fireEvent)('annotation:delete',documentId,annotationId);}return success;});};}},
                        {key:'addComment',get:function get(){return this.__addComment;},set:function set(fn){this.__addComment=function addComment(documentId,annotationId,content){return fn.apply(undefined,arguments).then(function(comment){(0,_event.fireEvent)('comment:add',documentId,annotationId,comment);return comment;});};}},
                        {key:'reportComment',get:function get(){return this.__reportComment;},set:function set(fn){this.__reportComment=function reportComment(documentId,commentId,reason,content){return fn.apply(undefined,arguments).then(function(comment){(0,_event.fireEvent)('comment:report',documentId,commentId,reason,content);return comment;});};}},
                        {key:'deleteComment',get:function get(){return this.__deleteComment;},set:function set(fn){this.__deleteComment=function deleteComment(documentId,commentId){return fn.apply(undefined,arguments).then(function(success){if(success){(0,_event.fireEvent)('comment:delete',documentId,commentId);}return success;});};}},
                        {key:'hideComment',get:function get(){return this.__hideComment;},set:function set(fn){this.__hideComment=function hideComment(documentId,commentId){return fn.apply(undefined,arguments).then(function(success){if(success){(0,_event.fireEvent)('comment:hide',documentId,commentId);}return success;});};}},
                        {key:'redisplayComment',get:function get(){return this.__redisplayComment;},set:function set(fn){this.__redisplayComment=function redisplayComment(documentId,commentId){return fn.apply(undefined,arguments).then(function(success){if(success){(0,_event.fireEvent)('comment:redisplay',documentId,commentId);}return success;});};}},
                        {key:'getInformation',get:function get(){return this.__getInformation;},set:function set(fn){this.__getInformation=function getInformation(documentId,annotationId){return fn.apply(undefined,arguments).then(function(success){if(success){(0,_event.fireEvent)('annotation:getInformation',documentId,annotationId);}return success;});};}},
                        {key:'voteComment',get:function get(){return this.__voteComment;},set:function set(fn){this.__voteComment=function voteComment(documentId,commentId){return fn.apply(undefined,arguments).then(function(success){if(success){(0,_event.fireEvent)('comment:vote',documentId,commentId);}return success;});};}},
                        {key:'editComment',get:function get(){return this.__editComment;},set:function set(fn){this.__editComment=function editComment(documentId,commentId){return fn.apply(undefined,arguments).then(function(success){if(success){(0,_event.fireEvent)('comment:edit',documentId,commentId);}return success;});};}},
                        {key:'subscribeQuestion',get:function get(){return this.__subscribeQuestion;},set:function set(fn){this.__subscribeQuestion=function subscribeQuestion(documentId,annotationId){return fn.apply(undefined,arguments).then(function(success){if(success){(0,_event.fireEvent)('comment:subscribe',documentId,annotationId);}return success;});};}},
                        {key:'unsubscribeQuestion',get:function get(){return this.__unsubscribeQuestion;},set:function set(fn){this.__unsubscribeQuestion=function unsubscribeQuestion(documentId,annotationId){return fn.apply(undefined,arguments).then(function(success){if(success){(0,_event.fireEvent)('comment:unsubscribe',documentId,annotationId);}return success;});};}},
                        {key:'markSolved',get:function get(){return this.__markSolved;},set:function set(fn){this.__markSolved=function markSolved(documentId,comment){return fn.apply(undefined,arguments).then(function(success){if(success){(0,_event.fireEvent)('comment:markSolved',documentId,comment);}return success;});};}},
                        {key:'getCommentsToPrint',get:function get(){return this.__getCommentsToPrint;},set:function set(fn){this.__getCommentsToPrint=function getCommentsToPrint(documentId){return fn.apply(undefined,arguments).then(function(success){if(success){(0,_event.fireEvent)('document:printannotations',documentId);}return success;});};}}
                        
                    ]);return StoreAdapter;
                }(); //Ende StoreAdapter
                exports.default=StoreAdapter;
                module.exports=exports['default'];
                /***/},
    /* 3 */
    /***/function(module,exports){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=abstractFunction;/**
            * Throw an Error for an abstract function that hasn't been implemented.
            *
            * @param {String} name The name of the abstract function
            */
            function abstractFunction(name){throw new Error(name+' is not implemented');}
            module.exports=exports['default'];/***/
    },
    /* 4 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.fireEvent=fireEvent;
            exports.addEventListener=addEventListener;
            exports.removeEventListener=removeEventListener;
            exports.handleDocClick = handleDocumentClickFunction;
            var _events=__webpack_require__(5);
            var _events2=_interopRequireDefault(_events);
            var _utils=__webpack_require__(6);
            var _editoverlay = __webpack_require__(29);
            function _interopRequireDefault(obj){
                return obj&&obj.__esModule?obj:{default:obj};
            }
            var emitter=new _events2.default();
            var clickNode=void 0;
            var count = 0;
        
            /**
             * This function handles the document click. It looks for annotations under the click point.
             * If there are more than one annotation, a modal window pops up and the user can select, which one he/she wanted to click.
             * @param {type} e the event object of the click
             * @param {type} commid 
             * @returns {undefined}
             */
            function handleDocumentClickFunction(e,commid = null){
                let tar = $('#' + e.target.id);
                if (tar.hasClass('moodle-dialogue') || tar.parents('.moodle-dialogue').length > 0) {
                    return; //Dialog (for example from atto-editor) was clicked.
                }
                //the last parameter is true to get an array instead of the first annotation found.
                var target=(0,_utils.findAnnotationAtPoint)(e.clientX,e.clientY,true);

                if (target != null && Object.prototype.toString.call( target ) === '[object Array]' && target.length>1) {
                    //creats a modal window to select which one of the overlapping annotation should be selected.
                    var modal = document.createElement('div');
                    modal.id= "myModal";
                    modal.className = "modal hide fade";
                    modal.setAttribute('tabindex', -1);
                    modal.setAttribute('role', "dialog");
                    modal.setAttribute('aria-labelledby', "myModalLabel");
                    modal.setAttribute('aria-hidden', "true");
                    var modaldialog = document.createElement('div');
                    modaldialog.className = "modal-dialog";
                    var modalcontent = document.createElement('div');
                    modalcontent.className = "modal-content";
                    var modalheader = document.createElement('div');
                    modalheader.className = "modal-header";
                    var headerClose = document.createElement('button');
                    headerClose.setAttribute('type', "button");
                    headerClose.className = "close";
                    headerClose.setAttribute('data-dismiss', "modal");
                    headerClose.setAttribute('aria-hidden', "true");
                    headerClose.innerHTML = "x";
                    
                    headerClose.addEventListener("click",function(){   
                                $('body').removeClass('modal-open');
                                $('#myModal').remove();
                            });
                            
                    var headertitle = document.createElement('h3');
                    headertitle.id = "myModalLabel";
                    headertitle.innerHTML = M.util.get_string('decision','pdfannotator');
                    headertitle.style.display = "inline-block";
                    modalheader.appendChild(headertitle);
                    modalheader.appendChild(headerClose);   


                    var modalbody = document.createElement('div');
                    modalbody.className = "modal-body";
                    var bodytext = document.createElement('p');
                    bodytext.innerHTML = M.util.get_string('decision:overlappingAnnotation','pdfannotator');
                    modalbody.appendChild(bodytext);

                    modalcontent.appendChild(modalheader);
                    modalcontent.appendChild(modalbody);
                    
                    modaldialog.appendChild(modalcontent);
                    modal.appendChild(modaldialog);

                    $('#body-wrapper').append(modal);
                    $('#myModal').modal({backdrop:false});
                    for(var i=0;i<target.length;i++){
                        (function(innerI){
                            var elemUse = document.createElement('button');
                            var elemType = target[innerI].getAttribute('data-pdf-annotate-type');
                            var elemImg = document.createElement('img');
                            elemImg.setAttribute('style','pointer-events:none;');
                            switch(elemType){
                                case 'point':
                                    elemImg.alt = M.util.get_string('point','pdfannotator');
                                    elemImg.title = M.util.get_string('point','pdfannotator');
                                    elemImg.src = M.util.image_url('pinbild','pdfannotator');
                                    break;
                                case 'area':
                                    elemImg.alt = M.util.get_string('rectangle','pdfannotator');
                                    elemImg.title = M.util.get_string('rectangle','pdfannotator');
                                    elemImg.src = M.util.image_url('i/completion-manual-n','core');
                                    break;
                                case 'highlight':
                                    elemImg.alt = M.util.get_string('highlight','pdfannotator');
                                    elemImg.title = M.util.get_string('highlight','pdfannotator');
                                    elemImg.src = M.util.image_url('text_highlight_picker','pdfannotator');
                                    break;
                                case 'strikeout':
                                    elemImg.alt = M.util.get_string('strikeout','pdfannotator');
                                    elemImg.title = M.util.get_string('strikeout','pdfannotator');
                                    elemImg.src = M.util.image_url('strikethrough','pdfannotator');
                                    break;
                                case 'textbox':
                                    elemImg.alt = M.util.get_string('textbox','pdfannotator');
                                    elemImg.title = M.util.get_string('textbox','pdfannotator');
                                    elemImg.src = M.util.image_url('text_color_picker','pdfannotator');
                                    break;
                                case 'drawing':
                                    elemImg.alt = M.util.get_string('drawing','pdfannotator');
                                    elemImg.title = M.util.get_string('drawing','pdfannotator');
                                    elemImg.src = M.util.image_url('editstring','pdfannotator');
                                    break;
                                case 'default':
                                    elemImg.alt = 'undefined';
                                    elemImg.title = 'undefined';
                                    elemImg.src = '';   
                            }
                            
                            elemUse.appendChild(elemImg);
                            
                            elemUse.addEventListener("click",function(){
                                // Emit annotation:blur if clickNode is no longer clicked
                                if(clickNode&&clickNode!==target[innerI]){
                                    emitter.emit('annotation:blur',clickNode);
                                }// Emit annotation:click if target was clicked
                                if(target[innerI]){
                                    emitter.emit('annotation:click',target[innerI]);
                                }
                                clickNode=target[innerI];
                                $('body').removeClass('modal-open');
                                $('#myModal').remove();
                            });
                            elemUse.addEventListener("mouseover",function(){
                                _editoverlay.createEditOverlay(target[innerI]);
                            });
                            elemUse.addEventListener("mouseout",function(){
                                _editoverlay.destroyEditOverlay(target[innerI]);
                            });
                            modalbody.appendChild(elemUse);
                        })(i);
                    }
                }else{
                    // Emit annotation:blur if clickNode is no longer clicked, but not if another node is clicked.
                    if(clickNode && !target){
                        emitter.emit('annotation:blur',clickNode);
                    }
                    // Emit annotation:click if target was clicked.
                    if(target){
                        if(commid !== null){
                            target.markCommentid = commid; 
                        }
                        emitter.emit('annotation:click',target);
                    }
                    clickNode=target;
                }

                return;
            }
            
            document.addEventListener('click',function handleDocumentClick(e){
                //R: The ClickEvent should only be happen, if the cursor is selected.
                if(document.querySelector('.cursor').className.indexOf('active') === -1){
                    return;
                }
                //if the click is in atto editor nothing should happen.
                var editorNodes = document.querySelectorAll('div.editor_atto_wrap')[0];
                var clickedElement;
                if(e.target.id) {
                    clickedElement = '#' + e.target.id;
                } else if(e.target.className[0]) {
                    clickedElement = '.' + e.target.className;
                } else {
                    clickedElement = '';
                }
                if(clickedElement && editorNodes && editorNodes.querySelector(clickedElement)) {
                    return;
                }
                //If moodle Modal beeing clicked.
                var modal = document.querySelectorAll('.modal.show')[0];
                if(modal) {
                    if(clickedElement && modal.querySelector(clickedElement)) {
                        return;
                    }
                }

                //If Modal Dialogue beeing clicked.
                var clickedMoodleDialogue = e.target.closest('.moodle-dialogue-base');
                if(clickedMoodleDialogue) {
                    return;
                }

                //if the click is on an input field or link or icon in editor toolbar ('I') nothing should happen. 
                if(e.target.tagName === 'INPUT' || e.target.tagName === 'A' || e.target.tagName === 'SELECT' || e.target.tagName === 'I' || e.target.tagName === "BUTTON"){
                    return;
                }
                //R: if the click is on the Commentlist nothing should happen.
                if(((typeof e.target.getAttribute('id')!='string') && e.target.id.indexOf('comment') !== -1) || e.target.className.indexOf('comment') !== -1 || e.target.parentNode.className.indexOf('comment') !== -1 || e.target.parentNode.className.indexOf('chat') !== -1){
                    return;
                }
                if(!(0,_utils.findSVGAtPoint)(e.clientX,e.clientY,true)){
                    return;
                }
                
                handleDocumentClickFunction(e);
                
            });

            function fireEvent(){
                if(arguments[0] === 'annotation:click'){
                    clickNode = arguments[1];
                }
                emitter.emit.apply(emitter,arguments);
            };
            function addEventListener(){
                emitter.on.apply(emitter,arguments);
            };
            function removeEventListener(){
                emitter.removeListener.apply(emitter,arguments);
            };
    /***/},
    /* 5 */
    /***/function(module,exports){// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	function EventEmitter(){this._events=this._events||{};this._maxListeners=this._maxListeners||undefined;}
        module.exports=EventEmitter;// Backwards-compat with node 0.10.x
	EventEmitter.EventEmitter=EventEmitter;
        EventEmitter.prototype._events=undefined;
        EventEmitter.prototype._maxListeners=undefined;
        // By default EventEmitters will print a warning if more than 10 listeners are
	// added to it. This is a useful default which helps finding memory leaks.
	EventEmitter.defaultMaxListeners=10;
        // Obviously not all Emitters should be limited to 10. This function allows
	// that to be increased. Set to zero for unlimited.
	EventEmitter.prototype.setMaxListeners=function(n){if(!isNumber(n)||n<0||isNaN(n))throw TypeError('n must be a positive number');this._maxListeners=n;return this;};
        EventEmitter.prototype.emit=function(type){
            var er,handler,len,args,i,listeners;
            if(!this._events)this._events={};// If there is no 'error' event listener then throw.
            if(type==='error'){if(!this._events.error||isObject(this._events.error)&&!this._events.error.length){er=arguments[1];if(er instanceof Error){throw er;// Unhandled 'error' event
                }throw TypeError('Uncaught, unspecified "error" event.');}}
            handler=this._events[type];
            if(isUndefined(handler))return false;
            if(isFunction(handler)){switch(arguments.length){// fast cases
                case 1:handler.call(this);break;case 2:handler.call(this,arguments[1]);break;case 3:handler.call(this,arguments[1],arguments[2]);break;// slower
                default:args=Array.prototype.slice.call(arguments,1);handler.apply(this,args);}
            }else if(isObject(handler)){args=Array.prototype.slice.call(arguments,1);listeners=handler.slice();len=listeners.length;for(i=0;i<len;i++){listeners[i].apply(this,args);}}
            return true;
        };
        EventEmitter.prototype.addListener=function(type,listener){var m;if(!isFunction(listener))throw TypeError('listener must be a function');if(!this._events)this._events={};// To avoid recursion in the case that type === "newListener"! Before
            // adding it to the listeners, first emit "newListener".
            if(this._events.newListener)this.emit('newListener',type,isFunction(listener.listener)?listener.listener:listener);if(!this._events[type])// Optimize the case of one listener. Don't need the extra array object.
            this._events[type]=listener;else if(isObject(this._events[type]))// If we've already got an array, just append.
            this._events[type].push(listener);else// Adding the second element, need to change to array.
            this._events[type]=[this._events[type],listener];// Check for listener leak
            if(isObject(this._events[type])&&!this._events[type].warned){if(!isUndefined(this._maxListeners)){m=this._maxListeners;}else{m=EventEmitter.defaultMaxListeners;}if(m&&m>0&&this._events[type].length>m){this._events[type].warned=true;console.error('(node) warning: possible EventEmitter memory '+'leak detected. %d listeners added. '+'Use emitter.setMaxListeners() to increase limit.',this._events[type].length);if(typeof console.trace==='function'){// not supported in IE 10
            console.trace();}}}return this;
        };
        EventEmitter.prototype.on=EventEmitter.prototype.addListener;
        EventEmitter.prototype.once=function(type,listener){if(!isFunction(listener))throw TypeError('listener must be a function');var fired=false;function g(){this.removeListener(type,g);if(!fired){fired=true;listener.apply(this,arguments);}}g.listener=listener;this.on(type,g);return this;};
        // emits a 'removeListener' event iff the listener was removed
	EventEmitter.prototype.removeListener=function(type,listener){var list,position,length,i;if(!isFunction(listener))throw TypeError('listener must be a function');if(!this._events||!this._events[type])return this;list=this._events[type];length=list.length;position=-1;if(list===listener||isFunction(list.listener)&&list.listener===listener){delete this._events[type];if(this._events.removeListener)this.emit('removeListener',type,listener);}else if(isObject(list)){for(i=length;i-->0;){if(list[i]===listener||list[i].listener&&list[i].listener===listener){position=i;break;}}if(position<0)return this;if(list.length===1){list.length=0;delete this._events[type];}else{list.splice(position,1);}if(this._events.removeListener)this.emit('removeListener',type,listener);}return this;};
        EventEmitter.prototype.removeAllListeners=function(type){var key,listeners;if(!this._events)return this;// not listening for removeListener, no need to emit
            if(!this._events.removeListener){if(arguments.length===0)this._events={};else if(this._events[type])delete this._events[type];return this;}// emit removeListener for all listeners on all events
            if(arguments.length===0){for(key in this._events){if(key==='removeListener')continue;this.removeAllListeners(key);}this.removeAllListeners('removeListener');this._events={};return this;}listeners=this._events[type];if(isFunction(listeners)){this.removeListener(type,listeners);}else if(listeners){// LIFO order
            while(listeners.length){this.removeListener(type,listeners[listeners.length-1]);}}delete this._events[type];return this;};
        EventEmitter.prototype.listeners=function(type){var ret;if(!this._events||!this._events[type])ret=[];else if(isFunction(this._events[type]))ret=[this._events[type]];else ret=this._events[type].slice();return ret;};
        EventEmitter.prototype.listenerCount=function(type){if(this._events){var evlistener=this._events[type];if(isFunction(evlistener))return 1;else if(evlistener)return evlistener.length;}return 0;};
        EventEmitter.listenerCount=function(emitter,type){return emitter.listenerCount(type);};
        function isFunction(arg){return typeof arg==='function';}
        function isNumber(arg){return typeof arg==='number';}
        function isObject(arg){return(typeof arg==='undefined'?'undefined':_typeof2(arg))==='object'&&arg!==null;}
        function isUndefined(arg){return arg===void 0;}
    /***/},
    /* 6 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.BORDER_COLOR=undefined;
            exports.findSVGContainer=findSVGContainer;
            exports.findSVGAtPoint=findSVGAtPoint;
            exports.findAnnotationAtPoint=findAnnotationAtPoint;
            exports.pointIntersectsRect=pointIntersectsRect;
            exports.getOffsetAnnotationRect=getOffsetAnnotationRect;
            exports.getAnnotationRect=getAnnotationRect;
            exports.scaleUp=scaleUp;
            exports.scaleDown=scaleDown;
            exports.getScroll=getScroll;
            exports.getOffset=getOffset;
            exports.disableUserSelect=disableUserSelect;
            exports.enableUserSelect=enableUserSelect;
            exports.getMetadata=getMetadata;
            //R: Function to round digits
            exports.roundDigits = roundDigits;
            var _createStylesheet=__webpack_require__(7);
            var _createStylesheet2=_interopRequireDefault(_createStylesheet);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            var BORDER_COLOR=exports.BORDER_COLOR='#00BFFF';
            var userSelectStyleSheet=(0,_createStylesheet2.default)({body:{'-webkit-user-select':'none','-moz-user-select':'none','-ms-user-select':'none','user-select':'none'}});
            userSelectStyleSheet.setAttribute('data-pdf-annotate-user-select','true');
            /**
            * Find the SVGElement that contains all the annotations for a page
            *
            * @param {Element} node An annotation within that container
            * @return {SVGElement} The container SVG or null if it can't be found
            */function findSVGContainer(node){
                var parentNode=node;
                while((parentNode=parentNode.parentNode)&&parentNode!==document){
                    if(parentNode.nodeName.toUpperCase()==='SVG'&&parentNode.getAttribute('data-pdf-annotate-container')==='true'){
                        return parentNode;
                    }
                }
                return null;
            }/**
            * Find an SVGElement container at a given point
            *
            * @param {Number} x The x coordinate of the point
            * @param {Number} y The y coordinate of the point
            * @param {Boolean} array If the return value should be an array or a single svg object.
            * @return {SVGElement} The container SVG or null if one can't be found. If more than one and array=true the return value is an array of SVGs.
            */function findSVGAtPoint(x,y,array){
                var elements=document.querySelectorAll('svg[data-pdf-annotate-container="true"]');
                if(array){
                    var ret = [];
                    //end R
                    for(var i=0,l=elements.length;i<l;i++){
                        var el=elements[i];
                        var rect=el.getBoundingClientRect();
                        if(pointIntersectsRect(x,y,rect)){
                            ret.push(el);
                        }
                    }
                    if(ret.length >0){
                        return ret;
                    }
                }else{
                    for(var i=0,l=elements.length;i<l;i++){
                        var el=elements[i];
                        var rect=el.getBoundingClientRect();
                        if(pointIntersectsRect(x,y,rect)){
                            return el;
                        }
                    } 
                }
                return null;
            }
            /**
             * Find an Element that represents an annotation at a given point
             *
             * @param {Number} x The x coordinate of the point
             * @param {Number} y The y coordinate of the point
             * @param {Boolean} array If the return value should be an array or a single svg object.
             * @return {Element} The annotation element or null if one can't be found. If array=true an array with annotations elements.
             */function findAnnotationAtPoint(x,y, array){
                var svg=findSVGAtPoint(x,y,array);
                if(!svg){
                    return;
                }
                if(array){
                    var elements=[];
                    for(var j=0;j<svg.length;j++){
                        elements.push(svg[j].querySelectorAll('[data-pdf-annotate-type]'));
                    }// Find a target element within SVG
                    var ret = [];
                    for(var i=0,l=elements.length;i<l;i++){
                        for(var inner = 0;inner < elements[i].length;inner++){
                           var el=elements[i][inner];
                            if(pointIntersectsRect(x,y,getOffsetAnnotationRect(el))){
                                ret.push(el);   
                            } 
                        }
                    }
                    if(ret.length === 1){
                        return ret[0];
                    }
                    if(ret.length>0){
                        return ret;
                    }
                }else{
                    elements = svg.querySelectorAll('[data-pdf-annotate-type]');
                    for(var i=0,l=elements.length;i<l;i++){
                        var el=elements[i][inner];
                        if(pointIntersectsRect(x,y,getOffsetAnnotationRect(el))){
                            return el;
                        } 
                    }
                }
                return null;
            }
                    
            /**
             * Determine if a point intersects a rect
             *
             * @param {Number} x The x coordinate of the point
             * @param {Number} y The y coordinate of the point
             * @param {Object} rect The points of a rect (likely from getBoundingClientRect)
             * @return {Boolean} True if a collision occurs, otherwise false
             */function pointIntersectsRect(x,y,rect){return y>=rect.top&&y<=rect.bottom&&x>=rect.left&&x<=rect.right;}/**
             * Get the rect of an annotation element accounting for offset.
             *
             * @param {Element} el The element to get the rect of
             * @return {Object} The dimensions of the element
             */function getOffsetAnnotationRect(el){
                    var rect=getAnnotationRect(el);
                    var _getOffset=getOffset(el);
                    var offsetLeft=_getOffset.offsetLeft;
                    var offsetTop=_getOffset.offsetTop;
                    return  {
                        top:rect.top+offsetTop,
                        left:rect.left+offsetLeft,
                        right:rect.right+offsetLeft,
                        bottom:rect.bottom+offsetTop
                    };
                }/**
             * Get the rect of an annotation element.
             *
             * @param {Element} el The element to get the rect of
             * @return {Object} The dimensions of the element
             */function getAnnotationRect(el){
                    var h=0,w=0,x=0,y=0;
                    var rect=el.getBoundingClientRect();
                    // TODO this should be calculated somehow
                    var LINE_OFFSET=16;
                    var isFirefox = /firefox/i.test(navigator.userAgent);
                    switch(el.nodeName.toLowerCase()){
                        case'path':
                            var minX=void 0,maxX=void 0,minY=void 0,maxY=void 0;
                            el.getAttribute('d').replace(/Z/,'').split('M').splice(1).forEach(function(p){var s=p.split(' ').map(function(i){return parseInt(i,10);});if(typeof minX==='undefined'||s[0]<minX){minX=s[0];}if(typeof maxX==='undefined'||s[2]>maxX){maxX=s[2];}if(typeof minY==='undefined'||s[1]<minY){minY=s[1];}if(typeof maxY==='undefined'||s[3]>maxY){maxY=s[3];}});
                            h=maxY-minY;
                            w=maxX-minX;
                            x=minX;
                            y=minY;
                            break;
                        case'line':
                            h=parseInt(el.getAttribute('y2'),10)-parseInt(el.getAttribute('y1'),10);
                            w=parseInt(el.getAttribute('x2'),10)-parseInt(el.getAttribute('x1'),10);
                            x=parseInt(el.getAttribute('x1'),10);
                            y=parseInt(el.getAttribute('y1'),10);
                            if(h===0){
                                h+=LINE_OFFSET;
                                y-=LINE_OFFSET/2;
                            }
                            break;
                        case'text':
                            h=rect.height;
                            w=rect.width;
                            x=parseInt(el.getAttribute('x'),10);
                            y=parseInt(el.getAttribute('y'),10)-h;
                            break;
                        case'g':
                            var _getOffset2=getOffset(el);
                            var offsetLeft=_getOffset2.offsetLeft;
                            var offsetTop=_getOffset2.offsetTop;
                            h=rect.height;
                            w=rect.width;
                            x=rect.left-offsetLeft;
                            y=rect.top-offsetTop;
                            if(el.getAttribute('data-pdf-annotate-type')==='strikeout'){
                                h+=LINE_OFFSET;
                                y-=LINE_OFFSET/2;
                            }
                            break;
                        case'rect':
                        case'svg':
                            h=parseInt(el.getAttribute('height'),10);
                            w=parseInt(el.getAttribute('width'),10);
                            x=parseInt(el.getAttribute('x'),10);
                            y=parseInt(el.getAttribute('y'),10);
                            break;
                    }// Result provides same properties as getBoundingClientRect
                    var result={top:y,left:x,width:w,height:h,right:x+w,bottom:y+h};// For the case of nested SVG (point annotations) and grouped
                    // lines or rects no adjustment needs to be made for scale.
                    // I assume that the scale is already being handled
                    // natively by virtue of the `transform` attribute.
                    if(!['svg','g'].includes(el.nodeName.toLowerCase())){
                        result=scaleUp(findSVGAtPoint(rect.left,rect.top),result);
                    }
                    // FF scales nativly and uses always the 100%-Attributes, so the svg has to be scaled up to proof, if it is on the same position.
                    if(isFirefox && ['svg'].includes(el.nodeName.toLowerCase())){
                        var svgTMP;
                        if((svgTMP = findSVGAtPoint(rect.left,rect.top)) !== null){
                            result=scaleUp(svgTMP,result);
                        }
                    }
                    return result;
                }
            /**
             * Adjust scale from normalized scale (100%) to rendered scale.
             *
             * @param {SVGElement} svg The SVG to gather metadata from
             * @param {Object} rect A map of numeric values to scale
             * @return {Object} A copy of `rect` with values scaled up
             */function scaleUp(svg,rect){
                if(svg === null){
                    return rect;
                }
                var result={};
                var _getMetadata=getMetadata(svg);
                var viewport=_getMetadata.viewport;
                Object.keys(rect).forEach(function(key){result[key]=rect[key]*viewport.scale;});
                return result;
            }/**
             * Adjust scale from rendered scale to a normalized scale (100%).
             *
             * @param {SVGElement} svg The SVG to gather metadata from
             * @param {Object} rect A map of numeric values to scale
             * @return {Object} A copy of `rect` with values scaled down
             */function scaleDown(svg,rect){var result={};var _getMetadata2=getMetadata(svg);var viewport=_getMetadata2.viewport;Object.keys(rect).forEach(function(key){result[key]=rect[key]/viewport.scale;});return result;}/**
             * Get the scroll position of an element, accounting for parent elements
             *
             * @param {Element} el The element to get the scroll position for
             * @return {Object} The scrollTop and scrollLeft position
             */function getScroll(el){var scrollTop=0;var scrollLeft=0;var parentNode=el;while((parentNode=parentNode.parentNode)&&parentNode!==document){scrollTop+=parentNode.scrollTop;scrollLeft+=parentNode.scrollLeft;}return{scrollTop:scrollTop,scrollLeft:scrollLeft};}/**
             * Get the offset position of an element, accounting for parent elements
             *
             * @param {Element} el The element to get the offset position for
             * @return {Object} The offsetTop and offsetLeft position
             */function getOffset(el){var parentNode=el;while((parentNode=parentNode.parentNode)&&parentNode!==document){if(parentNode.nodeName.toUpperCase()==='SVG'){break;}}var rect=parentNode.getBoundingClientRect();return{offsetLeft:rect.left,offsetTop:rect.top};}/**
             * Disable user ability to select text on page
             */function disableUserSelect(){if(!userSelectStyleSheet.parentNode){document.head.appendChild(userSelectStyleSheet);}}/**
             * Enable user ability to select text on page
             */function enableUserSelect(){if(userSelectStyleSheet.parentNode){userSelectStyleSheet.parentNode.removeChild(userSelectStyleSheet);}}/**
            * Get the metadata for a SVG container
            *
            * @param {SVGElement} svg The SVG container to get metadata for
            */function getMetadata(svg){return{documentId:svg.getAttribute('data-pdf-annotate-document'),pageNumber:parseInt(svg.getAttribute('data-pdf-annotate-page'),10),viewport:JSON.parse(svg.getAttribute('data-pdf-annotate-viewport'))};}
           
           /*
            * This function rounds a digit 
            * @param {type} num digit, which should be rounded
            * @param {type} places 
            * @return {undefined}
            */
            function roundDigits(num, places){ 
                return +(Math.round(num + "e+" + places)  + "e-" + places);
            }
    /***/},
    /* 7 */
    /***/function(module,exports){
            module.exports=function createStyleSheet(blocks){var style=document.createElement('style');var text=Object.keys(blocks).map(function(selector){return processRuleSet(selector,blocks[selector]);}).join('\n');style.setAttribute('type','text/css');style.appendChild(document.createTextNode(text));return style;};
            function processRuleSet(selector,block){return selector+' {\n'+processDeclarationBlock(block)+'\n}';}
            function processDeclarationBlock(block){return Object.keys(block).map(function(prop){return processDeclaration(prop,block[prop]);}).join('\n');}
            function processDeclaration(prop,value){if(!isNaN(value)&&value!=0){value=value+'px';}return hyphenate(prop)+': '+value+';';}
            function hyphenate(prop){return prop.replace(/[A-Z]/g,function(match){return'-'+match.toLowerCase();});}
    /***/},
    /* 8 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            var _uuid=__webpack_require__(9);
            var _uuid2=_interopRequireDefault(_uuid);
            var _StoreAdapter2=__webpack_require__(2);
            var _StoreAdapter3=_interopRequireDefault(_StoreAdapter2);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor)){throw new TypeError("Cannot call a class as a function");}}
            function _possibleConstructorReturn(self,call){if(!self){throw new ReferenceError("this hasn't been initialised - super() hasn't been called");}return call&&((typeof call==='undefined'?'undefined':_typeof2(call))==="object"||typeof call==="function")?call:self;}
            function _inherits(subClass,superClass){if(typeof superClass!=="function"&&superClass!==null){throw new TypeError("Super expression must either be null or a function, not "+(typeof superClass==='undefined'?'undefined':_typeof2(superClass)));}subClass.prototype=Object.create(superClass&&superClass.prototype,{constructor:{value:subClass,enumerable:false,writable:true,configurable:true}});if(superClass)Object.setPrototypeOf?Object.setPrototypeOf(subClass,superClass):subClass.__proto__=superClass;}
            // StoreAdapter for working with localStorage
            // This is ideal for testing, examples, and prototyping
            var LocalStoreAdapter=function(_StoreAdapter){_inherits(LocalStoreAdapter,_StoreAdapter);function LocalStoreAdapter(){_classCallCheck(this,LocalStoreAdapter);return _possibleConstructorReturn(this,Object.getPrototypeOf(LocalStoreAdapter).call(this,{getAnnotations:function getAnnotations(documentId,pageNumber){return new Promise(function(resolve,reject){var annotations=_getAnnotations(documentId).filter(function(i){return i.page===pageNumber&&i.class==='Annotation';});resolve({documentId:documentId,pageNumber:pageNumber,annotations:annotations});});},getAnnotation:function getAnnotation(documentId,annotationId){return Promise.resolve(_getAnnotations(documentId)[findAnnotation(documentId,annotationId)]);},addAnnotation:function addAnnotation(documentId,pageNumber,annotation){return new Promise(function(resolve,reject){annotation.class='Annotation';annotation.uuid=(0,_uuid2.default)();annotation.page=pageNumber;var annotations=_getAnnotations(documentId);annotations.push(annotation);updateAnnotations(documentId,annotations);resolve(annotation);});},editAnnotation:function editAnnotation(documentId,annotationId,annotation){return new Promise(function(resolve,reject){var annotations=_getAnnotations(documentId);annotations[findAnnotation(documentId,annotationId)]=annotation;updateAnnotations(documentId,annotations);resolve(annotation);});},deleteAnnotation:function deleteAnnotation(documentId,annotationId){return new Promise(function(resolve,reject){var index=findAnnotation(documentId,annotationId);if(index>-1){var annotations=_getAnnotations(documentId);annotations.splice(index,1);updateAnnotations(documentId,annotations);}resolve(true);});},getComments:function getComments(documentId,annotationId){return new Promise(function(resolve,reject){resolve(_getAnnotations(documentId).filter(function(i){return i.class==='Comment'&&i.annotation===annotationId;}));});},addComment:function addComment(documentId,annotationId,content){return new Promise(function(resolve,reject){var comment={class:'Comment',uuid:(0,_uuid2.default)(),annotation:annotationId,content:content};var annotations=_getAnnotations(documentId);annotations.push(comment);updateAnnotations(documentId,annotations);resolve(comment);});},deleteComment:function deleteComment(documentId,commentId){return new Promise(function(resolve,reject){_getAnnotations(documentId);var index=-1;var annotations=_getAnnotations(documentId);for(var i=0,l=annotations.length;i<l;i++){if(annotations[i].uuid===commentId){index=i;break;}}if(index>-1){annotations.splice(index,1);updateAnnotations(documentId,annotations);}resolve(true);});}}));}return LocalStoreAdapter;}(_StoreAdapter3.default);
            exports.default=LocalStoreAdapter;
            function _getAnnotations(documentId){return JSON.parse(localStorage.getItem(documentId+'/annotations'))||[];}
            function updateAnnotations(documentId,annotations){localStorage.setItem(documentId+'/annotations',JSON.stringify(annotations));}
            function findAnnotation(documentId,annotationId){var index=-1;var annotations=_getAnnotations(documentId);for(var i=0,l=annotations.length;i<l;i++){if(annotations[i].uuid===annotationId){index=i;break;}}return index;}
            module.exports=exports['default'];
    /***/},
    /* 9 */
    /***/function(module,exports){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=uuid;
            var REGEXP=/[xy]/g;
            var PATTERN='xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx';
            function replacement(c){var r=Math.random()*16|0;var v=c=='x'?r:r&0x3|0x8;return v.toString(16);}/**
            * Generate a univierally unique identifier
            *
            * @return {String}
            */function uuid(){return PATTERN.replace(REGEXP,replacement);}
            module.exports=exports['default'];
    /***/},
    /* 10 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=render;
            var _PDFJSAnnotate=__webpack_require__(1);
            var _PDFJSAnnotate2=_interopRequireDefault(_PDFJSAnnotate);
            var _appendChild=__webpack_require__(11);
            var _appendChild2=_interopRequireDefault(_appendChild);
            var _renderScreenReaderHints=__webpack_require__(20);
            var _renderScreenReaderHints2=_interopRequireDefault(_renderScreenReaderHints);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}/**
            * Render the response from PDFJSAnnotate.getStoreAdapter().getAnnotations to SVG
            *
            * @param {SVGElement} svg The SVG element to render the annotations to
            * @param {Object} viewport The page viewport data
            * @param {Object} data The response from PDFJSAnnotate.getStoreAdapter().getAnnotations
            * @return {Promise} Settled once rendering has completed
            *  A settled Promise will be either:
            *    - fulfilled: SVGElement
            *    - rejected: Error
            */function render(svg,viewport,data){
                return new Promise(function(resolve,reject){// Reset the content of the SVG
                    svg.innerHTML='';
                    svg.setAttribute('data-pdf-annotate-container',true);
                    svg.setAttribute('data-pdf-annotate-viewport',JSON.stringify(viewport));
                    svg.removeAttribute('data-pdf-annotate-document');
                    svg.removeAttribute('data-pdf-annotate-page');// If there's no data nothing can be done
                    if(!data){return resolve(svg);}
                    svg.setAttribute('data-pdf-annotate-document',data.documentId);
                    svg.setAttribute('data-pdf-annotate-page',data.pageNumber);// Make sure annotations is an array
                    if(!Array.isArray(data.annotations)||data.annotations.length===0){return resolve(svg);}// Append annotation to svg
                    
                    data.annotations.forEach(function(a){
                        (0,_appendChild2.default)(svg,a,viewport);
                    });
                    resolve(svg);
                });
            }
            module.exports=exports['default'];
    /***/},
    /* 11 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=appendChild;
            var _objectAssign=__webpack_require__(12);
            var _objectAssign2=_interopRequireDefault(_objectAssign);
            var _renderLine=__webpack_require__(13);
            var _renderLine2=_interopRequireDefault(_renderLine);
            var _renderPath=__webpack_require__(16);
            var _renderPath2=_interopRequireDefault(_renderPath);
            var _renderPoint=__webpack_require__(17);
            var _renderPoint2=_interopRequireDefault(_renderPoint);
            var _renderRect=__webpack_require__(18);
            var _renderRect2=_interopRequireDefault(_renderRect);
            var _renderText=__webpack_require__(19);
            var _renderText2=_interopRequireDefault(_renderText);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            var isFirefox=/firefox/i.test(navigator.userAgent);/**
            * Get the x/y translation to be used for transforming the annotations
            * based on the rotation of the viewport.
            *
            * @param {Object} viewport The viewport data from the page
            * @return {Object}
            */function getTranslation(viewport){var x=void 0;var y=void 0;// Modulus 360 on the rotation so that we only
                // have to worry about four possible values.
                switch(viewport.rotation%360){case 0:x=y=0;break;case 90:x=0;y=viewport.width/viewport.scale*-1;break;case 180:x=viewport.width/viewport.scale*-1;y=viewport.height/viewport.scale*-1;break;case 270:x=viewport.height/viewport.scale*-1;y=0;break;}return{x:x,y:y};}/**
            * Transform the rotation and scale of a node using SVG's native transform attribute.
            *
            * @param {Node} node The node to be transformed
            * @param {Object} viewport The page's viewport data
            * @return {Node}
            */function transform(node,viewport){
                var trans=getTranslation(viewport);// Let SVG natively transform the element
                node.setAttribute('transform','scale('+viewport.scale+') rotate('+viewport.rotation+') translate('+trans.x+', '+trans.y+')');
                // Manually adjust x/y for nested SVG nodes
                if(!isFirefox&&node.nodeName.toLowerCase()==='svg'){
                    node.setAttribute('x',parseInt(node.getAttribute('x'),10)*viewport.scale);
                    node.setAttribute('y',parseInt(node.getAttribute('y'),10)*viewport.scale);
                    var x=parseInt(node.getAttribute('x',10));
                    var y=parseInt(node.getAttribute('y',10));
                    var width=parseInt(node.getAttribute('width'),10);
                    var height=parseInt(node.getAttribute('height'),10);
                    var path=node.querySelector('path');
                    var svg=path.parentNode;// Scale width/height
                    [node,svg,path,node.querySelector('circle')].forEach(function(n){
                        n.setAttribute('width',parseInt(n.getAttribute('width'),10)*viewport.scale);
                        n.setAttribute('height',parseInt(n.getAttribute('height'),10)*viewport.scale);
                    });
                    // Transform path but keep scale at 100% since it will be handled natively
                    transform(path,(0,_objectAssign2.default)({},viewport,{scale:1}));
                    switch(viewport.rotation%360){
                        case 90:
                            node.setAttribute('x',viewport.width-y-width);
                            node.setAttribute('y',x);
                            svg.setAttribute('x',1);
                            svg.setAttribute('y',0);
                            break;
                        case 180:
                            node.setAttribute('x',viewport.width-x-width);
                            node.setAttribute('y',viewport.height-y-height);
                            svg.setAttribute('y',2);
                            break;
                        case 270:
                            node.setAttribute('x',y);
                            node.setAttribute('y',viewport.height-x-height);
                            svg.setAttribute('x',-1);
                            svg.setAttribute('y',0);
                            break;
                    }
                }
                return node;
            }/**
            * Append an annotation as a child of an SVG.
            *
            * @param {SVGElement} svg The SVG element to append the annotation to
            * @param {Object} annotation The annotation definition to render and append
            * @param {Object} viewport The page's viewport data
            * @return {SVGElement} A node that was created and appended by this function
            */function appendChild(svg,annotation,viewport){
                           if(!viewport){
                               viewport=JSON.parse(svg.getAttribute('data-pdf-annotate-viewport'));
                           }
                           annotation.viewport = viewport;
                           var child=void 0;
                           switch(annotation.type){
                               case'area':
                               case'highlight':child=(0,_renderRect2.default)(annotation);
                                   break;
                               case'strikeout':child=(0,_renderLine2.default)(annotation);
                                   break;
                               case'point':child=(0,_renderPoint2.default)(annotation);
                                   break;
                               case'textbox':child=(0,_renderText2.default)(annotation);
                                   break;
                               case'drawing':child=(0,_renderPath2.default)(annotation);
                                   break;
                           }// If no type was provided for an annotation it will result in node being null.
                // Skip appending/transforming if node doesn't exist.
                if(child){// Set attributes
                    child.setAttribute('data-pdf-annotate-id',annotation.uuid);
                    child.setAttribute('data-pdf-annotate-type',annotation.type);
                    child.setAttribute('data-pdf-annotate-owner',annotation.owner);
                    child.setAttribute('aria-hidden',true);
                    svg.appendChild(transform(child,viewport));
                }
                return child;
            }
            module.exports=exports['default'];
    /***/},
    /* 12 */
    /***/function(module,exports){/* eslint-disable no-unused-vars */
            'use strict';
            var hasOwnProperty=Object.prototype.hasOwnProperty;
            var propIsEnumerable=Object.prototype.propertyIsEnumerable;
            function toObject(val){if(val===null||val===undefined){throw new TypeError('Object.assign cannot be called with null or undefined');}return Object(val);}
            module.exports=Object.assign||function(target,source){
                var from;
                var to=toObject(target);
                var symbols;
                for(var s=1;s<arguments.length;s++){
                    from=Object(arguments[s]);
                    for(var key in from){
                        if(hasOwnProperty.call(from,key)){to[key]=from[key];}
                    }
                    if(Object.getOwnPropertySymbols){
                        symbols=Object.getOwnPropertySymbols(from);
                        for(var i=0;i<symbols.length;i++){
                            if(propIsEnumerable.call(from,symbols[i])){to[symbols[i]]=from[symbols[i]];}
                        }
                    }
                }
                return to;
            };
            
    /***/},
    /* 13 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=renderLine;
            var _setAttributes=__webpack_require__(14);
            var _setAttributes2=_interopRequireDefault(_setAttributes);
            var _normalizeColor=__webpack_require__(15);
            var _normalizeColor2=_interopRequireDefault(_normalizeColor);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}/**
            * Create SVGLineElements from an annotation definition.
            * This is used for anntations of type `strikeout`.
            *
            * @param {Object} a The annotation definition
            * @return {SVGGElement} A group of all lines to be rendered
            */function renderLine(a){var group=document.createElementNS('http://www.w3.org/2000/svg','g');(0,_setAttributes2.default)(group,{stroke:(0,_normalizeColor2.default)(a.color||'#f00'),strokeWidth:1});a.rectangles.forEach(function(r){var line=document.createElementNS('http://www.w3.org/2000/svg','line');(0,_setAttributes2.default)(line,{x1:r.x,y1:r.y,x2:r.x+r.width,y2:r.y});group.appendChild(line);});return group;}
            module.exports=exports['default'];
    /***/},
    /* 14 */
    /***/function(module,exports){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=setAttributes;
            var UPPER_REGEX=/[A-Z]/g;
            // Don't convert these attributes from camelCase to hyphenated-attributes
            var BLACKLIST=['viewBox'];
            var keyCase=function keyCase(key){if(BLACKLIST.indexOf(key)===-1){key=key.replace(UPPER_REGEX,function(match){return'-'+match.toLowerCase();});}return key;};
            /**
            * Set attributes for a node from a map
            *
            * @param {Node} node The node to set attributes on
            * @param {Object} attributes The map of key/value pairs to use for attributes
            */function setAttributes(node,attributes){Object.keys(attributes).forEach(function(key){node.setAttribute(keyCase(key),attributes[key]);});}
            module.exports=exports['default'];
    /***/},
    /* 15 */
    /***/function(module,exports){
            "use strict";
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=normalizeColor;
            var REGEX_HASHLESS_HEX=/^([a-f0-9]{6}|[a-f0-9]{3})$/i;/**
            * Normalize a color value
            *
            * @param {String} color The color to normalize
            * @return {String}
            */function normalizeColor(color){if(REGEX_HASHLESS_HEX.test(color)){color="#"+color;}return color;}
            module.exports=exports["default"];
    /***/},
    /* 16 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=renderPath;
            var _setAttributes=__webpack_require__(14);
            var _setAttributes2=_interopRequireDefault(_setAttributes);
            var _normalizeColor=__webpack_require__(15);
            var _normalizeColor2=_interopRequireDefault(_normalizeColor);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            /**
            * Create SVGPathElement from an annotation definition.
            * This is used for anntations of type `drawing`.
            *
            * @param {Object} a The annotation definition
            * @return {SVGPathElement} The path to be rendered
            */function renderPath(a){var d=[];var path=document.createElementNS('http://www.w3.org/2000/svg','path');for(var i=0,l=a.lines.length;i<l;i++){var p1=a.lines[i];var p2=a.lines[i+1];if(p2){d.push('M'+p1[0]+' '+p1[1]+' '+p2[0]+' '+p2[1]);}}(0,_setAttributes2.default)(path,{d:d.join(' ')+'Z',stroke:(0,_normalizeColor2.default)(a.color||'#000'),strokeWidth:a.width||1,fill:'none'});return path;}
            module.exports=exports['default'];
    /***/},
    /* 17 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=renderPoint;
            var _setAttributes=__webpack_require__(14);
            var _setAttributes2=_interopRequireDefault(_setAttributes);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            var SIZE=20;
            var D='M499.968 214.336q-113.832 0 -212.877 38.781t-157.356 104.625 -58.311 142.29q0 62.496 39.897 119.133t112.437 97.929l48.546 27.9 -15.066 53.568q-13.392 50.778 -39.06 95.976 84.816 -35.154 153.45 -95.418l23.994 -21.204 31.806 3.348q38.502 4.464 72.54 4.464 113.832 0 212.877 -38.781t157.356 -104.625 58.311 -142.29 -58.311 -142.29 -157.356 -104.625 -212.877 -38.781z';/**
            * Create SVGElement from an annotation definition.
            * This is used for anntations of type `comment`.
            *
            * @param {Object} a The annotation definition
            * @return {SVGElement} A svg to be rendered
            */function renderPoint(a){
               
               let posX = a.x;
               let posY = a.y;
               
               let colorInner;
               let colorLine;
               if(a.color){
                   colorInner = 'rgba(255,237,0,.8)';
                   colorLine = 'rgb(246,168,0)';
               }else{
                   colorInner = 'rgba(142,186,229,.8)';
                   colorLine = 'rgb(0,84,159)';
               }
               
                var outerSVG=document.createElementNS('http://www.w3.org/2000/svg','svg');
                var innerSVG=document.createElementNS('http://www.w3.org/2000/svg','svg');
                var path=document.createElementNS('http://www.w3.org/2000/svg','path');
                var path2=document.createElementNS('http://www.w3.org/2000/svg','path');
                var circle=document.createElementNS('http://www.w3.org/2000/svg','circle');
                (0,_setAttributes2.default)(outerSVG,{width:SIZE/2 +1, height:SIZE, x:posX - (SIZE/4), y:posY - SIZE});
                (0,_setAttributes2.default)(innerSVG,{width:SIZE,height:SIZE,x:0,y:SIZE*0.05*-1,viewBox:'-16 -18 64 64'});
                (0,_setAttributes2.default)(path,{d:'M0,47 Q0,28 10,15 A15,15 0,1,0 -10,15 Q0,28 0,47',stroke:'none',fill:colorInner,transform:''});
                (0,_setAttributes2.default)(path2,{d:'M0,47 Q0,28 10,15 A15,15 0,1,0 -10,15 Q0,28 0,47',strokeWidth:1,stroke:colorLine,fill:'none'});
                (0,_setAttributes2.default)(circle,{cx:0,cy:4,r:4,stroke:'none',fill:colorLine});
                innerSVG.appendChild(path);
                innerSVG.appendChild(path2);
                innerSVG.appendChild(circle);
                outerSVG.appendChild(innerSVG);
                
                return outerSVG;
            }
            module.exports=exports['default'];
    /***/},
    /* 18 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            var _typeof=typeof Symbol==="function"&&_typeof2(Symbol.iterator)==="symbol"?function(obj){return typeof obj==='undefined'?'undefined':_typeof2(obj);}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol?"symbol":typeof obj==='undefined'?'undefined':_typeof2(obj);};
            exports.default=renderRect;
            var _setAttributes=__webpack_require__(14);
            var _setAttributes2=_interopRequireDefault(_setAttributes);
            var _normalizeColor=__webpack_require__(15);
            var _normalizeColor2=_interopRequireDefault(_normalizeColor);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}/**
            * Create SVGRectElements from an annotation definition.
            * This is used for anntations of type `area` and `highlight`.
            *
            * @param {Object} a The annotation definition
            * @return {SVGGElement|SVGRectElement} A group of all rects to be rendered
            */function renderRect(a){
                if(a.type==='highlight'){
                    var _ret=function(){
                        var group=document.createElementNS('http://www.w3.org/2000/svg','g');
                        (0,_setAttributes2.default)(group,{fill:(0,_normalizeColor2.default)(a.color||'rgb(142,186,229)'),fillOpacity:0.35});
                        a.rectangles.forEach(function(r){group.appendChild(createRect(r));});
                        return{v:group};
                    }();
                    if((typeof _ret==='undefined'?'undefined':_typeof(_ret))==="object")return _ret.v;
                }else{
                    var rect=createRect(a);
                    (0,_setAttributes2.default)(rect,{stroke:(0,_normalizeColor2.default)(a.color||'rgb(0,84,159)'),fill:'none'});
                    return rect;
                }
            }
            function createRect(r){var rect=document.createElementNS('http://www.w3.org/2000/svg','rect');(0,_setAttributes2.default)(rect,{x:r.x,y:r.y,width:r.width,height:r.height});return rect;}
            module.exports=exports['default'];
    /***/},
    /* 19 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=renderText;
            var _setAttributes=__webpack_require__(14);
            var _setAttributes2=_interopRequireDefault(_setAttributes);
            var _normalizeColor=__webpack_require__(15);
            var _normalizeColor2=_interopRequireDefault(_normalizeColor);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}/**
            * Create SVGTextElement from an annotation definition.
            * This is used for anntations of type `textbox`.
            *
            * @param {Object} a The annotation definition
            * @return {SVGTextElement} A text to be rendered
            */function renderText(a){
                var text=document.createElementNS('http://www.w3.org/2000/svg','text');
                (0,_setAttributes2.default)(text,{x:a.x,y:a.y+parseInt(a.size,10),fill:(0,_normalizeColor2.default)(a.color||'#000'),fontSize:a.size});
                text.innerHTML=a.content;
                return text;
            }
            module.exports=exports['default'];
    /***/},
    /* 20 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=renderScreenReaderHints;
            var _insertScreenReaderHint=__webpack_require__(21);
            var _insertScreenReaderHint2=_interopRequireDefault(_insertScreenReaderHint);
            var _initEventHandlers=__webpack_require__(27);
            var _initEventHandlers2=_interopRequireDefault(_initEventHandlers);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            // TODO This is not the right place for this to live
            (0,_initEventHandlers2.default)();/**
            * Insert hints into the DOM for screen readers.
            *
            * @param {Array} annotations The annotations that hints are inserted for
            */function renderScreenReaderHints(annotations){annotations=Array.isArray(annotations)?annotations:[];// Insert hints for each type
                    Object.keys(SORT_TYPES).forEach(function(type){var sortBy=SORT_TYPES[type];annotations.filter(function(a){return a.type===type;}).sort(sortBy).forEach(function(a,i){return(0,_insertScreenReaderHint2.default)(a,i+1);});});}
            // Sort annotations first by y, then by x.
            // This allows hints to be injected in the order they appear,
            // which makes numbering them easier.
            function sortByPoint(a,b){if(a.y<b.y){return a.x-b.x;}else{return 1;}}// Sort annotation by it's first rectangle
            function sortByRectPoint(a,b){return sortByPoint(a.rectangles[0],b.rectangles[0]);}// Sort annotation by it's first line
            function sortByLinePoint(a,b){var lineA=a.lines[0];var lineB=b.lines[0];return sortByPoint({x:lineA[0],y:lineA[1]},{x:lineB[0],y:lineB[1]});}// Arrange supported types and associated sort methods
            var SORT_TYPES={'highlight':sortByRectPoint,'strikeout':sortByRectPoint,'drawing':sortByLinePoint,'textbox':sortByPoint,'point':sortByPoint,'area':sortByPoint};
            module.exports=exports['default'];
    /***/},
    /* 21 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=insertScreenReaderHint;
            var _createScreenReaderOnly=__webpack_require__(22);
            var _createScreenReaderOnly2=_interopRequireDefault(_createScreenReaderOnly);
            var _insertElementWithinChildren=__webpack_require__(23);
            var _insertElementWithinChildren2=_interopRequireDefault(_insertElementWithinChildren);
            var _insertElementWithinElement=__webpack_require__(24);
            var _insertElementWithinElement2=_interopRequireDefault(_insertElementWithinElement);
            var _renderScreenReaderComments=__webpack_require__(25);
            var _renderScreenReaderComments2=_interopRequireDefault(_renderScreenReaderComments);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}// Annotation types that support comments
            var COMMENT_TYPES=['highlight','point','area','strikeout'];/**
            * Insert a hint into the DOM for screen readers for a specific annotation.
            *
            * @param {Object} annotation The annotation to insert a hint for
            * @param {Number} num The number of the annotation out of all annotations of the same type
            */function insertScreenReaderHint(annotation){var num=arguments.length<=1||arguments[1]===undefined?0:arguments[1];switch(annotation.type){case'highlight':case'strikeout':var rects=annotation.rectangles;var first=rects[0];var last=rects[rects.length-1];(0,_insertElementWithinElement2.default)((0,_createScreenReaderOnly2.default)('Begin '+annotation.type+' annotation '+num,annotation.uuid),first.x,first.y,annotation.page,true);(0,_insertElementWithinElement2.default)((0,_createScreenReaderOnly2.default)('End '+annotation.type+' annotation '+num,annotation.uuid+'-end'),last.x+last.width,last.y,annotation.page,false);break;case'textbox':case'point':var text=annotation.type==='textbox'?' (content: '+annotation.content+')':'';(0,_insertElementWithinChildren2.default)((0,_createScreenReaderOnly2.default)(annotation.type+' annotation '+num+text,annotation.uuid),annotation.x,annotation.y,annotation.page);break;case'drawing':case'area':var x=typeof annotation.x!=='undefined'?annotation.x:annotation.lines[0][0];var y=typeof annotation.y!=='undefined'?annotation.y:annotation.lines[0][1];(0,_insertElementWithinChildren2.default)((0,_createScreenReaderOnly2.default)('Unlabeled drawing',annotation.uuid),x,y,annotation.page);break;}// Include comments in screen reader hint
                if(COMMENT_TYPES.includes(annotation.type)){(0,_renderScreenReaderComments2.default)(annotation.documentId,annotation.uuid);}}
            module.exports=exports['default'];
    /***/},
    /* 22 */
    /***/function(module,exports){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=createScreenReaderOnly;
            /**
            * Create a node that is only visible to screen readers
            *
            * @param {String} content The text content that should be read by screen reader
            * @param {String} [annotationId] The ID of the annotation assocaited
            * @return {Element} An Element that is only visible to screen readers
            */function createScreenReaderOnly(content,annotationId){var node=document.createElement('div');var text=document.createTextNode(content);node.appendChild(text);node.setAttribute('id','pdf-annotate-screenreader-'+annotationId);node.style.position='absolute';node.style.left='-10000px';node.style.top='auto';node.style.width='1px';node.style.height='1px';node.style.overflow='hidden';return node;}
           module.exports=exports['default'];
    /***/},
    /* 23 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=insertElementWithinChildren;
            var _insertElementWithinElement=__webpack_require__(24);
            var _insertElementWithinElement2=_interopRequireDefault(_insertElementWithinElement);
            var _utils=__webpack_require__(6);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            function _toConsumableArray(arr){if(Array.isArray(arr)){for(var i=0,arr2=Array(arr.length);i<arr.length;i++){arr2[i]=arr[i];}return arr2;}else{return Array.from(arr);}}
            /**
            * Insert an element at a point within the document.
            * This algorithm will try to insert between elements if possible.
            * It will however use `insertElementWithinElement` if it is more accurate.
            *
            * @param {Element} el The element to be inserted
            * @param {Number} x The x coordinate of the point
            * @param {Number} y The y coordinate of the point
            * @param {Number} pageNumber The page number to limit elements to
            * @return {Boolean} True if element was able to be inserted, otherwise false
            */function insertElementWithinChildren(el,x,y,pageNumber){// Try and use most accurate method of inserting within an element
                if((0,_insertElementWithinElement2.default)(el,x,y,pageNumber,true)){
                    return true;
                }// Fall back to inserting between elements
                var svg=document.querySelector('svg[data-pdf-annotate-page="'+pageNumber+'"]');
                var rect=svg.getBoundingClientRect();
                var nodes=[].concat(_toConsumableArray(svg.parentNode.querySelectorAll('.textLayer > div')));
                y=(0,_utils.scaleUp)(svg,{y:y}).y+rect.top;
                x=(0,_utils.scaleUp)(svg,{x:x}).x+rect.left;// Find the best node to insert before
                for(var i=0,l=nodes.length;i<l;i++){
                    var n=nodes[i];
                    var r=n.getBoundingClientRect();
                    if(y<=r.top){
                        n.parentNode.insertBefore(el,n);
                        return true;
                    }
                }// If all else fails try to append to the bottom
                var textLayer=svg.parentNode.querySelector('.textLayer');
                if(textLayer){
                    var textRect=textLayer.getBoundingClientRect();
                    if((0,_utils.pointIntersectsRect)(x,y,textRect)){
                        textLayer.appendChild(el);
                        return true;
                    }
                }
                return false;
            }
            module.exports=exports['default'];
    /***/},
    /* 24 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=insertElementWithinElement;
            var _utils=__webpack_require__(6);
            function _toConsumableArray(arr){if(Array.isArray(arr)){for(var i=0,arr2=Array(arr.length);i<arr.length;i++){arr2[i]=arr[i];}return arr2;}else{return Array.from(arr);}}/**
            * Insert an element at a point within the document.
            * This algorithm will only insert within an element amidst it's text content.
            *
            * @param {Element} el The element to be inserted
            * @param {Number} x The x coordinate of the point
            * @param {Number} y The y coordinate of the point
            * @param {Number} pageNumber The page number to limit elements to
            * @param {Boolean} insertBefore Whether the element is to be inserted before or after x
            * @return {Boolean} True if element was able to be inserted, otherwise false
            */function insertElementWithinElement(el,x,y,pageNumber,insertBefore){
                var OFFSET_ADJUST=2;// If inserting before adjust `x` by looking for element a few px to the right
                // Otherwise adjust a few px to the left
                // This is to allow a little tolerance by searching within the box, instead
                // of getting a false negative by testing right on the border.
                x=Math.max(x+OFFSET_ADJUST*(insertBefore?1:-1),0);
                var node=textLayerElementFromPoint(x,y+OFFSET_ADJUST,pageNumber);
                if(!node){return false;}// Now that node has been found inverse the adjustment for `x`.
                // This is done to accomodate tolerance by cutting off on the outside of the
                // text boundary, instead of missing a character by cutting off within.
                x=x+OFFSET_ADJUST*(insertBefore?-1:1);
                var svg=document.querySelector('svg[data-pdf-annotate-page="'+pageNumber+'"]');
                var left=(0,_utils.scaleDown)(svg,{left:node.getBoundingClientRect().left}).left-svg.getBoundingClientRect().left;
                var temp=node.cloneNode(true);
                var head=temp.innerHTML.split('');
                var tail=[];// Insert temp off screen
                temp.style.position='absolute';
                temp.style.top='-10000px';
                temp.style.left='-10000px';
                document.body.appendChild(temp);
                while(head.length){// Don't insert within HTML tags
                    if(head[head.length-1]==='>'){
                        while(head.length){
                            tail.unshift(head.pop());
                            if(tail[0]==='<'){
                                break;
                            }
                        }
                    }// Check if width of temp based on current head value satisfies x
                    temp.innerHTML=head.join('');
                    var width=(0,_utils.scaleDown)(svg,{width:temp.getBoundingClientRect().width}).width;
                    if(left+width<=x){
                        break;
                    }
                    tail.unshift(head.pop());
                }// Update original node with new markup, including element to be inserted
                node.innerHTML=head.join('')+el.outerHTML+tail.join('');temp.parentNode.removeChild(temp);return true;
            }/**
            * Get a text layer element at a given point on a page
            *
            * @param {Number} x The x coordinate of the point
            * @param {Number} y The y coordinate of the point
            * @param {Number} pageNumber The page to limit elements to
            * @return {Element} First text layer element found at the point
            */function textLayerElementFromPoint(x,y,pageNumber){
                   var svg=document.querySelector('svg[data-pdf-annotate-page="'+pageNumber+'"]');
                   var rect=svg.getBoundingClientRect();
                   y=(0,_utils.scaleUp)(svg,{y:y}).y+rect.top;
                   x=(0,_utils.scaleUp)(svg,{x:x}).x+rect.left;
                   return[].concat(_toConsumableArray(svg.parentNode.querySelectorAll('.textLayer [data-canvas-width]'))).filter(function(el){return(0,_utils.pointIntersectsRect)(x,y,el.getBoundingClientRect());})[0];
               }
            module.exports=exports['default'];
    /***/},
    /* 25 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=renderScreenReaderComments;
            var _PDFJSAnnotate=__webpack_require__(1);
            var _PDFJSAnnotate2=_interopRequireDefault(_PDFJSAnnotate);
            var _insertScreenReaderComment=__webpack_require__(26);
            var _insertScreenReaderComment2=_interopRequireDefault(_insertScreenReaderComment);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}/**
            * Insert the comments into the DOM to be available by screen reader
            *
            * Example output:
            *   <div class="screenReaderOnly">
            *    <div>Begin highlight 1</div>
            *    <ol aria-label="Comments">
            *      <li>Foo</li>
            *      <li>Bar</li>
            *      <li>Baz</li>
            *      <li>Qux</li>
            *    </ol>
            *  </div>
            *  <div>Some highlighted text goes here...</div>
            *  <div class="screenReaderOnly">End highlight 1</div>
            *
            * NOTE: `screenReaderOnly` is not a real class, just used for brevity
            *
            * @param {String} documentId The ID of the document
            * @param {String} annotationId The ID of the annotation
            * @param {Array} [comments] Optionally preloaded comments to be rendered
            * @return {Promise}
            */function renderScreenReaderComments(documentId,annotationId,comments){
                   var promise=void 0;
                   if(Array.isArray(comments)){
                       promise=Promise.resolve(comments);
                   }else{
                       promise=_PDFJSAnnotate2.default.getStoreAdapter().getComments(documentId,annotationId);
                   }
                   return promise.then(function(comments){// Node needs to be found by querying DOM as it may have been inserted as innerHTML
                       // leaving `screenReaderNode` as an invalid reference (see `insertElementWithinElement`).
                       var node=document.getElementById('pdf-annotate-screenreader-'+annotationId);
                       if(node){
                           var list=document.createElement('ol');
                           list.setAttribute('id','pdf-annotate-screenreader-comment-list-'+annotationId);
                           list.setAttribute('aria-label','Comments');node.appendChild(list);
                            // comments.forEach(_insertScreenReaderComment2.default);

                           for (var i=0; i < comments.length; i++) {
                               _insertScreenReaderComment2.default(comments[i]);
                           }

                    }});}
            module.exports=exports['default'];
    /***/},
    /* 26 */
    /***/function(module,exports){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=insertScreenReaderComment;/**
            * Insert a comment into the DOM to be available by screen reader
            *
            * @param {Object} comment The comment to be inserted
            */function insertScreenReaderComment(comment){if(!comment){return;}var list=document.querySelector('#pdf-annotate-screenreader-'+comment.annotation+' ol');if(list){var item=document.createElement('li');item.setAttribute('id','pdf-annotate-screenreader-comment-'+comment.uuid);item.appendChild(document.createTextNode(''+comment.content));list.appendChild(item);}}
            module.exports=exports['default'];
    /***/},
    /* 27 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.default=initEventHandlers;
            var _insertScreenReaderHint=__webpack_require__(21);
            var _insertScreenReaderHint2=_interopRequireDefault(_insertScreenReaderHint);
            var _renderScreenReaderHints=__webpack_require__(20);
            var _renderScreenReaderHints2=_interopRequireDefault(_renderScreenReaderHints);
            var _insertScreenReaderComment=__webpack_require__(26);
            var _insertScreenReaderComment2=_interopRequireDefault(_insertScreenReaderComment);
            var _renderScreenReaderComments=__webpack_require__(25);
            var _renderScreenReaderComments2=_interopRequireDefault(_renderScreenReaderComments);
            var _event=__webpack_require__(4);
            var _PDFJSAnnotate=__webpack_require__(1);
            var _PDFJSAnnotate2=_interopRequireDefault(_PDFJSAnnotate);

            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            /**
            * Initialize the event handlers for keeping screen reader hints synced with data
            */function initEventHandlers(){(0,_event.addEventListener)('annotation:add',function(documentId,pageNumber,annotation){reorderAnnotationsByType(documentId,pageNumber,annotation.type);});(0,_event.addEventListener)('annotation:edit',function(documentId,annotationId,annotation){reorderAnnotationsByType(documentId,annotation.page,annotation.type);});(0,_event.addEventListener)('annotation:delete',removeAnnotation);(0,_event.addEventListener)('comment:add',insertComments);(0,_event.addEventListener)('comment:delete',removeComment);}/**
            * Reorder the annotation numbers by annotation type
            *
            * @param {String} documentId The ID of the document
            * @param {Number} pageNumber The page number of the annotations
            * @param {Strig} type The annotation type
            */function reorderAnnotationsByType(documentId,pageNumber,type){_PDFJSAnnotate2.default.getStoreAdapter().getAnnotations(documentId,pageNumber).then(function(annotations){return annotations.annotations.filter(function(a){return a.type===type;});}).then(function(annotations){annotations.forEach(function(a){removeAnnotation(documentId,a.uuid);});return annotations;}).then(/*_renderScreenReaderHints2.default*/);}/**
            * Remove the screen reader hint for an annotation
            *
            * @param {String} documentId The ID of the document
            * @param {String} annotationId The Id of the annotation
            */function removeAnnotation(documentId,annotationId){removeElementById('pdf-annotate-screenreader-'+annotationId);removeElementById('pdf-annotate-screenreader-'+annotationId+'-end');}/**
            * Insert a screen reader hint for a comment
            *
            * @param {String} documentId The ID of the document
            * @param {String} annotationId The ID of tha assocated annotation
            * @param {Object} comment The comment to insert a hint for
            */function insertComments(documentId,annotationId,comment){
                           var list=document.querySelector('pdf-annotate-screenreader-comment-list-'+annotationId);
                           var promise=void 0;
                           if(!list){
                               promise=(0,_renderScreenReaderComments2.default)(documentId,annotationId,[]).then(function(){
                                   list=document.querySelector('pdf-annotate-screenreader-comment-list-'+annotationId);return true;
                               });
                           }
                           else{
                               promise=Promise.resolve(true);}promise.then(function(){
                               (0,_insertScreenReaderComment2.default)(comment);
                           });
                       }/**
            * Remove a screen reader hint for a comment
            *
            * @param {String} documentId The ID of the document
            * @param {String} commentId The ID of the comment
            */function removeComment(documentId,commentId){removeElementById('pdf-annotate-screenreader-comment-'+commentId);}/**
            * Remove an element from the DOM by it's ID if it exists
            *
            * @param {String} elementID The ID of the element to be removed
            */function removeElementById(elementId){
                   var el=document.getElementById(elementId);
                   if(el){
                       el.parentNode.removeChild(el);
                   }
               }module.exports=exports['default'];
    /***/},
    /* 28 */ /* Combines the UI functions to export for parent-module 1 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            var _event=__webpack_require__(4);
            var _edit=__webpack_require__(29);
            var _pen=__webpack_require__(30);
            var _point=__webpack_require__(31);
            var _rect=__webpack_require__(32);
            var _text=__webpack_require__(33);
            var _page=__webpack_require__(34);
            var _pickAnno=__webpack_require__(37);
            var _questionsRenderer = __webpack_require__(38);
            var _shortText = __webpack_require__(39);
            var _newAnnotations = __webpack_require__(40);
            var _ajaxloader=__webpack_require__(36);
            var _commentWrapper=__webpack_require__(35);
            exports.default={addEventListener:_event.addEventListener,removeEventListener:_event.removeEventListener,fireEvent:_event.fireEvent,disableEdit:_edit.disableEdit,enableEdit:_edit.enableEdit,disablePen:_pen.disablePen,enablePen:_pen.enablePen,setPen:_pen.setPen,disablePoint:_point.disablePoint,enablePoint:_point.enablePoint,disableRect:_rect.disableRect,enableRect:_rect.enableRect,disableText:_text.disableText,enableText:_text.enableText,setText:_text.setText,createPage:_page.createPage,renderPage:_page.renderPage,showLoader:_ajaxloader.showLoader,hideLoader:_ajaxloader.hideLoader,pickAnnotation:_pickAnno.pickAnnotation, renderQuestions:_questionsRenderer.renderQuestions, renderAllQuestions: _questionsRenderer.renderAllQuestions, shortenTextDynamic:_shortText.shortenTextDynamic, mathJaxAndShortenText:_shortText.mathJaxAndShortenText, loadNewAnnotations : _newAnnotations.load, loadEditor: _commentWrapper.loadEditor};
            module.exports=exports['default'];
    /***/},
    /** 29 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            var _slicedToArray=function(){
                function sliceIterator(arr,i){
                    var _arr=[];
                    var _n=true;
                    var _d=false;
                    var _e=undefined;
                    try{
                        for(var _i=arr[Symbol.iterator](),_s;!(_n=(_s=_i.next()).done);_n=true){
                            _arr.push(_s.value);
                            if(i&&_arr.length===i)break;
                        }
                    }catch(err){_d=true;_e=err;}
                    finally{
                        try{if(!_n&&_i["return"])_i["return"]();}
                        finally{if(_d)throw _e;}
                    }
                    return _arr;
                }
                return function(arr,i){
                    if(Array.isArray(arr)){
                        return arr;
                    }else if(Symbol.iterator in Object(arr)){
                        return sliceIterator(arr,i);
                    }else{
                        throw new TypeError("Invalid attempt to destructure non-iterable instance");
                    }
                };
            }();
            exports.enableEdit=enableEdit;
            exports.disableEdit=disableEdit;
            exports.createEditOverlay=createEditOverlay;
            exports.destroyEditOverlay = destroyEditOverlay;
            var _PDFJSAnnotate=__webpack_require__(1);
            var _PDFJSAnnotate2=_interopRequireDefault(_PDFJSAnnotate);
            var _appendChild=__webpack_require__(11);
            var _appendChild2=_interopRequireDefault(_appendChild);
            var _event=__webpack_require__(4);
            var _utils=__webpack_require__(6);
            var _ajaxloader= __webpack_require__(36);
            var _renderPoint= __webpack_require__(17);
            var _questionsRenderer = __webpack_require__(38);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            function _toConsumableArray(arr){if(Array.isArray(arr)){for(var i=0,arr2=Array(arr.length);i<arr.length;i++){arr2[i]=arr[i];}return arr2;}else{return Array.from(arr);}}
            var _enabled=false;
            var isDragging=false,overlay=void 0,overlayOld=void 0,annoId=0, isMoved=true;
            var dragOffsetX=void 0,dragOffsetY=void 0,dragStartX=void 0,dragStartY=void 0;
            var OVERLAY_BORDER_SIZE=3;
            var SIZE = 20;
            /**
            * Create an overlay for editing an annotation.
            *
            * @param {Element} target The annotation element to apply overlay for
            */function createEditOverlay(target){
                destroyEditOverlay();
                overlay=document.createElement('div');
                var anchor=document.createElement('a');
                var node = (0,_utils.findSVGContainer)(target);
                if(node){
                    var parentNode=node.parentNode;
                    var id=target.getAttribute('data-pdf-annotate-id');
                    var rect=(0,_utils.getAnnotationRect)(target);
                    var styleLeft=rect.left-OVERLAY_BORDER_SIZE;
                    var styleTop=rect.top-OVERLAY_BORDER_SIZE;
                    overlay.setAttribute('id','pdf-annotate-edit-overlay');
                    overlay.setAttribute('data-target-id',id);
                    overlay.style.boxSizing='content-box';
                    overlay.style.position='absolute';
                    overlay.style.top=styleTop+'px';
                    overlay.style.left=styleLeft+'px';
                    overlay.style.width=rect.width+'px';
                    overlay.style.height=rect.height+'px';
                    overlay.style.border=OVERLAY_BORDER_SIZE+'px solid '+_utils.BORDER_COLOR;
                    overlay.style.borderRadius=OVERLAY_BORDER_SIZE+'px';
                    overlay.style.zIndex=22;
                    anchor.innerHTML='x';
                    anchor.setAttribute('href','javascript://');
                    anchor.style.background='#fff';
                    anchor.style.borderRadius='20px';
                    anchor.style.border='1px solid #bbb';
                    anchor.style.color='#bbb';
                    anchor.style.fontSize='12px';
                    anchor.style.padding='0px 3px 7px';
                    anchor.style.textAlign='center';
                    anchor.style.textDecoration='none';
                    anchor.style.position='absolute';
                    anchor.style.top='-10px';
                    anchor.style.right='-10px';
                    anchor.style.width='11px';
                    anchor.style.height='11px';
                    anchor.style.boxSizing = 'content-box';
                    overlay.appendChild(anchor);
                    parentNode.appendChild(overlay);
                    document.addEventListener('click',handleDocumentClick);
                    document.addEventListener('keyup',handleDocumentKeyup);
                    document.addEventListener('mousedown',handleDocumentMousedown);
                    anchor.addEventListener('click',deleteAnnotation);
                    anchor.addEventListener('mouseover',function(){anchor.style.color='#35A4DC';anchor.style.borderColor='#999';anchor.style.boxShadow='0 1px 1px #ccc';});
                    anchor.addEventListener('mouseout',function(){anchor.style.color='#bbb';anchor.style.borderColor='#bbb';anchor.style.boxShadow='';});
                    overlay.addEventListener('mouseover',function(){if(!isDragging){anchor.style.display='';}});
                    overlay.addEventListener('mouseout',function(){anchor.style.display='none';});
                    overlayOld = {x: overlay.style.top, y: overlay.style.left};
                }
            }/**
            * Destroy the edit overlay if it exists.
            */function destroyEditOverlay(){
                if(overlay && overlay.parentNode !== null){
                    overlay.parentNode.removeChild(overlay);
                    overlay=null;
                }
                document.removeEventListener('click',handleDocumentClick);
                document.removeEventListener('keyup',handleDocumentKeyup);
                document.removeEventListener('mousedown',handleDocumentMousedown);
                document.removeEventListener('mousemove',handleDocumentMousemove);
                document.removeEventListener('mouseup',handleDocumentMouseup);
                (0,_utils.enableUserSelect)();
            }/**
            * Delete currently selected annotation
            */function deleteAnnotation(){
                if(!overlay){
                    return;
                }
                annoId=overlay.getAttribute('data-target-id');
                if (_capabilities.deleteany) {
                    notification.confirm(M.util.get_string('deletingCommentTitle','pdfannotator'),M.util.get_string('deletingAnnotation_manager','pdfannotator'),M.util.get_string('yesButton', 'pdfannotator'), M.util.get_string('cancelButton', 'pdfannotator'),deleteAnnotationCallback);
                } else {
                    notification.confirm(M.util.get_string('deletingCommentTitle','pdfannotator'),M.util.get_string('deletingAnnotation_student','pdfannotator'),M.util.get_string('yesButton', 'pdfannotator'), M.util.get_string('cancelButton', 'pdfannotator'),deleteAnnotationCallback);
                }
            }
            /**
             * Is called if the user confirms to delete the annotation.
             * This function destroys the annotation and deletes it from the database
             * @returns {undefined}
             */
            function deleteAnnotationCallback(){
                var annotationId;
                if(!overlay){
                    annotationId = annoId;
                }else{
                    annotationId=overlay.getAttribute('data-target-id');
                }
                var nodes=document.querySelectorAll('[data-pdf-annotate-id="'+annotationId+'"]');
                
                _PDFJSAnnotate2.default.getStoreAdapter().deleteAnnotation(_documentObject.annotatorid,annotationId)
                    .then(function(data){
                        if(data.status === "success"){
                            // destroy blue box (overlay)
                            destroyEditOverlay();
                            // destroy annotation DOM element
                            [].concat(_toConsumableArray(nodes)).forEach(function(n){
                                n.parentNode.removeChild(n);
                            });
                            // destroy Commentsfield
                            document.querySelector('.comment-list-container').innerHTML = '';
                            document.querySelector('.comment-list-form').setAttribute('style', 'display:none');
                            var visiblePageNum = document.getElementById('currentPage').value;
                            _questionsRenderer.renderQuestions(_documentObject.annotatorid,visiblePageNum);
                        }else{
                            
                        }
                    }, function(err){
                        notification.addNotification({
                            message: M.util.get_string('error:deleteAnnotation','pdfannotator'),
                            type: "error"
                        });
                        console.error(M.util.get_string('error:deleteAnnotation', 'pdfannotator'));     
                    });
            }
            
            /**
             * Handle document.click event
             *
             * @param {Event} e The DOM event that needs to be handled
             */function handleDocumentClick(e){
                //if the click is on an input field or link or icon in editor toolbar ('I') nothing should happen. 
                if(e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'BUTTON' || e.target.tagName === 'I'){
                    return;
                }
                //if the click is on the Commentlist nothing should happen.
                if(((typeof e.target.getAttribute('id')!='string') && e.target.id.indexOf('comment') !== -1) || e.target.className.indexOf('comment') !== -1 || e.target.parentNode.className.indexOf('comment') !== -1 || e.target.parentNode.className.indexOf('chat') !== -1){
                    return;
                }
                if(!(0,_utils.findSVGAtPoint)(e.clientX, e.clientY)){
                    return;
                }// Remove current overlay
                var overlay = document.getElementById('pdf-annotate-edit-overlay');
                if(overlay){
                    if(isDragging || e.target===overlay){
                        return;
                    }
                    destroyEditOverlay();
                }
                isMoved = false;
            }
            /**
            * Handle document.keyup event
            *
            * @param {Event} e The DOM event that needs to be handled
            */function handleDocumentKeyup(e){
                if (overlay&&e.keyCode === 46 && !e.target.closest('.edit-comment-form') && !e.target.closest('.comment-list-form')) {
                    deleteAnnotation();
                }
            }
            /**
            * Handle document.mousedown event
            *
            * @param {Event} e The DOM event that needs to be handled
            */function handleDocumentMousedown(e){
                if(e.target!==overlay){return;}// Highlight and strikeout annotations are bound to text within the document.
                // It doesn't make sense to allow repositioning these types of annotations.
                var annotationId=overlay.getAttribute('data-target-id');
                var target=document.querySelector('[data-pdf-annotate-id="'+annotationId+'"]');
                var type=target.getAttribute('data-pdf-annotate-type');
                if(type==='highlight' || type==='strikeout'){
                    return;
                }
                isDragging=true;
                dragOffsetX=e.clientX;
                dragOffsetY=e.clientY;
                dragStartX=overlay.offsetLeft;
                dragStartY=overlay.offsetTop;
                overlay.style.background='rgba(255, 255, 255, 0.7)';
                overlay.style.setProperty('cursor', 'move', 'important');
                overlay.querySelector('a').style.display='none';
                document.addEventListener('mousemove', handleDocumentMousemove);
                document.addEventListener('mouseup', handleDocumentMouseup);
                (0,_utils.disableUserSelect)();
            }/**
            * Handle document.mousemove event
            * 
            *
            * @param {Event} e The DOM event that needs to be handled
            */function handleDocumentMousemove(e){
                var annotationId=overlay.getAttribute('data-target-id');
                var parentNode=overlay.parentNode;
                var rect=parentNode.getBoundingClientRect();
                var y=dragStartY+(e.clientY-dragOffsetY);
                var x=dragStartX+(e.clientX-dragOffsetX);
                var minY=0;
                var maxY=rect.height;
                var minX=0;
                var maxX=rect.width;
                if(y>minY&&y+overlay.offsetHeight<maxY){
                    overlay.style.top=y+'px';
                }
                if(x>minX&&x+overlay.offsetWidth<maxX){
                    overlay.style.left=x+'px';
                }
                isMoved = true;
            }
            /**
            * Handle document.mouseup event
            * This function is responsible for shifting areas, pins, textboxes and drawings
            *
            * @param {Event} e The DOM event that needs to be handled
            */function handleDocumentMouseup(e){
                var annotationId=overlay.getAttribute('data-target-id');
                var target=document.querySelectorAll('[data-pdf-annotate-id="'+annotationId+'"]');
                var type=target[0].getAttribute('data-pdf-annotate-type');                                
                var svg=overlay.parentNode.querySelector('svg.annotationLayer');    
                var _getMetadata2=(0,_utils.getMetadata)(svg);                
                var documentId=_getMetadata2.documentId;    
                var pageNumber = _getMetadata2.pageNumber;
                var isFirefox = /firefox/i.test(navigator.userAgent);
                    
                overlay.querySelector('a').style.display='';
                function getDelta(propX,propY){
                    return calcDelta(parseInt(target[0].getAttribute(propX),10),parseInt(target[0].getAttribute(propY),10));
                }
                function calcDelta(x,y){
                    return {
                                deltaX:OVERLAY_BORDER_SIZE+(0,_utils.scaleDown)(svg,{x:overlay.offsetLeft}).x-x,
                                deltaY:OVERLAY_BORDER_SIZE+(0,_utils.scaleDown)(svg,{y:overlay.offsetTop}).y-y
                            };
                }
                (0,_ajaxloader.showLoader)();
                var oldX = 0;
                var oldY= 0;
                var viewY = dragStartY;
                var viewX = dragStartX;
                _PDFJSAnnotate2.default.getStoreAdapter().getAnnotation(documentId,annotationId).then(function(annotation){
                    oldX = annotation['annotation'].x;
                    oldY = annotation['annotation'].y;
                    (0,_ajaxloader.showLoader)();
                            if(['area','point','textbox'].indexOf(type)>-1){
                                (function(){
                                    var _getDelta=getDelta('x','y');   
                                    var deltaX=_getDelta.deltaX;          
                                    var deltaY=_getDelta.deltaY;
                                    [].concat(_toConsumableArray(target)).forEach(function(t,i){
                                        // adjust y coordinate if necessary                              
                                        if(deltaY!==0){
                                            var modelY=parseInt(t.getAttribute('y'),10)+deltaY;
                                            viewY=modelY;
                                            if(type==='point'){
                                                //+SIZE, because the pin should not be rendered right under the click point, instead it should be rendered centered above the click point
                                                modelY += SIZE;
                                            }
                                            if(type==='textbox'){
                                                viewY+=parseInt(annotation['annotation'].size,10);
                                            }if(type==='point' && !isFirefox){
                                                viewY=(0,_utils.scaleUp)(svg,{viewY:viewY}).viewY;
                                            }
                                            if(isFirefox){
                                                viewY -= 6;
                                            }
                                            if(annotation.rectangles){
                                                annotation.rectangles[i].y=modelY;
                                            }else {
                                                annotation['annotation'].y=modelY; // .toString();
                                            }

                                        }
                                        // adjust x coordinate if necessary
                                        if(deltaX!==0){
                                            var modelX=parseInt(t.getAttribute('x'),10)+deltaX;
                                            viewX=modelX;
                                            //+(1/4)Size, because the pin should be rendered centered of the click point not on the righthand side.
                                            if(type==='point'){
                                                modelX += (SIZE/4);
                                            }
                                            if(type==='point' && !isFirefox){
                                                viewX=(0,_utils.scaleUp)(svg,{viewX:viewX}).viewX;

                                            }
                                            if(isFirefox ){
                                                viewX -= 6;
                                            }
                                            //t.setAttribute('x',viewX);
                                            if(annotation.rectangles){
                                                annotation.rectangles[i].x=modelX;
                                            } else {
                                                annotation['annotation'].x = modelX; // .toString(); 
                                            }
                                        }
                                    });
                                })();
                            } else if(type==='drawing'){
                                (function(){
                                    var rect=(0,_utils.scaleDown)(svg,(0,_utils.getAnnotationRect)(target[0]));

                                    var _annotation$lines$=_slicedToArray(annotation['annotation'].lines[0],2);

                                    var originX=_annotation$lines$[0];
                                    var originY=_annotation$lines$[1];

                                    var _calcDelta=calcDelta(originX,originY);

                                    var deltaX=_calcDelta.deltaX;

                                    var deltaY=_calcDelta.deltaY;// origin isn't necessarily at 0/0 in relation to overlay x/y
                                    // adjust the difference between overlay and drawing coords

                                    deltaY+=originY-rect.top;
                                    deltaX+=originX-rect.left;

                                    annotation['annotation'].lines.forEach(function(line,i){
                                        var _annotation$lines$i=_slicedToArray(annotation['annotation'].lines[i],2);
                                        var x=_annotation$lines$i[0];
                                        var y=_annotation$lines$i[1];
                                        annotation['annotation'].lines[i][0]=(0,_utils.roundDigits)(x+deltaX,4);
                                        annotation['annotation'].lines[i][1]=(0,_utils.roundDigits)(y+deltaY,4);
                                    });
                                })();
                            }
                    (function editAnnotation(){
                        if(!overlay){
                            return;
                        }
                        if(dragStartX === viewX && dragStartY === viewY) {
                            return;
                        }
                        annoId=overlay.getAttribute('data-target-id');
                        notification.confirm(M.util.get_string('editAnnotationTitle','pdfannotator'),M.util.get_string('editAnnotation','pdfannotator'),M.util.get_string('yesButton', 'pdfannotator'), M.util.get_string('cancelButton', 'pdfannotator'), editAnnotationCallback, overlayToOldPlace);

                    })();
                    
                    function overlayToOldPlace() {
                        // Overlay back to old place.
                        overlay.style.top = overlayOld.x;
                        overlay.style.left = overlayOld.y;
                        // Show comments.
                        _event.fireEvent('annotation:click',target[0]);
                    }
                    /**
                     * Is called if the user confirms to move the annotation.
                     * This function destroys the annotation and deletes it from the database
                     * @returns {undefined}
                     */
                    function editAnnotationCallback(){
                        _PDFJSAnnotate2.default.getStoreAdapter().editAnnotation(documentId,pageNumber,annotationId,annotation).then(function(success){
                            (0,_ajaxloader.hideLoader)();
                            if(!success) {
                                overlayToOldPlace();

                                // Notification, that the annotation could not be edited.
                                notification.addNotification({
                                  message: M.util.get_string('editNotAllowed','pdfannotator'),
                                  type: "error"
                                });
                                setTimeout(function(){
                                    let notificationpanel = document.getElementById("user-notifications");
                                    while (notificationpanel.hasChildNodes()) {  
                                        notificationpanel.removeChild(notificationpanel.firstChild);
                                    } 
                                }, 4000);
                            }else{
                                if(['area','point','textbox'].indexOf(type)>-1){
                                    (function(){
                                        [].concat(_toConsumableArray(target)).forEach(function(t,i){
                                            t.setAttribute('y',viewY);
                                            t.setAttribute('x',viewX);
                                        });
                                    })();
                                }else if(type==='drawing'){
                                    target[0].parentNode.removeChild(target[0]);
                                    (0,_appendChild2.default)(svg,annotation['annotation']); 
                                }
                                //_renderPoint(annotation['annotation']);
                                _event.fireEvent('annotation:click',target[0]);
                            }
                        }, function (err){
                            overlayToOldPlace();

                            notification.addNotification({
                                message: M.util.get_string('error:editAnnotation','pdfannotator'),
                                type: "error"
                            });
                        });
                            
                    }

                }, function (err){
                    notification.addNotification({
                        message: M.util.get_string('error:getAnnotation','pdfannotator'),
                        type: "error"
                    });
                });
//                getComments(_fileid, annotationId);
                setTimeout(function(){isDragging=false;},0);
                overlay.style.background='';
                overlay.style.cursor='';
                document.removeEventListener('mousemove',handleDocumentMousemove);
                document.removeEventListener('mouseup',handleDocumentMouseup);
                (0,_utils.enableUserSelect)();
            }
        
            /**
             * Handle annotation.click event
             *
             * @param {Element} e The annotation element that was clicked
             */function handleAnnotationClick(target){
                if(isDragging){
                    return;
                }
                createEditOverlay(target);
            }/**
             * Enable edit mode behavior.
             */function enableEdit(){
                if(_enabled){return;}
                _enabled=true;
                document.getElementById('content-wrapper').classList.add('cursor-edit');
                (0,_event.addEventListener)('annotation:click',handleAnnotationClick);
            };/**
             * Disable edit mode behavior.
             */function disableEdit(){
                 destroyEditOverlay();
                 if(!_enabled){return;}
                 _enabled=false;document.getElementById('content-wrapper').classList.remove('cursor-edit');(0,_event.removeEventListener)('annotation:click',handleAnnotationClick);
            };
    /***/},
    /* 30 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.setPen=setPen;
            exports.enablePen=enablePen;
            exports.disablePen=disablePen;
            var _PDFJSAnnotate=__webpack_require__(1);
            var _PDFJSAnnotate2=_interopRequireDefault(_PDFJSAnnotate);
            var _appendChild=__webpack_require__(11);
            var _appendChild2=_interopRequireDefault(_appendChild);
            var _utils=__webpack_require__(6);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            var _enabled=false;
            var _penSize=void 0;
            var _penColor=void 0;
            var path=void 0;
            var lines=void 0;
            var _svg=void 0;/**
            
            * Handle document.mousedown event
            */function handleDocumentMousedown(){
                path=null;
                lines=[];
                document.addEventListener('mousemove',handleDocumentMousemove);
                document.addEventListener('mouseup',handleDocumentMouseup);
              }
            /**
            * Handle document.mouseup event
            *
            * @param {Event} e The DOM event to be handled
            */function handleDocumentMouseup(e){
                var svg=void 0;
                if(lines.length>1&&(svg=(0,_utils.findSVGAtPoint)(e.clientX,e.clientY))){
                    var _getMetadata=(0,_utils.getMetadata)(svg);
                    var documentId=_getMetadata.documentId;
                    var pageNumber=_getMetadata.pageNumber;
                    _PDFJSAnnotate2.default.getStoreAdapter().addAnnotation(documentId,pageNumber,{type:'drawing',width:_penSize,color:_penColor,lines:lines})
                            .then(function(annotation){
                                if(path){svg.removeChild(path);}
                                (0,_appendChild2.default)(svg,annotation);
                            }, function (err){
                                // Remove path
                                if(path){svg.removeChild(path);}
                                notification.addNotification({
                                    message: M.util.get_string('error:addAnnotation','pdfannotator'),
                                    type: "error"
                                });
                            });
                }
                document.removeEventListener('mousemove',handleDocumentMousemove);
                document.removeEventListener('mouseup',handleDocumentMouseup);
            }/**
            * Handle document.mousemove event
            *
            * @param {Event} e The DOM event to be handled
            */function handleDocumentMousemove(e){
                savePoint(e.clientX,e.clientY);}/**
            * Handle document.keyup event
            *
            * @param {Event} e The DOM event to be handled
            */function handleDocumentKeyup(e){// Cancel rect if Esc is pressed
                if(e.keyCode===27){
                    lines=null;
                    path.parentNode.removeChild(path);
                    document.removeEventListener('mousemove',handleDocumentMousemove);
                    document.removeEventListener('mouseup',handleDocumentMouseup);
                }
            }/**
            * Save a point to the line being drawn.
            *
            * @param {Number} x The x coordinate of the point
            * @param {Number} y The y coordinate of the point
            */
            function savePoint(x,y){
                var svg=(0,_utils.findSVGAtPoint)(x,y);
                if(!svg){return;}
                var rect=svg.getBoundingClientRect();
                var point=(0,_utils.scaleDown)(svg,{x:(0,_utils.roundDigits)(x-rect.left,4),y:(0,_utils.roundDigits)(y-rect.top,4)});
                lines.push([point.x,point.y]);
                if(lines.length<=1){return;}
                if(path){svg.removeChild(path);}
                path=(0,_appendChild2.default)(svg,{type:'drawing',color:_penColor,width:_penSize,lines:lines});
            }
            function handleContentTouchstart(e) {
                path=null;
                lines=[];
                _svg = (0, _utils.findSVGAtPoint)(e.touches[0].clientX, e.touches[0].clientY);
                saveTouchPoint(e.touches[0].clientX,e.touches[0].clientY);
            }
            function handleContentTouchmove(e) {
                e.preventDefault();
                saveTouchPoint(e.touches[0].clientX,e.touches[0].clientY);
            }
            function handleContentTouchend(e) {
                if (lines.length > 1){
                    var _getMetadata=(0,_utils.getMetadata)(_svg);
                    var documentId=_getMetadata.documentId;
                    var pageNumber=_getMetadata.pageNumber;
                    _PDFJSAnnotate2.default.getStoreAdapter().addAnnotation(documentId,pageNumber,{type:'drawing',width:_penSize,color:_penColor,lines:lines})
                            .then(function(annotation){
                                if(path){_svg.removeChild(path);}
                                (0,_appendChild2.default)(_svg,annotation);
                            }, function (err){
                                // Remove path
                                if(path){_svg.removeChild(path);}
                                notification.addNotification({
                                    message: M.util.get_string('error:addAnnotation','pdfannotator'),
                                    type: "error"
                                });
                            });
                }
            }
            function handleContentTouchcancel(e) {
                lines=null;
                path.parentNode.removeChild(path);
            }
            
            /* Save a touchpoint to the line being drawn.
            *
            * @param {Number} x The x coordinate of the point
            * @param {Number} y The y coordinate of the point
            */function saveTouchPoint(x,y){
                if(!_svg){return;}
                var rect=_svg.getBoundingClientRect();
                var point=(0,_utils.scaleDown)(_svg,{x:(0,_utils.roundDigits)(x-rect.left,4),y:(0,_utils.roundDigits)(y-rect.top,4)});
                lines.push([point.x,point.y]);
                if(lines.length<=1){return;}
                if(path){_svg.removeChild(path);}
                path=(0,_appendChild2.default)(_svg,{type:'drawing',color:_penColor,width:_penSize,lines:lines});
            }

            /**
            * Set the attributes of the pen.
            *
            * @param {Number} penSize The size of the lines drawn by the pen
            * @param {String} penColor The color of the lines drawn by the pen
            */function setPen(){var penSize=arguments.length<=0||arguments[0]===undefined?1:arguments[0];var penColor=arguments.length<=1||arguments[1]===undefined?'000000':arguments[1];_penSize=parseInt(penSize,10);_penColor=penColor;}/**
            * Enable the pen behavior
            */function enablePen(){
                           if(_enabled){
                               return;
                           }
                           _enabled=true;
                           var contentWrapper = document.getElementById('content-wrapper');
                           contentWrapper.classList.add('cursor-pen');
                           document.addEventListener('mousedown',handleDocumentMousedown);
                           document.addEventListener('keyup',handleDocumentKeyup);
                           contentWrapper.addEventListener('touchstart',handleContentTouchstart);
                           contentWrapper.addEventListener('touchmove',handleContentTouchmove);
                           contentWrapper.addEventListener('touchend',handleContentTouchend);
                           contentWrapper.addEventListener('touchcancel',handleContentTouchcancel);
                           (0,_utils.disableUserSelect)();
                       }/**
            * Disable the pen behavior
            */function disablePen(){
                           if(!_enabled){
                               return;
                           }
                           _enabled=false;
                           var contentWrapper = document.getElementById('content-wrapper');
                           contentWrapper.classList.remove('cursor-pen');
                           document.removeEventListener('mousedown',handleDocumentMousedown);
                           document.removeEventListener('keyup',handleDocumentKeyup);
                           contentWrapper.removeEventListener('touchstart',handleContentTouchstart);
                           contentWrapper.removeEventListener('touchmove',handleContentTouchmove);
                           contentWrapper.removeEventListener('touchend',handleContentTouchend);
                           contentWrapper.removeEventListener('touchcancel',handleContentTouchcancel);
                           (0,_utils.enableUserSelect)();
                       }
    /***/},
    /* 31 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            var _typeof=typeof Symbol==="function"&&_typeof2(Symbol.iterator)==="symbol"?function(obj){return typeof obj==='undefined'?'undefined':_typeof2(obj);}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol?"symbol":typeof obj==='undefined'?'undefined':_typeof2(obj);};
            exports.enablePoint=enablePoint;
            exports.disablePoint=disablePoint;
            var _PDFJSAnnotate=__webpack_require__(1);
            var _PDFJSAnnotate2=_interopRequireDefault(_PDFJSAnnotate);
            var _appendChild=__webpack_require__(11);
            var _appendChild2=_interopRequireDefault(_appendChild);
            var _utils=__webpack_require__(6);
            var _commentWrapper = __webpack_require__(35);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            var _enabled=false;
            var data=void 0;
            var _svg=void 0;
            var _rect=void 0;
            var dragging=false;
            //Test
            var textarea = void 0;
            var submitbutton = void 0;
            var form = void 0;
            var annotationObj;
            var documentId = -1;
            var pageNumber = 1;
            
            /**
            * Handle document.mouseup event
            *
            * @param {Event} The DOM event to be handled
            */function handleDocumentMouseup(e){
                //if the click is in comment wrapper area nothing should happen.
                var commentWrapperNodes = document.querySelectorAll('div#comment-wrapper')[0];
                var clickedElement;
                if(e.target.id) {
                    clickedElement = '#' + e.target.id;
                } else if(e.target.className[0]) {
                    clickedElement = '.' + e.target.className;
                } else {
                    clickedElement = '';
                }
                if(clickedElement && commentWrapperNodes.querySelector(clickedElement)) {
                    return;
                }

                //If Modal Dialogue beeing clicked.
                var clickedMoodleDialogue = e.target.closest('.moodle-dialogue-base');
                if(clickedMoodleDialogue) {
                    return;
                }

               //if the click is on the Commentlist nothing should happen.
                if(((typeof e.target.getAttribute('id')=='string') && e.target.id.indexOf('comment') !== -1) || e.target.className.indexOf('comment') !== -1 || e.target.parentNode.className.indexOf('comment') !== -1 || e.target.parentNode.className.indexOf('chat') !== -1 || e.target.tagName == 'INPUT' || e.target.tagName == 'LABEL'){
                    return;
                }
                _svg = (0,_utils.findSVGAtPoint)(e.clientX,e.clientY);
                if(!_svg){
                    return;
                }
                var _getMetadata=(0,_utils.getMetadata)(_svg);
                documentId=_getMetadata.documentId;
                pageNumber=_getMetadata.pageNumber;
                deleteUndefinedPin();
                var fn = () => {
                    [textarea,data] = (0,_commentWrapper.openComment)(e,handleCancelClick,handleSubmitClick,handleToolbarClick,handleSubmitBlur,'pin');
                    renderPin();
                }
                _commentWrapper.loadEditor('add', 0, fn);
            }
            
            // Reset dragging to false.
            function handleContentTouchstart(e){
                dragging = false;
            }
            // Set dragging to true, so we stop the handleContentTouchend function from running.
            function handleContentTouchmove(e){
                dragging = true;
            }
            /**
            * Handle content.touchend event
            *
            * @param {Event} The DOM event to be handled
            */function handleContentTouchend(e){
                // If the mobile user was scrolling return from this function.
                if (dragging) {
                    return;
                }
                //if the click is on the Commentlist nothing should happen.
                if(((typeof e.target.getAttribute('id')=='string') && e.target.id.indexOf('comment') !== -1) || e.target.className.indexOf('comment') !== -1 || e.target.parentNode.className.indexOf('comment') !== -1 || e.target.parentNode.className.indexOf('chat') !== -1 || e.target.tagName == 'INPUT' || e.target.tagName == 'LABEL'){
                    return;
                }
                let svg = (0,_utils.findSVGAtPoint)(e.changedTouches[0].clientX,e.changedTouches[0].clientY);
                if(!svg){
                    return;
                }
                var _getMetadata=(0,_utils.getMetadata)(svg);
                documentId=_getMetadata.documentId;
                pageNumber=_getMetadata.pageNumber;
                deleteUndefinedPin();
                var coordinates = {x: e.changedTouches[0].clientX, y: e.changedTouches[0].clientY};
                renderPinTouchscreen(coordinates);
                [textarea,data] = (0,_commentWrapper.openCommentTouchscreen)(e,handleCancelClick,handleSubmitClick,handleToolbarClick,handleSubmitBlur,'pin');
            }
            
            /**
             * If the toolbar is clicked, the point tool should be disabled and the commentswrapper should be closed
             * @param {type} e
             * @returns {undefined}
             */
            function handleToolbarClick(e){
                disablePoint();
                document.querySelector('.toolbar').removeEventListener('click',handleToolbarClick);
                
                (0,_commentWrapper.closeComment)(documentId,pageNumber,handleSubmitClick,handleCancelClick,null,false);
                deleteUndefinedPin();
                textarea = void 0;
            }
            
            function handleSubmitClick(e){
                savePoint(_svg);
                return false;
            }
            function handleCancelClick(e){
                textarea = void 0;
                //delete the temporay rendered Pin
                deleteUndefinedPin();
                enablePoint();
                (0,_commentWrapper.closeComment)(documentId,pageNumber,handleSubmitClick,handleCancelClick,null,false);
            }
            function handleSubmitBlur(){
                disablePoint();
                textarea = void 0;
                (0,_commentWrapper.closeComment)(documentId,pageNumber,handleSubmitClick,handleCancelClick,null,false);
            }
            /**
            * Handle input.blur event
            */function handleInputBlur(){/*disablePoint();*/savePoint();}/**
            * Handle input.keyup event
            *
            * @param {Event} e The DOM event to handle
            */function handleInputKeyup(e){if(e.keyCode===27){disablePoint();closeInput();}else if(e.keyCode===13){/*disablePoint();*/savePoint();}}

            function renderPin(){
                var clientX=(0,_utils.roundDigits)(data.x,4);
                var clientY=(0,_utils.roundDigits)(data.y,4);
                var content=textarea.value.trim();
                var svg=(0,_utils.findSVGAtPoint)(clientX,clientY);
                if(!svg){
                    return{v:void 0};
                }
                _rect=svg.getBoundingClientRect();
                var annotation = initializeAnnotation(_rect,svg);
                annotationObj = annotation;
                annotation.color = true;
                (0,_appendChild2.default)(svg,annotation);
            }
            function renderPinTouchscreen(coordinates){
                var clientX=(0,_utils.roundDigits)(coordinates.x,4);
                var clientY=(0,_utils.roundDigits)(coordinates.y,4);
                var svg=(0,_utils.findSVGAtPoint)(clientX,clientY);
                if(!svg){
                    return{v:void 0};
                }
                _rect=svg.getBoundingClientRect();
                var annotation = initializeAnnotationTouchscreen(_rect,svg,coordinates);
                annotationObj = annotation;
                annotation.color = true;
                (0,_appendChild2.default)(svg,annotation);
            }
            
            /**
             * This function deletes all annotations which data-pdf-annotate-id is undefined. An annotation is undefined, if it is only temporarily displayed.
             * @returns {undefined}
             */
            function deleteUndefinedPin(){
                let n = document.querySelector('[data-pdf-annotate-id="undefined"]');
                if(n){
                    n.parentNode.removeChild(n);
                }
            }
            
            function initializeAnnotation(rect,svg){
                var clientX=(0,_utils.roundDigits)(data.x,4);
                var clientY=(0,_utils.roundDigits)(data.y,4);
                return Object.assign({type:'point'},(0,_utils.scaleDown)(svg,{x:clientX-((0,_utils.roundDigits)(rect.left,4)),y:clientY-((0,_utils.roundDigits)(rect.top,4))}));
            }
            function initializeAnnotationTouchscreen(rect,svg,coordinates){
                var clientX=(0,_utils.roundDigits)(coordinates.x,4);
                var clientY=(0,_utils.roundDigits)(coordinates.y,4);                
                return Object.assign({type:'point'},(0,_utils.scaleDown)(svg,{x:clientX-((0,_utils.roundDigits)(rect.left,4)),y:clientY-((0,_utils.roundDigits)(rect.top,4))}));
            }
            /**
            * Save a new point annotation from input
            */
            function savePoint(svg = null){
                if(textarea.value.trim().length > 0){
                    disablePoint();
                    var page = pageNumber;
                    if (!svg) {
                        var elements=document.querySelectorAll('svg[data-pdf-annotate-container="true"]');
                        var svg=elements[page-1];
                    }
                    var _ret=function(){
                        var clientX=(0,_utils.roundDigits)(data.x,4);
                        var clientY=(0,_utils.roundDigits)(data.y,4);
                        var content=textarea.value.trim();
                        if(!svg){
                            return{v:void 0};
                        }
                        var rect=svg.getBoundingClientRect();
                        
                        var _getMetadata=(0,_utils.getMetadata)(svg);
                        var documentId=_getMetadata.documentId;
                        var pageNumber=page;
                        var annotation=Object.assign({type:'point'},(0,_utils.scaleDown)(svg,{x:clientX-((0,_utils.roundDigits)(_rect.left,4)),y:clientY-((0,_utils.roundDigits)(_rect.top,4))}));
                        var commentVisibility= read_visibility_of_checkbox();
                        var isquestion = 1; //The Point was created so the comment is a question
                        _PDFJSAnnotate2.default.getStoreAdapter().addAnnotation(documentId,pageNumber,annotation)
                                .then(function(annotation){
                                    _PDFJSAnnotate2.default.getStoreAdapter().addComment(documentId,annotation.uuid,content,commentVisibility,isquestion)
                                    .then(function(msg){
                                        if(!msg) { throw new Error(); }
                                        deleteUndefinedPin();
                                //get old y-koordniate, because of scrolling
                                        annotation.y = annotationObj.y;
                                        (0,_appendChild2.default)(svg,annotation); 
                                        document.querySelector('.toolbar').removeEventListener('click',handleToolbarClick);
                                        document.querySelector('button.cursor').click();
                                        (0,_commentWrapper.showCommentsAfterCreation)(annotation.uuid);
                                    })
                                    .catch(function(err){
                                        /*if there is an error in addComment, the annotation will be deleted!*/ 
                                        var annotationid = annotation.uuid;
                                        _PDFJSAnnotate2.default.getStoreAdapter().deleteAnnotation(documentId,annotationid, false);
                                    });
                                }, function (err){
                                    deleteUndefinedPin();
                                    notification.addNotification({
                                        message: M.util.get_string('error:addAnnotation','pdfannotator'),
                                        type: "error"
                                    }); 
                                });
                    }();
                    if((typeof _ret==='undefined'?'undefined':_typeof(_ret))==="object"){
                        (0,_commentWrapper.closeComment)(documentId,pageNumber,handleSubmitClick,handleCancelClick,null,true);
                        return _ret.v;
                    }
                    textarea = void 0;
                    (0,_commentWrapper.closeComment)(documentId,pageNumber,handleSubmitClick,handleCancelClick,null,true);
                }else{
                    notification.addNotification({
                        message: M.util.get_string('min0Chars', 'pdfannotator'),
                        type: "error"
                    });
                    textarea.focus();
                }
            }
            function closeInput(){data.removeEventListener('blur',handleInputBlur);data.removeEventListener('keyup',handleInputKeyup);document.body.removeChild(data);data=null;}/**
            * Enable point annotation behavior
            */function enablePoint(){
                if(_enabled){
                    return;
                }
                _enabled=true;
                document.getElementById('content-wrapper').classList.add('cursor-point');
                document.addEventListener('mouseup',handleDocumentMouseup);
                document.addEventListener('touchstart', handleContentTouchstart);
                document.addEventListener('touchmove', handleContentTouchmove);
                document.addEventListener('touchend',handleContentTouchend);
            }
            /**
            * Disable point annotation behavior
            */function disablePoint(){
                _enabled=false;
                document.getElementById('content-wrapper').classList.remove('cursor-point');
                document.removeEventListener('mouseup',handleDocumentMouseup);
                document.removeEventListener('touchstart', handleContentTouchstart);
                document.removeEventListener('touchmove', handleContentTouchmove);
                document.removeEventListener('touchend',handleContentTouchend);
            }
    /***/},
 /* 32 */
    /***/function(module,exports,__webpack_require__){
        'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.enableRect=enableRect;
            exports.disableRect=disableRect;
            var _PDFJSAnnotate=__webpack_require__(1);
            var _PDFJSAnnotate2=_interopRequireDefault(_PDFJSAnnotate);
            var _appendChild=__webpack_require__(11);
            var _appendChild2=_interopRequireDefault(_appendChild);
            var _setAttributes=__webpack_require__(14);
            var _setAttributes2=_interopRequireDefault(_setAttributes);
            var _utils=__webpack_require__(6);
            var _event = __webpack_require__(4);
            
            var _commentWrapper = __webpack_require__(35);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            function _toConsumableArray(arr){if(Array.isArray(arr)){for(var i=0,arr2=Array(arr.length);i<arr.length;i++){arr2[i]=arr[i];}return arr2;}else{return Array.from(arr);}}
            var _enabled=false;
            var _type=void 0;
            var overlay=void 0;
            var originY=void 0;
            var originX=void 0;
            var documentId = -1;
            var pageNumber = 1;
            
            var textarea = void 0;
            var submitbutton = void 0;
            var resetbutton = void 0;
            var form = void 0;
            var data=void 0;
            var rectsSelection = void 0;
            var rectObj;
            var _svg=void 0;
            var rect=void 0;

            /**
            * Get the current window selection as rects
            *
            * @return {Array} An Array of rects
            */
            function getSelectionRects(){
                try{
                    var selection=window.getSelection();
                    try{
                        var helper = selection.anchorNode.className.indexOf('helper');
                    }catch(e){
                        helper = null;
                    }
                    if(helper !== null && helper !== -1){
                        return null;
                    }
                    var range=selection.getRangeAt(0);
                    var rects=range.getClientRects();
                    if(rects.length>0&&rects[0].width>0&&rects[0].height>0){
                        return rects;
                    }
                }catch(e){

                }
                return null;
            }
        /**
        * Handle document.mousedown event
        *
        * @param {Event} e The DOM event to handle
        */
        function handleDocumentMousedown(e){
            if(!(_svg=(0,_utils.findSVGAtPoint)(e.clientX,e.clientY))|| _type!=='area'){
                return;
            }
            rect=_svg.getBoundingClientRect();
            originY=e.clientY;
            originX=e.clientX;
            overlay=document.createElement('div');
            overlay.style.position='absolute';
            overlay.id = 'overlay-rect';
            overlay.style.top=originY-rect.top+'px';
            overlay.style.left=originX-rect.left+'px';
            overlay.style.border='3px solid '+_utils.BORDER_COLOR;
            overlay.style.borderRadius='3px';
            _svg.parentNode.appendChild(overlay);
            document.addEventListener('mousemove',handleDocumentMousemove);
            (0,_utils.disableUserSelect)();
        }

        // Handle document.touchstart event
        function handleDocumentTouchstart(e){
            if(_type =='highlight' || _type == 'strikeout'){
                // Dont show the contextmenu for highlighting and strikeout.
                document.getElementById('content-wrapper').addEventListener('contextmenu', event => {
                    event.preventDefault();
                    event.stopPropagation();
                    event.stopImmediatePropagation();
                    return false;
                });
            }
            
            if(!(_svg=(0,_utils.findSVGAtPoint)(e.touches[0].clientX,e.touches[0].clientY)) || _type!=='area'){
                return;
            }
            // Disable scrolling on the page.
            document.documentElement.style.overflow = 'hidden';
            document.getElementById('content-wrapper').style.overflow = 'hidden';

            rect=_svg.getBoundingClientRect();
            originY=e.touches[0].clientY;
            originX=e.touches[0].clientX;
            overlay=document.createElement('div');
            overlay.style.position='absolute';
            overlay.style.top=originY-rect.top+'px';
            overlay.style.left=originX-rect.left+'px';
            overlay.style.border='3px solid '+_utils.BORDER_COLOR;
            overlay.style.borderRadius='3px';
            _svg.parentNode.appendChild(overlay);
            document.addEventListener('touchmove',handleDocumentTouchmove);
            
            (0,_utils.disableUserSelect)();
        }

        /**
        * Handle document.mousemove event
        *
        * @param {Event} e The DOM event to handle
        */
        function handleDocumentMousemove(e){
            if(originX+(e.clientX-originX)<rect.right){
                overlay.style.width=e.clientX-originX+'px';
            }
            if(originY+(e.clientY-originY)<rect.bottom){
                overlay.style.height=e.clientY-originY+'px';
            }
        }

        // Handle document.touchmove event
        function handleDocumentTouchmove(e){
            if(originX+(e.touches[0].clientX-originX)<rect.right){
                overlay.style.width=e.touches[0].clientX-originX+'px';
            }
            if(originY+(e.touches[0].clientY-originY)<rect.bottom){
                overlay.style.height=e.touches[0].clientY-originY+'px';
            }
        }
        
        /**
         * Tests if the overlay is too small. An overlay is too small if the width or height are less 10 px or are NaN
         * @param {type} overlay
         * @returns {unresolved}
         */
        function isOverlayTooSmall(overlay){
            var width = parseInt(overlay.style.width);
            var height = parseInt(overlay.style.height);
            return isNaN(width) || isNaN(height) || (width<10) || (height < 10);
        }
        /**
        * Handle document.mouseup event
        * concerns area,highlight and strikeout
        * @param {Event} e The DOM event to handle
        */
        function handleDocumentMouseup(e){
            //if the cursor is clicked nothing should happen!
            if((typeof e.target.getAttribute('className')!='string') &&  e.target.className.indexOf('cursor') === -1){
                document.removeEventListener('mousemove',handleDocumentMousemove);
                disableRect();
                if(_type==='area'&&overlay){
                    if(isOverlayTooSmall(overlay)){
                        overlay.parentNode.removeChild(overlay);
                        overlay=null;
                        enableRect(_type);
                        return;
                    }
                    renderRect(_type,[{top:parseInt(overlay.style.top,10)+rect.top,left:parseInt(overlay.style.left,10)+rect.left,width:parseInt(overlay.style.width,10),height:parseInt(overlay.style.height,10)}],null);
                    
                    let fn = () => {
                        [textarea,data] = (0,_commentWrapper.openComment)(e,handleCancelClick,handleSubmitClick,handleToolbarClick,handleSubmitBlur,_type);
                    }
                    _commentWrapper.loadEditor('add', 0, fn);
                }else if((rectsSelection=getSelectionRects()) && _type!=='area'){
                    renderRect(_type,[].concat(_toConsumableArray(rectsSelection)).map(function(r){return{top:r.top,left:r.left,width:r.width,height:r.height};}),null);
                    
                    let fn = () => {
                        [textarea,data] = (0,_commentWrapper.openComment)(e,handleCancelClick,handleSubmitClick,handleToolbarClick,handleSubmitBlur,_type);
                    }
                    _commentWrapper.loadEditor('add', 0, fn);
                }else{
                    enableRect(_type);
                    //Do nothing!
                }
            }
        }
        // Handle document.touchend event
        function handleDocumentTouchend(e){
            // Enable the scrolling again 
            document.documentElement.style.overflow = 'auto';
            document.getElementById('content-wrapper').style.overflow = 'auto';

            //if the cursor is clicked nothing should happen!
            if((typeof e.target.getAttribute('className')!='string') &&  e.target.className.indexOf('cursor') === -1){
                document.removeEventListener('touchmove',handleDocumentTouchmove);
                disableRect();
                if(_type==='area'&&overlay){
                    if(isOverlayTooSmall(overlay)){
                        overlay.parentNode.removeChild(overlay);
                        overlay=null;
                        enableRect(_type);
                        return;
                    }
                    var _svg=overlay.parentNode.querySelector('svg.annotationLayer');
                    renderRect(_type,[{top:parseInt(overlay.style.top,10)+rect.top,left:parseInt(overlay.style.left,10)+rect.left,width:parseInt(overlay.style.width,10),height:parseInt(overlay.style.height,10)}],null);
                    
                    [textarea,data] = (0,_commentWrapper.openComment)(e,handleCancelTouch,handleSubmitClick,handleToolbarClick,handleSubmitBlur,_type);
                }else if((rectsSelection=getSelectionRects()) && _type!=='area'){
                    renderRect(_type,[].concat(_toConsumableArray(rectsSelection)).map(function(r){return{top:r.top,left:r.left,width:r.width,height:r.height};}),null);
                    [textarea,data] = (0,_commentWrapper.openComment)(e,handleCancelTouch,handleSubmitClick,handleToolbarClick,handleSubmitBlur,_type);
                }else{
                    enableRect(_type);
                    //Do nothing!
                }
            }
        }

        function handleToolbarClick(e){
            //delete Overlay
            if(_type==='area'&&overlay){
                if(overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                    overlay=null;
                }
            }
            document.querySelector('.toolbar').removeEventListener('click',handleToolbarClick);
            (0,_commentWrapper.closeComment)(documentId,pageNumber,handleSubmitClick,handleCancelClick,null,false);
            deleteUndefinedRect();
        }

        
        function handleSubmitClick(e){
            var rects=void 0;
            if(_type!=='area'&&(rects=rectsSelection)){
                saveRect(_type,[].concat(_toConsumableArray(rects)).map(function(r){return{top:r.top,left:r.left,width:r.width,height:r.height};}),null,e);
            }else if(_type==='area'&&overlay){
                saveRect(_type,[{top:parseInt(overlay.style.top,10)+rect.top,left:parseInt(overlay.style.left,10)+rect.left,width:parseInt(overlay.style.width,10),height:parseInt(overlay.style.height,10)}],null,e,overlay);
            }
            return false;
        }
        
        function handleCancelClick(e){
            //delete Overlay
            if(_type==='area'&&overlay){
                overlay.parentNode.removeChild(overlay);
                overlay=null;
            }
            //Hide the form for Comments
            (0,_commentWrapper.closeComment)(documentId,pageNumber,handleSubmitClick,handleCancelClick,null,false);
            deleteUndefinedRect();
            //register EventListeners to allow new Annotations
            enableRect(_type);
            (0,_utils.enableUserSelect)();
        }

        function handleCancelTouch(e){
            // When using on mobile devices scrolling will be prevented, here we have to allow it again.
            document.documentElement.style.overflow = 'auto';
            document.getElementById('content-wrapper').style.overflow = 'auto';

            //delete Overlay
            if(_type==='area'&&overlay){
                overlay.parentNode.removeChild(overlay);
                overlay=null;
            }
            //Hide the form for Comments
            (0,_commentWrapper.closeComment)(documentId,pageNumber,handleSubmitClick,handleCancelClick,null,false);
            deleteUndefinedRect();
            
            // Because of a scrolling issue we have to disable the area annotation after canceling the annotation.
            if (_type ==='area') {
                disableRect();
                document.querySelector('button.cursor').click();
            } else {
                enableRect(_type);
                (0,_utils.enableUserSelect)();
            }
        }
        
        function handleSubmitBlur(){
            if(overlay){
                overlay.parentNode.removeChild(overlay);
                overlay=null;
            }
            (0,_commentWrapper.closeComment)(documentId,pageNumber,handleSubmitClick,handleCancelClick,null,false);
            deleteUndefinedRect();
        }

        /**
        * Handle document.keyup event
        *
        * @param {Event} e The DOM event to handle
        */
        function handleDocumentKeyup(e){// Cancel rect if Esc is pressed
            if(e.keyCode===27){var selection=window.getSelection();selection.removeAllRanges();if(overlay&&overlay.parentNode){overlay.parentNode.removeChild(overlay);overlay=null;document.removeEventListener('mousemove',handleDocumentMousemove);}}
        }
        
        function renderRect(type,rects,color){
            rect=_svg.getBoundingClientRect();
            var _getMetadata=(0,_utils.getMetadata)(_svg);
            documentId=_getMetadata.documentId;
            pageNumber=_getMetadata.pageNumber;
            var annotation = initializeAnnotation(type,rects,'rgb(255,237,0)',_svg);
            rectObj = [_svg,annotation];
            (0,_appendChild2.default)(_svg,annotation);
        }
        /**
         * This function deletes all annotations which data-pdf-annotate-id is undefined. An annotation is undefined, if it is only temporarily displayed.
         * @returns {undefined}
         */
        function deleteUndefinedRect(){
            let n = document.querySelector('[data-pdf-annotate-id="undefined"]');
            if(n){
                n.parentNode.removeChild(n);
            }
        }

        
        function initializeAnnotation(type,rects,color,svg){
                
            var node=void 0;
            var annotation=void 0;
            if(!svg){return;}
            if(!color){
                if(type==='highlight'){
                    color='rgb(142,186,229)';
                }else if(type==='strikeout'){
                    color='rgb(0,84,159)';
                }
            }
            // Initialize the annotation
            annotation={type:type,color:color,rectangles:[].concat(_toConsumableArray(rects)).map(function(r){var offset=0;if(type==='strikeout'){offset=r.height/2;}return(0,_utils.scaleDown)(svg,{y:r.top+offset-rect.top,x:r.left-rect.left,width:r.width,height:r.height});}).filter(function(r){return r.width>0&&r.height>0&&r.x>-1&&r.y>-1;})};// Short circuit if no rectangles exist
            if(annotation.rectangles.length===0){
                return;
            }// Special treatment for area as it only supports a single rect
            if(type==='area'){
                var _rect=annotation.rectangles[0];
                delete annotation.rectangles;
                annotation.x=(0,_utils.roundDigits)(_rect.x,4);
                annotation.y=(0,_utils.roundDigits)(_rect.y,4);
                annotation.width=(0,_utils.roundDigits)(_rect.width,4);
                annotation.height=(0,_utils.roundDigits)(_rect.height,4);
            }else{
                annotation.rectangles = annotation.rectangles.map(function(elem, index, array){
                    return {x:(0,_utils.roundDigits)(elem.x,4),y:(0,_utils.roundDigits)(elem.y,4),width:(0,_utils.roundDigits)(elem.width,4),height:(0,_utils.roundDigits)(elem.height,4)};
                });
            }
            return annotation;
        }
        
        /**
        * Save a rect annotation
        *
        * @param {String} type The type of rect (area, highlight, strikeout)
        * @param {Array} rects The rects to use for annotation
        * @param {String} color The color of the rects
        */
        function saveRect(type,rects,color,e,overlay){
            var annotation = initializeAnnotation(type,rects,color,_svg);
            var _getMetadata=(0,_utils.getMetadata)(_svg);
            var documentId=_getMetadata.documentId;
            var pageNumber=_getMetadata.pageNumber;
            var content=textarea.value.trim();
            if(textarea.value.trim().length > 0){
                
                (0,_commentWrapper.closeComment)(documentId,pageNumber,handleSubmitClick,handleCancelClick,null,true);
                
                if(_type==='area'&&overlay){
                    overlay.parentNode.removeChild(overlay);
                    overlay=null;
                    document.removeEventListener('mousemove',handleDocumentMousemove);
                    (0,_utils.enableUserSelect)();
                }
                // Add the annotation
                _PDFJSAnnotate2.default.getStoreAdapter()
                    .addAnnotation(documentId,pageNumber,annotation)
                    .then(function(annotation){                        
                        var commentVisibility= read_visibility_of_checkbox();
                        var isquestion = 1; //The annotation was created, so this comment has to be a question;
                        _PDFJSAnnotate2.default.getStoreAdapter().addComment(documentId,annotation.uuid,content,commentVisibility,isquestion)
                            .then(function(msg){
                                if(!msg) throw new Error();
                                //delete previous annotation to render new one with the right id
                                deleteUndefinedRect();
                                //get Old rectangles because of scrolling
                                annotation.rectangles = rectObj[1].rectangles;

                                (0,_appendChild2.default)(_svg,annotation);
                                document.querySelector('.toolbar').removeEventListener('click',handleToolbarClick);
                                //simulate an click on cursor
                                document.querySelector('button.cursor').click();
                                (0,_commentWrapper.showCommentsAfterCreation)(annotation.uuid);
                            })
                            .catch(function(){
                                //if there is an error in addComment, the annotation should be deleted!
                                var annotationid = annotation.uuid;
                                _PDFJSAnnotate2.default.getStoreAdapter().deleteAnnotation(documentId,annotationid, false);
                            });
                    }, function (err){
                        deleteUndefinedRect();
                        notification.addNotification({
                            message: M.util.get_string('error:addAnnotation','pdfannotator'),
                            type: "error"
                        });
                    });
            }else{
               notification.addNotification({
                    message: M.util.get_string('min0Chars', 'pdfannotator'),
                    type: "error"
                });
                handleCancelClick(e);
                textarea.focus(); 
            }
        }
        /**
        * Enable rect behavior
        */
        function enableRect(type){
            _type=type;
            if(_enabled){return;}
            
            if(_type === 'area'){
                document.getElementById('content-wrapper').classList.add('cursor-area');
            }else if(_type === 'highlight'){
                document.getElementById('content-wrapper').classList.add('cursor-highlight');
            }else if(_type === 'strikeout'){
                document.getElementById('content-wrapper').classList.add('cursor-strikeout');
            }
            
            _enabled=true;
            document.addEventListener('mouseup',handleDocumentMouseup);
            document.addEventListener('mousedown',handleDocumentMousedown);
            document.addEventListener('keyup',handleDocumentKeyup);

            document.addEventListener('touchstart', handleDocumentTouchstart);
            document.addEventListener('touchend', handleDocumentTouchend);
        }
        /**
        * Disable rect behavior
        */
        function disableRect(){
            if(!_enabled){return;}
            _enabled=false;
            if(_type === 'area'){
                document.getElementById('content-wrapper').classList.remove('cursor-area');
            }else if(_type === 'highlight'){
                document.getElementById('content-wrapper').classList.remove('cursor-highlight');
            }else if(_type === 'strikeout'){
                document.getElementById('content-wrapper').classList.remove('cursor-strikeout');
            }
            document.removeEventListener('mouseup',handleDocumentMouseup);
            document.removeEventListener('mousedown',handleDocumentMousedown);
            document.removeEventListener('keyup',handleDocumentKeyup);

            document.removeEventListener('touchstart', handleDocumentTouchstart);
            document.removeEventListener('touchend', handleDocumentTouchend);
        }
/***/},
/* 33 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            var _typeof=typeof Symbol==="function"&&_typeof2(Symbol.iterator)==="symbol"?function(obj){return typeof obj==='undefined'?'undefined':_typeof2(obj);}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol?"symbol":typeof obj==='undefined'?'undefined':_typeof2(obj);};
            exports.setText=setText;
            exports.enableText=enableText;
            exports.disableText=disableText;
            var _PDFJSAnnotate=__webpack_require__(1);
            var _PDFJSAnnotate2=_interopRequireDefault(_PDFJSAnnotate);
            var _appendChild=__webpack_require__(11);
            var _appendChild2=_interopRequireDefault(_appendChild);
            var _utils=__webpack_require__(6);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            var _enabled=false;
            var input=void 0;
            var pos = void 0;
            var _textSize=void 0;
            var _textColor=void 0;
            var svg=void 0;
            var rect=void 0;/**
            * Handle document.mouseup event
            *
            *
            * @param {Event} e The DOM event to handle
            */function handleDocumentMouseup(e){ // betrifft textbox
                if(input||!(svg=(0,_utils.findSVGAtPoint)(e.clientX,e.clientY))){
                    return;
                } 
                let scrollTop = window.pageYOffset;
                input=document.createElement('input');
                input.setAttribute('id','pdf-annotate-text-input');
                input.setAttribute('placeholder',M.util.get_string('enterText','pdfannotator'));
                input.style.border='3px solid '+_utils.BORDER_COLOR;
                input.style.borderRadius='3px';
                input.style.position='absolute';
                input.style.top=(e.clientY+scrollTop)+'px';
                input.style.left=e.clientX+'px';
                input.style.fontSize=_textSize+'px';
                input.addEventListener('blur',handleInputBlur);
                input.addEventListener('keyup',handleInputKeyup);
                document.body.appendChild(input);
                input.focus();
                rect=svg.getBoundingClientRect();
                pos = {x: e.clientX, y: e.clientY };
            }
            /**
            * Handle input.blur event
            */function handleInputBlur(){
                saveText();
            }/**
            * Handle input.keyup event
            *
            * @param {Event} e The DOM event to handle
            */function handleInputKeyup(e){if(e.keyCode===27){closeInput();}else if(e.keyCode===13){saveText();}}/**
            * Save a text annotation from input
            */function saveText(){
                if(input.value.trim().length>0){
                    var _ret=function(){
                        var clientX=parseInt(pos.x,10);
                        //text size additional to y to render the text right under the mouse click
                        var clientY=parseInt(pos.y,10);
                        //var svg=(0,_utils.findSVGAtPoint)(clientX,clientY);
                        if(!svg){
                            return{v:void 0};
                        }
                        var _getMetadata=(0,_utils.getMetadata)(svg);
                        var documentId=_getMetadata.documentId;
                        var pageNumber=_getMetadata.pageNumber;
                        
                        var annotation=Object.assign({type:'textbox',size:_textSize,color:_textColor,content:input.value.trim()},(0,_utils.scaleDown)(svg,{x:(0,_utils.roundDigits)(clientX-rect.left,4),y:(0,_utils.roundDigits)(clientY-rect.top,4),width:(0,_utils.roundDigits)(input.offsetWidth,4),height:(0,_utils.roundDigits)(input.offsetHeight,4)}));
                        _PDFJSAnnotate2.default.getStoreAdapter().addAnnotation(documentId,pageNumber,annotation)
                                .then(function(annotation){
                                    //annotation.y = annotation.y +parseInt(annotation.size,10);
                                    (0,_appendChild2.default)(svg,annotation); 
                                    document.querySelector('button.cursor').click();
                                }, function (err){
                                    notification.addNotification({
                                        message: M.util.get_string('error:addAnnotation', 'pdfannotator'),
                                        type: "error"
                                    });
                                });
                    }();
                    if((typeof _ret==='undefined'?'undefined':_typeof(_ret))==="object")
                        return _ret.v;
                }
                closeInput();
            }/**
            * Close the input
            */function closeInput(){try{if(input){input.removeEventListener('blur',handleInputBlur);input.removeEventListener('keyup',handleInputKeyup);document.body.removeChild(input);input=null; pos = null;}}catch{}}/**
            * Set the text attributes
            *
            * @param {Number} textSize The size of the text
            * @param {String} textColor The color of the text
            */function setText(){var textSize=arguments.length<=0||arguments[0]===undefined?12:arguments[0];var textColor=arguments.length<=1||arguments[1]===undefined?'000000':arguments[1];_textSize=parseInt(textSize,10);_textColor=textColor;}/**
            * Enable text behavior
            */function enableText(){
                if(_enabled){
                    return;
                }
                _enabled=true;
                document.getElementById('content-wrapper').classList.add('cursor-text');
                document.addEventListener('mouseup',handleDocumentMouseup);
            }/**
            * Disable text behavior
            */function disableText(){
                if(!_enabled){return;
                }
                _enabled=false;
                document.getElementById('content-wrapper').classList.remove('cursor-text');
                document.removeEventListener('mouseup',handleDocumentMouseup);
            }
    /***/},
    /* 34 */
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            var _slicedToArray=function(){
                function sliceIterator(arr,i){
                    var _arr=[];
                    var _n=true;
                    var _d=false;
                    var _e=undefined;
                    try{
                        for(var _i=arr[Symbol.iterator](),_s;!(_n=(_s=_i.next()).done);_n=true){
                            _arr.push(_s.value);
                            if(i&&_arr.length===i)break;
                        }
                    }catch(err){
                        _d=true;
                        _e=err;
                    }finally{
                        try{
                            if(!_n&&_i["return"])_i["return"]();
                        }finally{
                            if(_d)throw _e;
                        }
                    }
                    return _arr;
                }
                return function(arr,i){
                    if(Array.isArray(arr)){
                        return arr;
                    }else if(Symbol.iterator in Object(arr)){
                        return sliceIterator(arr,i);
                    }else{
                        throw new TypeError("Invalid attempt to destructure non-iterable instance");
                    }
                };
            }();
                        
            exports.createPage=createPage;
            exports.renderPage=renderPage;
            var _PDFJSAnnotate=__webpack_require__(1);
            var _PDFJSAnnotate2=_interopRequireDefault(_PDFJSAnnotate);
            var _renderScreenReaderHints=__webpack_require__(20);
            var _renderScreenReaderHints2=_interopRequireDefault(_renderScreenReaderHints);
            var _appendChild=__webpack_require__(11);
            var _appendChild2=_interopRequireDefault(_appendChild);
            var _utils=__webpack_require__(6);
            var _renderQuestions = __webpack_require__(38);
            function _interopRequireDefault(obj){
                return obj&&obj.__esModule?obj:{default:obj};
            }
            var SIZE = 20;
            // Template for creating a new page
            //helper Layer as a Child of Textlayer added, because in firefox the handleDocumentClick only fires, if the click is outside of Textlayer or is on a child of Textlayer
            var PAGE_TEMPLATE='\n  <div style="visibility: hidden;" class="page" data-loaded="false">\n    <div class="canvasWrapper">\n      <canvas></canvas>\n    </div>\n    <svg class="annotationLayer"></svg>\n    <div class="textLayer"><div class="helperLayer"></div></div>\n  </div>\n';/**
            * Create a new page to be appended to the DOM.
            *
            * @param {Number} pageNumber The page number that is being created
            * @return {HTMLElement}
            */function createPage(pageNumber){
                   var temp=document.createElement('div');
                   temp.innerHTML=PAGE_TEMPLATE;
                   var page=temp.children[0];
                   var canvas=page.querySelector('canvas');
                   page.setAttribute('id','pageContainer'+pageNumber);
                   page.setAttribute('data-page-number',pageNumber);
                   canvas.mozOpaque=true;
                   canvas.setAttribute('id','page'+pageNumber);
                   return page;
               }
               
            let listOfPagesLoaded = [];
            /**
            * Render a page that has already been created.
            *
            * @param {Number} pageNumber The page number to be rendered
            * @param {Object} renderOptions The options for rendering
            * @return {Promise} Settled once rendering has completed
            *  A settled Promise will be either:
            *    - fulfilled: [pdfPage, annotations]
            *    - rejected: Error
            */function renderPage(pageNumber,renderOptions, reset = false){
               if(reset){
                   listOfPagesLoaded = [];
                   currentAnnotations = [];
               }
               if(listOfPagesLoaded.indexOf(pageNumber) !== -1){
                   return;
               }
               listOfPagesLoaded.push(pageNumber);

                var documentId=renderOptions.documentId;
                var pdfDocument=renderOptions.pdfDocument;
                var scale=renderOptions.scale;
                var _rotate=renderOptions.rotate;// Load the page and annotations
                return Promise.all([pdfDocument.getPage(pageNumber),_PDFJSAnnotate2.default.getAnnotations(documentId,pageNumber)])
                    .then(function(_ref){
                        var _ref2=_slicedToArray(_ref,2);
                        var pdfPage=_ref2[0];
                        var annotations=_ref2[1];
                        currentAnnotations[pageNumber] = annotations.annotations;

                        var page=document.getElementById('pageContainer'+pageNumber);
                        var svg=page.querySelector('.annotationLayer');
                        var canvas=page.querySelector('.canvasWrapper canvas');
                        var canvasContext=canvas.getContext('2d',{alpha:false});
                        var viewport=pdfPage.getViewport({scale:scale,rotation:_rotate});
                        var viewportWithoutRotate=pdfPage.getViewport({scale:scale,rotation:0});
                        var transform=scalePage(pageNumber,viewport,canvasContext);// Render the page
                        return Promise.all([pdfPage.render({canvasContext:canvasContext,viewport:viewport,transform:transform}),_PDFJSAnnotate2.default.render(svg,viewportWithoutRotate,annotations)])
                            .then(function(){
                                // Text content is needed for a11y, but is also necessary for creating
                                // highlight and strikeout annotations which require selecting text.
                                return pdfPage.getTextContent({normalizeWhitespace:true})
                                    .then(function(textContent){
                                        return new Promise(function(resolve,reject){
                                            require(['mod_pdfannotator/pdf_viewer'], function(pdfjsViewer) {
                                                // Render text layer for a11y of text content
                                                var textLayer=page.querySelector('.textLayer');
                                                var textLayerFactory=new pdfjsViewer.DefaultTextLayerFactory();
                                                var eventBus=new pdfjsViewer.EventBus();
                                                // (Optionally) enable hyperlinks within PDF files.
                                                var pdfLinkService=new pdfjsViewer.PDFLinkService({
                                                    eventBus,
                                                });
                                                // (Optionally) enable find controller.
                                                var pdfFindController=new pdfjsViewer.PDFFindController({
                                                    linkService: pdfLinkService,
                                                    eventBus,
                                                });
                                                var pageIdx=pageNumber-1;
                                                var highlighter = new pdfjsViewer.TextHighlighter({
                                                    pdfFindController,
                                                    eventBus,
                                                    pageIdx
                                                });
                                                var textLayerBuilder=textLayerFactory.createTextLayerBuilder(
                                                    textLayer,
                                                    pageIdx,
                                                    viewport,
                                                    true,
                                                    eventBus,
                                                    highlighter,
                                                );
                                                pdfLinkService.setViewer(textLayerBuilder);
                                                textLayerBuilder.setTextContent(textContent);
                                                textLayerBuilder.render();// Enable a11y for annotations

                                                // Timeout is needed to wait for `textLayerBuilder.render`
                                                //setTimeout(function(){try{(0,_renderScreenReaderHints2.default)(annotations.annotations);resolve();}catch(e){reject(e);}});
                                                //ur weil setTimeout auskommentiert ist!!!!!
                                                resolve();
                                            });
                                        });
                                    });
                            }).then(function(){// Indicate that the page was loaded
                                page.setAttribute('data-loaded','true');
                                
                                return[pdfPage,annotations];
                            });
                    }, function (err){
                        notification.addNotification({
                            message: M.util.get_string('error:renderPage', 'pdfannotator'),
                            type: "error"
                        });
                    });
            }/**
            * Scale the elements of a page.
            *
            * @param {Number} pageNumber The page number to be scaled
            * @param {Object} viewport The viewport of the PDF page (see pdfPage.getViewport(scale, rotation))
            * @param {Object} context The canvas context that the PDF page is rendered to
            * @return {Array} The transform data for rendering the PDF page
            */function scalePage(pageNumber,viewport,context){var page=document.getElementById('pageContainer'+pageNumber);var canvas=page.querySelector('.canvasWrapper canvas');var svg=page.querySelector('.annotationLayer');var wrapper=page.querySelector('.canvasWrapper');var textLayer=page.querySelector('.textLayer');var outputScale=getOutputScale(context);var transform=!outputScale.scaled?null:[outputScale.sx,0,0,outputScale.sy,0,0];var sfx=approximateFraction(outputScale.sx);var sfy=approximateFraction(outputScale.sy);// Adjust width/height for scale
                page.style.visibility='';canvas.width=roundToDivide(viewport.width*outputScale.sx,sfx[0]);canvas.height=roundToDivide(viewport.height*outputScale.sy,sfy[0]);canvas.style.width=roundToDivide(viewport.width,sfx[1])+'px';canvas.style.height=roundToDivide(viewport.height,sfx[1])+'px';svg.setAttribute('width',viewport.width);svg.setAttribute('height',viewport.height);svg.style.width=viewport.width+'px';svg.style.height=viewport.height+'px';page.style.width=viewport.width+'px';page.style.height=viewport.height+'px';wrapper.style.width=viewport.width+'px';wrapper.style.height=viewport.height+'px';textLayer.style.width=viewport.width+'px';textLayer.style.height=viewport.height+'px';return transform;}/**
            * Approximates a float number as a fraction using Farey sequence (max order of 8).
            *
            * @param {Number} x Positive float number
            * @return {Array} Estimated fraction: the first array item is a numerator,
            *                 the second one is a denominator.
            */function approximateFraction(x){// Fast path for int numbers or their inversions.
                if(Math.floor(x)===x){return[x,1];}var xinv=1/x;var limit=8;if(xinv>limit){return[1,limit];}else if(Math.floor(xinv)===xinv){return[1,xinv];}var x_=x>1?xinv:x;// a/b and c/d are neighbours in Farey sequence.
                var a=0,b=1,c=1,d=1;// Limit search to order 8.
                while(true){// Generating next term in sequence (order of q).
                var p=a+c,q=b+d;if(q>limit){break;}if(x_<=p/q){c=p;d=q;}else{a=p;b=q;}}// Select closest of neighbours to x.
                if(x_-a/b<c/d-x_){return x_===x?[a,b]:[b,a];}else{return x_===x?[c,d]:[d,c];}}
            function getOutputScale(ctx){var devicePixelRatio=window.devicePixelRatio||1;var backingStoreRatio=ctx.webkitBackingStorePixelRatio||ctx.mozBackingStorePixelRatio||ctx.msBackingStorePixelRatio||ctx.oBackingStorePixelRatio||ctx.backingStorePixelRatio||1;var pixelRatio=devicePixelRatio/backingStoreRatio;return{sx:pixelRatio,sy:pixelRatio,scaled:pixelRatio!==1};}
            function roundToDivide(x,div){var r=x%div;return r===0?x:Math.round(x-r+div);}
    /***/},
    /* 35 *//* own module to handle the comment wrapper
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.deleteUndefinedRect=deleteUndefinedRect;
            exports.openComment = openComment;
            exports.closeComment = closeComment;
            exports.showCommentsAfterCreation = showCommentsAfterCreation;
            exports.openCommentTouchscreen = openCommentTouchscreen;
            exports.loadEditor = loadEditor;
            var _PDFJSAnnotate=__webpack_require__(1);
            var _event=__webpack_require__(4);
            var _PDFJSAnnotate2=_interopRequireDefault(_PDFJSAnnotate);
            var _setAttributes=__webpack_require__(14);
            var _setAttributes2=_interopRequireDefault(_setAttributes);
            var _utils=__webpack_require__(6);
            var _ajaxloader=__webpack_require__(36);
            var _questionRenderer=__webpack_require__(38);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            
            var _e = null;
            var textarea = void 0;
            var submitbutton = void 0;
            var resetbutton = void 0;
            var form = void 0;
            var data=void 0;
            var rectsSelection = void 0;
            
            function deleteUndefinedRect(){
                let n = document.querySelector('[data-pdf-annotate-id="undefined"]');
                n.parentNode.removeChild(n);
            }
            
            function showCommentsAfterCreation(annoid){
                //Remove Loader from Comment-Container
                (0,_ajaxloader.hideLoader)();
                
                //Show Comments
                let target = $('[data-pdf-annotate-id='+annoid+']')[0];
                //fire annotation:click event with the annotation as target
                _event.fireEvent('annotation:click',target);
            }
            
            /**
             * This function closes the comment wrapper
             * @param {type} documentId The id of the document (for render the questions afterwards)
             * @param {type} pageNumber The page number (for render the questions afterwards)
             * @param {type} handleSubmitClick Handler to remove
             * @param {type} handleCancelClick Handler to remove
             * @param {type} toolbarClick Handler to remove
             * @param {boolean} loading True, if the loader should be displayed
             * @returns {void}
             */
            function closeComment(documentId,pageNumber,handleSubmitClick, handleCancelClick,toolbarClick, loading){
                document.querySelector('.comment-list-form').setAttribute('style','display:none');
                document.querySelector('.comment-list-form').removeEventListener('onsubmit',handleSubmitClick);
                document.getElementById('commentCancel').removeEventListener('click',handleCancelClick);
                document.querySelector('.toolbar').removeEventListener('click',toolbarClick);
                document.getElementById('commentSubmit').value = M.util.get_string('answerButton','pdfannotator');
                document.getElementById('id_pdfannotator_content').value = "";
                document.getElementById('id_pdfannotator_content').placeholder = M.util.get_string('addAComment','pdfannotator');
                // Reset the typed text for other editors.
                var editorArea = document.querySelector('#id_pdfannotator_contenteditable'); // Atto editor.
                if (!editorArea) { // TinyMCE editor.
                    var iframe = document.getElementById("myarea_ifr");
                    if (iframe) {
                        editorArea = iframe.contentWindow.document.getElementById("tinymce");
                    }
                }
                if(editorArea) {
                    editorArea.innerHTML = '';
                }
                data=null;
                textarea=null;
                submitbutton=null;
                if(loading){
                    (0,_ajaxloader.showLoader)();
                }else{
                    _questionRenderer.renderQuestions(documentId,pageNumber);
                }
            }
            
            /**
             * Opens the commen wrapper
             * @param {event} e the click Event of the annotation
             * @param {type} cancelClick EventListener to add
             * @param {type} submitClick EventListener to add
             * @param {type} toolbarClick EventListener to add
             * @param {type} submitBlur EventListener to add
             * @param {type} _type which type of annotation 
             * @returns {Array} [0] textarea [1] data (position of the annotation)
             */
            function openComment(e,cancelClick,submitClick,toolbarClick,submitBlur,_type){ 
                //save e for later 
                _e = e;
                
                var button1 = document.getElementById('allQuestions'); // to be found in index template
                button1.style.display = 'inline';
                var button2 = document.getElementById('questionsOnThisPage'); // to be found in index template
                button2.style.display = 'inline';

                //title 
                $('#comment-wrapper h4')[0].innerHTML = M.util.get_string('comments','pdfannotator');
                //add Eventlistener to Toolbar. Every Click in Toolbar should cancel the Annotation-Comment-Creation
                document.querySelector('.toolbar').addEventListener('click',toolbarClick);
                //Hide shown comments
                document.querySelector('.comment-list-container').innerHTML = '<p></p>';
                form = document.querySelector('.comment-list-form');
                $(document).ready(function(){
                    form.setAttribute('style','display:inherit');
                    $('#anonymousDiv').show();
                    $('#privateDiv').show();
                    $('#protectedDiv').show();
                });
                textarea = document.getElementById('id_pdfannotator_content');
                textarea.placeholder = M.util.get_string('startDiscussion','pdfannotator');
                submitbutton = document.getElementById('commentSubmit');
                submitbutton.value = M.util.get_string('createAnnotation','pdfannotator');
                resetbutton = document.getElementById('commentCancel');
                resetbutton.addEventListener('click',cancelClick);
                form.onsubmit = submitClick;
                //fixCommentForm();
                if(_type === 'pin'){
                    data = new Object();
                    data.x = e.clientX;
                    data.y = e.clientY;
                }else{
                    data = document.createElement('div');
                    data.setAttribute('id','pdf-annotate-point-input');
                    data.style.border='3px solid '+_utils.BORDER_COLOR;
                    data.style.borderRadius='3px';
                    data.style.display = 'none';
                    data.style.position='absolute';
                    data.style.top=e.clientY+'px';
                    data.style.left=e.clientX+'px';
                }
                
                form.addEventListener('blur',submitBlur);
                textarea.focus();
                return [textarea,data];
            }

            function openCommentTouchscreen(e,cancelClick,submitClick,toolbarClick,submitBlur,_type){ 
                //save e for later 
                _e = e;
                
                var button1 = document.getElementById('allQuestions'); // to be found in index template
                button1.style.display = 'inline';
                var button2 = document.getElementById('questionsOnThisPage'); // to be found in index template
                button2.style.display = 'inline';
                
                //title 
                $('#comment-wrapper h4')[0].innerHTML = M.util.get_string('comments','pdfannotator');
                //add Eventlistener to Toolbar. Every Click in Toolbar should cancel the Annotation-Comment-Creation
                document.querySelector('.toolbar').addEventListener('click',toolbarClick);
                //Hide shown comments
                document.querySelector('.comment-list-container').innerHTML = '<p></p>';
                form = document.querySelector('.comment-list-form');
                form.setAttribute('style','display:inherit');
                $('#anonymousCheckbox').show();
                $('#privateCheckbox').show();
                $('#protectedCheckbox').show();
                textarea = document.getElementById('id_pdfannotator_content');
                textarea.placeholder = M.util.get_string('startDiscussion','pdfannotator');
                submitbutton = document.getElementById('commentSubmit');
                submitbutton.value = M.util.get_string('createAnnotation','pdfannotator');
                resetbutton = document.getElementById('commentCancel');
                resetbutton.addEventListener('click',cancelClick);
                form.onsubmit = submitClick;
                //fixCommentForm();
                if(_type === 'pin'){
                    data = new Object();
                    data.x = e.changedTouches[0].clientX;
                    data.y = e.changedTouches[0].clientY;
                }else{
                    data = document.createElement('div');
                    data.setAttribute('id','pdf-annotate-point-input');
                    data.style.border='3px solid '+_utils.BORDER_COLOR;
                    data.style.borderRadius='3px';
                    data.style.display = 'none';
                    data.style.position='absolute';
                    data.style.top=e.clientY+'px';
                    data.style.left=e.clientX+'px';
                }
                
                form.addEventListener('blur',submitBlur);
                textarea.focus();
                return [textarea,data];
            }

            /**
             * 
             * @param {type} action can be add for adding comments. Or edit for editing comments.
             * @param {int} uuid
             * @param {Function} fn a callback funtion. It will be called after the Promises in this funktion finish.
             *
             *  
             */
            function loadEditor(action='add', uuid=0, fn=null, fnParams=null){                
                // search the placeholder for editor.
                let addCommentEditor = document.querySelectorAll('#add_comment_editor_wrapper');
                let editCommentEditor = document.querySelectorAll (`#edit_comment_editor_wrapper_${uuid}`);

                if (action === "add") {
                    _ajaxloader.showLoader(`.editor-loader-placeholder-${action}`);

                    // remove old editor and old input values of draftitemid and editorformat, if exists.
                    if (addCommentEditor.length > 0) {
                        addCommentEditor[0].remove();
                    }

                    let data = {};
                    templates.render('mod_pdfannotator/add_comment_editor_placeholder', data)
                    .then(function(html, js) {
                        let commentListForm = document.getElementById('comment-list-form');
                        templates.prependNodeContents(commentListForm, html, js);
                    })
                    .then(function() {
                        let args = {'action': action, 'cmid': _cm.id};
                        Fragment.loadFragment('mod_pdfannotator', 'open_add_comment_editor', _contextId, args)
                        .done(function(html, js) {
                            if (!html) {
                                throw new TypeError("Invalid HMTL Input");
                            }
                            templates.replaceNode(document.getElementById('editor-commentlist-inputs'), html, js);
                            if (fn instanceof Function) {
                                (0,fn)(fnParams);
                            }
                            _ajaxloader.hideLoader(`.editor-loader-placeholder-${action}`);
                            return true;
                        })
                        .then(function() {                            
                            let commentText = document.getElementById('id_pdfannotator_contenteditable');
                            if(commentText) {
                                commentText.focus();
                            }
                        });
                    })
                    .catch(notification.exception);
                } else if(action === "edit") {
                    _ajaxloader.showLoader(`.editor-loader-placeholder-${action}-${uuid}`);

                    // remove old editor and old input values of draftitemid and editorformat, if exists.
                    if (editCommentEditor.length > 0) {
                        editCommentEditor[0].remove();
                    }

                    let data = {'uuid': uuid};
                    let editTextarea;
                    templates.render('mod_pdfannotator/edit_comment_editor_placeholder', data)
                    .then(function(html, js) {
                        let editForm = document.getElementById(`edit${uuid}`);
                        templates.prependNodeContents(editForm, html, js);
                        editTextarea =  document.getElementById(`editarea${uuid}`);
                        editTextarea.style.display = "none";
                        return true;
                    })
                    .then(function() {
                        let args = {'action': action, 'cmid': _cm.id, 'uuid': uuid};
                        Fragment.loadFragment('mod_pdfannotator', 'open_edit_comment_editor', _contextId, args)
                        .then(function(html, js) {
                            if (!html) {
                                throw new TypeError("Invalid HMTL Input");
                            }
                            //templates.runTemplateJS(js);
                            let editCommentEditorElement = document.getElementById(`edit_comment_editor_wrapper_${uuid}`);
                            html = html.split('displaycontent:');
                            let isreplaced = templates.appendNodeContents(editCommentEditorElement, html[0], js);
                            editTextarea.innerText = html[1];
    
                            _ajaxloader.hideLoader(`.editor-loader-placeholder-${action}-${uuid}`);

                            let editForm = document.getElementById(`edit${uuid}`)
                            let chatMessage = document.getElementById(`chatmessage${uuid}`);
                            let editAreaEditable = document.getElementById(`editarea${uuid}editable`);
                            editAreaEditable.innerHTML = editTextarea.value;
                            if(editForm.style.display === "none") {
                                editForm.style.cssText += ';display:block;';
                                chatMessage.innerHTML = "";
                            }
                            return true;
                        })
                        .then(function() {                            
                            let commentText = document.getElementById(`editarea${uuid}`);
                            if(commentText) {
                                commentText.focus();
                            }
                            if (fn instanceof Function) {
                                (0,fn)(fnParams);
                            }
                        });
                    })
                    .catch(notification.exception);
                } else {
                    // nothing to do.
                }
            }
            
    /***/},
    /* 36 *//*OWN Module! To show and hide ajaxloader*/
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.showLoader=showLoader;
            exports.hideLoader=hideLoader;
            
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            
            /**
             * hides the loading animation
             * @returns {undefined}
             */
            function hideLoader(selector='.comment-list-container'){
                let loader = document.querySelector('#ajaxLoaderCreation');
                if(loader !== null){
                    let commentContainer = document.querySelectorAll(`${selector}`)[0];
                    commentContainer.removeChild(loader);
                }
            }
            
            /**
             * Shows an loading animation in the comment wrapper
             * @returns {undefined}
             */
            function showLoader(selector='.comment-list-container'){
                let commentContainer = document.querySelector(`${selector}`);
                commentContainer.innerHTML = '';
                let img = document.createElement('img');
                img.id = "ajaxLoaderCreation";
                img.src = M.util.image_url('i/loading');
                img.alt = M.util.get_string('loading','pdfannotator');
                img.style = "display: block;margin-left: auto;margin-right: auto;";
                commentContainer.appendChild(img);
            }
        },
    /* 37 *//*OWN Module! To pick an annotation*/
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.pickAnnotation=pickAnnotation;
            var _event=__webpack_require__(4);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            
            /**
             * This function scrolls to the specific annotation and selects the annotation
             * @param {type} page of the annotation
             * @param {type} annoid the id of the picked annotation
             * @param {type} commid id of the comment, if a comment should be marked, else null
             * @return {void}
             */
            function pickAnnotation(page,annoid,commid){             
                //[0] for only first element (it only can be one element)
                var target = $('[data-pdf-annotate-id='+annoid+']')[0];
                if(commid !== null){
                    target.markCommentid = commid;        
                }
               _event.fireEvent('annotation:click',target);
               
               //Scroll to defined page (because of the picked annotation (new annotation, new answer or report) from overview)
               var targetDiv = $('[data-target-id='+annoid+']')[0];
               var pageOffset = document.getElementById('pageContainer'+page).offsetTop;

               var contentWrapper = $('#content-wrapper');
               contentWrapper.scrollTop(pageOffset + targetDiv.offsetTop - 100); 
               contentWrapper.scrollLeft(targetDiv.offsetLeft - contentWrapper.width() + 100); 

            }
        },
    /* 38 *//*OWN Module! To show questions of one PDF-Page on the right side*/
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.renderQuestions=renderQuestions;
            exports.renderAllQuestions = renderAllQuestions;
            var _event=__webpack_require__(4);
            var _shortText=__webpack_require__(39);     
            var _PDFJSAnnotate=__webpack_require__(1);
            var _PDFJSAnnotate2=_interopRequireDefault(_PDFJSAnnotate);
            
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            
            /**
             * This function renders on the right side the questions of all annotations of a specific page.
             * 
             * @param {type} documentId the Id of the pdf
             * @param {type} pageNumber the requested pagenumber
             * @param {type} activeCall specifies that the function was called by click on button with id='questionsOnThisPage'
             * @return {undefined}
             */
            function renderQuestions (documentId, pageNumber, activeCall = null){
                let pattern = $('#searchPattern').val();
                _PDFJSAnnotate2.default.getStoreAdapter().getQuestions(documentId,pageNumber, pattern).then(function(questions){
                    let container = document.querySelector('.comment-list-container');
                    let title = $('#comment-wrapper > h4')[0];
                    if(pattern === '') {
                        title.innerHTML = M.util.get_string('questionstitle','pdfannotator') + ' ' + pageNumber;
                    } else {
                        title.innerHTML = M.util.get_string('searchresults','pdfannotator');
                    }
                    var button1 = document.getElementById('allQuestions'); // to be found in index template
                    button1.style.display = 'inline';
                    var button2 = document.getElementById('questionsOnThisPage'); // to be found in index template
                    button2.style.display = 'none';
                    
                    //only if form is not shown, otherwise the questions should not be rendered
                    if(document.querySelector('.comment-list-form').style.display === 'none' || activeCall){
                        
                        if(activeCall) {
                           document.querySelector('.comment-list-form').style.display = 'none';
                        }
                        container.innerHTML = '';

                        if(questions.length < 1){
                            if (pattern === '') {
                                container.innerHTML = M.util.get_string('noquestions','pdfannotator');
                            } else {
                                container.innerHTML = M.util.get_string('nosearchresults','pdfannotator');
                            }
                        }else{
                            for(let id in questions){
                                let question = questions[id];
                                let questionWrapper = document.createElement('div');
                                questionWrapper.className = 'chat-message comment-list-item questions';
                                let questionText = document.createElement('span');
                                questionText.className = 'more';
                                questionText.innerHTML = question.content;
                                let questionAnswercount = document.createElement('span');
                                questionAnswercount.innerHTML = question.answercount;
                                questionAnswercount.className = 'questionanswercount';
                                
                                let questionPix = document.createElement('i');
                                questionPix.classList = "icon fa fa-comment fa-fw questionanswercount";
                                questionPix.title = M.util.get_string('answers', 'pdfannotator');
                                
                                let iconWrapper = document.createElement('div');
                                iconWrapper.className = 'icon-wrapper';

                                if (question.solved != 0){
                                    let solvedPix = document.createElement('i');
                                    solvedPix.classList = "icon fa fa-lock fa-fw solvedicon";
                                    solvedPix.title = M.util.get_string('questionSolved', 'pdfannotator');
                                    iconWrapper.appendChild(solvedPix);
                                }
                                
                                iconWrapper.appendChild(questionPix);
                                iconWrapper.appendChild(questionAnswercount);
                                
                                questionWrapper.appendChild(questionText);
                                questionWrapper.appendChild(iconWrapper);
                                
                                container.appendChild(questionWrapper);
                                (function(questionObj,questionDOM){
                                    questionDOM.onclick = function(e){
                                        if(questionObj.page !== undefined && questionObj.page !== $('#currentPage').val()) {
                                          $('#content-wrapper').scrollTop(document.getElementById('pageContainer'+questionObj.page).offsetTop);
                                        }                                        
                                        (function scrollToAnnotation(annotationid, pageNumber) {
                                            let target = $('[data-pdf-annotate-id='+annotationid+']')[0]; 
                                            // if scrolled to a different page (see a few lines above) and page isn't loaded yet; wait and try again
                                            if (target === undefined) {
                                                setTimeout(function() {scrollToAnnotation(annotationid, pageNumber);}, 200);
                                            } else {
                                                _event.fireEvent('annotation:click',target);
                                                var targetDiv = $('[data-target-id='+annotationid+']')[0];
                                                var contentWrapper = $('#content-wrapper');
                                                if(pageNumber === undefined) {
                                                    pageNumber = $('#currentPage').val();
                                                }
                                                var pageOffset = document.getElementById('pageContainer'+pageNumber).offsetTop;
                                                contentWrapper.scrollTop(pageOffset + targetDiv.offsetTop - 100); 
                                                contentWrapper.scrollLeft(targetDiv.offsetLeft - contentWrapper.width() + 100);
                                            }
                                        })(questionObj.annotationid, questionObj.page);                                       
                                    };
                                })(question,questionWrapper);
                            }
                            // comment overview column
                            _shortText.mathJaxAndShortenText('.more', 4);                            
                        }  
                    }
                }, function (err){
                    notification.addNotification({
                        message: M.util.get_string('error:getQuestions', 'pdfannotator'),
                        type: "error"
                    });
                });  
            }
            
            
            
            /**
             * Function renders overview column for all questions in this document
             * 
             * @param {type} documentId
             * @return {undefined}
             */
            function renderAllQuestions (documentId) {
                _PDFJSAnnotate2.default.getStoreAdapter().getQuestions(documentId).then(function(questions){
                    let container = document.querySelector('.comment-list-container');
                    let title = $('#comment-wrapper > h4')[0];
                    title.innerHTML = M.util.get_string('allquestionstitle','pdfannotator') + ' ' + questions.pdfannotatorname;
                    
                    container.innerHTML = '';

                    questions = questions.questions;
                    
                    var button1 = document.getElementById('allQuestions'); // to be found in index.mustache template
                    button1.style.display = 'none';
                    var button2 = document.getElementById('questionsOnThisPage'); // to be found in index.mustache template
                    button2.style.display = 'inline';
                   
                    if(document.querySelector('.comment-list-form').style.display !== 'none') {
                        document.querySelector('.comment-list-form').style.display = 'none';
                    }
                    
                    if(questions.length < 1){
                        container.innerHTML = M.util.get_string('noquestions_view','pdfannotator');
                    }else{
                        for(var page in questions){
                            let questionWrapper = document.createElement('div');
                            questionWrapper.className = 'chat-message comment-list-item questions page';
                            let questionText = document.createElement('span');
                            questionText.innerHTML = M.util.get_string('page', 'pdfannotator')+ ' ' +page;
                            let questionAnswercount = document.createElement('span');
                            questionAnswercount.innerHTML = questions[page].length;
                            questionAnswercount.className = 'questionanswercount';
                           
                            let questionPix = document.createElement('i');
                            questionPix.classList = "icon fa fa-comments fa-fw questionanswercount";
                            questionPix.title = M.util.get_string('questionsimgtitle','pdfannotator');
                            let iconWrapper = document.createElement('div');
                            iconWrapper.classList = "icon-wrapper";
                            iconWrapper.appendChild(questionAnswercount);
                            iconWrapper.appendChild(questionPix);
                            questionWrapper.appendChild(questionText);
                            questionWrapper.appendChild(iconWrapper);
                            container.appendChild(questionWrapper);
                            (function(page,questionDOM){
                                questionDOM.onclick = function(e){
                                    $('#content-wrapper').scrollTop(document.getElementById('pageContainer'+page).offsetTop);
                                    var pageinputfield = document.getElementById('currentPage');
                                    if(pageinputfield.value === page) {
                                        renderQuestions(documentId, page, 1);
                                    }
                                    
                                };
                            })(page,questionWrapper);
                        }
                    }
                    
                }, function (err){
                    notification.addNotification({
                        message: M.util.get_string('error:getAllQuestions', 'pdfannotator'),
                        type: "error"
                    });
                }); 
            }
        },
    /* 39 *//*OWN Module! To shorten a specific text*/
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.shortenText=shortenText;
            exports.shortenTextDynamic=shortenTextDynamic;
            exports.mathJaxAndShortenText=mathJaxAndShortenText;
            
            /**
            * Shorten display of any report or question to a maximum of 80 characters and display
            * a 'view more'/'view less' link
            * 
            * Copyright 2013 Viral Patel and other contributors
            * http://viralpatel.net
            * 
            * slightly modified by RWTH Aachen in 2018
            * 
            * Permission is hereby granted, free of charge, to any person obtaining
            * a copy of this software and associated documentation files (the
            * "Software"), to deal in the Software without restriction, including
            * without limitation the rights to use, copy, modify, merge, publish,
            * distribute, sublicense, and/or sell copies of the Software, and to
            * permit persons to whom the Software is furnished to do so, subject to
            * the following conditions:
            * 
            * The above copyright notice and this permission notice shall be
            * included in all copies or substantial portions of the Software.
            * 
            * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
            * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
            * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
            * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
            * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
            * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
            * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
            * 
            * @param {type} selector 
            * @param {type} maxLength
            * @param {type} ellipsesText
            * @returns {undefined}
            */
            function shortenText(selector, maxLength = 80, ellipsesText = '...'){
                var showChar = maxLength;
                var moretext = M.util.get_string('showmore', 'pdfannotator');
                var lesstext = M.util.get_string('showless', 'pdfannotator');
                $(selector).each(function() {
                    if($(this).children().first().attr('id')=== 'content') { return; }
                    var content = $(this).html();
                    //determine if the message should be shortend, here only the characters without the html should be considered
                    var contentWithoudTags = this.innerText; 
                    if(contentWithoudTags.length > (showChar + ellipsesText.length)) {
                        //for the clip-function you should import textclipper.js
                        var c = clip(unescape(content),showChar, {html:true, indicator: ''});//clipped content, the indicator is nothing, because we add the ellipsesText manually in the html
                        var h = content; // complete content
                        var html = '<span id="content">' + c + '</span><span class="moreellipses">' + ellipsesText+ '&nbsp;</span><span class="morecontent"><span class="completeContent">' + h + '</span><span class="clippedContent">'+c+'</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';        
                        $(this).html(html);
                    }

                });

                $(selector+" .morelink").click(function(){
                        if($(this).hasClass("less")) {
                            $(this).removeClass("less");
                            $(this).html(moretext); // entspricht innerHTML
                            $(this).parent().prev().prev().html($(this).prev().html());
                        //    $(selector+" #content").html($(selector+" .morecontent .clippedContent").html());
                        } else {
                            $(this).addClass("less");
                            $(this).html(lesstext);
                            $(this).parent().prev().prev().html($(this).prev().prev().html());
                         //   $(selector+" #content").html($(selector+" .morecontent .completeContent").html());
                        }
                        $(this).parent().prev().toggle();       //span .moreellipses
                        return false;
                });
                
            };
            
            /**
             * This function shortens the text. The length of the string is determined by the size of the parent and the given divisor.
             * @param {type} parentselector The selector of which the size should be referenced
             * @param {type} selector The selector where the text should be shortened
             * @param {type} divisor The textlength is the size / divisior
             * @param {type} ellipsesText text which should be displayed to point out that the text was shortened
             * @returns {undefined}
             */
            function shortenTextDynamic(parentselector, selector, divisor){
                if (parentselector === null) {
                    let elem = document.querySelector(selector);
                    if (elem !== null) {
                        var parent = elem.parentElement;
                    } else {
                        return;
                    }
                } else {
                    var parent = document.querySelector(parentselector);
                }
                if (parent !== null) {
                    let minCharacters = 80;
                    let maxCharacters = 120;
                    let nCharactersToDisplay = parent.offsetWidth / divisor;

                    if (nCharactersToDisplay < minCharacters) {
                        shortenText(selector);
                    } else if (nCharactersToDisplay > maxCharacters) {
                        nCharactersToDisplay = maxCharacters;
                        shortenText(selector, nCharactersToDisplay);
                    } else {
                        shortenText(selector, nCharactersToDisplay);
                    }
                }else{
                    shortenText(selector); // Default: 80 characters
                }
            }
            
            /**
             * Renders MathJax and calls shortenText() afterwards.
             * @param {type} selector
             * @param {type} divisor
             * @param {type} click
             * @returns {undefined}
             */
            function mathJaxAndShortenText(selector, divisor, click = false){
                   if (typeof(MathJax) !== "undefined") {
                        // Add the Mathjax-function and the shortenText function to the queue.
                        MathJax.Hub.Queue(['Typeset', MathJax.Hub], [function(){
                            shortenTextDynamic(null, selector, divisor);
                            if (click) {                                       
                                $(selector+" .morelink").click();
                            }
                        }, null]);
                    } else {
                        shortenTextDynamic(null, selector, divisor);
                        if (click) {                                       
                            $(selector+" .morelink").click();
                        }
                    } 
            }
            
        },
         /* 40 *//*OWN Module! To load new annotations (Synchronisation between sessions)*/
    /***/function(module,exports,__webpack_require__){
            'use strict';
            Object.defineProperty(exports,"__esModule",{value:true});
            exports.load = loadNewAnnotations;
            var _event=__webpack_require__(4);
            var _shortText=__webpack_require__(39);
            var _PDFJSAnnotate=__webpack_require__(1);
            var _PDFJSAnnotate2=_interopRequireDefault(_PDFJSAnnotate);
            var _appendChild=__webpack_require__(11);
            var _appendChild2=_interopRequireDefault(_appendChild);
            function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}
            var _utils=__webpack_require__(6);
            var SIZE = 20;
            /**
            * This functions checks, if two annotations are at the same position an looks the same.
            * @return boolean, true if same and false if not
            */
            function isAnnotationsPosEqual(annotationA, annotationB){
               switch(annotationA.type){
                   case 'area':
                       return (parseInt(annotationA.x) === parseInt(annotationB.x) && parseInt(annotationA.y) === parseInt(annotationB.y) && parseInt(annotationA.width) === parseInt(annotationB.width) && parseInt(annotationA.height) === parseInt(annotationB.height));
                   case 'drawing':
                       return (annotationA.color === annotationB.color && JSON.stringify(annotationA.lines) === JSON.stringify(annotationB.lines) && parseInt(annotationA.width) === parseInt(annotationB.width));
                   case 'highlight':
                   case 'strikeout':
                       //strikeout and highlight cannot be shifted, so they are the same
                       return true;
                   case 'point':
                       return (parseInt(annotationA.x) === parseInt(annotationB.x) && parseInt(annotationA.y) === parseInt(annotationB.y));
                   case 'textbox':
                       return (parseInt(annotationA.x) === parseInt(annotationB.x) && parseInt(annotationA.y) === parseInt(annotationB.y) && parseInt(annotationA.width) === parseInt(annotationB.width) && parseInt(annotationA.height) === parseInt(annotationB.height) && annotationA.content === annotationB.content && annotationA.color === annotationB.color && parseInt(annotationA.size) === parseInt(annotationB.size));
                   default:
                       return false;
               }
           }
           /**
            * This function edits the SVG-Object of the annotation in the DOM
            * @param {type} type type of annotation
            * @param {type} node the annotation node
            * @param {type} svg the outer svg
            * @param {type} annotation the annotation object
            * @returns {void}
            */
            function editAnnotationSVG(type, node, svg, annotation){
                if(['area',/*'highlight',*/'point','textbox'].indexOf(type)>-1){
                    (function(){
                        var x = annotation.x;
                        var y = annotation.y ;
                        if(type === 'point'){
                            x = annotation.x - (SIZE/4);
                            y = annotation.y - SIZE;
                        }

                            node.setAttribute('y',y);
                            node.setAttribute('x',x);
                    })();
                } else if(type==='drawing'){
                    (function(){
                        node.parentNode.removeChild(node);
                        (0,_appendChild2.default)(svg,annotation);
                    })();
                }
            }
            /**
             * This function synchronizes the annotations 
             * It calls itself 5 secs after the function finishes. So every 5+ seconds the annotations are updated.
             * It loads only the annotations of the current shown page.
             */
            function loadNewAnnotations(){
                //determine which page is shown, to only load these annotations.
                var pageNumber = document.getElementById('currentPage').value;
                var page=document.getElementById('pageContainer'+pageNumber);
                if(page === null){
                    setTimeout(loadNewAnnotations, 5000);
                    return;
                }
                var svg=page.querySelector('.annotationLayer');
                var metadata = _utils.getMetadata(svg);
                var viewport = metadata.viewport;
                var documentId = metadata.documentId;
                //Sometimes the page is not loaded yet, than try again in 5secs
                if(isNaN(documentId) || documentId === null){
                    setTimeout(loadNewAnnotations, 5000);
                    return;
                }
                //Get annotations from database to get the newest.
                _PDFJSAnnotate2.default.getAnnotations(documentId,pageNumber)
                    .then(function(data){
                        var newAnnotations = [];
                        newAnnotations[pageNumber] = data.annotations;
                        var oldAnnotations = currentAnnotations.slice();
                        currentAnnotations[pageNumber] = newAnnotations[pageNumber];
                        var exists = false;
                        for (var annotationID in newAnnotations[pageNumber]) {
                            var annotation = newAnnotations[pageNumber][annotationID];
                            for(var oldAnnoid in oldAnnotations[pageNumber]){
                                var oldAnno = oldAnnotations[pageNumber][oldAnnoid];
                                annotation.uuid = parseInt(annotation.uuid);
                                oldAnno.uuid = parseInt(oldAnno.uuid);
                                if(oldAnno !== undefined && annotation.uuid === oldAnno.uuid){
                                    if(!isAnnotationsPosEqual(annotation,oldAnno)){
                                        var node = document.querySelector('[data-pdf-annotate-id="'+oldAnno.uuid+'"]');
                                        if(node !== null){
                                            editAnnotationSVG(annotation.type, node, svg, annotation);
                                        }
                                    }
                                    exists = true;
                                    break;
                                } else if (oldAnno.newAnno && isAnnotationsPosEqual(annotation,oldAnno)){
                                    //Annotation was just added and is the same in newAnnotations
                                    //do Nothing
                                    delete oldAnno.newAnno;
                                    break;
                                }

                            }
                            if(!exists){
                                //append annotation to svg
                                (0,_appendChild2.default)(svg,annotation,viewport);
                            }
                            exists = false;
                        }
                        var exists = false;
                        for( var oldAnnoid in oldAnnotations[pageNumber]){
                            var oldAnno = oldAnnotations[pageNumber][oldAnnoid];
                            for (var annotationID in newAnnotations[pageNumber]) {
                                var annotation = newAnnotations[pageNumber][annotationID];
                                if(oldAnno.uuid == annotation.uuid){
                                    exists = true;
                                    break;
                                }
                            }
                            if(!exists  && !oldAnno.newAnno){
                                var node = document.querySelector('[data-pdf-annotate-id="'+oldAnno.uuid+'"]');
                                if(node !== null){
                                    node.parentNode.removeChild(node);
                                }
                            }
                            exists = false;
                        }
                        //call this function to repeat in 5 secs
                    //    setTimeout(loadNewAnnotations, 5000); 
                }, function (err){
                    notification.addNotification({
                        message: M.util.get_string('error:getAnnotations', 'pdfannotator'),
                        type: "error"
                    });
                });
            }
        }
    /***end of submodules of Module 2***/]));});;//# sourceMappingURL=pdf-annotate.js.map
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(3)(module)))

/***/ },
/* 3 */
/***/ function(module, exports) {

	module.exports = function(module) {
		if(!module.webpackPolyfill) {
			module.deprecate = function() {};
			module.paths = [];
			// module.parent = undefined by default
			module.children = [];
			module.webpackPolyfill = 1;
		}
		return module;
	}


/***/ },
/* 4 */
/***/ function(module, exports) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	  value: true
	});
	exports.default = initColorPicker;
	// Color picker component
	var COLORS = [{ hex: '#000000', name: 'Black' }, { hex: '#EF4437', name: 'Red' }, { hex: '#E71F63', name: 'Pink' }, { hex: '#8F3E97', name: 'Purple' }, { hex: '#65499D', name: 'Deep Purple' }, { hex: '#4554A4', name: 'Indigo' }, { hex: '#2083C5', name: 'Blue' }, { hex: '#35A4DC', name: 'Light Blue' }, { hex: '#09BCD3', name: 'Cyan' }, { hex: '#009688', name: 'Teal' }, { hex: '#43A047', name: 'Green' }, { hex: '#8BC34A', name: 'Light Green' }, { hex: '#FDC010', name: 'Yellow' }, { hex: '#F8971C', name: 'Orange' }, { hex: '#F0592B', name: 'Deep Orange' }, { hex: '#F06291', name: 'Light Pink' }];

	function initColorPicker(el, value, onChange) {
	  function setColor(value) {
	    var fireOnChange = arguments.length <= 1 || arguments[1] === undefined ? true : arguments[1];

	    currentValue = value;
	    a.setAttribute('data-color', value);
	    a.style.background = value;
	    if (fireOnChange && typeof onChange === 'function') {
	      onChange(value);
	    }
	    closePicker();
	  }

	  function togglePicker() {
	    if (isPickerOpen) {
	      closePicker();
	    } else {
	      openPicker();
	    }
	  }

	  function closePicker() {
	    document.removeEventListener('keyup', handleDocumentKeyup);
	    if (picker && picker.parentNode) {
	      picker.parentNode.removeChild(picker);
	    }
	    isPickerOpen = false;
	    a.focus();
	  }

	  function openPicker() {
	    if (!picker) {
	      picker = document.createElement('div');
	      picker.style.background = '#fff';
	      picker.style.border = '1px solid #ccc';
	      picker.style.padding = '2px';
	      picker.style.position = 'absolute';
	      picker.style.width = '122px';
	      el.style.position = 'relative';

	      COLORS.map(createColorOption).forEach(function (c) {
	        c.style.margin = '2px';
	        c.onclick = function () {
	          // Select text/pen instead of cursor.
                  if(c.parentNode.parentNode.className === 'text-color') {
                      document.querySelector('#pdftoolbar button.text').click(); 
                  } else if(c.parentNode.parentNode.className === 'pen-color') {
                      document.querySelector('#pdftoolbar button.pen').click(); 
                  }
                  setColor(c.getAttribute('data-color'));
	        };
	        picker.appendChild(c);
	      });
	    }

	    document.addEventListener('keyup', handleDocumentKeyup);
	    el.appendChild(picker);
	    isPickerOpen = true;
	  }

	  function createColorOption(color) {
	    var e = document.createElement('a');
	    e.className = 'color';
	    e.setAttribute('href', 'javascript://');
	    e.setAttribute('title', color.name);
	    e.setAttribute('data-color', color.hex);
	    e.style.background = color.hex;
	    return e;
	  }

	  function handleDocumentKeyup(e) {
	    if (e.keyCode === 27) {
	      closePicker();
	    }
	  }

	  var picker = void 0;
	  var isPickerOpen = false;
	  var currentValue = void 0;
	  var a = createColorOption({ hex: value });
          a.title = M.util.get_string('colorPicker','pdfannotator');
	  a.onclick = togglePicker;
	  el.appendChild(a);
	  setColor(value, false);
	}

/***/ }
/******/ ]);
}); //require JQuery closed
}

/**
 * 
 */
function read_visibility_of_checkbox(){
    var commentVisibility= "public";
        if (document.querySelector('#anonymousCheckbox').checked) {
            commentVisibility = "anonymous";
            document.querySelector('#anonymousCheckbox').checked = false;
        } 
        
        if (document.querySelector('#privateCheckbox') != null) {
            if (document.querySelector('#privateCheckbox').checked) {
              commentVisibility = "private";
              document.querySelector('#privateCheckbox').checked = false;
            }
        } 
        
        if (document.querySelector('#protectedCheckbox') != null) {
            if (document.querySelector('#protectedCheckbox').checked) {
              commentVisibility = "protected";
              document.querySelector('#protectedCheckbox').checked = false;
            } 
        }                              
    return commentVisibility;    
}

/**
 * Extract text from HTML.
 */
function extract_text_from_html(html) {
    let tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent;
}

function get_post_content(commentList) {
    var commentInsidePtag = commentList.querySelectorAll('');
}