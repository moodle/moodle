<?php  // $Id$
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-onwards Moodle Pty Ltd  http://moodle.com          //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once($CFG->dirroot.'/mod/data/field/file/field.class.php');
// Base class is 'file'

class data_field_picture extends data_field_file {
    var $type = 'picture';
    var $previewwidth  = 50;
    var $previewheight = 50;

    function data_field_picture($field=0, $data=0) {
        parent::data_field_base($field, $data);
    }

    function display_add_field($recordid=0) {
        global $CFG;
        $filepath = '';
        $filename = '';
        $description = '';
        if ($recordid) {
            if ($content = get_record('data_content', 'fieldid', $this->field->id, 'recordid', $recordid)) {
                $filename = $content->content;
                $description = $content->content1;
            }
            $path = $this->data->course.'/'.$CFG->moddata.'/data/'.$this->data->id.'/'.$this->field->id.'/'.$recordid;
            require_once($CFG->libdir.'/filelib.php');
            $filepath = get_file_url("$path/$filename");
        }
        $str = '<div title="'.s($this->field->description).'">';
        $str .= '<fieldset><legend><span class="accesshide">'.$this->field->name.'</span></legend>';
        $str .= '<input type="hidden" name ="field_'.$this->field->id.'_file" id="field_'.$this->field->id.'_file"  value="fakevalue" />';
        $str .= '<label for="field_'.$this->field->id.'">'.get_string('picture','data'). '</label>&nbsp;<input type="file" name ="field_'.$this->field->id.'" id="field_'.$this->field->id.'" /><br />';
        $str .= '<label for="field_'.$this->field->id.'_filename">'.get_string('alttext','data') .'</label>&nbsp;<input type="text" name="field_'
                .$this->field->id.'_filename" id="field_'.$this->field->id.'_filename" value="'.s($description).'" /><br />';
        $str .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.s($this->field->param3).'" />';
        if ($filepath) {
            $str .= '<img width="'.s($this->previewwidth).'" height="'.s($this->previewheight).'" src="'.$filepath.'" alt="" />';
        }
        $str .= '</fieldset>';
        $str .= '</div>';
        return $str;
    }

    function display_search_field($value = '') {
        return '<input type="text" size="16" name="f_'.$this->field->id.'" value="'.$value.'" />';
    }

    function parse_search_field() {
        return optional_param('f_'.$this->field->id, '', PARAM_NOTAGS);
    }

    function generate_sql($tablealias, $value) {
        return " ({$tablealias}.fieldid = {$this->field->id} AND {$tablealias}.content LIKE '%{$value}%') ";
    }

    function display_browse_field($recordid, $template) {
        global $CFG;
        if ($content = get_record('data_content', 'fieldid', $this->field->id, 'recordid', $recordid)){
            if (isset($content->content)) {
                $contents[0] = $content->content;
                $contents[1] = $content->content1;
            }
            if (empty($contents[0])) {
                // Nothing to show
                return '';
            }
            $alt = empty($contents[1])? '':$contents[1];
            $title = empty($contents[1])? '':$contents[1];
            $src = $contents[0];
            $path = $this->data->course.'/'.$CFG->moddata.'/data/'.$this->data->id.'/'.$this->field->id.'/'.$recordid;

            $thumbnaillocation = $CFG->dataroot .'/'. $path .'/thumb/'.$src;
            require_once($CFG->libdir.'/filelib.php');
            $source = get_file_url("$path/$src");
            $thumbnailsource = file_exists($thumbnaillocation) ? get_file_url("$path/thumb/$src") : $source;

            if ($template == 'listtemplate') {
                $width = $this->field->param4 ? ' width="'.s($this->field->param4).'" ' : ' ';
                $height = $this->field->param5 ? ' height="'.s($this->field->param5).'" ' : ' ';
                $str = '<a href="view.php?d='.$this->field->dataid.'&amp;rid='.$recordid.'"><img '.
                     $width.$height.' src="'.$thumbnailsource.'" alt="'.s($alt).'" title="'.s($title).'" style="border:0px" /></a>';
            } else {
                $width = $this->field->param1 ? ' width="'.s($this->field->param1).'" ':' ';
                $height = $this->field->param2 ? ' height="'.s($this->field->param2).'" ':' ';
                $str = '<a href="'.$source.'"><img '.$width.$height.' src="'.$source.'" alt="'.s($alt).'" title="'.s($title).'" style="border:0px" /></a>';
            }
            return $str;
        }
        return false;
    }

    function update_field() {
        // Get the old field data so that we can check whether the thumbnail dimensions have changed
        $oldfield = get_record('data_fields', 'id', $this->field->id);
        if (!update_record('data_fields', $this->field)) {
            notify('updating of new field failed!');
            return false;
        }

        // Have the thumbnail dimensions changed?
        if ($oldfield && ($oldfield->param4 != $this->field->param4 || $oldfield->param5 != $this->field->param5)) {
            // Check through all existing records and update the thumbnail
            if ($contents = get_records('data_content', 'fieldid', $this->field->id)) {
                if (count($contents) > 20) {
                    notify(get_string('resizingimages', 'data'), 'notifysuccess');
                    echo "\n\n";
                    // To make sure that ob_flush() has the desired effect
                    ob_flush();
                }
                foreach ($contents as $content) {
                    @set_time_limit(300);
                    // Might be slow!
                    $this->update_thumbnail($content);
                }
            }
        }
        return true;
    }

