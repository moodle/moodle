<?php

$regionsinfo = 'pagelayout';
if ($PAGE->blocks->region_has_content('side-pre', $OUTPUT)) {
    $regionsinfo .= '-pre';
}
if ($PAGE->blocks->region_has_content('side-post', $OUTPUT)) {
    $regionsinfo .= '-post';
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php echo $PAGE->pagetype ?>" class="<?php echo $PAGE->bodyclasses ?>">
<?php echo $OUTPUT->standard_top_of_body_html(); ?>
<div id="page" class="<?php echo $regionsinfo ?>">
<!-- First, let's wrap the sides -->
    <div id="wrapper-t">
        <div id="wrapper-l">
            <div id="wrapper-r">
                <div id="wrapper-b">
                <!-- Now, let's cap the corners -->
                    <div id="wrapper-tl">
                        <div id="wrapper-tr">
                            <div id="wrapper-bl">
                                <div id="wrapper-br">
                                    <div class="clearfix" id="header">
                                        <div id="header-t">
                                            <div id="header-r">
                                                <div id="header-l">
                                                    <div id="header-m" class="clearfix header">
                                                        <h1 class="headermain"><?php echo $PAGE->heading ?></h1>
                                                        <div class="headermenu"><?php echo $PAGE->headingmenu ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    
                                <?php if (empty($PAGE->layout_options['nonavbar']) and $PAGE->has_navbar()) { // This is the navigation bar with breadcrumbs  ?>
                                <div class="navbar clearfix">
                                    <div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
                                    <div class="navbutton"><?php echo $PAGE->button; ?></div>
                                </div>
                                <?php } ?>
                                <!-- END OF HEADER -->
                                <div id="content">
                                    <table id="layout-table" summary="layout">
                                        <tr>
                                            <?php if (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content('side-pre', $OUTPUT)) { ?>
                                            <td id="region-side-pre" class="block-region">
                                                <?php echo $OUTPUT->blocks_for_region('side-pre') ?>
                                            </td>
                                            <?php } ?>
                                            <td id="content" class="content">
                                                <?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
                                            </td>
                                            <?php if (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content('side-post', $OUTPUT)) { ?>
                                            <td id="region-side-post" class="block-region">
                                                <?php echo $OUTPUT->blocks_for_region('side-post') ?>
                                            </td>
                                            <?php } ?>
                                        </tr>
                                    </table>
                                </div>
                                <!-- END OF CONTENT -->

                                <?php if (empty($PAGE->layout_options['nofooter'])) { ?>
                                <div class="footer" id="footer">
                                    <div id="footer-b" class="clearfix">
                                        <div id="footer-l">
                                            <div id="footer-r">
                                                <div id="footer-m" class="clearfix">

                                                    <div id="footer-logo">
                                                        <a href="http://moodle.org" target="_blank"><img src="<?php echo $OUTPUT->pix_url('logo','theme');?>" title="Moodle <?php echo $CFG->release ?>" /></a>
                                                    </div>

                                                    <div id="footer-helplink">
                                                        <?php echo page_doc_link(get_string('moodledocslink')) ?>
                                                    </div>

                                                    <div id="footer-loggedinas">
                                                        <?php echo $OUTPUT->login_info(); ?>
                                                    </div>
                                                    
                                                    <?php echo $OUTPUT->standard_footer_html(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end #page -->
<script type="text/javascript" charset="utf-8">
/* <![CDATA[ */
    var CSSClass={};CSSClass.is=function(e,c){if(typeof e=="string")e=document.getElementById(e);var classes=e.className;if(!classes)return false;if(classes==c)return true;return e.className.search("\\b"+c+"\\b")!=-1;};CSSClass.add=function(e,c){if(typeof e=="string")e=document.getElementById(e);if(CSSClass.is(e,c))return;if(e.className)c=" "+c;e.className+=c;};CSSClass.remove=function(e,c){if(typeof e=="string")e=document.getElementById(e);e.className=e.className.replace(new RegExp("\\b"+c+"\\b\\s*","g"),"");};

    var jsscript = {

        corrections: function () {

            // check for layouttabel and add haslayouttable class to body
            function setbodytag () {
                var bd = document.getElementsByTagName('body')[0];
                if (bd) {
                    var tagname = 'nolayouttable';
                    if (document.getElementById('middle-column')) {
                        var lc = document.getElementById('left-column');
                        var rc = document.getElementById('right-column');
                        if ( lc && rc ) {
                            tagname = 'haslayouttable rightandleftcolumn';
                        } else if (lc) {
                            tagname = 'haslayouttable onlyleftcolumn';
                        } else if (rc) {
                            tagname = 'haslayouttable onlyrightcolumn';
                        } else {
                            tagname = 'haslayouttable onlymiddlecolumn';
                        }
                    }
                    CSSClass.add(bd, tagname);
                } else {
                    setTimeout(function() { setbodytag() }, 10);
                }
            };

            setbodytag();
        },

        init: function() {
            jsscript.corrections();
        }
    };

    jsscript.init();
/* ]]> */
</script>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>