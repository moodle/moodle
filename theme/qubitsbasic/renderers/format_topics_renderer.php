<?php

class theme_qubitsbasic_format_topics_renderer extends \format_topics\output\renderer{ 

    
    protected function render_content(\renderable $widget) {
        $data = $widget->export_for_template($this); // You get an object with properties.
        $data->issiteadmin = is_siteadmin();

        // There you can access, add, update the $data object properties
        return $this->render_from_template($widget->get_template_name($this), $data);
    }
    

}
