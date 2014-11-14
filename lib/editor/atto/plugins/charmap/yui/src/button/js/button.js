// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/*
 * @package    atto_charmap
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Atto text editor character map plugin
 *
 * @module moodle-atto_charmap-button
 */

var COMPONENTNAME = 'atto_charmap',
    CSS = {
        BUTTON: 'atto_charmap_character',
        CHARMAP: 'atto_charmap_selector'
    },
    /*
     * Map of special characters, kindly borrowed from TinyMCE.
     *
     * Each entries contains in order:
     * - {String} HTML code
     * - {String} HTML numerical code
     * - {Boolean} Whether or not to include it in the list
     * - {String} The language string key
     *
     * @property CHARMAP
     * @type {Array}
     */
    CHARMAP = [
        ['&nbsp;',    '&#160;',  true, 'nobreakspace'],
        ['&amp;',     '&#38;',   true, 'ampersand'],
        ['&quot;',    '&#34;',   true, 'quotationmark'],
        // Finance.
        ['&cent;',    '&#162;',  true, 'centsign'],
        ['&euro;',    '&#8364;', true, 'eurosign'],
        ['&pound;',   '&#163;',  true, 'poundsign'],
        ['&yen;',     '&#165;',  true, 'yensign'],
        // Signs.
        ['&copy;',    '&#169;',  true, 'copyrightsign'],
        ['&reg;',     '&#174;',  true, 'registeredsign'],
        ['&trade;',   '&#8482;', true, 'trademarksign'],
        ['&permil;',  '&#8240;', true, 'permillesign'],
        ['&micro;',   '&#181;',  true, 'microsign'],
        ['&middot;',  '&#183;',  true, 'middledot'],
        ['&bull;',    '&#8226;', true, 'bullet'],
        ['&hellip;',  '&#8230;', true, 'threedotleader'],
        ['&prime;',   '&#8242;', true, 'minutesfeet'],
        ['&Prime;',   '&#8243;', true, 'secondsinches'],
        ['&sect;',    '&#167;',  true, 'sectionsign'],
        ['&para;',    '&#182;',  true, 'paragraphsign'],
        ['&szlig;',   '&#223;',  true, 'sharpsesszed'],
        // Quotations.
        ['&lsaquo;',  '&#8249;', true, 'singleleftpointinganglequotationmark'],
        ['&rsaquo;',  '&#8250;', true, 'singlerightpointinganglequotationmark'],
        ['&laquo;',   '&#171;',  true, 'leftpointingguillemet'],
        ['&raquo;',   '&#187;',  true, 'rightpointingguillemet'],
        ['&lsquo;',   '&#8216;', true, 'leftsinglequotationmark'],
        ['&rsquo;',   '&#8217;', true, 'rightsinglequotationmark'],
        ['&ldquo;',   '&#8220;', true, 'leftdoublequotationmark'],
        ['&rdquo;',   '&#8221;', true, 'rightdoublequotationmark'],
        ['&sbquo;',   '&#8218;', true, 'singlelow9quotationmark'],
        ['&bdquo;',   '&#8222;', true, 'doublelow9quotationmark'],
        ['&lt;',      '&#60;',   true, 'lessthansign'],
        ['&gt;',      '&#62;',   true, 'greaterthansign'],
        ['&le;',      '&#8804;', true, 'lessthanorequalto'],
        ['&ge;',      '&#8805;', true, 'greaterthanorequalto'],
        ['&ndash;',   '&#8211;', true, 'endash'],
        ['&mdash;',   '&#8212;', true, 'emdash'],
        ['&macr;',    '&#175;',  true, 'macron'],
        ['&oline;',   '&#8254;', true, 'overline'],
        ['&curren;',  '&#164;',  true, 'currencysign'],
        ['&brvbar;',  '&#166;',  true, 'brokenbar'],
        ['&uml;',     '&#168;',  true, 'diaeresis'],
        ['&iexcl;',   '&#161;',  true, 'invertedexclamationmark'],
        ['&iquest;',  '&#191;',  true, 'turnedquestionmark'],
        ['&circ;',    '&#710;',  true, 'circumflexaccent'],
        ['&tilde;',   '&#732;',  true, 'smalltilde'],
        ['&deg;',     '&#176;',  true, 'degreesign'],
        ['&minus;',   '&#8722;', true, 'minussign'],
        ['&plusmn;',  '&#177;',  true, 'plusminussign'],
        ['&divide;',  '&#247;',  true, 'divisionsign'],
        ['&frasl;',   '&#8260;', true, 'fractionslash'],
        ['&times;',   '&#215;',  true, 'multiplicationsign'],
        ['&sup1;',    '&#185;',  true, 'superscriptone'],
        ['&sup2;',    '&#178;',  true, 'superscripttwo'],
        ['&sup3;',    '&#179;',  true, 'superscriptthree'],
        ['&frac14;',  '&#188;',  true, 'fractiononequarter'],
        ['&frac12;',  '&#189;',  true, 'fractiononehalf'],
        ['&frac34;',  '&#190;',  true, 'fractionthreequarters'],
        // Math / logical.
        ['&fnof;',    '&#402;',  true, 'functionflorin'],
        ['&int;',     '&#8747;', true, 'integral'],
        ['&sum;',     '&#8721;', true, 'narysumation'],
        ['&infin;',   '&#8734;', true, 'infinity'],
        ['&radic;',   '&#8730;', true, 'squareroot'],
        ['&sim;',     '&#8764;', false,'similarto'],
        ['&cong;',    '&#8773;', false,'approximatelyequalto'],
        ['&asymp;',   '&#8776;', true, 'almostequalto'],
        ['&ne;',      '&#8800;', true, 'notequalto'],
        ['&equiv;',   '&#8801;', true, 'identicalto'],
        ['&isin;',    '&#8712;', false,'elementof'],
        ['&notin;',   '&#8713;', false,'notanelementof'],
        ['&ni;',      '&#8715;', false,'containsasmember'],
        ['&prod;',    '&#8719;', true, 'naryproduct'],
        ['&and;',     '&#8743;', false,'logicaland'],
        ['&or;',      '&#8744;', false,'logicalor'],
        ['&not;',     '&#172;',  true, 'notsign'],
        ['&cap;',     '&#8745;', true, 'intersection'],
        ['&cup;',     '&#8746;', false,'union'],
        ['&part;',    '&#8706;', true, 'partialdifferential'],
        ['&forall;',  '&#8704;', false,'forall'],
        ['&exist;',   '&#8707;', false,'thereexists'],
        ['&empty;',   '&#8709;', false,'diameter'],
        ['&nabla;',   '&#8711;', false,'backwarddifference'],
        ['&lowast;',  '&#8727;', false,'asteriskoperator'],
        ['&prop;',    '&#8733;', false,'proportionalto'],
        ['&ang;',     '&#8736;', false,'angle'],
        // Undefined.
        ['&acute;',   '&#180;',  true, 'acuteaccent'],
        ['&cedil;',   '&#184;',  true, 'cedilla'],
        ['&ordf;',    '&#170;',  true, 'feminineordinalindicator'],
        ['&ordm;',    '&#186;',  true, 'masculineordinalindicator'],
        ['&dagger;',  '&#8224;', true, 'dagger'],
        ['&Dagger;',  '&#8225;', true, 'doubledagger'],
        // Alphabetical special chars.
        ['&Agrave;',  '&#192;',  true, 'agrave_caps'],
        ['&Aacute;',  '&#193;',  true, 'aacute_caps'],
        ['&Acirc;',   '&#194;',  true, 'acircumflex_caps'],
        ['&Atilde;',  '&#195;',  true, 'atilde_caps'],
        ['&Auml;',    '&#196;',  true, 'adiaeresis_caps'],
        ['&Aring;',   '&#197;',  true, 'aringabove_caps'],
        ['&AElig;',   '&#198;',  true, 'ligatureae_caps'],
        ['&Ccedil;',  '&#199;',  true, 'ccedilla_caps'],
        ['&Egrave;',  '&#200;',  true, 'egrave_caps'],
        ['&Eacute;',  '&#201;',  true, 'eacute_caps'],
        ['&Ecirc;',   '&#202;',  true, 'ecircumflex_caps'],
        ['&Euml;',    '&#203;',  true, 'ediaeresis_caps'],
        ['&Igrave;',  '&#204;',  true, 'igrave_caps'],
        ['&Iacute;',  '&#205;',  true, 'iacute_caps'],
        ['&Icirc;',   '&#206;',  true, 'icircumflex_caps'],
        ['&Iuml;',    '&#207;',  true, 'idiaeresis_caps'],
        ['&ETH;',     '&#208;',  true, 'eth_caps'],
        ['&Ntilde;',  '&#209;',  true, 'ntilde_caps'],
        ['&Ograve;',  '&#210;',  true, 'ograve_caps'],
        ['&Oacute;',  '&#211;',  true, 'oacute_caps'],
        ['&Ocirc;',   '&#212;',  true, 'ocircumflex_caps'],
        ['&Otilde;',  '&#213;',  true, 'otilde_caps'],
        ['&Ouml;',    '&#214;',  true, 'odiaeresis_caps'],
        ['&Oslash;',  '&#216;',  true, 'oslash_caps'],
        ['&OElig;',   '&#338;',  true, 'ligatureoe_caps'],
        ['&Scaron;',  '&#352;',  true, 'scaron_caps'],
        ['&Ugrave;',  '&#217;',  true, 'ugrave_caps'],
        ['&Uacute;',  '&#218;',  true, 'uacute_caps'],
        ['&Ucirc;',   '&#219;',  true, 'ucircumflex_caps'],
        ['&Uuml;',    '&#220;',  true, 'udiaeresis_caps'],
        ['&Yacute;',  '&#221;',  true, 'yacute_caps'],
        ['&Yuml;',    '&#376;',  true, 'ydiaeresis_caps'],
        ['&THORN;',   '&#222;',  true, 'thorn_caps'],
        ['&agrave;',  '&#224;',  true, 'agrave'],
        ['&aacute;',  '&#225;',  true, 'aacute'],
        ['&acirc;',   '&#226;',  true, 'acircumflex'],
        ['&atilde;',  '&#227;',  true, 'atilde'],
        ['&auml;',    '&#228;',  true, 'adiaeresis'],
        ['&aring;',   '&#229;',  true, 'aringabove'],
        ['&aelig;',   '&#230;',  true, 'ligatureae'],
        ['&ccedil;',  '&#231;',  true, 'ccedilla'],
        ['&egrave;',  '&#232;',  true, 'egrave'],
        ['&eacute;',  '&#233;',  true, 'eacute'],
        ['&ecirc;',   '&#234;',  true, 'ecircumflex'],
        ['&euml;',    '&#235;',  true, 'ediaeresis'],
        ['&igrave;',  '&#236;',  true, 'igrave'],
        ['&iacute;',  '&#237;',  true, 'iacute'],
        ['&icirc;',   '&#238;',  true, 'icircumflex'],
        ['&iuml;',    '&#239;',  true, 'idiaeresis'],
        ['&eth;',     '&#240;',  true, 'eth'],
        ['&ntilde;',  '&#241;',  true, 'ntilde'],
        ['&ograve;',  '&#242;',  true, 'ograve'],
        ['&oacute;',  '&#243;',  true, 'oacute'],
        ['&ocirc;',   '&#244;',  true, 'ocircumflex'],
        ['&otilde;',  '&#245;',  true, 'otilde'],
        ['&ouml;',    '&#246;',  true, 'odiaeresis'],
        ['&oslash;',  '&#248;',  true, 'oslash'],
        ['&oelig;',   '&#339;',  true, 'ligatureoe'],
        ['&scaron;',  '&#353;',  true, 'scaron'],
        ['&ugrave;',  '&#249;',  true, 'ugrave'],
        ['&uacute;',  '&#250;',  true, 'uacute'],
        ['&ucirc;',   '&#251;',  true, 'ucircumflex'],
        ['&uuml;',    '&#252;',  true, 'udiaeresis'],
        ['&yacute;',  '&#253;',  true, 'yacute'],
        ['&thorn;',   '&#254;',  true, 'thorn'],
        ['&yuml;',    '&#255;',  true, 'ydiaeresis'],
        ['&Alpha;',   '&#913;',  true, 'alpha_caps'],
        ['&Beta;',    '&#914;',  true, 'beta_caps'],
        ['&Gamma;',   '&#915;',  true, 'gamma_caps'],
        ['&Delta;',   '&#916;',  true, 'delta_caps'],
        ['&Epsilon;', '&#917;',  true, 'epsilon_caps'],
        ['&Zeta;',    '&#918;',  true, 'zeta_caps'],
        ['&Eta;',     '&#919;',  true, 'eta_caps'],
        ['&Theta;',   '&#920;',  true, 'theta_caps'],
        ['&Iota;',    '&#921;',  true, 'iota_caps'],
        ['&Kappa;',   '&#922;',  true, 'kappa_caps'],
        ['&Lambda;',  '&#923;',  true, 'lambda_caps'],
        ['&Mu;',      '&#924;',  true, 'mu_caps'],
        ['&Nu;',      '&#925;',  true, 'nu_caps'],
        ['&Xi;',      '&#926;',  true, 'xi_caps'],
        ['&Omicron;', '&#927;',  true, 'omicron_caps'],
        ['&Pi;',      '&#928;',  true, 'pi_caps'],
        ['&Rho;',     '&#929;',  true, 'rho_caps'],
        ['&Sigma;',   '&#931;',  true, 'sigma_caps'],
        ['&Tau;',     '&#932;',  true, 'tau_caps'],
        ['&Upsilon;', '&#933;',  true, 'upsilon_caps'],
        ['&Phi;',     '&#934;',  true, 'phi_caps'],
        ['&Chi;',     '&#935;',  true, 'chi_caps'],
        ['&Psi;',     '&#936;',  true, 'psi_caps'],
        ['&Omega;',   '&#937;',  true, 'omega_caps'],
        ['&alpha;',   '&#945;',  true, 'alpha'],
        ['&beta;',    '&#946;',  true, 'beta'],
        ['&gamma;',   '&#947;',  true, 'gamma'],
        ['&delta;',   '&#948;',  true, 'delta'],
        ['&epsilon;', '&#949;',  true, 'epsilon'],
        ['&zeta;',    '&#950;',  true, 'zeta'],
        ['&eta;',     '&#951;',  true, 'eta'],
        ['&theta;',   '&#952;',  true, 'theta'],
        ['&iota;',    '&#953;',  true, 'iota'],
        ['&kappa;',   '&#954;',  true, 'kappa'],
        ['&lambda;',  '&#955;',  true, 'lambda'],
        ['&mu;',      '&#956;',  true, 'mu'],
        ['&nu;',      '&#957;',  true, 'nu'],
        ['&xi;',      '&#958;',  true, 'xi'],
        ['&omicron;', '&#959;',  true, 'omicron'],
        ['&pi;',      '&#960;',  true, 'pi'],
        ['&rho;',     '&#961;',  true, 'rho'],
        ['&sigmaf;',  '&#962;',  true, 'finalsigma'],
        ['&sigma;',   '&#963;',  true, 'sigma'],
        ['&tau;',     '&#964;',  true, 'tau'],
        ['&upsilon;', '&#965;',  true, 'upsilon'],
        ['&phi;',     '&#966;',  true, 'phi'],
        ['&chi;',     '&#967;',  true, 'chi'],
        ['&psi;',     '&#968;',  true, 'psi'],
        ['&omega;',   '&#969;',  true, 'omega'],
        // Symbols.
        ['&alefsym;', '&#8501;', false,'alefsymbol'],
        ['&piv;',     '&#982;',  false,'pisymbol'],
        ['&real;',    '&#8476;', false,'realpartsymbol'],
        ['&thetasym;','&#977;',  false,'thetasymbol'],
        ['&upsih;',   '&#978;',  false,'upsilonhooksymbol'],
        ['&weierp;',  '&#8472;', false,'weierstrassp'],
        ['&image;',   '&#8465;', false,'imaginarypart'],
        // Arrows.
        ['&larr;',    '&#8592;', true, 'leftwardsarrow'],
        ['&uarr;',    '&#8593;', true, 'upwardsarrow'],
        ['&rarr;',    '&#8594;', true, 'rightwardsarrow'],
        ['&darr;',    '&#8595;', true, 'downwardsarrow'],
        ['&harr;',    '&#8596;', true, 'leftrightarrow'],
        ['&crarr;',   '&#8629;', false,'carriagereturn'],
        ['&lArr;',    '&#8656;', false,'leftwardsdoublearrow'],
        ['&uArr;',    '&#8657;', false,'upwardsdoublearrow'],
        ['&rArr;',    '&#8658;', false,'rightwardsdoublearrow'],
        ['&dArr;',    '&#8659;', false,'downwardsdoublearrow'],
        ['&hArr;',    '&#8660;', false,'leftrightdoublearrow'],
        ['&there4;',  '&#8756;', false,'therefore'],
        ['&sub;',     '&#8834;', false,'subsetof'],
        ['&sup;',     '&#8835;', false,'supersetof'],
        ['&nsub;',    '&#8836;', false,'notasubsetof'],
        ['&sube;',    '&#8838;', false,'subsetoforequalto'],
        ['&supe;',    '&#8839;', false,'supersetoforequalto'],
        ['&oplus;',   '&#8853;', false,'circledplus'],
        ['&otimes;',  '&#8855;', false,'circledtimes'],
        ['&perp;',    '&#8869;', false,'perpendicular'],
        ['&sdot;',    '&#8901;', false,'dotoperator'],
        ['&lceil;',   '&#8968;', false,'leftceiling'],
        ['&rceil;',   '&#8969;', false,'rightceiling'],
        ['&lfloor;',  '&#8970;', false,'leftfloor'],
        ['&rfloor;',  '&#8971;', false,'rightfloor'],
        ['&lang;',    '&#9001;', false,'leftpointinganglebracket'],
        ['&rang;',    '&#9002;', false,'rightpointinganglebracket'],
        ['&loz;',     '&#9674;', true, 'lozenge'],
        ['&spades;',  '&#9824;', true, 'blackspadesuit'],
        ['&clubs;',   '&#9827;', true, 'blackclubsuit'],
        ['&hearts;',  '&#9829;', true, 'blackheartsuit'],
        ['&diams;',   '&#9830;', true, 'blackdiamondsuit'],
        ['&ensp;',    '&#8194;', false,'enspace'],
        ['&emsp;',    '&#8195;', false,'emspace'],
        ['&thinsp;',  '&#8201;', false,'thinspace'],
        ['&zwnj;',    '&#8204;', false,'zerowidthnonjoiner'],
        ['&zwj;',     '&#8205;', false,'zerowidthjoiner'],
        ['&lrm;',     '&#8206;', false,'lefttorightmark'],
        ['&rlm;',     '&#8207;', false,'righttoleftmark'],
        ['&shy;',     '&#173;',  false,'softhyphen']
    ];

