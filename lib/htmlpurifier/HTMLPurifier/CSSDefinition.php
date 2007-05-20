<?php

require_once 'HTMLPurifier/AttrDef/CSS/Background.php';
require_once 'HTMLPurifier/AttrDef/CSS/BackgroundPosition.php';
require_once 'HTMLPurifier/AttrDef/CSS/Border.php';
require_once 'HTMLPurifier/AttrDef/CSS/Color.php';
require_once 'HTMLPurifier/AttrDef/CSS/Composite.php';
require_once 'HTMLPurifier/AttrDef/CSS/Font.php';
require_once 'HTMLPurifier/AttrDef/CSS/FontFamily.php';
require_once 'HTMLPurifier/AttrDef/CSS/Length.php';
require_once 'HTMLPurifier/AttrDef/CSS/ListStyle.php';
require_once 'HTMLPurifier/AttrDef/CSS/Multiple.php';
require_once 'HTMLPurifier/AttrDef/CSS/Percentage.php';
require_once 'HTMLPurifier/AttrDef/CSS/TextDecoration.php';
require_once 'HTMLPurifier/AttrDef/CSS/URI.php';
require_once 'HTMLPurifier/AttrDef/Enum.php';

/**
 * Defines allowed CSS attributes and what their values are.
 * @see HTMLPurifier_HTMLDefinition
 */
class HTMLPurifier_CSSDefinition
{
    
    /**
     * Assoc array of attribute name to definition object.
     */
    var $info = array();
    
    /**
     * Constructs the info array.  The meat of this class.
     */
    function setup($config) {
        
        $this->info['text-align'] = new HTMLPurifier_AttrDef_Enum(
            array('left', 'right', 'center', 'justify'), false);
        
        $border_style =
        $this->info['border-bottom-style'] = 
        $this->info['border-right-style'] = 
        $this->info['border-left-style'] = 
        $this->info['border-top-style'] =  new HTMLPurifier_AttrDef_Enum(
            array('none', 'hidden', 'dotted', 'dashed', 'solid', 'double',
            'groove', 'ridge', 'inset', 'outset'), false);
        
        $this->info['border-style'] = new HTMLPurifier_AttrDef_CSS_Multiple($border_style);
        
        $this->info['clear'] = new HTMLPurifier_AttrDef_Enum(
            array('none', 'left', 'right', 'both'), false);
        $this->info['float'] = new HTMLPurifier_AttrDef_Enum(
            array('none', 'left', 'right'), false);
        $this->info['font-style'] = new HTMLPurifier_AttrDef_Enum(
            array('normal', 'italic', 'oblique'), false);
        $this->info['font-variant'] = new HTMLPurifier_AttrDef_Enum(
            array('normal', 'small-caps'), false);
        
        $uri_or_none = new HTMLPurifier_AttrDef_CSS_Composite(
            array(
                new HTMLPurifier_AttrDef_Enum(array('none')),
                new HTMLPurifier_AttrDef_CSS_URI()
            )
        );
        
        $this->info['list-style-position'] = new HTMLPurifier_AttrDef_Enum(
            array('inside', 'outside'), false);
        $this->info['list-style-type'] = new HTMLPurifier_AttrDef_Enum(
            array('disc', 'circle', 'square', 'decimal', 'lower-roman',
            'upper-roman', 'lower-alpha', 'upper-alpha', 'none'), false);
        $this->info['list-style-image'] = $uri_or_none;
        
        $this->info['list-style'] = new HTMLPurifier_AttrDef_CSS_ListStyle($config);
        
        $this->info['text-transform'] = new HTMLPurifier_AttrDef_Enum(
            array('capitalize', 'uppercase', 'lowercase', 'none'), false);
        $this->info['color'] = new HTMLPurifier_AttrDef_CSS_Color();
        
        $this->info['background-image'] = $uri_or_none;
        $this->info['background-repeat'] = new HTMLPurifier_AttrDef_Enum(
            array('repeat', 'repeat-x', 'repeat-y', 'no-repeat')
        );
        $this->info['background-attachment'] = new HTMLPurifier_AttrDef_Enum(
            array('scroll', 'fixed')
        );
        $this->info['background-position'] = new HTMLPurifier_AttrDef_CSS_BackgroundPosition();
        
        $border_color = 
        $this->info['border-top-color'] = 
        $this->info['border-bottom-color'] = 
        $this->info['border-left-color'] = 
        $this->info['border-right-color'] = 
        $this->info['background-color'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
            new HTMLPurifier_AttrDef_Enum(array('transparent')),
            new HTMLPurifier_AttrDef_CSS_Color()
        ));
        
        $this->info['background'] = new HTMLPurifier_AttrDef_CSS_Background($config);
        
        $this->info['border-color'] = new HTMLPurifier_AttrDef_CSS_Multiple($border_color);
        
