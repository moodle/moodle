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
    function block($bc, $region) {
        $bc = clone($bc); // Avoid messing up the object passed in.
        $bc->prepare($this, $this->page, $this->target);

        $skiptitle = strip_tags($bc->title);
        if (empty($skiptitle)) {
            $output = '';
            $skipdest = '';
        } else {
            $output = html_writer::tag('a', array('href' => '#sb-' . $bc->skipid, 'class' => 'skip-block'),
                    get_string('skipa', 'access', $skiptitle));
            $skipdest = html_writer::tag('span', array('id' => 'sb-' . $bc->skipid, 'class' => 'skip-block-to'), '');
        }

        $bc->attributes['id'] = $bc->id;
        $bc->attributes['class'] = $bc->get_classes_string();
        
        $output .= html_writer::start_tag('div', $bc->attributes);
        
        /** Rounded corners **/
        $output .= html_writer::start_tag('div', array('class'=>'corner-box'));
        $output .= html_writer::start_tag('div', array('class'=>'rounded-corner top-left')).html_writer::end_tag('div');
        $output .= html_writer::start_tag('div', array('class'=>'rounded-corner top-right')).html_writer::end_tag('div');

        $controlshtml = $this->block_controls($bc->controls);

        $title = '';
        if ($bc->title) {
            $title = html_writer::tag('h2', null, $bc->title);
        }

        if ($title || $controlshtml) {
            $output .= html_writer::tag('div', array('class' => 'header'),
                    html_writer::tag('div', array('class' => 'title'),
                    $title . $controlshtml));
        }

        $output .= html_writer::start_tag('div', array('class' => 'content'));
        $output .= $bc->content;

        if ($bc->footer) {
            $output .= html_writer::tag('div', array('class' => 'footer'), $bc->footer);
        }

        $output .= html_writer::end_tag('div');

                /** Four rounded corner ends **/
        $output .= html_writer::start_tag('div', array('class'=>'rounded-corner bottom-left')).html_writer::end_tag('div');
        $output .= html_writer::start_tag('div', array('class'=>'rounded-corner bottom-right')).html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');

        if ($bc->annotation) {
            $output .= html_writer::tag('div', array('class' => 'blockannotation'), $bc->annotation);
        }
        $output .= $skipdest;

        $this->init_block_hider_js($bc);

        return $output;
    }

}