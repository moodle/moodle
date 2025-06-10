<?php
/*
 * 
 *************************************************************************
 **                         Moodle Terms of uses                        **
 *************************************************************************
 * @author     David Lowe 
 * @co-author  Disha Devaiya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 *************************************************************************
 *
 * This file contains the phonetics to be included in the plugin.
 * Settings that allow configuration of the list of phonetics in the Phonetic Editor Settings.
 */

defined('MOODLE_INTERNAL') || die();

$phonetics = array();
//hex,dec,true,comment

//Pulmonic Consonants
$phonetics['consanants_p'] = array(   
    array('&#x0070;', '&#112;', true, 'LATIN SMALL LETTER P'),
    array('&#x0070;&#x02B0;', '&#112;&#688;', true, 'LATIN SMALL LETTER P WITH MODIFIER LETTER SMALL H'),
    array('&#x0062;', '&#98;', true, 'LATIN SMALL LETTER B'),
    array('&#x0074;', '&#116;', true, 'LATIN SMALL LETTER T'),
    array('&#x0074;&#x02B0;', '&#116;&#688;', true, 'LATIN SMALL LETTER T WITH MODIFIER LETTER SMALL H'),
    array('&#x0064;', '&#100;', true, 'LATIN SMALL LETTER D'),
    array('&#x0288;', '&#648;', true, 'LATIN SMALL LETTER T WITH RETROFLEX HOOK'),
    array('&#x0256;', '&#598;', true, 'LATIN SMALL LETTER D WITH TAIL'),
    array('&#x0063;', '&#99;', true, 'LATIN SMALL LETTER C'),
    array('&#x025F;', '&#607;', true, 'LATIN SMALL LETTER DOTLESS J WITH STROKE'),
    array('&#x006B;', '&#107;', true, 'LATIN SMALL LETTER K'),
    array('&#x006B;&#x02B0;', '&#107;&#688;', true, 'LATIN SMALL LETTER K WITH MODIFIER LETTER SMALL H'),
    array('&#x0067;', '&#103;', true, 'LATIN SMALL LETTER G'),
    array('&#x0071;', '&#113;', true, 'LATIN SMALL LETTER Q'),
    array('&#x0262;', '&#610;', true, 'LATIN LETTER SMALL CAPITAL G'),
    array('&#x0294;', '&#660;', true, 'GLOTTAL STOP'),
    array('&#x006D;', '&#109;', true, 'LATIN SMALL LETTER M'),
    array('&#x0271;', '&#625;', true, 'LATIN SMALL LETTER M WITH HOOK'),
    array('&#x006E;', '&#110;', true, 'LATIN SMALL LETTER N'),
    array('&#x0273;', '&#627;', true, 'LATIN SMALL LETTER N WITH RETROFLEX HOOK'),
    array('&#x0272;', '&#626;', true, 'LATIN SMALL LETTER N WITH LEFT HOOK'),
    array('&#x014B;', '&#331;', true, 'LATIN SMALL LETTER ENG'),
    array('&#x0274;', '&#628;', true, 'LATIN LETTER SMALL CAPITAL N'),
    array('&#x0299;', '&#665;', true, 'LATIN LETTER SMALL CAPITAL B'),
    array('&#x0072;', '&#114;', true, 'LATIN SMALL LETTER R'),
    array('&#x0280;', '&#640;', true, 'LATIN LETTER SMALL CAPITAL R'),
    array('&#x027E;', '&#638;', true, 'LATIN SMALL LETTER R WITH FISHHOOK'),
    array('&#x027D;', '&#637;', true, 'LATIN SMALL LETTER R WITH TAIL'),
    array('&#x0278;', '&#632;', true, 'LATIN SMALL LETTER PHI'),
    array('&#x03B2;', '&#946;', true, 'GREEK SMALL LETTER BETA'),
    array('&#x0066;', '&#102;', true, 'LATIN SMALL LETTER F'),
    array('&#x0076;', '&#118;', true, 'LATIN SMALL LETTER V'),
    array('&#x03B8;', '&#952;', true, 'GREEK SMALL LETTER THETA'),
    array('&#x00f0;', '&#240;', true, 'LATIN SMALL LETTER ETH'),
    array('&#x0073;', '&#115;', true, 'LATIN SMALL LETTER S'),
    array('&#x007A;', '&#122;', true, 'LATIN SMALL LETTER Z'),
    array('&#x0283;', '&#643;', true, 'LATIN SMALL LETTER ESH'),
    array('&#x02A7;', '&#679;', true, 'LATIN SMALL LETTER TESH DIGRAPH'),
    array('&#x0292;', '&#658;', true, 'LATIN SMALL LETTER EZH'),
    array('&#x02A4;', '&#676;', true, 'LATIN SMALL LETTER DEZH DIGRAPH'),
    array('&#x0282;', '&#642;', true, 'LATIN SMALL LETTER S WITH HOOK'),
    array('&#x0290;', '&#656;', true, 'LATIN SMALL LETTER Z WITH RETROFLEX HOOK'),
    array('&#x00E7;', '&#231;', true, 'LATIN SMALL LETTER C WITH CEDILLA'),
    array('&#x029D;', '&#669;', true, 'VOICED PALATAL FRICATIVE'),
    array('&#x78;', '&#120;', true, 'LATIN SMALL LETTER X'),
    array('&#x0263;', '&#611;', true, 'LATIN SMALL LETTER GAMMA'),
    array('&#x03C7;', '&#967;', true, 'GREEK SMALL LETTER CH'),
    array('&#x0281;', '&#641;', true, 'LATIN LETTER SMALL CAPITAL INVERTED R'),
    array('&#x0127;', '&#295;', true, 'LATIN SMALL LETTER H WITH STROKE'),
    array('&#x0295;', '&#661;', true, 'LATIN LETTER PHARYNGEAL VOICED FRICATIVE'),
    array('&#x68;', '&#104;', true, 'LATIN SMALL LETTER H'),
    array('&#x0266;', '&#614;', true, 'LATIN SMALL LETTER H WITH HOOK'),
    array('&#x026C;', '&#620;', true, 'LATIN SMALL LETTER L WITH BELT'),
    array('&#x026E;', '&#622;', true, 'LATIN SMALL LETTER LEZH'),
    array('&#x028B;', '&#651;', true, 'LATIN SMALL LETTER V WITH HOOK'),
    array('&#x0279;', '&#633;', true, 'LATIN SMALL LETTER TURNED R'),
    array('&#x027B;', '&#635;', true, 'LATIN SMALL LETTER TURNED R WITH HOOK'),
    array('&#x6a;', '&#106;', true, 'LATIN SMALL LETTER J'),
    array('&#x0270;', '&#624;', true, 'LATIN SMALL LETTER TURNED M WITH LONG LEG'),
    array('&#x6c;', '&#108;', true, 'LATIN SMALL LETTER L'),
    array('&#x026D;', '&#621;', true, 'LATIN SMALL LETTER L WITH RETROFLEX HOOK'),
    array('&#x028E;', '&#654;', true, 'LATIN SMALL LETTER TURNED Y'),
    array('&#x029F;', '&#671;', true, 'LATIN LETTER SMALL CAPITAL L'),
    array('&#x026B;', '&#619;', true, 'LATIN SMALL LETTER L WITH MIDDLE TILDE'),
    array('&#x77;', '&#119;', true, 'LATIN SMALL LETTER W'));

