<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package   theme_lambda
 * @copyright 2020 redPIthemes
 *
 */
 
 class theme_lambda_core_renderer extends core_renderer {
	 
	     /** @var custom_menu_item language The language menu if created */
    protected $language = null;

    /**
     * The standard tags that should be included in the <head> tag
     * including a meta description for the front page
     *
     * @return string HTML fragment.
     */
    public function standard_head_html() {
        global $SITE, $PAGE;

        $output = parent::standard_head_html();

        // Setup help icon overlays.
        $this->page->requires->yui_module('moodle-core-popuphelp', 'M.core.init_popuphelp');
        $this->page->requires->strings_for_js(array(
            'morehelp',
            'loadinghelp',
        ), 'moodle');

        if ($PAGE->pagelayout == 'frontpage') {
            $summary = s(strip_tags(format_text($SITE->summary, FORMAT_HTML)));
            if (!empty($summary)) {
                $output .= "<meta name=\"description\" content=\"$summary\" />\n";
            }
        }

        return $output;
    }
	
    public function favicon() {
        global $CFG;

        $theme = theme_config::load('lambda');
        $favicon = $theme->setting_file_url('favicon', 'favicon');

        if (!empty(($favicon))) {
            $urlreplace = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
            $favicon = str_replace($urlreplace, '', $favicon);
            return new moodle_url($favicon);
        }
        return parent::favicon();
    }
	
	    /*
     * This renders the navbar.
     * Uses bootstrap compatible html.
     */
    public function navbar() {
        $items = $this->page->navbar->get_items();
        if (empty($items)) {
            return '';
        }

        $breadcrumbs = array();
        foreach ($items as $item) {
            $item->hideicon = true;
            $breadcrumbs[] = $this->render($item);
        }
        $divider = '<span class="divider">'.get_separator().'</span>';
        $list_items = '<li>'.join(" $divider</li><li>", $breadcrumbs).'</li>';
        $title = '<span class="accesshide" id="navbar-label">'.get_string('pagepath').'</span>';
        return $title . '<nav aria-labelledby="navbar-label"><ul class="breadcrumb">' .
                $list_items . '</ul></nav>';
    }
	
	
	
	    /**
     * This code renders the navbar button to control the display of the custom menu
     * on smaller screens.
     *
     * Do not display the button if the menu is empty.
     *
     * @return string HTML fragment
     */
    protected function navbar_button() {
        global $CFG;

        if (empty($CFG->custommenuitems) && $this->lang_menu() == '') {
            return '';
        }

        $iconbar = html_writer::tag('span', '', array('class' => 'icon-bar'));
        $button = html_writer::tag('a', $iconbar . "\n" . $iconbar. "\n" . $iconbar, array(
            'class'       => 'btn btn-navbar',
            'data-toggle' => 'collapse',
            'data-target' => '.nav-collapse'
        ));
        return $button;
    }
	
	    /**
     * Renders tabtree
     *
     * @param tabtree $tabtree
     * @return string
     */
    protected function render_tabtree(tabtree $tabtree) {
        if (empty($tabtree->subtree)) {
            return '';
        }
        $firstrow = $secondrow = '';
        foreach ($tabtree->subtree as $tab) {
            $firstrow .= $this->render($tab);
            if (($tab->selected || $tab->activated) && !empty($tab->subtree) && $tab->subtree !== array()) {
                $secondrow = $this->tabtree($tab->subtree);
            }
        }
        return html_writer::tag('ul', $firstrow, array('class' => 'nav nav-tabs')) . $secondrow;
    }
	
	    /**
     * Renders tabobject (part of tabtree)
     *
     * This function is called from {@link core_renderer::render_tabtree()}
     * and also it calls itself when printing the $tabobject subtree recursively.
     *
     * @param tabobject $tabobject
     * @return string HTML fragment
     */
    protected function render_tabobject(tabobject $tab) {
        if (($tab->selected and (!$tab->linkedwhenselected)) or $tab->activated) {
            return html_writer::tag('li', html_writer::tag('a', $tab->text), array('class' => 'active'));
        } else if ($tab->inactive) {
            return html_writer::tag('li', html_writer::tag('a', $tab->text), array('class' => 'disabled'));
        } else {
            if (!($tab->link instanceof moodle_url)) {
                // backward compartibility when link was passed as quoted string
                $link = "<a href=\"$tab->link\" title=\"$tab->title\">$tab->text</a>";
            } else {
                $link = html_writer::link($tab->link, $tab->text, array('title' => $tab->title));
            }
            $params = $tab->selected ? array('class' => 'active') : null;
            return html_writer::tag('li', $link, $params);
        }
    }
	
    public function custom_menu($custommenuitems = '') {
        global $CFG, $PAGE;

        // Don't apply auto-linking filters.
        $filtermanager = filter_manager::instance();
        $filteroptions = array('originalformat' => FORMAT_HTML, 'noclean' => true);
        $skipfilters = array('activitynames', 'data', 'glossary', 'sectionnames', 'bookchapters');

        // Filter custom user menu.
        // Don't filter custom user menu on the theme settings page. Otherwise it ends up
        // filtering the edit field itself resulting in a loss of tags.
        if ($PAGE->pagetype != 'admin-setting-themesettings' && stripos($CFG->customusermenuitems, '{') !== false) {
            $CFG->customusermenuitems = $filtermanager->filter_text($CFG->customusermenuitems, $PAGE->context,
                    $filteroptions, $skipfilters);
        }

        // Filter custom menu.
        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        if (stripos($custommenuitems, '{') !== false) {
            $custommenuitems = $filtermanager->filter_text($custommenuitems, $PAGE->context, $filteroptions, $skipfilters);
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }	 
	
	protected function render_custom_menu(custom_menu $menu) {
        global $CFG;
		
		$hasdisplaymycourses = theme_lambda_get_setting('mycourses_dropdown');
		
        if (isloggedin() && !isguestuser()  && $hasdisplaymycourses) { 
 
            $branchlabel = get_string('mycourses') ;
            $branchurl   = new moodle_url('');
            $branchtitle = $branchlabel;
            $branchsort  = 10000; 
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);

            if (!$sortorder = $CFG->navsortmycoursessort) {
                $sortorder = 'sortorder';
            }
            $courses_limit = $CFG->navcourselimit;
 			if ($mycourses = enrol_get_my_courses(NULL, 'visible DESC, '.$sortorder.' ASC', $courses_limit)) {
				foreach ($mycourses as $mycourse) {
					if ($CFG->navshowfullcoursenames) {
						$current_menu_item = $mycourse->fullname;
					} else {
						$current_menu_item = $mycourse->shortname;
					}
					$current_menu_item = format_string($current_menu_item, true, ['context' => context_course::instance(SITEID), "escape" => false]);
					$current_menu_item_title = format_string($mycourse->fullname, true, ['context' => context_course::instance(SITEID), "escape" => false]);
                	$branch->add($current_menu_item, new moodle_url('/course/view.php', array('id' => $mycourse->id)), $current_menu_item_title);
            	}
			}
			else {
            	$branch->add(get_string('myhome'), new moodle_url('/my/index.php'), get_string('myhome'));
			}
        }

        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if (!$menu->has_children() && !$haslangmenu) {
            return '';
        }

        if ($haslangmenu) {
            $strlang =  get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $menu->add($currentlang, new moodle_url(''), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        $content = '<ul class="nav">';
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }

        return $content.'</ul>';
    }
	
	protected function render_custom_menu_item(custom_menu_item $menunode, $level = 0 ) {
        static $submenucount = 0;

        $content = '';
        if ($menunode->has_children()) {

            if ($level == 1) {
                $class = 'dropdown';
            } else {
                $class = 'dropdown-submenu';
            }

            if ($menunode === $this->language) {
                $class .= ' langmenu';
            }
            $content = html_writer::start_tag('li', array('class' => $class));
            // If the child has menus render it as a sub menu.
            $submenucount++;
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#cm_submenu_'.$submenucount;
            }
            $content .= html_writer::start_tag('a', array('href'=>$url, 'class'=>'dropdown-toggle', 'data-toggle'=>'dropdown', 'title'=>$menunode->get_title()));
            $content .= $menunode->get_text();
            if ($level == 1) {
                $content .= '<div class="caret"></div>';
            }
            $content .= '</a>';
            $content .= '<ul class="dropdown-menu">';
            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item($menunode, 0);
            }
            $content .= '</ul>';
        } else {
            // The node doesn't have children so produce a final menuitem.
            // Also, if the node's text matches '####', add a class so we can treat it as a divider.
            if (preg_match("/^#+$/", $menunode->get_text())) {
                // This is a divider.
                $content = '<li class="divider">&nbsp;</li>';
            } else {
                $content = '<li>';
                if ($menunode->get_url() !== null) {
                    $url = $menunode->get_url();
                } else {
                    $url = '#';
                }
                $content .= html_writer::link($url, $menunode->get_text(), array('title' => $menunode->get_title()));
                $content .= '</li>';
            }
        }
        return $content;
    }



	

    
    public function footer() {
        global $CFG, $DB, $USER;
        $output = $this->container_end_all(true);
        $footer = $this->opencontainers->pop('header/footer');

        if (debugging() and $DB and $DB->is_transaction_started()) {
        }
        $footer = str_replace($this->unique_end_html_token, $this->page->requires->get_end_code(), $footer);
        $this->page->set_state(moodle_page::STATE_DONE);
        if(!empty($this->page->theme->settings->persistentedit) && property_exists($USER, 'editing') && $USER->editing && !$this->really_editing) {
            $USER->editing = false;
        }

        return $output . $footer;
    }
   
    public function footerblocks($region, $classes = array(), $tag = 'aside') {
        $classes = (array)$classes;
        $classes[] = 'block-region';
        $attributes = array(
            'id' => 'block-region-'.preg_replace('#[^a-zA-Z0-9_\-]+#', '-', $region),
            'class' => join(' ', $classes),
            'data-blockregion' => $region,
            'data-droptarget' => '1'
        );
        return html_writer::tag($tag, $this->blocks_for_region($region), $attributes);
    }
	
	public function lambda_footer_scripts() {
		$tag = 'script';
		$upOne = dirname(__DIR__, 1);
		return html_writer::tag($tag, file_get_contents ($upOne . '/javascript/scripts.js'));
    }
	
	public function lambda_fp_slideshow() {
		static $theme;
		$theme = theme_config::load('lambda');
	
		$tag = "script";
		$html = "";
		
		$loader='';
		if(theme_lambda_get_setting('slideshow_loader')==0) {$loader='bar';}
		else if(theme_lambda_get_setting('slideshow_loader')==1) {$loader='pie';}
		else if(theme_lambda_get_setting('slideshow_loader')==3) {$loader='none';}
		
		$imgfx='random';
		if (theme_lambda_get_setting('slideshow_imgfx')!='') {$imgfx=theme_lambda_get_setting('slideshow_imgfx');}
		
		$slideshow_height='auto';
		if(theme_lambda_get_setting('slideshow_height')=='responsive') {			
			if (!empty(theme_lambda_get_setting('slide1image'))) {
				$context = context_system::instance();
				$filename = $theme->settings->slide1image;
				$fs = get_file_storage();
				$file = $fs->get_file($context->id, 'theme_lambda', 'slide1image', 0, '/', $filename);
				$imageinfo = $file->get_imageinfo();
				$height = $imageinfo['height'];
				$width = $imageinfo['width'];
				$relative = $height/$width*100;
				$relative .= '%';
				$slideshow_height=$relative;
			}
		}
		
		$advance='true';
		if(theme_lambda_get_setting('slideshow_advance')==0) {$advance='false';}
		
		$navhover='true';
		if(theme_lambda_get_setting('slideshow_nav')==0) {$navhover='false';}

		
		$html .= "
		(function($) {
 		$(document).ready(function(){
		$('#camera_wrap').camera({
			fx: '".$imgfx."',
			height: '".$slideshow_height."',
			loader: '".$loader."',
			thumbnails: false,
			pagination: false,
			autoAdvance: ".$advance.",
			hover: false,
			navigationHover: ".$navhover.",
			mobileNavHover: ".$navhover.",
			opacityOnGrid: false
		});
	 	});
		}) (jQuery);
		";

		return html_writer::tag($tag, $html);
    }
	
	public function lambda_fp_carousel() {
		$tag = "script";
		$html = "";
		
		$carousel_img_dim = theme_lambda_get_setting('carousel_img_dim');
		$carousel_img_dim = substr($carousel_img_dim, 0, -2);
		
		$html .= "
		var width = $(window).innerWidth();
		(function($) {
 		$(document).ready(function(){
		$('.slider1').bxSlider({
			pager: false,
			nextSelector: '#slider-next',
			prevSelector: '#slider-prev',
			nextText: '>',
			prevText: '<',
			slideWidth: ".$carousel_img_dim.",
    		minSlides: 1,
    		maxSlides: (width < 430) ? 1 : 6,
			moveSlides: 0,
			shrinkItems: true,
			useCSS: true,
			wrapperClass: 'bx-wrapper',
    		slideMargin: 10	
		});
	 	});
		}) (jQuery);
		";

		return html_writer::tag($tag, $html);
    }
    
	public function context_header_settings_menu() {
        $context = $this->page->context;
        $menu = new action_menu();

        $items = $this->page->navbar->get_items();
        $currentnode = end($items);

        $showcoursemenu = false;
        $showusermenu = false;

        // We are on the course home page.
        if (($context->contextlevel == CONTEXT_COURSE) &&
                !empty($currentnode) &&
                ($currentnode->type == navigation_node::TYPE_COURSE || $currentnode->type == navigation_node::TYPE_SECTION)) {
            $showcoursemenu = true;
        }

        $courseformat = course_get_format($this->page->course);
        // This is a single activity course format, always show the course menu on the activity main page.
        if ($context->contextlevel == CONTEXT_MODULE &&
                !$courseformat->has_view_page()) {

            $this->page->navigation->initialise();
            $activenode = $this->page->navigation->find_active_node();
            // If the settings menu has been forced then show the menu.
            if ($this->page->is_settings_menu_forced()) {
                $showcoursemenu = true;
            } else if (!empty($activenode) && ($activenode->type == navigation_node::TYPE_ACTIVITY ||
                            $activenode->type == navigation_node::TYPE_RESOURCE)) {

                // We only want to show the menu on the first page of the activity. This means
                // the breadcrumb has no additional nodes.
                if ($currentnode && ($currentnode->key == $activenode->key && $currentnode->type == $activenode->type)) {
                    $showcoursemenu = true;
                }
            }
        }

        // This is the user profile page.
        if ($context->contextlevel == CONTEXT_USER &&
                !empty($currentnode) &&
                ($currentnode->key === 'myprofile')) {
            $showusermenu = true;
        }

        if ($showcoursemenu) {
            $settingsnode = $this->page->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                // We only add a list to the full settings menu if we didn't include every node in the short menu.
                if ($skipped) {
                    $text = get_string('morenavigationlinks');
                    $url = new moodle_url('/course/admin.php', array('courseid' => $this->page->course->id));
                    $link = new action_link($url, $text, null, null, new pix_icon('t/edit', $text));
                    $menu->add_secondary_action($link);
                }
            }
        } else if ($showusermenu) {
            // Get the course admin node from the settings navigation.
            $settingsnode = $this->page->settingsnav->find('useraccount', navigation_node::TYPE_CONTAINER);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $this->build_action_menu_from_navigation($menu, $settingsnode);
            }
        } else {
			$items = $this->page->navbar->get_items();
            $navbarnode = end($items);
		}
        return $this->render($menu);
    }
	
	public function lambda_h5p_header() {
		$header = new stdClass();
		$header->contextheader = $this->context_header();
		$header->headeractions = $this->page->get_header_actions();
		return $this->render_from_template('theme_lambda/lambda_h5p_header',$header);
	}
	
	protected function build_action_menu_from_navigation(action_menu $menu,
                                                       navigation_node $node,
                                                       $indent = false,
                                                       $onlytopleafnodes = false) {
        $skipped = false;
        // Build an action menu based on the visible nodes from this navigation tree.
        foreach ($node->children as $menuitem) {
            if ($menuitem->display) {
                if ($onlytopleafnodes && $menuitem->children->count()) {
                    $skipped = true;
                    continue;
                }
                if ($menuitem->action) {
                    if ($menuitem->action instanceof action_link) {
                        $link = $menuitem->action;
                        // Give preference to setting icon over action icon.
                        if (!empty($menuitem->icon)) {
                            $link->icon = $menuitem->icon;
                        }
                    } else {
                        $link = new action_link($menuitem->action, $menuitem->text, null, null, $menuitem->icon);
                    }
                } else {
                    if ($onlytopleafnodes) {
                        $skipped = true;
                        continue;
                    }
                    $link = new action_link(new moodle_url('#'), $menuitem->text, null, ['disabled' => true], $menuitem->icon);
                }
                if ($indent) {
                    $link->add_class('m-l-1');
                }
                if (!empty($menuitem->classes)) {
                    $link->add_class(implode(" ", $menuitem->classes));
                }
                $menu->add_secondary_action($link);
                $skipped = $skipped || $this->build_action_menu_from_navigation($menu, $menuitem, true);
            }
        }
        return $skipped;
    }
	
}