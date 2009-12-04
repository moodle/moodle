<?php
$configs = array();

$numberofsections = array();

for ($i = 1; $i < 53; $i++){
	$numberofsections[$i] = $i;
}
$increments = array();

for ($i = 1; $i < 11; $i++){
	$increments[$i] = $i;
}

$selected = array(1 => array(22,2), 
                  2 => array(40,5));

for($i = 1; $i < 3; $i++){
    $configs[] = new admin_setting_configselect('numsections'.$i, get_string('numsections'.$i, 'block_section_links'), 
                        get_string('numsectionsdesc'.$i, 'block_section_links'),
                        $selected[$i][0], $numberofsections);

    $configs[] = new admin_setting_configselect('incby'.$i, get_string('incby'.$i, 'block_section_links'), 
                        get_string('incbydesc'.$i, 'block_section_links'),
                        $selected[$i][1], $increments);
}
                                          
foreach ($configs as $config) {
    $config->plugin = 'blocks/section_links';
    $settings->add($config);
}

?>