//Vowels
$phonetics['vowels'] = array(
    array('&#x0069;', '&#105;', true, 'LATIN SMALL LETTER I'),
    array('&#x0079;', '&#121;', true, 'LATIN SMALL LETTER Y'),
    array('&#x268;', '&#616;', true, 'LATIN SMALL LETTER BARRED I'),
    array('&#x0289;', '&#649;', true, 'LATIN SMALL LETTER U BAR'),
    array('&#x026F;', '&#623;', true, 'LATIN SMALL LETTER TURNED M'),
    array('&#x0075;', '&#117;', true, 'LATIN SMALL LETTER U'),
    array('&#x026A;', '&#618;', true, 'LATIN LETTER SMALL CAPITAL I'),
    array('&#x028F;', '&#655;', true, 'LATIN LETTER SMALL CAPITAL Y'),
    array('&#x028A;', '&#650;', true, 'LATIN SMALL LETTER UPSILON'),
    array('&#x0065;', '&#101;', true, 'LATIN SMALL LETTER E'),
    array('&#x00F8;', '&#248;', true, 'LATIN SMALL LETTER O WITH STROKE'),
    array('&#x0258;', '&#600;', true, 'LATIN SMALL LETTER REVERSED E'),
    array('&#x0259;', '&#601;', true, 'LATIN SMALL LETTER SCHWA'),
    array('&#x025A;', '&#602;', true, 'LATIN SMALL LETTER SCHWA WITH HOOK'),
    array('&#x0275;', '&#629;', true, 'LATIN SMALL LETTER BARRED O'),
    array('&#x0264;', '&#612;', true, 'LATIN SMALL LETTER RAMS HORN'),
    array('&#x006F;', '&#111;', true, 'LATIN SMALL LETTER O'),
    array('&#x025B;', '&#603;', true, 'LATIN SMALL LETTER OPEN E'),
    array('&#x025B;&#x0303', '&#603;&#771;', true, 'LATIN SMALL LETTER OPEN E WITH TILDE'),
    array('&#x0153;', '&#339;', true, 'LATIN SMALL LIGATURE OE'),
    array('&#x0153;&#x0303', '&#339;&#771;', true, 'LATIN SMALL LIGATURE OE WITH TILDE'),
    array('&#x025C;', '&#604;', true, 'LATIN SMALL LETTER REVERSED OPEN E'),
    array('&#x025D;', '&#605;', true, 'LATIN SMALL LETTER REVERSED OPEN E WITH HOOK'),
    array('&#x025E;', '&#606;', true, 'LATIN SMALL LETTER CLOSED REVERSED OPEN E'),
    array('&#x1D27;', '&#7463;', true, 'GREEK LETTER SMALL CAPITAL LAMDA'),
    array('&#x0254;', '&#596;', true, 'LATIN SMALL LETTER OPEN O'),
    array('&#x0254;&#x0303', '&#596;&#771;', true, 'LATIN SMALL LETTER OPEN O WITH TILDE'),
    array('&#x00E6;', '&#230;', true, 'LATIN SMALL LETTER AE'),
    array('&#x0250;', '&#592;', true, 'LATIN SMALL LETTER TURNED A'),
    array('&#x0061;', '&#097;', true, 'LATIN SMALL LETTER A'),
    array('&#x0276;', '&#630;', true, 'LATIN LETTER SMALL CAPITAL OE'),
    array('&#x0251;', '&#593;', true, 'LATIN SMALL LETTER ALPHA'),
    array('&#x0251;&#x0303', '&#593;&#771;', true, 'LATIN SMALL LETTER ALPHA WITH TILDE'),
    array('&#x0252;', '&#594;', true, 'LATIN SMALL LETTER TURNED ALPHA')
);