/**
 * Atto text editor charmap plugin.
 *
 * @namespace M.atto_charmap
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_charmap').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    /**
     * A reference to the current selection at the time that the dialogue
     * was opened.
     *
     * @property _currentSelection
     * @type Range
     * @private
     */
    _currentSelection: null,

    initializer: function() {
        this.addButton({
            icon: 'e/special_character',
            callback: this._displayDialogue
        });
    },

    /**
     * Display the Character Map selector.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {
        // Store the current selection.
        this._currentSelection = this.get('host').getSelection();
        if (this._currentSelection === false) {
            return;
        }

        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('insertcharacter', COMPONENTNAME),
            focusAfterHide: true
        }, true);

        // Set the dialogue content, and then show the dialogue.
        dialogue.set('bodyContent', this._getDialogueContent())
                .show();
    },

    /**
     * Return the dialogue content for the tool.
     *
     * @method _getDialogueContent
     * @private
     * @return {Node} The content to place in the dialogue.
     */
    _getDialogueContent: function() {
        var template = Y.Handlebars.compile(
            '<div class="{{CSS.CHARMAP}}">' +
                '{{#each CHARMAP}}' +
                    '{{#if this.[2]}}' +
                    '<button class="{{../../CSS.BUTTON}}" ' +
                        'aria-label="{{get_string this.[3] ../../component}}" ' +
                        'title="{{get_string this.[3] ../../component}}" ' +
                        'data-character="{{this.[0]}}" ' +
                    '>{{{this.[0]}}}</button>' +
                    '{{/if}}' +
                '{{/each}}' +
            '</div>'
        );

        var content = Y.Node.create(template({
            component: COMPONENTNAME,
            CSS: CSS,
            CHARMAP: CHARMAP
        }));

        content.delegate('click', this._insertChar, '.' + CSS.BUTTON, this);
        return content;
    },

    /**
     * Insert the picked character into the editor.
     *
     * @method _insertChar
     * @param {EventFacade} e
     * @private
     */
    _insertChar: function(e) {
        var character = e.target.getData('character');

        // Hide the dialogue.
        this.getDialogue({
            focusAfterHide: null
        }).hide();

        var host = this.get('host');

        // Focus on the last point.
        host.setSelection(this._currentSelection);

        // And add the character.
        host.insertContentAtFocusPoint(character);

        // And mark the text area as updated.
        this.markUpdated();
    }
});
