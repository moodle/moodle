<?php

/**
 * XHTML 1.1 Image Module provides basic image embedding.
 * @note There is specialized code for removing empty images in
 *       HTMLPurifier_Strategy_RemoveForeignElements
 */
class HTMLPurifier_HTMLModule_Image extends HTMLPurifier_HTMLModule
{
    
    public $name = 'Image';
    
    public function __construct() {
        $img = $this->addElement(
            'img', 'Inline', 'Empty', 'Common',
            array(
                'alt*' => 'Text',
                'height' => 'Length',
                'longdesc' => 'URI', 
                'src*' => new HTMLPurifier_AttrDef_URI(true), // embedded
                'width' => 'Length'
            )
        );
        // kind of strange, but splitting things up would be inefficient
        $img->attr_transform_pre[] =
        $img->attr_transform_post[] =
            new HTMLPurifier_AttrTransform_ImgRequired();
    }
    
}