//Non-Pulmonic Consonants
$phonetics['consanants_n'] = array(
    array('&#x0298;', '&#664;', true, 'LATIN LETTER BILABIAL CLICK'),
    array('&#x01C0;', '&#448;', true, 'DENTAL CLICK'),
    array('&#x1D4E;', '&#7502;', true, 'MODIFIER LETTER SMALL TURNED I'),
    array('&#x01C2;', '&#450;', true, 'PALATAL CLICK'),
    array('&#x01C1;', '&#449;', true, 'Alveolar lateral click'),
    array('&#x0253;', '&#595;', true, 'Voiced bilabial implosive'),
    array('&#x0257;', '&#599;', true, 'LATIN SMALL LETTER D WITH HOOK'),
    array('&#x0284;', '&#644;', true, 'LATIN SMALL LETTER DOTLESS J WITH STROKE AND HOOK'),
    array('&#x0260;', '&#608;', true, 'LATIN SMALL LETTER G WITH HOOK'),
    array('&#x029B;', '&#667;', true, 'LATIN LETTER SMALL CAPITAL G WITH HOOK')
);

//Other symbols
$phonetics['other'] = array(
    array('&#x028D;', '&#653;', true, 'LATIN SMALL LETTER TURNED W'),
    array('&#x0265;', '&#613;', true, 'LATIN SMALL LETTER TURNED H'),
    array('&#x029C;', '&#668;', true, 'LATIN LETTER SMALL CAPITAL H'),
    array('&#x02A2;', '&#674;', true, 'LATIN LETTER REVERSED GLOTTAL STOP WITH STROKE'),
    array('&#x02A1;', '&#673;', true, 'LATIN LETTER GLOTTAL STOP WITH STROKE'),
    array('&#x0255;', '&#597;', true, 'LATIN SMALL LETTER C WITH CURL'),
    array('&#x0291;', '&#657;', true, 'LATIN SMALL LETTER Z WITH CURL'),
    array('&#x027A;', '&#634;', true, 'LATIN SMALL LETTER TURNED R WITH LONG LEG'),
    array('&#x0267;', '&#615;', true, 'LATIN SMALL LETTER HENG WITH HOOK'),
    array('&#x02E5;', '&#741;', true, 'MODIFIER LETTER EXTRA-HIGH TONE BAR'),
    array('&#x02E6;', '&#742;', true, 'MODIFIER LETTER HIGH TONE BAR'),
    array('&#x02E7;', '&#743;', true, 'MODIFIER LETTER MID TONE BAR'),
    array('&#x02E8;', '&#744;', true, 'MODIFIER LETTER LOW TONE BAR'),
    array('&#x02E9;', '&#745;', true, 'MODIFIER LETTER EXTRA-LOW TONE BAR'),
    array('&#x02E5;&#x02E9;', '&#741;&#745;', true, 'MODIFIER LETTER RISING TONE BAR'),
    array('&#x02E9;&#x02E5;', '&#745;&#741;', true, 'MODIFIER LETTER FALLING TONE BAR'),
    array('&#x02E7;&#x02E5;', '&#743;&#741;', true, 'MODIFIER LETTER HIGH RISING TONE BAR'),
    array('&#x02E8;&#x02E7;', '&#744;&#743;', true, 'MODIFIER LETTER LOW RISING TONE BAR'),
    array('&#x02E7;&#x02E5;&#x02E7;', '&#743;&#741;&#743;', true, 'MODIFIER LETTER RISING-FALLING TONE BAR'),
    array('&#x2191;', '&#8593;', true, 'UPSTEP'),
    array('&#x2193;', '&#8595;', true, 'DOWNSTEP'),
    array('&#x2197;', '&#8599;', true, 'GLOBAL RISE'),
    array('&#x2198;', '&#8600;', true, 'GLOBAL FALL')
);

