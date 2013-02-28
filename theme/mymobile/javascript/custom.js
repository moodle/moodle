$(document).bind("mobileinit", function(){
//mobile init stuff 11/12/10
//turn off ajax forms...
$.mobile.defaultPageTransition = "slide";
});

$(document).ready(function() {
    //get some vars to start
    var siteurl = $('.mobilesiteurl').attr("id");
    var mytheme = $(".datatheme").attr("id");
    var mythemeb = $(".datathemeb").attr("id");


    //function below does generic stuff before creating all pages...
    $('div').live('pagebeforecreate', function(event, ui) {
        //turn off ajax on all forms for now as of beta1
        $('form').attr("data-ajax", "false");
        //lesson
        $('.lessonbutton.standardbutton a').attr("data-role", "button");
        $('#page-mod-lesson-viewPAGE div.fitemtitle label').addClass("afirst");

        //tablet column removal switch
        $('.slider').live("change",function() {
            var slids = $(this).val();
            M.util.set_user_preference('theme_mymobile_chosen_colpos', slids);
            if (slids == "off") {
                $('.ui-page-active').removeClass("has-myblocks");
            } else {
                $('.ui-page-active').addClass("has-myblocks");
            }
        });

        //tabs- links set to external to fix forms
        $('div.tabtree ul.tabrow0').attr("data-role", "controlgroup");
        $('div.tabtree ul.tabrow12').attr("data-role", "controlgroup");
        $('div.tabtree li a').attr("data-role", "button").attr("data-ajax", "false");

        //jump to current or bottom
        $('a.jumptocurrent').live('tap', function() {
            var position = $(".ui-page-active .section.current").position();
            if (!position) {
                var position = $(".ui-page-active .mobilefooter").position();
            }
            $.mobile.silentScroll(position.top);
            $(this).removeClass("ui-btn-active");
            return false;
        });

        //scroll to top
        $('a#uptotop').live('tap', function() {
            var position = $(".ui-page-active").position();
            $.mobile.silentScroll(position.top);
            $(this).removeClass("ui-btn-active");
            return false;
        });

        //remove message notifcation overlay on tap 6/21/11
        $('a#notificationno').live('tap', function() {
            $('#newmessageoverlay').remove();
            return false;
        });

        //calendar and other links that need to be external
        $('.maincalendar .filters a, li.activity.scorm a, div.files a, #page-user-filesPAGE li div a, .maincalendar .bottom a, .section li.url.modtype_url a, .resourcecontent .resourcemediaplugin a, #underfooter .noajax a, .block_mnet_hosts .content a, .block_private_files .content a, a.portfolio-add-link, #attempts td a').attr("data-ajax", "false");

        //add blank to open in window for some
        $('#page-mod-url-viewPAGE div.urlworkaround a, #page-mod-resource-viewPAGE div.resourceworkaround a, .mediaplugin a.mediafallbacklink, #page-mod-resource-viewPAGE .resourcemp3 a, .foldertree li a').attr("target", "_blank").attr("data-role", "button").attr("data-icon", "plus");

        //// **** general stuff *** ////
        $('form fieldset').attr("data-role", "fieldcontain");
        $('form .fitem').attr("data-role", "fieldcontain");

        //submit button for forum
        $('#page-mod-url-viewPAGE div.urlworkaround a').attr("data-role", "button");

        //survey form fix
        $('#surveyform').attr("action", siteurl + '/mod/survey/save.php');

        //nav select navigtation NEW
        $("#navselect").live("change",function() {
            var meb = encodeURI($(this).val());
            $(this).val("-1");
            if (meb != "" && meb != "-1") {
                $.mobile.changePage(meb);
            }
       });

    });


    //course page only js
    $('div.path-site, div.path-course-view, .path-course-view div.generalpage').live('pagebeforecreate', function(event, ui) {
        //course listing
        $('.section li img.activityicon').addClass("ui-li-icon");
        $('.course-content ul.section, .sitetopic ul.section').attr("data-role", "listview").attr("data-inset", "true").attr("data-theme", mythemeb);
        $('.topics div.left.side').addClass("ui-bar-" + mytheme);
        $('.section.hidden div.headingwrap').attr("data-theme", mythemeb);
        //$('.topics #section-0 div.left.side').removeClass("ui-li ui-li-divider ui-btn ui-bar-a");
        $('.section .resource.modtype_resource a, .section .modtype_survey a').attr("data-ajax", "false");

        //toggle completion checkmarks and form fixes
        $('.togglecompletion input[type="image"]').attr("data-role", "none");
        $('.togglecompletion input[type="image"]').click(function() {
            $(".section .togglecompletion").attr("action", '');
            var mylocc = siteurl + "/course/togglecompletion.php";
            $(".section .togglecompletion").attr("action", mylocc);
            this.form.submit();
            return false;
        });

        // Force the class ui-li-desc on non-detected elements.
        $('ul.section div.availabilityinfo, ul.section div.contentafterlink').addClass('ui-li-desc');

        // Force some classes on dimmed elements.
        $('ul.section div.dimmed_text > span').addClass('instancename');
    });

    //forum listing only stuff
    $('div#page-mod-forum-viewPAGE, #page-mod-forum-view div.generalpage').live('pagebeforecreate',function(event, ui){
        //forums listing change theme for other theme
        $('table.forumheaderlist').attr("data-role", "controlgroup");
        $('table.forumheaderlist thead tr').attr("data-role", "button").attr("data-theme", mythemeb);
        $('table.forumheaderlist td.topic a').attr("data-role", "button").attr("data-icon", "arrow-r").attr("data-iconpos", "right").attr("data-theme", mythemeb);
    });

    $('div#page-mod-forum-viewPAGE').live('pageinit',function(event, ui){
        $('.forumheaderlist td.topic').each(function(index) {
            var ggg = $(this).nextAll("td.replies").text();
            $(this).find('a').append('<span class="ui-li-count ui-btn-up-a ui-btn-corner-all"> ' + ggg + '</span>');
         });
    });

    //forum discussion page only stuff
    $('div#page-mod-forum-discussPAGE, #page-mod-forum-discuss div.generalpage, div.forumtype-single, .forumtype-single div.generalpage, div#page-mod-forum-postPAGE').live('pagebeforecreate',function(event, ui){
        //actual forum posting
        $('.forumpost div.row.header').addClass("ui-li ui-li-divider ui-btn ui-bar-" + mytheme);
        $('.options div.commands').attr("data-role", "controlgroup").attr("data-type", "horizontal");
        $('.options div.commands a').attr("data-role", "button").attr("data-ajax", "false").attr("data-inline", "true");
        $('.forumpost div.author a').attr("data-inline", "true");
        $('.options div.commands').contents().filter(function() {
            return this.nodeType == 3; //Node.TEXT_NODE
        }).remove();
        //function above removes | in div.commands
    });

    //frontpage only stuff
    $('div#page-site-indexPAGE, div.pagelayout-coursecategory').live('pagebeforecreate',function(event, ui){
        //course boxes on category pages and front page stuff
        //forum posts on front page only
        $('.forumpost div.row.header').addClass("ui-li ui-li-divider ui-btn ui-bar-" + mytheme);
        $('div.subscribelink a').attr("data-role", "button").attr("data-inline", "true");
        $('.unlist').attr("data-role", "controlgroup");
        $('div.coursebox a').attr("data-role", "button").attr("data-icon", "arrow-r").attr("data-iconpos", "right").attr("data-theme", mythemeb);
        $('.box.categorybox').attr("data-role", "controlgroup");
        $('div.categorylist div.category a').attr("data-role", "button").attr("data-theme", mythemeb);
        $('#shortsearchbox, #coursesearch2 #shortsearchbox').attr("data-type", "search");
    });

    $('div#page-site-indexPAGE').live('pageinit',function(event, ui){
        $('div.categorylist div.category').each(function(index) {
            var ggb = $(this).find("span.numberofcourse").text().replace('(','').replace(')','');
            if (ggb != "") {
                $(this).find('a').append('<span class="ui-li-count ui-btn-up-a ui-btn-corner-all">' + ggb + '</span>');
            }
        });
    });

    //chat only stuff
    $('div#chatpage, div.path-mod-chat').live('pagebeforecreate',function(event, ui){
        $('#input-message, #button-send').attr("data-role", "none");
        $('#enterlink a').attr("data-role", "button").attr("data-ajax", "false").attr("data-icon", "plus");
        $('form, input, button').attr("data-ajax", "false");
    });

    //login page only stuff
    $('div#page-login-indexPAGE').live('pagebeforecreate',function(event, ui){
        //signup form fix
        $('.path-login .signupform #signup').attr("action", siteurl + '/login/signup.php');
        $('.path-login #guestlogin').attr("action", siteurl + '/login/index.php');
    });

    //messaging only stuff
    $('div#page-message-indexPAGE').live('pagebeforecreate',function(event, ui){
        //below to fix form actions here and there
        $("#usergroupform").attr("action", '');
        //if (userform == "") {
        var myloc = siteurl + "/message/index.php";
        $("#usergroupform").attr("action", myloc);
        //messaging links
        $('.path-message td.link').attr("data-role", "controlgroup").attr("data-type", "horizontal");
        $('.path-message td.link a').attr("data-role", "button").attr("data-inline", "true");
    });

    //database and glossary only stuff
    $('div#page-mod-data-viewPAGE, div#page-mod-glossary-viewPAGE').live('pagebeforecreate',function(event, ui){
        $('.defaulttemplate td a').attr("data-role", "button").attr("data-ajax", "false").attr("data-inline", "true");
        $('#options select, .aliases select').attr("data-native-menu", "true");
        $('#pref_search, .glossarysearchbox input[type="text"]').attr("data-type", "search");
        $('#options').attr("action", siteurl + '/mod/data/view.php');
        $('#page-mod-glossary-viewPAGE form').each(function(index) {
            var glossform = $(this).attr("action");
            if (glossform == "view.php") {
                $(this).attr("action", siteurl + '/mod/glossary/view.php');
            }
        });
    });

    //mymoodle only stuff
    $('div#page-my-indexPAGE').live('pagebeforecreate',function(event, ui){
        //my moodle page fixes
        //block_course_overview
        $('.block_course_overview div.headingwrap').attr("data-role", "none");
        $('.block_course_overview h3.main a').attr("data-theme", mytheme);
    });

    //resource only stuff to help embedded PDFs, provides link to open in new window
    $('div#page-mod-resource-viewPAGE').live('pagebeforecreate',function(event, ui){
        $('div.resourcepdf').each(function(index) {
            var thisopen = $(this).find('#resourceobject').attr("data");
            $(this).append('<a class="mobileresource" href="' +thisopen+ '" target="_blank"></a>');
        });
    });

    //quiz page only js
    $('div#page-mod-quiz-viewPAGE, div#page-mod-quiz-attemptPAGE, div#page-mod-quiz-summaryPAGE, div#page-mod-quiz-reviewPAGE, #page-mod-quiz-attempt #content2').live('pagebeforecreate',function(event, ui){
        //add quiz timer into quiz page
        $('#quiz-timer').remove();
        $('.mymobilecontent').prepend('<div id="quiz-timer" > <span id="quiz-time-left"></span></div>');
        $('.que .info').addClass("ui-bar-" + mytheme);
        $('.que input.submit').attr("data-role", "none");
        $('div.submitbtns a, div.quizattemptcounts a').attr("data-role", "button").attr("data-ajax", "false");
        $('#page-mod-quiz-attemptPAGE .questionflag input, .path-mod-quiz .questionflag input').attr("data-role", "none");
    });

    //assignment page only stuff
    $('#page-mod-assignment-viewPAGE').live('pagebeforecreate',function(event, ui){
         //below fixes the advanced upload edit notes button
         $('#page-mod-assignment-viewPAGE div[rel="upload.php"]').parent().attr("action", siteurl + '/mod/assignment/upload.php');
    });

    //hotpot page only stuff
    $('div.path-mod-hotpot').live('pagebeforecreate',function(event, ui){
        $('.path-mod-hotpot button').attr("data-role", "none");
    });

    //collapsed topic only stuff
    $('div#page-course-view-topcollPAGE').live('pagebeforecreate',function(event, ui){
        $('#page-course-view-topcollPAGE ul.section').attr("data-role", "none");
        $('.section li img').removeClass("ui-li-icon");
        $.getScript('../course/format/topcoll/module.js');
        $('#page-course-view-topcollPAGE tr.cps a').attr("data-role", "button").attr("data-icon", "arrow-r");
        $('#page-course-view-topcollPAGE #thetopics').attr("data-role", "controlgroup");
        $('#page-course-view-topcollPAGE td.cps_centre').each(function(index) {
            var cpsc = $(this).text().replace('<br>','').replace(')','');
            $(this).prev('td').find('a').append('<span class="ui-li-count ui-btn-up-a ui-btn-corner-all">' + cpsc + '</span>');
        });
    });

    ///// functions below does stuff after creating page for some cleaning...
    $('div').live('pageinit',function(event, ui){
        $('.path-calendar div.ui-radio label:first-child, .path-mod-lesson div.ui-radio label:first-child, #page-mod-wiki-createPAGE div.ui-radio label:first-child').addClass("afirst");
        $('.forumpost div.author a').removeAttr('data-role');
        //$('.questionflagimage2').removeClass("ui-btn-hidden");a#notificationyes
        //image replacement
        $(this).find(".ithumb .course-content .summary img, .ithumb .course-content .activity.label img, .ithumb .sitetopic .no-overflow img").click(function() {
            var turl = $(this).attr("src");
            window.open(turl);
        });
    });
});