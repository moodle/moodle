<?php

class theme_anomaly_core_renderer extends core_renderer {

    /**
     * Prints a nice side block with an optional header.
     *
     * The content is described
     * by a {@link block_contents} object.
     *
     * @param block_contents $bc HTML for the content
     * @param string $region the region the block is appearing in.
     * @return string the HTML to be output.
     */
    function block(block_contents $bc, $region) {

        $bc = clone($bc); // Avoid messing up the object passed in.
        if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        }
        if ($bc->collapsible == block_contents::HIDDEN) {
            $bc->add_class('hidden');
        }
        if (!empty($bc->controls)) {
            $bc->add_class('block_with_controls');
        }

        $skiptitle = strip_tags($bc->title);
        if (empty($skiptitle)) {
            $output = '';
            $skipdest = '';
        } else {
            $output = html_writer::tag('a', get_string('skipa', 'access', $skiptitle), array('href' => '#sb-' . $bc->skipid, 'class' => 'skip-block'));
            $skipdest = html_writer::tag('span', '', array('id' => 'sb-' . $bc->skipid, 'class' => 'skip-block-to'));
        }

        $output .= html_writer::start_tag('div', $bc->attributes);

        /** Rounded corners **/
        $output .= html_writer::start_tag('div', array('class'=>'corner-box'));
        $output .= html_writer::start_tag('div', array('class'=>'rounded-corner top-left')).html_writer::end_tag('div');
        $output .= html_writer::start_tag('div', array('class'=>'rounded-corner top-right')).html_writer::end_tag('div');

        $controlshtml = $this->block_controls($bc->controls);

        $title = '';
        if ($bc->title) {
            $title = html_writer::tag('h2', $bc->title);
        }

        if ($title || $controlshtml) {
            $output .= html_writer::tag('div', html_writer::tag('div', html_writer::tag('div', '', array('class'=>'block_action')). $title . $controlshtml, array('class' => 'title')), array('class' => 'header'));
        }

        $output .= html_writer::start_tag('div', array('class' => 'content'));
        if (!$title && !$controlshtml) {
            $output .= html_writer::tag('div', '', array('class'=>'block_action notitle'));
        }
        $output .= $bc->content;

        if ($bc->footer) {
            $output .= html_writer::tag('div', $bc->footer, array('class' => 'footer'));
        }

        $output .= html_writer::end_tag('div');

                /** Four rounded corner ends **/
        $output .= html_writer::start_tag('div', array('class'=>'rounded-corner bottom-left')).html_writer::end_tag('div');
        $output .= html_writer::start_tag('div', array('class'=>'rounded-corner bottom-right')).html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');

        if ($bc->annotation) {
            $output .= html_writer::tag('div', $bc->annotation, array('class' => 'blockannotation'));
        }
        $output .= $skipdest;

        $this->init_block_hider_js($bc);

        return $output;
    }

    /**
     * Renders a custom menu object (located in outputcomponents.php)
     *
     * The custom menu this method override the render_custom_menu function
     * in outputrenderers.php
     * @staticvar int $menucount
     * @param custom_menu $menu
     * @return string
     */
    protected function render_custom_menu(custom_menu $menu) {

        // If the menu has no children return an empty string
        if (!$menu->has_children()) {
            return '';
        }

        // Add a login or logout link
        if (isloggedin()) {
            $branchlabel = get_string('logout');
            $branchurl   = new moodle_url('/login/logout.php');
        } else {
            $branchlabel = get_string('login');
            $branchurl   = new moodle_url('/login/index.php');
        }
        $branch = $menu->add($branchlabel, $branchurl, $branchlabel, -1);

        // Initialise this custom menu
        $content = html_writer::start_tag('ul', array('class'=>'dropdown dropdown-horizontal'));
        // Render each child
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item);
        }
        // Close the open tags
        $content .= html_writer::end_tag('ul');
        // Return the custom menu
        return $content;
    }

    /**
     * Renders a custom menu node as part of a submenu
     *
     * The custom menu this method override the render_custom_menu_item function
     * in outputrenderers.php
     *
     * @see render_custom_menu()
     *
     * @staticvar int $submenucount
     * @param custom_menu_item $menunode
     * @return string
     */
    protected function render_custom_menu_item(custom_menu_item $menunode) {
        // Required to ensure we get unique trackable id's
        static $submenucount = 0;
        $content = html_writer::start_tag('li');
        if ($menunode->has_children()) {
            // If the child has menus render it as a sub menu
            $submenucount++;
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#cm_submenu_'.$submenucount;
            }
            $content .= html_writer::start_tag('span', array('class'=>'customitem'));
            $content .= html_writer::link($url, $menunode->get_text(), array('title'=>$menunode->get_title()));
            $content .= html_writer::end_tag('span');
            $content .= html_writer::start_tag('ul');
            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item($menunode);
            }
            $content .= html_writer::end_tag('ul');
        } else {
            // The node doesn't have children so produce a final menuitem

            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#';
            }
            $content .= html_writer::link($url, $menunode->get_text(), array('title'=>$menunode->get_title()));
        }
        $content .= html_writer::end_tag('li');
        // Return the sub menu
        return $content;
    }

}