//Spacing Modifiers
$phonetics['spacing'] = array(
    array('&#x02B0;', '&#688;', true, 'MODIFIER LETTER SMALL H'),
    array('&#x02B7;', '&#695;', true, 'MODIFIER LETTER SMALL W'),
    array('&#x02B2;', '&#690;', true, 'MODIFIER LETTER SMALL J'),
    array('&#x1D5E;', '&#7518;', true, 'MODIFIER LETTER SMALL GREEK GAMMA'),
    array('&#x02E4;', '&#740;', true, 'MODIFIER LETTER SMALL REVERSED GLOTTAL STOP'),
    array('&#x02E1;', '&#737;', true, 'MODIFIER LETTER SMALL L'),
    array('&#x207f;', '&#8319;', true, 'MODIFIER LETTER SMALL N'),
    array('&#x02E0;', '&#736;', true, 'MODIFIER LETTER SMALL GAMMA'),
    array('&#x02C8;', '&#712;', true, 'MODIFIER LETTER VERTICAL LINE'),
    array('&#x02CC;', '&#716;', true, 'MODIFIER LETTER LOW VERTICAL LINE'),
    array('&#x02D0;', '&#720;', true, 'MODIFIER LETTER TRIANGULAR COLON'),
    array('&#x02D1;', '&#721;', true, 'MODIFIER LETTER HALF TRIANGULAR COLON')
);

//combining

