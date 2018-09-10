<?php

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

/**
 * mod/hotpot/mediafilter/class.php
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get the standard Moodle mediaplugin filter
require_once($CFG->dirroot.'/filter/mediaplugin/filter.php');

/**
 * hotpot_mediafilter
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class hotpot_mediafilter {

    // media filetypes that this filter can handle
    // this initial list is of the file types that Moodle's standard mediaplugin can handle
    // media file types specified by individual media players will be added to this list
    public $media_filetypes = array(
        // any params allowed (flash audio/video, html5 audio/video)
        'aac'=>'any', 'f4v'=>'any', 'flv'=>'any', 'm4a'=>'any', 'm4v'=>'any',
        'mp4'=>'any', 'oga'=>'any', 'ogg'=>'any', 'ogv'=>'any', 'webm'=>'any',
        // only "d=WIDTHxHEIGHT" param allowed in moodle filter
        'avi'=>'size', 'm4v'=>'size', 'm4a'=>'size', 'mov'=>'size',
        'mp4'=>'size', 'mpeg'=>'size', 'mpg'=>'size', 'swf'=>'size', 'wmv'=>'size',
        // no params allowed in moodle filter
        'mp3'=>'none', 'ra'=>'none', 'ram'=>'none', 'rm'=>'none', 'rv'=>'none'
    );

    public $param_names = 'movie|song_url|src|url';
    //  wmp        : url
    //  quicktime  : src
    //  realplayer : src
    //  flash      : movie
    //  other      : song_url

    public $tagopen = '(?:(<)|(\\\\u003C))'; // left angle-bracket (uses two parenthese)
    public $tagchars = '(?(1)[^>]|(?(2).(?!\\\\u003E)))*?';  // string of chars inside the tag
    public $tagclose = '(?(1)>|(?(2)\\\\u003E))'; // right angle-bracket (to match the left one)
    public $tagreopen = '(?(1)<|(?(2)\\\\u003C))'; // another left angle-bracket (to match the first one)
    //$tagopen = '(?:(<)|(&lt;)|(&amp;#x003C;))';
    //$tagclose = '(?(2)>|(?(3)&gt;|(?(4)&amp;#x003E;)))';

    public $link_search = '';
    public $object_search = '';
    public $object_searches = array();

    public $js_inline = '';
    public $js_external = '';

    public $players  = array();
    public $defaultplayer = 'moodle';

    public $moodle_flashvars = array('waitForPlay', 'autoPlay', 'buffer');
    // bgColour, btnColour, btnBorderColour,
    // iconColour, iconOverColour,
    // trackColour, handleColour, loaderColour,
    // waitForPlay, autoPlay, buffer

    // constructor function

    /**
     * __construct
     *
     * @param xxx $output (passed by reference)
     */
    function __construct($output)  {
        global $CFG, $THEME;

        $this->players[$this->defaultplayer] = new hotpot_mediaplayer();

        $flashvars_paramnames = array();
        $querystring_paramname = array();

        $players = get_list_of_plugins('mod/hotpot/mediafilter/hotpot'); // sorted
        foreach ($players as $player) {
            $filepath = $CFG->dirroot.'/mod/hotpot/mediafilter/hotpot/'.$player.'/class.php';
            if (file_exists($filepath) && include_once($filepath)) {
                $playerclass = 'hotpot_mediaplayer_'.$player;
                $this->players[$player] = new $playerclass();

                // note the names urls in flashvars and querystring
                if ($name = $this->players[$player]->flashvars_paramname) {
                    $flashvars_paramnames[$name] = true;
                }
                if ($name = $this->players[$player]->querystring_paramname) {
                    $querystring_paramnames[$name] = true;
                }

                // add aliases to this player
                foreach ($this->players[$player]->aliases as $alias) {
                    $this->players[$alias] =&$this->players[$player];
                }

                // add any new media file types
                foreach ($this->players[$player]->media_filetypes as $filetype) {
                    if (! array_key_exists($filetype, $this->media_filetypes)) {
                        $this->media_filetypes[$filetype] = '';
                    }
                }
            }
        }

        $filetypes = implode('|', array_keys($this->media_filetypes));
        $filepath = '[^"'."'?]*".'\.('.$filetypes.')[^"'."']*";

        // detect backslash before double quotes and slashes within JavaScript
        $escape = '(?:\\\\)?';

        // search string to extract <a> tags
        $this->link_search = '/'.$this->tagopen.'a'.'\s+'.$this->tagchars.'href='.$escape.'"('.$filepath.')'.$escape.'"'.$this->tagchars.$this->tagclose.'.*?'.$this->tagreopen.$escape.'\/a'.$this->tagclose.'/is';

        // search string to extract <object> or <embed> tags
        $this->object_search = '/'.$this->tagopen.'(object|embed)'.'\s'.$this->tagchars.$this->tagclose.'(.*?)(?:'.$this->tagreopen.'(?:\\\\)?'.'\/\3'.$this->tagclose.')+/is';

        // search Flashvars with specific names
        // $flashvars_paramnames e.g. TheSound
        // e.g. param name="Flashvars" value="TheSound=abc.mp3"
        if ($flashvars_paramnames = implode('|', array_keys($flashvars_paramnames))) {
            $this->object_searches[] = '/'.$this->tagopen.'param'.'\s+'.$this->tagchars.'name='.$escape.'"FlashVars'.$escape.'"'.$this->tagchars.'value='.$escape.'"(?:'.$flashvars_paramnames.')=('.$filepath.')'.$escape.'"'.$this->tagchars.$this->tagclose.'/is';
        }

        // html tags and attributes to search for urls
        $tags = array(
            'object'=>'data', 'embed'=>'src', 'a'=>'href'
        );

        // search for specific querystrings
        // e.g. param name="movie" value="player.swf?mp3=abc.mp3"
        // e.g. object data="player.swf?mp3=abc.mp3"
        // e.g. embed src="player.swf?mp3=abc.mp3"
        // e.g. a href="player.swf?mp3=abc.mp3"
        if ($querystring_paramnames = implode('|', array_keys($querystring_paramnames))) {
            $querystring_filepath = '[^"'."'?]*".'\?[^"'."']*(?:$querystring_paramnames)=($filepath)".'[^"'."']*";

            if ($this->param_names) {
                $this->object_searches[] = '/'.$this->tagopen.'param'.'\s+'.$this->tagchars.'name='.$escape.'"(?:'.$this->param_names.')'.$escape.'"'.$this->tagchars.'value='.$escape.'"'.$querystring_filepath.$escape.'"'.$this->tagchars.$this->tagclose.'/is';
            }
            foreach ($tags as $tag => $attribute) {
                $this->object_searches[] = '/'.$this->tagopen.$tag.'\s+'.$this->tagchars.$attribute.'='.$escape.'"'.$querystring_filepath.$escape.'"'.$this->tagchars.$this->tagclose.'.*?'.$this->tagreopen.$escape.'\/'.$tag.$this->tagclose.'/is';
            }
        }

        // search for full urls
        // e.g. param name="movie" value="abc.mp3"
        // e.g. object data="mp3=abc.mp3"
        // e.g. embed src="abc=abc.mp3"
        // e.g. a href="abc=abc.mp3"
        if ($this->param_names) {
            $this->object_searches[] = '/'.$this->tagopen.'param'.'\s+'.$this->tagchars.'name='.$escape.'"(?:'.$this->param_names.')'.$escape.'"'.$this->tagchars.'value='.$escape.'"('.$filepath.')'.$escape.'"'.$this->tagchars.$this->tagclose.'/is';
        }
        foreach ($tags as $tag => $attribute) {
            $this->object_searches[] = '/'.$this->tagopen.$tag.'\s+'.$this->tagchars.$attribute.'='.$escape.'"('.$filepath.')'.$escape.'"'.$this->tagchars.$this->tagclose.'.*?'.$this->tagreopen.$escape.'\/'.$tag.$this->tagclose.'/is';
        }

        // check player settings
        $names = array_keys($this->players);
        foreach ($names as $name) {

            // convert  player url to absolute url
            $player = &$this->players[$name];
            if ($player->playerurl && ! preg_match('/^(?:https?:)?\/+/i', $player->playerurl)) {
                $player->playerurl = $CFG->wwwroot.'/mod/hotpot/mediafilter/hotpot/'.$player->playerurl;
            }

            // set basic flashvars settings
            $options = &$player->options;
            if (is_null($options['flashvars'])) {
                if (empty($THEME->filter_mediaplugin_colors)) {
                    $options['flashvars'] = ''
                        .'bgColour=000000&'
                        .'btnColour=ffffff&'.'btnBorderColour=cccccc&'
                        .'iconColour=000000&'.'iconOverColour=00cc00&'
                        .'trackColour=cccccc&'.'handleColour=ffffff&'
                        .'loaderColour=ffffff&'.'waitForPlay=yes'
                    ;
                } else {
                    // You can set this up in your theme/xxx/config.php
                    $options['flashvars'] = $THEME->filter_mediaplugin_colors;
                }
                $options['flashvars'] = htmlspecialchars($options['flashvars']);
            }
        }
    }

    /**
     * fix
     *
     * @param xxx $text
     * @param xxx $output (passed by reference)
     */
    function fix($text, $output) {
        $this->fix_objects($text, $output);
        $this->fix_links($text, $output);
        $this->fix_specials($text, $output);
    }

    /**
     * fix_objects
     *
     * @param xxx $text
     * @param xxx $output (passed by reference)
     */
    function fix_objects($text, $output)  {
        // Segments[0][0] = '<object classid=\"CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6\" width=\"100\" height=\"30\"><param name=\"url\" value=\"http://localhost/moodle/19/mysql/file.php/2/hennyjellema/frag.01.mp3\" /><param name=\"autostart\" value=\"false\" /><param name=\"showcontrols\" value=\"true\" /><\/object>';
        $callback = array($this, 'fix_object');
        $callback = partial($callback, $output);
        $output->$text = preg_replace_callback($this->object_search, $callback, $output->$text);
    }

    /**
     * fix_object
     *
     * @param xxx $output (passed by reference)
     * @param xxx $object
     * @param xxx $unicode
     * @param xxx $quote (optional, default="'")
     * @return xxx
     */
    function fix_object($output, $match)  {
        $object = $match[0];
        $unicode = $match[2];

        $url = '';
        $filetype = '';
        foreach ($this->object_searches as $search) {
            if (preg_match($search, $object, $matches)) {
                $url = $matches[3];
                $filetype = $matches[4];
                break;
            }
        }

        if ($url=='') {
            return $object;
        }

        // strip inner tags (e.g. <embed>)
        $txt = preg_replace('/'.$this->tagopen.'.*?'.$this->tagclose.'/', '', $object);
        $txt = trim($txt);

        // if url has a query string, we assume the target url
        // is one of the values in the query string
        // $pos : 0=first value, 1=named value, 2=last value
        $pos = 1;
        switch ($pos) {
            case 0: $search = '/^[^?]*\?'.'[^=]+=([^&]*)'.'.*$/'; break;
            case 1: $search = '/^[^?]*\?'.'(?:file|song_url|src|thesound|mp3)+=([^&]*)'.'.*$/'; break;
            case 2: $search = '/^[^?]*\?'.'(?:[^=]+=[^&]*&(?:amp;))*'.'[^=]+=([^&]*)'.'$/'; break;
        }
        $url = preg_replace($search, '$1', $url, 1);

        // create new media player for this media file
        $player = $this->get_defaultplayer($url, $filetype);
        if ($player=='moodle' && isset($this->media_filetypes[$filetype])) {
            $allow = $this->media_filetypes[$filetype];
            $count = 0;
            if ($allow=='size') {
                $url = preg_replace('/^([^?#&]*).*?(d=\d{1,4}x\d{1,4}).*$/', '$1?$2', $url, -1, $count);
            }
            if ($allow=='none' || ($allow=='size' && $count==0)) {
                $url = preg_replace('/^([^?]*)[?#&].*$/', '$1', $url);
            }
        }
        $options = array('unicode' => $unicode, 'player' => $player);
        $link = '<a href="'.$url.'">'.$txt.'</a>';
        return $this->fix_link($output, $options, $link);
    }

    /**
     * fix_specials
     *
     * @param xxx $output (passed by reference)
     * @param xxx $text
     */
    function fix_specials($text, $output)  {
        // search for [url   player   width   height   options]
        //     url : the (relative or absolute) url of the media file
        //     player : string of alpha chars (underscore and hyphen are also allowed)
        //         "img" or "image" : insert an <img> tag for this url
        //         "a" or "link" : insert a link to the url
        //         "object" or "movie" : url is a stand-alone movie; insert <object> tags
        //         "moodle" : insert a standard moodle media player to play the media file
        //         otherwise the url is for a media file, so insert a player to play/display it
        //     width : the required display width (e.g. 50 or 50px or 10em)
        //     height : the required display height (e.g. 25 or 25px or 5em)
        //     options : xx OR xx= OR xx=abc123 OR xx="a b c 1 2 3"
        // Note: only url is required; others values are optional
        $filetypes = implode('|', array_keys($this->media_filetypes));
        $search = ''
            .'/\[\s*'
            .'('.'[^ \]]*?'.'\.(?:'.$filetypes.')(?:\?[^ \]]*)?)' // 1: url (+ querystring)
            .'(\s+[a-z][0-9a-z._-]*)?' // 2: player
            .'(\s+\d+(?:\.\d+)?[a-z]*)?' // 3: width
            .'(\s+\d+(?:\.\d+)?[a-z]*)?' // 4: height
            .'((?:\s+[^ =\]]+(?:=(?:(?:\\\\?"[^"]*")|\w*))?)*)' // 5: options
            .'\s*\]'
            .'((?:\s*<br\s*\/?>)*)' // 6: trailing newlines
            .'/is'
        ;
        $callback = array($this, 'fix_special');
        $callback = partial($callback, $output);
        $output->$text = preg_replace_callback($search, $callback, $output->$text);
    }

    /**
     * fix_special
     *
     * @param xxx $match
     * @param xxx $output (passed by reference)
     * @return xxx
     */
    function fix_special($output, $match)  {

        $url = trim($match[1]);
        $player = trim($match[2]);
        $width = trim($match[3]);
        $height = trim($match[4]);
        $options = trim($match[5]);
        $space = trim($match[6]);

        // convert $url to $absoluteurl
        $absoluteurl = $output->convert_url_relative($url);
        //$absoluteurl = $output->convert_url($url, '');

        // set height equal to width, if necessary
        if ($width && ! $height) {
            $height = $width;
        }

        //if ($player=='' && $this->image_filetypes && preg_match('/\.(?:'.$this->image_filetypes.')/i', $url)) {
        //    $player = 'img';
        //}

        //if ($player=='img' || $player=='image') {
        //    return '<img src="'.$absoluteurl.'" width="'.$width.'" height="'.$height.'" />';
        //}

        if ($player=='') {
            $player = $this->get_defaultplayer($url);
        }

        // $options_array will be passed to mediaplugin_filter
        $options_array = array();

        // add $player, $width and $height to $option_array
        if ($player=='movie' || $player=='object') {
            $options_array['movie'] = $absoluteurl;
            $options_array['skipmediaurl'] = true;
        } else if ($player=='center' || $player=='hide') {
            $options_array[$player] = true;
            $player = '';
        } else if ($player) {
            $options_array['player'] = $player;
        }

        if ($width) {
            $options_array['width'] = $width;
        }
        if ($height) {
            $options_array['height'] = $height;
        }

        // transfer $options to $option_array
        if (preg_match_all('/([^ =\]]+)(=((?:\\\\?"[^"]*")|\w*))?/s', $options, $matches)) {
            $i_max = count($matches[0]);
            for ($i=0; $i<$i_max; $i++) {
                $name = $matches[1][$i];
                if ($matches[2][$i]) {
                    $options_array[$name] = trim($matches[3][$i], '"\\');
                } else {
                    $options_array[$name] = true; // boolean switch
                }
            }
        }

        // remove trailing space if player is to be centered or hidden
        if (! empty($options_array['center']) || ! empty($options_array['hide'])) {
            $space = '';
        }

        $link = '<a href="'.$absoluteurl.'" target="_blank">'.$url.'</a>';
        return $this->fix_link($output, $options_array, $link).$space;
    }

    /**
     * fix_links
     *
     * @param xxx $output (passed by reference)
     * @param xxx $text
     */
    function fix_links($text, $output)  {
        $callback = array($this, 'fix_link');
        $callback = partial($callback, $output, array());
        $output->$text = preg_replace_callback($this->link_search, $callback, $output->$text);
    }

    /**
     * fix_link
     *
     * @param xxx $match
     * @param xxx $output (passed by reference)
     * @param xxx $options (optional, default=array)
     * @return xxx
     */
    function fix_link($output, $options, $match)  {
        global $CFG, $PAGE;
        static $load_flowplayer = 0;
        static $eolas_fix_applied = 0;

        if (is_string($match)) {
            $link = $match;
            $unicode = '';
        } else if (is_array($match)) {
            $link = $match[0];
            $unicode = $match[2];
        } else {
            debugging('Oops, $match is not an array or string !');
            $args = func_get_args();
            print_object(count($args));
            die;
        }

        if (array_key_exists('unicode', $options)) {
            $unicode = $options['unicode'];
        }

        // set player default, if necessary
        if (empty($options['player'])) {
            $options['player'] = $this->defaultplayer;
        }

        // hide player if required
        if (array_key_exists('hide', $options)) {
            if ($options['hide']) {
                $options['width'] = 1;
                $options['height'] = 1;
                if ($options['player']=='moodle') {
                    $options['autoPlay'] = 'yes';
                    $options['waitForPlay'] = 'no';
                }
            }
            unset($options['hide']);
            unset($options['center']);
        }

        // call filter to add media player
        if (empty($options['movie']) && $options['player']=='moodle') {

            $filter = new filter_mediaplugin($output->hotpot->context, array());
            $object = $filter->filter($link);

            if ($object==$link) {
                // do nothing
            } else if ($eolas_fix_applied==$output->hotpot->id) {
                // eolas_fix.js and ufo.js have already been added for this quiz
            } else {
                if ($eolas_fix_applied==0) {
                    // 1st quiz - eolas_fix.js was added by filter/mediaplugin/filter.php
                } else {
                    // 2nd (or later) quiz - e.g. we are being called by hotpot_cron()
                    $PAGE->requires->js('/mod/hotpot/mediafilter/eolas_fix.js');
                    //$object .= '<script defer="defer" src="'.$CFG->wwwroot.'/mod/hotpot/mediafilter/eolas_fix.js" type="text/javascript"></script>';
                }
                $PAGE->requires->js('/mod/hotpot/mediafilter/ufo.js', true);
                //$object .= '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/hotpot/mediafilter/ufo.js"></script>';
                $eolas_fix_applied = $output->hotpot->id;
            }

            $search = '/(flashvars:")([^"]*)(")/';
            $callback = array($this, 'fix_flashvars');
            $callback = partial($callback, $options);
            $object = preg_replace_callback($search, $callback, $object);

            // fix height and width (e.g. height="15", "height": 15)
            foreach (array('width', 'height') as $option) {
                if (array_key_exists($option, $options)) {
                    $search = array('/(?<='.$option.':")\w+(?=")/i', '/(?<="'.$option.'": )\w+/i');
                    $object = preg_replace($search, $options[$option], $object);
                }
            }
        } else {
            $object = $this->mediaplugin_filter($output->hotpot, $link, $options);
        }

        // center content if required
        if (array_key_exists('center', $options)) {
            if ($options['center']) {
                $object = '<div style="text-align:center;">'.$object.'</div>';
            }
            unset($options['center']);
        }

        // if required, remove the link contained in the object tag
        // Note: strcmp() returns true if strings are different
        $player = $options['player'];
        if ($this->players[$player]->removelink && strcmp($object, $link)) {
            $search = '/<a href="[^"]*"[^>]*>[^<]*<\/a>\s*/is';
            $object = preg_replace($search, '', $object);
        }

        // extract the external javascripts
        $search = '/\s*<script[^>]*src[^>]*>.*?<\/script>\s*/is';
        if (preg_match_all($search, $object, $scripts, PREG_OFFSET_CAPTURE)) {
            foreach (array_reverse($scripts[0]) as $script) {
                // $script: [0] = matched string, [1] = offset to start of string
                // remove the javascript from the player
                $object = substr_replace($object, "\n", $script[1], strlen($script[0]));
                // store this javascript so it can be run later
                $this->js_external = trim($script[0])."\n".$this->js_external;
            }
        }

        // extract the inline javascripts
        $search = '/\s*<script[^>]*>.*?<\/script>\s*/is';
        if (preg_match_all($search, $object, $scripts, PREG_OFFSET_CAPTURE)) {
            foreach (array_reverse($scripts[0]) as $script) {
                // $script: [0] = matched string, [1] = offset to start of string
                // remove the script from the player
                $object = substr_replace($object, "\n", $script[1], strlen($script[0]));
                // format the script (helps readability of the html source)
                $script[0] = $this->format_script($script[0]);
                //store this javascript so it can be run later
                $this->js_inline = trim($script[0])."\n".$this->js_inline;
            }
            if ($this->js_inline && $load_flowplayer==0) {
                $load_flowplayer = 1;
                $this->js_inline .= ''
                    .'<script type="text/javascript">'."\n"
                    ."//<![CDATA[\n"
                    ."\t".'M.util.load_flowplayer();'."\n"
                    ."//]]>\n"
                    ."</script>\n"
                ;
            }
        }

        // remove white space between tags, standardize other white space to a single space
        $object = preg_replace('/(?<=>)\s+(?=<)/', '', $object);
        $object = preg_replace('/\s+/', ' ', $object);

        if ($unicode) {
            // encode angle brackets as javascript $unicode
            $object = str_replace('<', '\\u003C', $object);
            $object = str_replace('>', '\\u003E', $object);
            //$object = str_replace('&amp;', '&', $object);
        }

        return $object;
    }

    /**
     * get_defaultplayer
     *
     * @param xxx $url
     * @return xxx
     */
    function get_defaultplayer($url, $filetype='') {
        if ($filetype=='') {
            $filetype = pathinfo($url, PATHINFO_EXTENSION);
        }
        foreach ($this->players as $playername => $player) {
            if (in_array($filetype, $player->media_filetypes)) {
                return $playername;
            }
        }
        return $this->defaultplayer;
    }

    /**
     * fix_flashvars
     *
     * @param xxx $match
     * @param xxx $options (passed by reference)
     * @return xxx
     */
    function fix_flashvars($options, $match)  {
        global $CFG;

        $before = $match[1];
        $flashvars = $match[2];
        $after  = $match[3];

        // entities_to_utf8() is required undo the call to htmlentities(), see MDL-5223
        // this is necessary to allow waitForPlay and autoPlay to be effective on Firefox
        $flashvars = hotpot_textlib('entities_to_utf8', $flashvars);

        $vars = explode('&', $flashvars);
        foreach ($this->moodle_flashvars as $var) {
            if (array_key_exists($var, $options)) {
                $vars = preg_grep("/^$var=/", $vars, PREG_GREP_INVERT);
                $vars[] = "$var=".hotpot_textlib('utf8_to_entities', $options[$var]);
            }
        }

        return $before.implode('&', $vars).$after;
    }

    /**
     * format_script
     *
     * @param xxx $str
     * @param xxx $quote (optional, default="'")
     * @return xxx
     */
    function format_script($str)  {
        // fix indents
        $str = preg_replace('/^ +/m', "\t", $str);

        // format FO (Flash Object) properties (one property per line)
        $search = '/var FO\s*=\s*\{\s*(.*?)\s*\}/is';
        // $1 : properties of the FO object
        if (preg_match_all($search, $str, $matches, PREG_OFFSET_CAPTURE)) {
            $search = '/\s*(\w+)\s*:\s*(".*?",?)/is';
            // $1 : the name of an FO object property
            // $2 : the value of an FO object property
            $replace = "\t".'$1 : $2'."\n\t";
            $i_max = count($matches[0]) - 1;
            for ($i=$i_max; $i>=0; $i--) {
                list($match, $start) = $matches[0][$i];
                $length = strlen($match);
                $properties = preg_replace($search, $replace, $matches[1][$i][0]);
                $str = substr_replace($str, 'var FO ={'."\n\t".$properties.'}', $start, $length);
            }
        } else {
            $str = preg_replace('/\s*(M.util.add_[^;]*;)\s*/', "\n\t".'$1'."\n", $str);
        }
        return $str;
    }

    /**
     * mediaplugin_filter
     *
     * @param xxx $courseid
     * @param xxx $text
     * @param xxx $options (optional, default=array)
     * @return xxx
     */
    function mediaplugin_filter($hotpot, $text, $options=array())  {
        // this function should be overloaded by the subclass
        return $text;
    }
}

