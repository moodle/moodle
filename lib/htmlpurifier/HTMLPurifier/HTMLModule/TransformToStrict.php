<?php

require_once 'HTMLPurifier/ChildDef/StrictBlockquote.php';

require_once 'HTMLPurifier/TagTransform/Simple.php';
require_once 'HTMLPurifier/TagTransform/Center.php';
require_once 'HTMLPurifier/TagTransform/Font.php';

require_once 'HTMLPurifier/AttrTransform/Lang.php';
require_once 'HTMLPurifier/AttrTransform/BgColor.php';
require_once 'HTMLPurifier/AttrTransform/BoolToCSS.php';
require_once 'HTMLPurifier/AttrTransform/Border.php';
require_once 'HTMLPurifier/AttrTransform/Name.php';
require_once 'HTMLPurifier/AttrTransform/Length.php';
require_once 'HTMLPurifier/AttrTransform/ImgSpace.php';
require_once 'HTMLPurifier/AttrTransform/EnumToCSS.php';

/**
 * Proprietary module that transforms deprecated elements into Strict
 * HTML (see HTML 4.01 and XHTML 1.0) when possible.
 */

class HTMLPurifier_HTMLModule_TransformToStrict extends HTMLPurifier_HTMLModule
{
    
    var $name = 'TransformToStrict';
    