$phonetics['combinational'] = array(
    array('&#x325;', '&#805;', true, 'COMBINING RING BELOW'),
    array('&#x32c;', '&#812;', true, 'COMBINING CARON BELOW'),
    array('&#x339', '&#825;', true, 'COMBINING RIGHT HALF RING BELOW'),
    array('&#x31c;', '&#796;', true, 'COMBINING LEFT HALF RING BELOW'),
    array('&#x31f;', '&#799;', true, 'COMBINING PLUS SIGN BELOW'),
    array('&#x331;', '&#817;', true, 'COMBINING MACRON BELOW'),
    array('&#x308;', '&#776;', true, 'COMBINING DIAERESIS'),
    array('&#x33d;', '&#829;', true, 'COMBINING X ABOVE'),
    array('&#x329;', '&#809;', true, 'COMBINING VERTICAL LINE BELOW'),
    array('&#x32f;', '&#815;', true, 'COMBINING INVERTED BREVE BELOW'),
    array('&#x324;', '&#804;', true, 'COMBINING DIAERESIS BELOW'),
    array('&#x330;', '&#816;', true, 'COMBINING TILDE BELOW'),
    array('&#x33c;', '&#828;', true, 'COMBINING SEAGULL BELOW'),
    array('&#x334;', '&#820;', true, 'COMBINING TILDE OVERLAY'),
    array('&#x31d;', '&#797;', true, 'COMBINING UP TACK BELOW'),
    array('&#x31e;', '&#798;', true, 'COMBINING DOWN TACK BELOW'),
    array('&#x319;', '&#793;', true, 'COMBINING RIGHT TACK BELOW'),
    array('&#x318;', '&#792;', true, 'COMBINING LEFT TACK BELOW'),
    array('&#x32a;', '&#810;', true, 'COMBINING BRIDGE BELOW'),
    array('&#x33a;', '&#826;', true, 'COMBINING INVERTED BRIDGE BELOW'),
    array('&#x33b;', '&#827;', true, 'COMBINING SQUARE BELOW'),
    array('&#x342;', '&#834;', true, 'COMBINING GREEK PERISPOMENI'),
    array('&#x31a;', '&#794;', true, 'COMBINING LEFT ANGLE ABOVE'),
    array('&#x02DE;', '&#734;', true, 'MODIFIER LETTER RHOTIC HOOK'),
    array('&#x035C;', '&#860;', true, 'COMBINING DOUBLE BREVE BELOW'),
    array('&#x0361;', '&#865;', true, 'COMBINING DOUBLE INVERTED BREVE'),
    array('&#x0306;', '&#774;', true, 'COMBINING BREVE'),
    array('&#x030B;', '&#779;', true, 'COMBINING DOUBLE ACUTE ACCENT'),
    array('&#x0301;', '&#769;', true, 'COMBINING ACUTE ACCENT'),
    array('&#x0304;', '&#772;', true, 'COMBINING MACRON'),
    array('&#x0300;', '&#768;', true, 'COMBINING GRAVE ACCENT'),
    array('&#x030F;', '&#783;', true, 'COMBINING DOUBLE GRAVE ACCENT'),
    array('&#x030C;', '&#780;', true, 'COMBINING CARON'),
    array('&#x0302;', '&#770;', true, 'COMBINING CIRCUMFLEX ACCENT'),
    array('&#x1DC4;', '&#7620;', true, 'COMBINING MACRON-ACUTE'),
    array('&#x1DC5;', '&#7621;', true, 'COMBINING GRAVE-MACRON'),
    array('&#x1DC8;', '&#7624;', true, 'COMBINING GRAVE-ACUTE-GRAVE')
);

$ADMIN->add('editoratto',
    new admin_category(
        'atto_phonetic',
        new lang_string('pluginname', 'atto_phonetic')
    )
);

$settings = new admin_settingpage(
    'atto_phonetic_settings',
    new lang_string('settings', 'atto_phonetic')
);

if ($ADMIN->fulltree) {

    $tab = 0;
    foreach ($phonetics as $tabs => $phoneticmap) {
        $tab++;
        $name = new lang_string('librarygroup'.$tab, 'atto_phonetic');
        $desc = new lang_string('librarygroup'.$tab.'_desc', 'atto_phonetic');
        $default = '';
        foreach ($phoneticmap as $i => $phonetic) {
            if ($phonetic[2] == true) {
                $default .= $phonetic[1]."\r\n";
            }
        }

        $setting = new admin_setting_configtextarea(
            'atto_phonetic/librarygroup'. $tab,
            $name,
            $desc,
            $default
        );

        $settings->add($setting);
    }
}
