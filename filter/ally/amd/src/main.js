/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Main library.
 *
 * @package
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/templates', 'core/str', 'filter_ally/ally',
        'filter_ally/imagecover', 'filter_ally/util'],
function($, Templates, Strings, Ally, ImageCover, Util) {
    return new function() {

        var self = this;

        document.addEventListener('core_filters/contentUpdated', () => {
            // When Snap lazy loads a section it triggers this event.
            // We can ensure everything has been processed on lazy load by recalling the second
            // stage initialization.
            self.initStageTwo();
        });

        self.canViewFeedback = false;
        self.canDownload = false;
        self.initialised = false;
        self.params = {};
        self.observedNodes = new WeakSet();

        /**
         * Get nodes by xpath.
         * @param {string} xpath
         * @returns {Array}
         */
        var getNodesByXpath = function(xpath) {
            var expression = window.document.createExpression(xpath);
            var result = expression.evaluate(window.document, XPathResult.ANY_TYPE);
            var nodes = [];
            do {
                var node = result.iterateNext();
                nodes.push(node);
            } while (node);
            return nodes;
        };

        /**
         * Get single node by xpath.
         * @param {string} xpath
         * @returns {Node}
         */
        var getNodeByXpath = function(xpath) {
            var expression = window.document.createExpression(xpath);
            var result = expression.evaluate(window.document, XPathResult.FIRST_ORDERED_NODE_TYPE);
            return result.singleNodeValue;
        };

        /**
         * Render template and insert result in appropriate place.
         * @param {object} data
         * @param {string} pathHash
         * @param {node} targetEl
         * @return {promise}
         */
        var renderTemplate = function(data, pathHash, targetEl) {
            var dfd = $.Deferred();

            if ($(targetEl).parents('.filter-ally-wrapper').length) {
                // This has already been processed.
                dfd.resolve();
                return dfd.promise();
            }

            // Too expensive to do at module level - this is a course level capability.
            data.canviewfeedback = self.canViewFeedback;
            data.candownload = self.canDownload;
            data.html = '<span id="content-target-' + pathHash + '"></span>';

            Templates.render('filter_ally/wrapper', data)
                .done(function(result) {
                    var presentWrappers = $(targetEl).next().find('span[data-file-id="' + pathHash + '"]');
                    if (presentWrappers.length == 0) {
                        $(targetEl).after(result);

                        // We are inserting the module element next to the target as opposed to replacing the
                        // target as we want to ensure any listeners attributed to the module element persist.
                        $('#content-target-' + pathHash).after(targetEl);
                        $('#content-target-' + pathHash).remove();
                    }
                    dfd.resolve();
                });

            return dfd.promise();
        };

        /**
         * Place holder items that are matched by selector.
         * @param {string} selector
         * @param {string} map
         * @return {promise}
         */
        var placeHoldSelector = function(selector, map) {
            var dfd = $.Deferred();

            var c = 0;

            var length = $(selector).length;
            if (!length) {
                dfd.resolve();
            }
            $(selector).each(function() {

                /**
                 * Check that all selectors have been processed.
                 */
                var checkComplete = function() {
                    if (c === length) {
                        dfd.resolve();
                    }
                };
                var url,
                    type;

                if ($(this).prop("tagName").toLowerCase() === 'a') {
                    url = $(this).attr('href');
                    type = 'a';
                } else {
                    url = $(this).attr('src');
                    type = 'img';
                }
                var regex;
                if (url.indexOf('?') > -1) {
                    regex = /pluginfile.php\/(\d*)\/(.*)(\?)/;
                } else {
                    regex = /pluginfile.php\/(\d*)\/(.*)/;
                }
                var match = url.match(regex);
                var pathHash;
                if (match) {
                    var path = match[1] + '/' + match[2];
                    path = decodeURIComponent(path);
                    pathHash = map[path];
                }

                if (pathHash === undefined) {
                    // Maybe 'slasharguments' setting is disabled for this host.
                    // Let's see if the file URI is found in the URL query.
                    var query = Util.getQuery(url);
                    if (query.file) {
                        var filePath = decodeURIComponent(query.file);
                        regex = /\/(\d*)\/(.*)/;

                        match = filePath.match(regex);
                        if (match) {
                            path = match[1] + '/' + match[2];
                            path = decodeURIComponent(path);
                            pathHash = map[path];
                        }
                    }
                }

                // Pathhash was definitely not found :( .
                if (pathHash === undefined) {
                    c++;
                    checkComplete();
                    return;
                }

                var data = {
                    isimage: type === 'img',
                    fileid: pathHash,
                    url: url
                };

                renderTemplate(data, pathHash, $(this))
                    .done(function() {
                        c++;
                        checkComplete();
                    });
            });
            return dfd.promise();
        };

        /**
         * Add place holders for forum module image attachments (note, regular files are covered by php).
         * @param {array} forumFileMapping
         * @return {promise}
         */
        var placeHoldForumModule = function(forumFileMapping) {
            var dfd = $.Deferred();
            placeHoldSelector('.forumpost .attachedimages img[src*="pluginfile.php"], ' +
                '.forumpost .body-content-container a[href*="pluginfile.php"]', forumFileMapping)
                .done(function() {
                    dfd.resolve();
                });
            return dfd.promise();
        };

        /**
         * Add place holders for assign module additional files.
         * @param {array} assignFileMapping
         * @return {promise}
         */
        var placeHoldAssignModule = function(assignFileMapping) {
            var dfd = $.Deferred();
            Util.whenTrue(function() {
                return $('div[id*="assign_files_tree"] .ygtvitem').length > 0;
            }, 10)
                .done(function() {
                    placeHoldSelector('div[id*="assign_files_tree"] a[href*="pluginfile.php"]', assignFileMapping);
                    dfd.resolve();
                });
            return dfd.promise();
        };

        /**
         * Add place holders for folder module files.
         * @param {array} folderFileMapping
         * @return {promise}
         */
        var placeHoldFolderModule = function(folderFileMapping) {
            var dfd = $.Deferred();
            Util.whenTrue(function() {
                return $('.foldertree > .filemanager .ygtvitem').length > 0;
            }, 10)
                .done(function() {
                    var unwrappedlinks = '.foldertree > .filemanager span:not(.filter-ally-wrapper) > a[href*="pluginfile.php"]';
                    placeHoldSelector(unwrappedlinks, folderFileMapping)
                        .done(function() {
                            dfd.resolve();
                        });
                });
            return dfd.promise();
        };

        /**
         * Add place holders for glossary module files.
         * @param {array} glossaryFileMapping
         * @return {promise}
         */
        var placeHoldGlossaryModule = function(glossaryFileMapping) {
            var dfd = $.Deferred();

            // Glossary attachment markup is terrible!
            // The first thing we need to do is rewrite the glossary attachments so that they are encapsulated.
            $('.entry .attachments > br').each(function() {
                var mainAnchor = $(this).prev('a[href*="pluginfile.php"]');
                mainAnchor.addClass('ally-glossary-attachment');
                var iconAnchor = $(mainAnchor).prev('a[href*="pluginfile.php"]');
                $(this).after('<div class="ally-glossary-attachment-row"></div>');
                var container = $(this).next('.ally-glossary-attachment-row');
                container.append(iconAnchor);
                container.append(mainAnchor);
                $(this).remove();
            });

            var unwrappedlinks = '.entry .attachments .ally-glossary-attachment';
            placeHoldSelector(unwrappedlinks, glossaryFileMapping)
                .done(function() {
                    dfd.resolve();
                });
            return dfd.promise();
        };

        /**
         * Encode a file path so that it can be used to find things by uri.
         * @param {string} filePath
         * @returns {string}
         */
        var urlEncodeFilePath = function(filePath) {
            var parts = filePath.split('/');
            for (var p in parts) {
                parts[p] = encodeURIComponent(parts[p]);
            }
            var encoded = parts.join('/');
            return encoded;
        };

        /**
         * General function for finding lesson component file elements and then add mapping.
         * @param {array} map
         * @param {string} selectorPrefix
         * @return promise
         */
        var placeHoldLessonGeneral = function(map, selectorPrefix) {
            var dfd = $.Deferred();
            if (map.length === 0) {
                dfd.resolve();
            } else {
                for (var c in map) {
                    var path = urlEncodeFilePath(c);
                    var sel = selectorPrefix + 'img[src*="' + path + '"], ' + selectorPrefix + 'a[href*="' + path + '"]';
                    placeHoldSelector(sel, map).done(function() {
                        dfd.resolve();
                    });
                }
            }
            return dfd.promise();
        };

        /**
         * Placehold lesson page contents.
         * @param {array} pageContentsMap
         * @returns promise
         */
        var placeHoldLessonPageContents = function(pageContentsMap) {
            return placeHoldLessonGeneral(pageContentsMap, '');
        };

        /**
         * Placehold lesson answers.
         * @param {array} pageAnswersMap
         * @returns promise
         */
        var placeHoldLessonAnswersContent = function(pageAnswersMap) {
            return placeHoldLessonGeneral(pageAnswersMap,
                '.studentanswer table tr:nth-child(1) '); // Space at end of selector intended.
        };

        /**
         * Placehold lesson responses.
         * @param {array} pageResponsesMap
         * @returns promise
         */
        var placeHoldLessonResponsesContent = function(pageResponsesMap) {
            return placeHoldLessonGeneral(pageResponsesMap,
                '.studentanswer table tr.lastrow '); // Space at end of selector intended.
        };

        /**
         * Add place holders for lesson module files.
         * @param {array} lessonFileMapping
         * @return {promise}
         */
        var placeHoldLessonModule = function(lessonFileMapping) {
            var dfd = $.Deferred();

            var pageContentsMap = lessonFileMapping.page_contents;
            var pageAnswersMap = lessonFileMapping.page_answers;
            var pageResponsesMap = lessonFileMapping.page_responses;

            placeHoldLessonPageContents(pageContentsMap)
                .then(function() {
                    return placeHoldLessonAnswersContent(pageAnswersMap);
                })
                .then(function() {
                    return placeHoldLessonResponsesContent(pageResponsesMap);
                })
                .then(function() {
                    dfd.resolve();
                });
            return dfd.promise();
        };

        /**
         * Add place holders for resource module.
         * @param {object} moduleFileMapping
         * @return {promise}
         */
        var placeHoldResourceModule = function(moduleFileMapping) {
            var dfd = $.Deferred();
            var c = 0;

            /**
             * Once all modules processed, resolve promise for this function.
             */
            var checkAllProcessed = function() {
                c++;
                // All resource modules have been dealt with.
                if (c >= Object.keys(moduleFileMapping).length) {
                    dfd.resolve();
                }
            };
            for (var moduleId in moduleFileMapping) {
                var pathHash = moduleFileMapping[moduleId].content;
                if ($('body').hasClass('theme-snap') && !$('body').hasClass('format-tiles')) {
                    var moduleEl = $('#module-' + moduleId + ':not(.snap-native) .activityinstance ' +
                        '.snap-asset-link a:first-of-type:not(.clickable-region)');
                } else {
                    var moduleEl = $('#module-' + moduleId + ' .activity-instance ' +
                        'a:first-of-type:not(.clickable-region,.editing_move)');
                }
                var processed = moduleEl.find('.filter-ally-wrapper');
                if (processed.length > 0) {
                    checkAllProcessed(); // Already processed.
                    continue;
                }
                var data = {
                    isimage: false,
                    fileid: pathHash,
                    url: $(moduleEl).attr('href')
                };
                renderTemplate(data, pathHash, moduleEl)
                    .done(checkAllProcessed);
            }
            return dfd.promise();
        };

        var buildContentIdent = function(component, table, field, id) {
            return [component, table, field, id].join(':');
        };

        /**
         * Add annotations to sections content.
         * @param {array} sectionMapping
         */
        var annotateSections = function(sectionMapping) {
            var dfd = $.Deferred();

            for (var s in sectionMapping) {
                var sectionId = sectionMapping[s];
                var ident = buildContentIdent('course', 'course_sections', 'summary', sectionId);

                var selectors = [
                    '#' + s + ' > .content div[class*="summarytext"] .no-overflow',
                    '#' + s + ' > .section-item div[class*="summarytext"] .no-overflow', // Moodle 4.4+
                    'body.theme-snap #' + s + ' > .content > .summary > div > .no-overflow' // Snap.
                ];
                $(selectors.join(',')).attr('data-ally-richcontent', ident);
            }

            dfd.resolve();
            return dfd.promise();
        };

        /**
         * Annotate module introductions.
         * @param {array} introMapping
         * @param {string} module
         * @param {array} additionalSelectors
         */
        const annotateModuleIntros = function(introMapping, module, additionalSelectors) {
            for (const i in introMapping) {
                const annotation = introMapping[i];

                // Description selector for when activity modules show a description on the course page.
                // We need to be specific here for non course pages to skip this.
                const descriptionSelector = self.config.moodleversion >= 2023100900 ?
                    // Selector for Moodle 4.3+
                    'li.activity.modtype_' + module + '#module-' + i + ' .activity-description .no-overflow > .no-overflow' :
                    // Selector for < Moodle 4.3
                    'li.activity.modtype_' + module + '#module-' + i + ' .description .no-overflow > .no-overflow';

                const selectors = [
                    'body.path-mod-' + module + '.cmid-' + i + ' #intro > .no-overflow',
                    descriptionSelector,
                    'li.snap-activity.modtype_' + module + '#module-' + i + ' .contentafterlink > .no-overflow'
                ];

                if (additionalSelectors) {
                    for (const a in additionalSelectors) {
                        selectors.push(additionalSelectors[a].replace('{{i}}', i));
                    }
                }
                $(selectors.join(',')).attr('data-ally-richcontent', annotation);
            }
        };

        /**
         * Add annotations to forums.
         * @param {array} forumMapping
         */
        var annotateForums = function(forumMapping) {
            // Annotate introductions.
            var intros = forumMapping.intros;
            annotateModuleIntros(intros, 'forum');

            // Annotate discussions.
            var discussions = forumMapping.posts;
            for (var d in discussions) {
                var post = 'p' + d;
                var annotation = discussions[d];
                var selectors = [
                    "#page-mod-forum-discuss #" + post +
                    ' div.forumpost div.no-overflow'
                ];
                $(selectors.join(',')).attr('data-ally-richcontent', annotation);
            }
        };

        /**
         * Add annotations to Open Forums.
         * @param {array} forumMapping
         */
        var annotateMRForums = function(forumMapping) {

            // Annotate introductions.
            var intros = forumMapping.intros;
            annotateModuleIntros(intros, 'hsuforum', ['#hsuforum-header .hsuforum_introduction > .no-overflow']);

            var discussions = forumMapping.posts;
            for (var d in discussions) {
                var annotation = discussions[d];
                var postSelector = 'article[id="p' + d + '"] div.posting';
                $(postSelector).attr('data-ally-richcontent', annotation);
            }
        };

        /**
         * Add annotations to glossary.
         * @param {array} mapping
         */
        var annotateGlossary = function(mapping) {
            // Annotate introductions.
            var intros = mapping.intros;
            annotateModuleIntros(intros, 'glossary');

            // Annotate entries.
            var entries = mapping.entries;
            for (var e in entries) {
                var annotation = entries[e];
                var entryFooter = $('.entrylowersection .commands a[href*="id=' + e + '"]');
                var entry = $(entryFooter).parents('.glossarypost').find('.entry .no-overflow');
                $(entry).attr('data-ally-richcontent', annotation);
            }
        };

        /**
         * Add annotations to page.
         * @param {array} mapping
         */
        var annotatePage = function(mapping) {
            var intros = mapping.intros;
            annotateModuleIntros(intros, 'page', ['li.snap-native.modtype_page#module-{{i}} .contentafterlink > .summary-text']);

            // Annotate content.
            var content = mapping.content;
            for (var c in content) {
                var annotation = content[c];
                var selectors = [
                    `#page-mod-page-view.cmid-${c} #region-main .box.generalbox > .no-overflow`,
                    `li.snap-native.modtype_page#module-${c} .pagemod-content`
                ];
                $(selectors.join(',')).attr('data-ally-richcontent', annotation);
            }
        };

        /**
         * Add annotations to book.
         * @param {array} mapping
         */
        var annotateBook = function(mapping) {
            var intros = mapping.intros;

            // For book, the only place the intro shows is on the course page when you select "display description on course page"
            // in the module settings.
            annotateModuleIntros(intros, 'book',
                ['li.snap-native.modtype_book#module-{{i}} .contentafterlink > .summary-text .no-overflow']);

            // Annotate content.
            var content = mapping.chapters,
chapterId;

            if (self.params.chapterid) {
                chapterId = self.params.chapterid;
            } else {
                var urlParams = new URLSearchParams(window.location.search);
                chapterId = urlParams.get('chapterid');
            }

            $.each(content, function(ch, annotation) {
                if (chapterId != ch) {
                    return;
                }
                var selectors = [
                    '#page-mod-book-view #region-main .box.generalbox.book_content > .no-overflow',
                    'li.snap-native.modtype_page#module-' + ch + ' .pagemod-content'
                ];
                $(selectors.join(',')).attr('data-ally-richcontent', annotation);
            });
        };

        /**
         * Add annotations to lesson.
         * @param {array} mapping
         */
        var annotateLesson = function(mapping) {
            var intros = mapping.intros;

            // For lesson, the only place the intro shows is on the course page when you select "display description on course page"
            // in the module settings.
            annotateModuleIntros(intros, 'lesson',
                ['li.snap-native.modtype_lesson#module-{{i}} .contentafterlink > .summary-text .no-overflow']);

            // Annotate content.
            var content = mapping.lesson_pages;

            for (var p in content) {
                if (document.body.id === "page-mod-lesson-edit") {
                    var xpath = '//a[@id="lesson-' + p + '"]//ancestor::table//tbody/tr/td/div[contains(@class, "no-overflow")]';
                    var annotation = content[p];
                    var node = getNodeByXpath(xpath);
                    $(node).attr('data-ally-richcontent', annotation);
                } else {
                    // Try get page from form.
                    var node = getNodeByXpath('//form[contains(@action, "continue.php")]//input[@name="pageid"]');
                    if (node) {
                        var pageId = $(node).val();
                    } else {
                        var urlParams = new URLSearchParams(window.location.search);
                        var pageId = urlParams.get('pageid');
                    }

                    if (pageId != p) {
                        continue;
                    }
                    var annotation = content[p];
                    var selectors = [
                        // Regular page.
                        '#page-mod-lesson-view #region-main .box.contents > .no-overflow',
                        // Question page.
                        '#page-mod-lesson-view #region-main form > fieldset > .fcontainer > .contents .no-overflow',
                        // Lesson page.
                        'li.snap-native.modtype_page#module-' + p + ' .pagemod-content'
                    ];

                    $(selectors.join(',')).attr('data-ally-richcontent', annotation);
                }
            }

            // Annotate answer answers.
            Strings.get_strings([
                {key: 'answer', component: 'mod_lesson'},
                {key: 'response', component: 'mod_lesson'}
            ]).then(function(strings) {
                var answerLabel = strings[0];
                var responseLabel = strings[1];
                var answers = mapping.lesson_answers;

                var processAnswerResponse = function(pageId, i, label, annotation) {
                    var xpath = '//a[@id="lesson-' + pageId + '"]//ancestor::table' +
                        '//td/label[contains(text(),"' + label + ' ' + i + '")]/ancestor::tr/td[2]';
                    var nodes = getNodesByXpath(xpath);
                    for (var n in nodes) {
                        var node = nodes[n];
                        $(node).attr('data-ally-richcontent', annotation);
                    }
                };

                for (var a in answers) {
                    // Increment anum so that we can get the answer number.
                    // Note, we can trust that this is correct because you can't order answers and the code in the lesson component
                    // orders answers by id.
                    var annotation = answers[a];

                    var tmpArr = a.split('_');
                    var pageId = tmpArr[0];
                    var ansId = tmpArr[1];
                    var anum = tmpArr[2];

                    // Process answers when on lesson edit page.
                    if (document.body.id === "page-mod-lesson-edit") {
                        processAnswerResponse(pageId, anum, answerLabel, annotation);
                    } else {
                        // Wrap answers in labels.
                        $('#page-mod-lesson-view label[for="id_answerid_' + ansId + '"]').attr('data-ally-richcontent', annotation);

                        if (self.params.answerid && self.params.answerid == ansId) {
                            $('.studentanswer tr:nth-of-type(1) > td div').attr('data-ally-richcontent', annotation);
                        } else {
                            var answerWrapperId = 'answer_wrapper_' + ansId;
                            var answerEl = $('#id_answerid_' + ansId);
                            if (answerEl.data('annotated') != 1) {
                                // We only want to wrap this once.
                                var contentEls = answerEl.nextAll();
                                answerEl.parent('label').append('<span id="answer_wrapper_' + ansId + '"></span>');
                                $('#' + answerWrapperId).append(contentEls);
                            }
                            answerEl.data('annotated', 1);
                        }
                        $('#answer_wrapper_' + a).attr('data-ally-richcontent', annotation);
                    }
                }

                // Annotate answer responses.
                var responses = mapping.lesson_answers_response;
                for (var r in responses) {
                    var annotation = responses[r];

                    var tmpArr = r.split('_');
                    var pageId = tmpArr[0];
                    var respId = tmpArr[1];
                    var rnum = tmpArr[2];

                    if (document.body.id === "page-mod-lesson-edit") {
                        processAnswerResponse(pageId, rnum, responseLabel, annotation);
                    } else if (self.params.answerid && self.params.answerid == respId) {
                        // Just incase you are wondering, yes answer ids ^ are the same as response ids ;-).
                        var responseWrapperId = 'response_wrapper_' + respId;
                        if (!$('.studentanswer tr.lastrow > td #' + responseWrapperId).length) {
                            // We only want to wrap this once, hence above ! length check.
                            var contentEls = $('.studentanswer tr.lastrow > td > br').nextAll();
                            $('.studentanswer tr.lastrow > td > br').after('<span id="' + responseWrapperId + '"></span>');
                            $('#' + responseWrapperId).append(contentEls);
                        }

                        $('#' + responseWrapperId).attr('data-ally-richcontent', annotation);
                    }

                }
            });
        };

        /**
         * Annotate supported modules
         * @param {array} moduleMapping
         */
        var annotateModules = function(moduleMapping) {
            var dfd = $.Deferred();

            if (moduleMapping.mod_forum !== undefined) {
                annotateForums(moduleMapping.mod_forum);
            }
            if (moduleMapping.mod_hsuforum !== undefined) {
                annotateMRForums(moduleMapping.mod_hsuforum);
            }
            if (moduleMapping.mod_glossary !== undefined) {
                annotateGlossary(moduleMapping.mod_glossary);
            }
            if (moduleMapping.mod_page !== undefined) {
                annotatePage(moduleMapping.mod_page);
            }
            if (moduleMapping.mod_book !== undefined) {
                annotateBook(moduleMapping.mod_book);
            }
            if (moduleMapping.mod_lesson !== undefined) {
                annotateLesson(moduleMapping.mod_lesson);
            }
            dfd.resolve();
            return dfd.promise();
        };

        /**
         * Annotates course summary if found on footer.
         * @param {object} mapping
         */
        var annotateSnapCourseSummary = function(mapping) {
            var dfd = $.Deferred();
            var snapFooterCourseSummary = $('#snap-course-footer-summary > div.no-overflow');
            if (snapFooterCourseSummary.length) {
                var ident = buildContentIdent('course', 'course', 'summary', mapping.courseId);
                snapFooterCourseSummary.attr('data-ally-richcontent', ident);
            }
            dfd.resolve();
            return dfd.promise();
        };

        /**
         * Annotate html block.
         * @param {object} mapping
         */
        var annotateHtmlBlock = function(mapping) {
            var dfd = $.Deferred();

            var items = mapping.block_html;
            for (var i in items) {
                var ident = items[i];
                var selectors = [
                    '#inst' + i + '.block_html > .card-body > .card-text > .no-overflow',
                    '#inst' + i + '.block_html > .content > .no-overflow'
                ];
                var selector = selectors.join(',');
                $(selector).attr('data-ally-richcontent', ident);
            }
            dfd.resolve();
            return dfd.promise();
        };

        /**
         * Apply place holders and add annotations to content.
         * @return {promise}
         */
        var applyPlaceHolders = function() {
            M.util.js_pending('filter_ally_applyPlaceHolders');
            var dfd = $.Deferred();

            if (ally_module_maps === undefined || ally_section_maps === undefined) {
                dfd.resolve();
                return dfd.promise();
            }

            var tasks = [{
                mapVar: ally_module_maps.file_resources,
                method: placeHoldResourceModule
            },
            {
                mapVar: ally_module_maps.assignment_files,
                method: placeHoldAssignModule
            },
            {
                mapVar: ally_module_maps.folder_files,
                method: placeHoldFolderModule
            },
            {
                mapVar: ally_module_maps.forum_files,
                method: placeHoldForumModule
            },
            {
                mapVar: ally_module_maps.glossary_files,
                method: placeHoldGlossaryModule
            },
            {
                mapVar: ally_module_maps.lesson_files,
                method: placeHoldLessonModule
            },
            {
                mapVar: ally_section_maps,
                method: annotateSections
            },
            {
                mapVar: ally_annotation_maps,
                method: annotateModules
            },
            {
                mapVar: {courseId: self.courseId},
                method: annotateSnapCourseSummary
            },
            {
                mapVar: ally_annotation_maps,
                method: annotateHtmlBlock
            }];

            $(document).ready(function() {
                var completed = 0;
                /**
                 * Run this once a task has resolved.
                 */
                var onTaskComplete = function() {
                    completed++;
                    if (completed === tasks.length) {
                        // All tasks completed.
                        M.util.js_complete('filter_ally_applyPlaceHolders');
                        dfd.resolve();
                    }
                };

                for (var t in tasks) {
                    var task = tasks[t];
                    if (Object.keys(task.mapVar).length > 0) {
                        task.method(task.mapVar)
                            .done(onTaskComplete);
                    } else {
                        // Skipped this task because mappings are empty.
                        onTaskComplete();
                    }
                }
            });
            return dfd.promise();
        };

        var debounceApplyPlaceHolders = Util.debounce(function() {
            return applyPlaceHolders();
        }, 1000);

        /**
         * Initialise JS stage two.
         */
        this.initStageTwo = function() {
            const {jwt, config} = self;
            if (self.canViewFeedback || self.canDownload) {
                debounceApplyPlaceHolders()
                    .done(function() {
                        ImageCover.init();
                        Ally.init(jwt, config);
                        try {
                            var selector = $('.foldertree > .filemanager');
                            var targetNode = selector[0];
                            if (targetNode) {
                                var observerConfig = {attributes: true, childList: true, subtree: true};
                                var callback = function(mutationsList) {
                                    mutationsList.filter(function(mutation) {
                                        return mutation.type === 'childList';
                                    }).forEach(function() {
                                        placeHoldFolderModule(ally_module_maps.folder_files);
                                    });
                                };
                                var observer = new MutationObserver(callback);

                                // Avoid observing the same DOM node multiple times.
                                // Note: If a node is removed and reinserted into the DOM, it becomes a new object reference.
                                // This check will allow re-observing such new nodes, which is desirable,
                                // since the original observer won't persist across DOM removal.
                                if (!self.observedNodes.has(targetNode)) {
                                    observer.observe(targetNode, observerConfig);
                                    self.observedNodes.add(targetNode);
                                }
                            }
                        } catch (error) {
                            setInterval(function() {
                                placeHoldFolderModule(ally_module_maps.folder_files);
                            }, 5000);
                        }
                        self.initialised = true;
                    });

                $(document).ajaxComplete(function() {
                    if (!self.initialised) {
                        return;
                    }
                    debounceApplyPlaceHolders();
                });
                // For Snap theme.
                if ($('body.theme-snap').length) {
                    $(document).ajaxComplete(function(event, xhr, settings) {
                        // Search ally server response.
                        if (settings.url.includes('ally.js')) {
                            setTimeout(function() {
                                // Show score icons that are hidden, see INT-18688.
                                $('.ally-feedback.ally-active.ally-score-indicator-embedded span').each(function() {
                                    if (this.style.display == 'none') {
                                        this.style.display = 'block';
                                        if (this.getAttribute('class') == 'ally-scoreindicator-container') {
                                            this.style.display = 'inline-block';
                                            this.children[0].style.display = 'inline-block';
                                        }
                                    }
                                });
                            }, 5000);
                            $(document).off('ajaxComplete');
                        }
                    });
                }
            }
        };

        /**
         * Init function.
         * @param {string} jwt
         * @param {object} config
         * @param {boolean} canViewFeedback
         * @param {boolean} canDownload
         * @param {int} courseId
         * @param {object} params
         */
        this.init = function(jwt, config, canViewFeedback, canDownload, courseId, params) {

            self.canViewFeedback = canViewFeedback;
            self.canDownload = canDownload;
            self.courseId = courseId;
            self.params = params;
            self.jwt = jwt;
            self.config = config;

            var pluginJSURL = function(path) {
                return M.cfg.wwwroot + "/pluginfile.php/" + M.cfg.contextid + "/filter_ally/" + path;
            };

            var polyfills = {};
            if (!document.evaluate) {
                polyfills['filter_ally/wgxpath'] = pluginJSURL("vendorjs/wgxpath.install");
            }
            if (typeof (URLSearchParams) === 'undefined') {
                polyfills['filter_ally/urlsearchparams'] = [
                    'https://cdnjs.cloudflare.com/ajax/libs/url-search-params/1.1.0/url-search-params.amd.js',
                    pluginJSURL('vendorjs/url-search-params.amd') // CDN fallback.
                ];
            }
            if (polyfills !== {}) {
                // Polyfill document.evaluate.
                require.config(
                    {
                        enforceDefine: false,
                        paths: polyfills
                    }
                );
                var requires = Object.keys(polyfills);

                require(requires, function() {
                    if (typeof (URLSearchParams) === 'undefined') {
                        window.URLSearchParams = arguments[1]; // Second arg in require (which is URLSearchParams)
                    }
                    self.initStageTwo();
                });

                return;
            }
            self.initStageTwo();
        };
    }();
});