    // we're actually modifying these elements, not defining them
    var $elements = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p',
        'blockquote', 'table', 'td', 'th', 'tr', 'img', 'a', 'hr', 'br',
        'caption', 'ul', 'ol', 'li');
    
    var $info_tag_transform = array(
        // placeholders, see constructor for definitions
        'font'  => false,
        'menu'  => false,
        'dir'   => false,
        'center'=> false
    );
    
    var $attr_collections = array(
        'Lang' => array(
            'lang' => false // placeholder
        )
    );
    
    var $info_attr_transform_post = array(
        'lang' => false // placeholder
    );
    
    function HTMLPurifier_HTMLModule_TransformToStrict() {
        
        // behavior with transformations when there's another CSS property
        // working on it is interesting: the CSS will *always* override
        // the deprecated attribute, whereas an inline CSS declaration will
        // override the corresponding declaration in, say, an external
        // stylesheet. This behavior won't affect most people, but it
        // does represent an operational difference we CANNOT fix.
        
        // deprecated tag transforms
        $this->info_tag_transform['font']   = new HTMLPurifier_TagTransform_Font();
        $this->info_tag_transform['menu']   = new HTMLPurifier_TagTransform_Simple('ul');
        $this->info_tag_transform['dir']    = new HTMLPurifier_TagTransform_Simple('ul');
        $this->info_tag_transform['center'] = new HTMLPurifier_TagTransform_Center();
        
        foreach ($this->elements as $name) {
            $this->info[$name] = new HTMLPurifier_ElementDef();
            $this->info[$name]->standalone = false;
        }
        
        // deprecated attribute transforms
        
        // align battery
        $align_lookup = array();
        $align_values = array('left', 'right', 'center', 'justify');
        foreach ($align_values as $v) $align_lookup[$v] = "text-align:$v;";
        $this->info['h1']->attr_transform_pre['align'] =
        $this->info['h2']->attr_transform_pre['align'] =
        $this->info['h3']->attr_transform_pre['align'] =
        $this->info['h4']->attr_transform_pre['align'] =
        $this->info['h5']->attr_transform_pre['align'] =
        $this->info['h6']->attr_transform_pre['align'] =
        $this->info['p'] ->attr_transform_pre['align'] = 
                    new HTMLPurifier_AttrTransform_EnumToCSS('align', $align_lookup);
        
        // xml:lang <=> lang mirroring, implement in TransformToStrict,
        // this is overridden in TransformToXHTML11
        $this->info_attr_transform_post['lang'] = new HTMLPurifier_AttrTransform_Lang();
        $this->attr_collections['Lang']['lang'] = new HTMLPurifier_AttrDef_Lang();
        
        // this should not be applied to XHTML 1.0 Transitional, ONLY
        // XHTML 1.0 Strict. We may need three classes
        $this->info['blockquote']->content_model_type = 'strictblockquote';
        $this->info['blockquote']->child = false; // recalculate please!
        
        $this->info['table']->attr_transform_pre['bgcolor'] = 
        $this->info['tr']->attr_transform_pre['bgcolor'] = 
        $this->info['td']->attr_transform_pre['bgcolor'] = 
        $this->info['th']->attr_transform_pre['bgcolor'] = new HTMLPurifier_AttrTransform_BgColor();
        
        $this->info['img']->attr_transform_pre['border'] = new HTMLPurifier_AttrTransform_Border();
        
        $this->info['img']->attr_transform_pre['name'] = 
        $this->info['a']->attr_transform_pre['name'] = new HTMLPurifier_AttrTransform_Name();
        
        $this->info['td']->attr_transform_pre['width'] = 
        $this->info['th']->attr_transform_pre['width'] = 
        $this->info['hr']->attr_transform_pre['width'] = new HTMLPurifier_AttrTransform_Length('width');
        
        $this->info['td']->attr_transform_pre['nowrap'] = 
        $this->info['th']->attr_transform_pre['nowrap'] = new HTMLPurifier_AttrTransform_BoolToCSS('nowrap', 'white-space:nowrap;');
        
        $this->info['td']->attr_transform_pre['height'] = 
        $this->info['th']->attr_transform_pre['height'] = new HTMLPurifier_AttrTransform_Length('height');
        
        $this->info['img']->attr_transform_pre['hspace'] = new HTMLPurifier_AttrTransform_ImgSpace('hspace');
        $this->info['img']->attr_transform_pre['vspace'] = new HTMLPurifier_AttrTransform_ImgSpace('vspace');
        
        $this->info['hr']->attr_transform_pre['size'] = new HTMLPurifier_AttrTransform_Length('size', 'height');
        
        // this transformation is not precise but often good enough.
        // different browsers use different styles to designate noshade
        $this->info['hr']->attr_transform_pre['noshade'] = new HTMLPurifier_AttrTransform_BoolToCSS('noshade', 'color:#808080;background-color:#808080;border: 0;');
        
        $this->info['br']->attr_transform_pre['clear'] =
            new HTMLPurifier_AttrTransform_EnumToCSS('clear', array(
                'left' => 'clear:left;',
                'right' => 'clear:right;',
                'all' => 'clear:both;',
                'none' => 'clear:none;',
            ));
        
        // this is a slightly unreasonable attribute
        $this->info['caption']->attr_transform_pre['align'] =
            new HTMLPurifier_AttrTransform_EnumToCSS('align', array(
                // we're following IE's behavior, not Firefox's, due
                // to the fact that no one supports caption-side:right,
                // W3C included (with CSS 2.1)
                'left' => 'text-align:left;',
                'right' => 'text-align:right;',
                'top' => 'caption-side:top;',
                'bottom' => 'caption-side:bottom;' // not supported by IE
            ));
        
        $this->info['table']->attr_transform_pre['align'] = 
            new HTMLPurifier_AttrTransform_EnumToCSS('align', array(
                'left' => 'float:left;',
                'center' => 'margin-left:auto;margin-right:auto;',
                'right' => 'float:right;'
            ));
        
        $this->info['img']->attr_transform_pre['align'] = 
            new HTMLPurifier_AttrTransform_EnumToCSS('align', array(
                'left' => 'float:left;',
                'right' => 'float:right;',
                'top' => 'vertical-align:top;',
                'middle' => 'vertical-align:middle;',
                'bottom' => 'vertical-align:baseline;',
            ));
        
        $this->info['hr']->attr_transform_pre['align'] = 
            new HTMLPurifier_AttrTransform_EnumToCSS('align', array(
                'left' => 'margin-left:0;margin-right:auto;text-align:left;',
                'center' => 'margin-left:auto;margin-right:auto;text-align:center;',
                'right' => 'margin-left:auto;margin-right:0;text-align:right;'
            ));
        
        $ul_types = array(
            'disc'   => 'list-style-type:disc;',
            'square' => 'list-style-type:square;',
            'circle' => 'list-style-type:circle;'
        );
        $ol_types = array(
            '1'   => 'list-style-type:decimal;',
            'i'   => 'list-style-type:lower-roman;',
            'I'   => 'list-style-type:upper-roman;',
            'a'   => 'list-style-type:lower-alpha;',
            'A'   => 'list-style-type:upper-alpha;'
        );
        $li_types = $ul_types + $ol_types;
        
        $this->info['ul']->attr_transform_pre['type'] =
            new HTMLPurifier_AttrTransform_EnumToCSS('type', $ul_types);
        $this->info['ol']->attr_transform_pre['type'] =
            new HTMLPurifier_AttrTransform_EnumToCSS('type', $ol_types, true);
        $this->info['li']->attr_transform_pre['type'] =
            new HTMLPurifier_AttrTransform_EnumToCSS('type', $li_types, true);
        
        
    }
    
    var $defines_child_def = true;
    function getChildDef($def) {
        if ($def->content_model_type != 'strictblockquote') return false;
        return new HTMLPurifier_ChildDef_StrictBlockquote($def->content_model);
    }
    
}

?>