        $border_width = 
        $this->info['border-top-width'] = 
        $this->info['border-bottom-width'] = 
        $this->info['border-left-width'] = 
        $this->info['border-right-width'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
            new HTMLPurifier_AttrDef_Enum(array('thin', 'medium', 'thick')),
            new HTMLPurifier_AttrDef_CSS_Length(true) //disallow negative
        ));
        
        $this->info['border-width'] = new HTMLPurifier_AttrDef_CSS_Multiple($border_width);
        
        $this->info['letter-spacing'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
            new HTMLPurifier_AttrDef_Enum(array('normal')),
            new HTMLPurifier_AttrDef_CSS_Length()
        ));
        
        $this->info['word-spacing'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
            new HTMLPurifier_AttrDef_Enum(array('normal')),
            new HTMLPurifier_AttrDef_CSS_Length()
        ));
        
        $this->info['font-size'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
            new HTMLPurifier_AttrDef_Enum(array('xx-small', 'x-small',
                'small', 'medium', 'large', 'x-large', 'xx-large',
                'larger', 'smaller')),
            new HTMLPurifier_AttrDef_CSS_Percentage(),
            new HTMLPurifier_AttrDef_CSS_Length()
        ));
        
        $this->info['line-height'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
            new HTMLPurifier_AttrDef_Enum(array('normal')),
            new HTMLPurifier_AttrDef_CSS_Number(true), // no negatives
            new HTMLPurifier_AttrDef_CSS_Length(true),
            new HTMLPurifier_AttrDef_CSS_Percentage(true)
        ));
        
        $margin =
        $this->info['margin-top'] = 
        $this->info['margin-bottom'] = 
        $this->info['margin-left'] = 
        $this->info['margin-right'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
            new HTMLPurifier_AttrDef_CSS_Length(),
            new HTMLPurifier_AttrDef_CSS_Percentage(),
            new HTMLPurifier_AttrDef_Enum(array('auto'))
        ));
        
        $this->info['margin'] = new HTMLPurifier_AttrDef_CSS_Multiple($margin);
        
        // non-negative
        $padding =
        $this->info['padding-top'] = 
        $this->info['padding-bottom'] = 
        $this->info['padding-left'] = 
        $this->info['padding-right'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
            new HTMLPurifier_AttrDef_CSS_Length(true),
            new HTMLPurifier_AttrDef_CSS_Percentage(true)
        ));
        
        $this->info['padding'] = new HTMLPurifier_AttrDef_CSS_Multiple($padding);
        
        $this->info['text-indent'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
            new HTMLPurifier_AttrDef_CSS_Length(),
            new HTMLPurifier_AttrDef_CSS_Percentage()
        ));
        
        $this->info['width'] =
        $this->info['height'] = 
        new HTMLPurifier_AttrDef_CSS_Composite(array(
            new HTMLPurifier_AttrDef_CSS_Length(true),
            new HTMLPurifier_AttrDef_CSS_Percentage(true),
            new HTMLPurifier_AttrDef_Enum(array('auto'))
        ));
        
        $this->info['text-decoration'] = new HTMLPurifier_AttrDef_CSS_TextDecoration();
        
        $this->info['font-family'] = new HTMLPurifier_AttrDef_CSS_FontFamily();
        
        // this could use specialized code
        $this->info['font-weight'] = new HTMLPurifier_AttrDef_Enum(
            array('normal', 'bold', 'bolder', 'lighter', '100', '200', '300',
            '400', '500', '600', '700', '800', '900'), false);
        
        // MUST be called after other font properties, as it references
        // a CSSDefinition object
        $this->info['font'] = new HTMLPurifier_AttrDef_CSS_Font($config);
        
        // same here
        $this->info['border'] =
        $this->info['border-bottom'] = 
        $this->info['border-top'] = 
        $this->info['border-left'] = 
        $this->info['border-right'] = new HTMLPurifier_AttrDef_CSS_Border($config);
        
        $this->info['border-collapse'] = new HTMLPurifier_AttrDef_Enum(array(
            'collapse', 'seperate'));
        
        $this->info['caption-side'] = new HTMLPurifier_AttrDef_Enum(array(
            'top', 'bottom'));
        
        $this->info['table-layout'] = new HTMLPurifier_AttrDef_Enum(array(
            'auto', 'fixed'));
        
        $this->info['vertical-align'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
            new HTMLPurifier_AttrDef_Enum(array('baseline', 'sub', 'super',
                'top', 'text-top', 'middle', 'bottom', 'text-bottom')),
            new HTMLPurifier_AttrDef_CSS_Length(),
            new HTMLPurifier_AttrDef_CSS_Percentage()
        ));
        
        // partial support
        $this->info['white-space'] = new HTMLPurifier_AttrDef_Enum(array('nowrap'));
        
    }
    
}

?>