    function update_content($recordid, $value, $name) {
        parent::update_content($recordid, $value, $name);
        $content = get_record('data_content','fieldid', $this->field->id, 'recordid', $recordid);
        $this->update_thumbnail($content);
        // Regenerate the thumbnail
    }

    function update_thumbnail($content) {
        // (Re)generate thumbnail image according to the dimensions specified in the field settings.
        // If thumbnail width and height are BOTH not specified then no thumbnail is generated, and
        // additionally an attempted delete of the existing thumbnail takes place.
        global $CFG;
        require_once($CFG->libdir . '/gdlib.php');
        $datalocation = $CFG->dataroot .'/'.$this->data->course.'/'.$CFG->moddata.'/data/'.
                        $this->data->id.'/'.$this->field->id.'/'.$content->recordid;
        $originalfile = $datalocation.'/'.$content->content;
        if (!file_exists($originalfile)) {
            return;
        }
        if (!file_exists($datalocation.'/thumb')) {
            mkdir($datalocation.'/thumb', $CFG->directorypermissions);
            // robertall: Why hardcode 0777??
        }
        $thumbnaillocation = $datalocation.'/thumb/'.$content->content;
        $imageinfo = GetImageSize($originalfile);
        $image->width  = $imageinfo[0];
        $image->height = $imageinfo[1];
        $image->type   = $imageinfo[2];
        if (!$image->width || !$image->height) {
            // Should not happen
            return;
        }
        switch ($image->type) {
            case 1:
                if (function_exists('ImageCreateFromGIF')) {
                    $im = ImageCreateFromGIF($originalfile);
                } else {
                    return;
                }
                break;
            case 2:
                if (function_exists('ImageCreateFromJPEG')) {
                    $im = ImageCreateFromJPEG($originalfile);
                } else {
                    return;
                }
                break;
            case 3:
                if (function_exists('ImageCreateFromPNG')) {
                    $im = ImageCreateFromPNG($originalfile);
                } else {
                    return;
                }
                break;
        }
        $thumbwidth  = isset($this->field->param4)?$this->field->param4:'';
        $thumbheight = isset($this->field->param5)?$this->field->param5:'';
        if ($thumbwidth || $thumbheight) {
            // Only if either width OR height specified do we want a thumbnail
            $wcrop = $image->width;
            $hcrop = $image->height;
            if ($thumbwidth && !$thumbheight) {
                $thumbheight = $image->height * $thumbwidth / $image->width;
            } else if($thumbheight && !$thumbwidth) {
                $thumbwidth = $image->width * $thumbheight / $image->height;
            } else {
                // BOTH are set - may need to crop if aspect ratio differs
                $hratio = $image->height / $thumbheight;
                $wratio = $image->width  / $thumbwidth;
                if ($wratio > $hratio) {
                    // Crop the width
                    $wcrop = intval($thumbwidth * $hratio);
                } elseif($hratio > $wratio) {
                    //Crop the height
                    $hcrop = intval($thumbheight * $wratio);
                }
            }

            // At this point both $thumbwidth and $thumbheight are set, and $wcrop and $hcrop

            if (function_exists('ImageCreateTrueColor') and $CFG->gdversion >= 2) {
                $im1 = ImageCreateTrueColor($thumbwidth,$thumbheight);
            } else {
                $im1 = ImageCreate($thumbwidth,$thumbheight);
            }
            if ($image->type == 3) {
                // Prevent alpha blending for PNG images
                imagealphablending($im1, false);
            }
            $cx = $image->width  / 2;
            $cy = $image->height / 2;

            // These "half" measurements use the "crop" values rather than the actual dimensions
            $halfwidth  = floor($wcrop * 0.5);
            $halfheight = floor($hcrop * 0.5);

            ImageCopyBicubic($im1, $im, 0, 0, $cx-$halfwidth, $cy-$halfheight,
                             $thumbwidth, $thumbheight, $halfwidth*2, $halfheight*2);

            if ($image->type == 3) {
                // Save alpha transparency for PNG images
                imagesavealpha($im1, true);
            }
            if (function_exists('ImageJpeg') && $image->type != 3) {
                @touch($thumbnaillocation);
                // Helps in Safe mode
                if (ImageJpeg($im1, $thumbnaillocation, 90)) {
                    @chmod($thumbnaillocation, 0666);
                    // robertall: Why hardcode 0666??
                }
            } elseif (function_exists('ImagePng') && $image->type == 3) {
                @touch($thumbnaillocation);
                // Helps in Safe mode
                if (ImagePng($im1, $thumbnaillocation, 9)) {
                    @chmod($thumbnaillocation, 0666);
                    // robertall: Why hardcode 0666??
                }
            }
        } else {
            // Try to remove the thumbnail - we don't want thumbnailing active
            @unlink($thumbnaillocation);
        }
    }

    function text_export_supported() {
        return false;
    }

}

?>