/**
 * hotpot_mediaplayer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class hotpot_mediaplayer {
    public $aliases = array();
    public $playerurl = '';
    public $flashvars = array();
    public $flashvars_paramname = '';
    public $querystring_paramname = '';
    public $options = array(
        'width' => 0, 'height' => 0, 'build' => 40,
        'quality' => 'high', 'majorversion' => '6', 'flashvars' => null
    );
    public $more_options = array();
    public $media_filetypes = array();
    public $spantext = '';
    public $removelink = true;

    /**
     * contructor for this class
     */
    function __construct()  {
        $this->options = array_merge($this->options, $this->more_options);
    }

    /**
     * generate
     *
     * @param xxx $filetype
     * @param xxx $link
     * @param xxx $mediaurl
     * @param xxx $options
     * @return xxx
     */
    function generate($filetype, $link, $mediaurl, $options)  {
        global $CFG;

        // cache language strings
        static $str;
        if (! isset($str->$filetype)) {
            $str->$filetype = $filetype.'audio'; // get_string($filetype.'audio', 'mediaplugin');
        }

        // $id must be unique to prevent it being stored in Moodle's text cache
        static $id_count = 0;
        $id = str_replace('hotpot_mediaplayer_', '', get_class($this)).'_'.time().sprintf('%02d', ($id_count++));


        // add movie id to $options, if necessary
        // this is required in order to allow Flash addCallback on IE
        // 2009/11/30 - it is not necessary for IE8, maybe not necessary at all
        //if (! isset($options['id'])) {
        //    $options['id'] = 'ufo_'.$id;
        //}

        // add movie url to $options, if necessary
        if (! isset($options['movie'])) {
            $options['movie'] = $this->playerurl;
            if ($this->querystring_paramname) {
                $options['movie'] .= '?'.$this->querystring_paramname.'='.$mediaurl;
            }
        }

        // do we need to make sure the mediaurl is added to flashvars?
        if ($this->flashvars_paramname && empty($options['skipmediaurl'])) {
            $find_mediaurl = true;
        } else {
            $find_mediaurl = false;
        }

        // get list of option names to be cleaned
        $search = '/^player|playerurl|querystring_paramname|flashvars_paramname|skipmediaurl$/i';
        $names = preg_grep($search, array_keys($options), PREG_GREP_INVERT);

        // clean the options
        foreach ($names as $name) {

            switch ($name) {

                case 'id':
                    // allow a-z A-Z 0-9 and underscore (could use PARAM_SAFEDIR, but that allows hyphen too)
                    $options[$name] = preg_replace('/\W/', '', $options[$name]);
                    break;

                case 'movie':
                    // clean_param() will reject url if it contains spaces
                    $options[$name] = str_replace(' ', '%20', $options[$name]);
                    $options[$name] = clean_param($options[$name], PARAM_URL);
                    break;

                case 'flashvars':

                    // split flashvars into an array
                    $flashvars = str_replace('&amp;', '&', $options[$name]);
                    $flashvars = explode('&', $flashvars);

                    // loop through $flashvars, cleaning as we go
                    $options[$name] = array();
                    $found_mediaurl = false;
                    foreach ($flashvars as $flashvar) {
                        if (trim($flashvar)=='') {
                            continue;
                        }
                        list($n, $v) = explode('=', $flashvar, 2);
                        $n = clean_param($n, PARAM_ALPHANUM);
                        if ($n==$this->flashvars_paramname) {
                            $found_mediaurl = true;
                            $options[$name][$n] = clean_param($v, PARAM_URL);
                        } else if (array_key_exists($n, $this->flashvars)) {
                            $options[$name][$n] = clean_param($v, $this->flashvars[$n]);
                        } else {
                            // $flashvar not defined for this media player so ignore it
                        }
                    }

                    // add media url to flashvars, if necessary
                    if ($find_mediaurl && ! $found_mediaurl) {
                        $n = $this->flashvars_paramname;
                        $options[$name][$n] = clean_param($mediaurl, PARAM_URL);
                    }

                    // add flashvars values passed via $options
                    foreach ($this->flashvars as $n => $type) {
                        if (isset($options[$n])) {
                            $options[$name][$n] = clean_param($options[$n], $type);
                            unset($options[$n]);
                        }
                    }

                    // rebuild $flashvars
                    $flashvars = array();
                    foreach ($options[$name] as $n => $v) {
                        $flashvars[] = "$n=".$v; // urlencode($v);
                    }

                    // join $namevalues back together
                    $options[$name] = implode('&', $flashvars);
                    unset($flashvars);
                    break;

                default:
                    $quote = '';
                    if (isset($options[$name])) {
                        $value = $options[$name];
                        if (preg_match('/^(\\\\*["'."']".')?(.*)'.'$1'.'$/', $value, $matches)) {
                            $quote = $matches[1];
                            $value = $matches[2];
                        }
                        $options[$name] = $quote.clean_param($value, PARAM_ALPHANUM).$quote;
                    }
            } // end switch $name
        } // end foreach $names

        // re-order options ("movie" first, "flashvars" last)
        $names = array_merge(
            array('id'), array('movie'),
            preg_grep('/^id|movie|flashvars$/i', $names, PREG_GREP_INVERT),
            array('flashvars')
        );

        $args = array();
        $properties = array();
        foreach ($names as $name) {
            if (empty($options[$name])) {
                continue;
            }
            $args[$name] = $options[$name];
            $properties[] = $name.':"'.$this->obfuscate_js(addslashes_js($options[$name])).'"';
        }
        $properties = implode(',', $properties);

        if (strlen($this->spantext)) {
            $spantext = $this->spantext;
        } else {
            $size = '';
            if (isset($options['width'])) {
                $size .= ' width="'.$options['width'].'"';
            }
            if (isset($options['height'])) {
                $size .= ' height="'.$options['height'].'"';
            }
            $spantext = '<img src="'.$CFG->wwwroot.'/pix/spacer.gif"'.$size.' alt="'.$str->$filetype.'" />';
        }

        return $link
            .'<span class="mediaplugin mediaplugin_'.$filetype.'" id="'.$id.'">'.$spantext.'</span>'."\n"
            .'<script type="text/javascript">'."\n"
            .'//<![CDATA['."\n"
            .'  var FO = { '.$properties.' };'."\n"
            .'  UFO.create(FO, "'.$this->obfuscate_js($id).'");'."\n"
            .'  UFO.main("'.$this->obfuscate_js($id).'");'."\n"
            .'//]]>'."\n"
            .'</script>'
        ;
    }

    /**
     * obfuscate_js
     *
     * @param xxx $str
     * @return xxx
     */
    function obfuscate_js($str)  {
        global $CFG;

        if (empty($CFG->hotpot_enableobfuscate)) {
            return $str;
        }

        $obfuscated = '';
        $strlen = strlen($str);
        for ($i=0; $i<$strlen; $i++) {
            if ($i==0 || mt_rand(0,2)) {
                $obfuscated .= '\\u'.sprintf('%04X', ord($str{$i}));
            } else {
                $obfuscated .= $str{$i};
            }
        }
        return $obfuscated;
    }
}
