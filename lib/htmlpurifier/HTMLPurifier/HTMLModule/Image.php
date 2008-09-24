<?php

require_once 'HTMLPurifier/HTMLModule.php';

require_once 'HTMLPurifier/AttrDef/URI.php';
require_once 'HTMLPurifier/AttrTransform/ImgRequired.php';

HTMLPurifier_ConfigSchema::define(
    'HTML', 'MaxImgLength', 1200, 'int/null', '
<p>
 This directive controls the maximum number of pixels in the width and
 height attributes in <code>img</code> tags. This is
 in place to prevent imagecrash attacks, disable with null at your own risk.
 This directive is similar to %CSS.MaxImgLength, and both should be
 concurrently edited, although there are
 subtle differences in the input format (the HTML max is an integer).
</p>
');

/**
 * XHTML 1.1 Image Module provides basic image embedding.
 * @note There is specialized code for removing empty images in
 *       HTMLPurifier_Strategy_RemoveForeignElements
 */
class HTMLPurifier_HTMLModule_Image extends HTMLPurifier_HTMLModule
{
    
    var $name = 'Image';
    
    function setup($config) {
        $max = $config->get('HTML', 'MaxImgLength');
        $img =& $this->addElement(
            'img', true, 'Inline', 'Empty', 'Common',
            array(
                'alt*' => 'Text',
                // According to the spec, it's Length, but percents can
                // be abused, so we allow only Pixels. A trusted module
                // could overload this with the real value.
                'height' => 'Pixels#' . $max,
                'width' => 'Pixels#' . $max,
                'longdesc' => 'URI', 
                'src*' => new HTMLPurifier_AttrDef_URI(true), // embedded
            )
        );
        if ($max === null || $config->get('HTML', 'Trusted')) {
            $img->attr['height'] =
            $img->attr['width'] = 'Length';
        }
        
        // kind of strange, but splitting things up would be inefficient
        $img->attr_transform_pre[] =
        $img->attr_transform_post[] =
            new HTMLPurifier_AttrTransform_ImgRequired();
    }
    
}

