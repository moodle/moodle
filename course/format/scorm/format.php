<?php // $Id$
      // format.php - course format featuring scorm player
      //              included from view.php

    require_once("$CFG->dirroot/mod/forum/lib.php");

    require_once($CFG->dirroot.'/mod/scorm/lib.php');
    $organization = optional_param('organization', '', PARAM_INT);

    // Bounds for block widths
    define('BLOCK_L_MIN_WIDTH', 100);
    define('BLOCK_L_MAX_WIDTH', 210);
    define('BLOCK_R_MIN_WIDTH', 100);
    define('BLOCK_R_MAX_WIDTH', 210);

    optional_variable($preferred_width_left,  blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]));
    optional_variable($preferred_width_right, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]));
    $preferred_width_left = min($preferred_width_left, BLOCK_L_MAX_WIDTH);
    $preferred_width_left = max($preferred_width_left, BLOCK_L_MIN_WIDTH);
    $preferred_width_right = min($preferred_width_right, BLOCK_R_MAX_WIDTH);
    $preferred_width_right = max($preferred_width_right, BLOCK_R_MIN_WIDTH);

    $strgroups  = get_string('groups');
    $strgroupmy = get_string('groupmy');
    $strupdate = get_string('update');
    $strscorm = get_string('modulename','scorm');
    $editing    = $PAGE->user_is_editing();

    echo '<table id="layout-table" cellspacing="0">';
    echo '<tr>';

    if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $editing) {
        echo '<td style="width: '.$preferred_width_left.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        echo '</td>';
    }

    echo '<td id="middle-column"><div class="mod-scorm">';
    if ($scorms = get_all_instances_in_course("scorm", $course)) {
        // The SCORM with the least id is the course SCORM  
        $scorm = current($scorms);
        if (! $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
        $colspan = '';
        $headertext = '<table width="100%"><tr><td class="title">'.get_string('name').': <b>'.format_string($scorm->name).'</b>';
        if (isteacher($course->id) || isadmin()) {
            if (isediting($course->id)) {
                // Display update scorm icon
                $path = $CFG->wwwroot.'/course';
                $headertext .= '<span class="commands">'.
                        '<a title="'.$strupdate.'" href="'.$path.'/mod.php?update='.$cm->id.'&amp;sesskey='.sesskey().'">'.
                        '<img src="'.$CFG->pixpath.'/t/edit.gif" hspace="2" height="11" width="11" border="0" alt="'.$strupdate.'" /></a></span>';
            }
            $headertext .= '</td>';
            // Display report link
            $trackedusers = get_record('scorm_scoes_track', 'scormid', $scorm->id, '', '', '', '', 'count(distinct(userid)) as c');
            if ($trackedusers->c > 0) {
                $headertext .= '<td class="reportlink">'.
                              '<a target="'.$CFG->framename.'" href="'.$CFG->wwwroot.'/mod/scorm/report.php?id='.$cm->id.'">'.
                               get_string('viewallreports','scorm',$trackedusers->c).'</a>';
            } else {
                $headertext .= '<td class="reportlink">'.get_string('noreports','scorm');
            }
            $colspan = ' colspan="2"';
        } 
        $headertext .= '</td></tr><tr><td'.$colspan.'>'.format_text(get_string('summary').':<br />'.$scorm->summary).'</td></tr></table>';
        print_simple_box($headertext,'','100%');
?>
        <?php print_simple_box_start('center','100%'); ?>
        <div class="structurehead"><?php print_string('coursestruct','scorm') ?></div>
        <?php
            if (empty($organization)) {
                $organization = $scorm->launch;
            }
            if ($orgs = get_records_select_menu('scorm_scoes',"scorm='$scorm->id' AND organization='' AND launch=''",'id','id,title')) {
                if (count($orgs) > 1) {
         ?>
            <div class='center'>
		    <?php print_string('organizations','scorm') ?>
                <form name='changeorg' method='post' action='view.php?id=<?php echo $course->id ?>'>
                    <?php choose_from_menu($orgs, 'organization', "$organization", '','submit()') ?>
                </form>
            </div>
         <?php
                }
            }
            $orgidentifier = '';
            if ($org = get_record('scorm_scoes','id',$organization)) {
                if (($org->organization == '') && ($org->launch == '')) {
                    $orgidentifier = $org->identifier;
                } else {
                    $orgidentifier = $org->organization;
                }
            }
            $result = scorm_get_toc($scorm,'structlist',$orgidentifier);
            $incomplete = $result->incomplete;
            echo $result->toc;
            print_simple_box_end();
         ?>
              <div class="center">
              <form name="theform" method="post" action="<?php echo $CFG->wwwroot ?>/mod/scorm/playscorm.php?id=<?php echo $cm->id ?>"<?php echo $scorm->popup == 1?' target="newwin"':'' ?>>
              <?php
                  if ($scorm->hidebrowse == 0) {
                      print_string("mode","scorm");
                      echo ': <input type="radio" id="b" name="mode" value="browse" /><label for="b">'.get_string('browse','scorm').'</label>'."\n";
                      if ($incomplete === true) {
                          echo '<input type="radio" id="n" name="mode" value="normal" checked="checked" /><label for="n">'.get_string('normal','scorm')."</label>\n";
                      } else {
                          echo '<input type="radio" id="r" name="mode" value="review" checked="checked" /><label for="r">'.get_string('review','scorm')."</label>\n";
                      }
                  } else {
                      if ($incomplete === true) {
                          echo '<input type="hidden" name="mode" value="normal" />'."\n";
                      } else {
                          echo '<input type="hidden" name="mode" value="review" />'."\n";
                      }
                  }
              ?>
              <br />
              <input type="hidden" name="scoid" />
              <input type="hidden" name="currentorg" value="<?php echo $orgidentifier ?>" />
              <input type="submit" value="<? print_string('entercourse','scorm') ?>" />
              </form>
          </div>
          <script language="javascript" type="text/javascript">
          <!--
              function playSCO(scoid) {
                  document.theform.scoid.value = scoid;
                  document.theform.submit();
              }

              function expandCollide(which,list) {
                  var nn=document.ids?true:false
                  var w3c=document.getElementById?true:false
                  var beg=nn?"document.ids.":w3c?"document.getElementById(":"document.all.";
                  var mid=w3c?").style":".style";

                  if (eval(beg+list+mid+".display") != "none") {
                      which.src = "<?php echo $CFG->wwwroot ?>/mod/scorm/pix/plus.gif";
                      eval(beg+list+mid+".display='none';");
                  } else {
                      which.src = "<?php echo $CFG->wwwroot ?>/mod/scorm/pix/minus.gif";
                      eval(beg+list+mid+".display='block';");
                  }
              }
          -->
          </script>
<?php
    } else {
        if (isteacheredit($course->id)) {
            // Create a new scorm activity
	        redirect('mod.php?id='.$course->id.'&amp;section=0&sesskey='.sesskey().'&amp;add=scorm');
        } else {
            notify('Could not find a SCORM course here');
        }
    }
    echo '</div></td>';

    // The right column
    if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $editing) {
        echo '<td style="width: '.$preferred_width_right.'px;" id="right-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
        echo '</td>';
    }

    echo '</tr>';
    echo '</table>';

?>
