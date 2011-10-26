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

class cssparser {
  private $css;
  private $html;

  function cssparser($html = true) {
    // Register "destructor"
    register_shutdown_function(array(&$this, "finalize"));
    $this->html = ($html != false);
    $this->Clear();
  }

  function finalize() {
    unset($this->css);
  }

  function Clear() {
    unset($this->css);
    $this->css = array();
    if($this->html) {
      $this->Add("ADDRESS", "");
      $this->Add("APPLET", "");
      $this->Add("AREA", "");
      $this->Add("A", "text-decoration : underline; color : Blue;");
      $this->Add("A:visited", "color : Purple;");
      $this->Add("BASE", "");
      $this->Add("BASEFONT", "");
      $this->Add("BIG", "");
      $this->Add("BLOCKQUOTE", "");
      $this->Add("BODY", "");
      $this->Add("BR", "");
      $this->Add("B", "font-weight: bold;");
      $this->Add("CAPTION", "");
      $this->Add("CENTER", "");
      $this->Add("CITE", "");
      $this->Add("CODE", "");
      $this->Add("DD", "");
      $this->Add("DFN", "");
      $this->Add("DIR", "");
      $this->Add("DIV", "");
      $this->Add("DL", "");
      $this->Add("DT", "");
      $this->Add("EM", "");
      $this->Add("FONT", "");
      $this->Add("FORM", "");
      $this->Add("H1", "");
      $this->Add("H2", "");
      $this->Add("H3", "");
      $this->Add("H4", "");
      $this->Add("H5", "");
      $this->Add("H6", "");
      $this->Add("HEAD", "");
      $this->Add("HR", "");
      $this->Add("HTML", "");
      $this->Add("IMG", "");
      $this->Add("INPUT", "");
      $this->Add("ISINDEX", "");
      $this->Add("I", "font-style: italic;");
      $this->Add("KBD", "");
      $this->Add("LINK", "");
      $this->Add("LI", "");
      $this->Add("MAP", "");
      $this->Add("MENU", "");
      $this->Add("META", "");
      $this->Add("OL", "");
      $this->Add("OPTION", "");
      $this->Add("PARAM", "");
      $this->Add("PRE", "");
      $this->Add("P", "");
      $this->Add("SAMP", "");
      $this->Add("SCRIPT", "");
      $this->Add("SELECT", "");
      $this->Add("SMALL", "");
      $this->Add("STRIKE", "");
      $this->Add("STRONG", "");
      $this->Add("STYLE", "");
      $this->Add("SUB", "");
      $this->Add("SUP", "");
      $this->Add("TABLE", "");
      $this->Add("TD", "");
      $this->Add("TEXTAREA", "");
      $this->Add("TH", "");
      $this->Add("TITLE", "");
      $this->Add("TR", "");
      $this->Add("TT", "");
      $this->Add("UL", "");
      $this->Add("U", "text-decoration : underline;");
      $this->Add("VAR", "");
    }
  }

  function SetHTML($html) {
    $this->html = ($html != false);
  }

  function Add($key, $codestr) {
    $key = strtolower($key);
    $codestr = strtolower($codestr);
    if(!isset($this->css[$key])) {
      $this->css[$key] = array();
    }
    $codes = explode(";",$codestr);
    if(count($codes) > 0) {
      $codekey=''; $codevalue='';
      foreach($codes as $code) {
        $code = trim($code);
        $this->assignValues(explode(":",$code),$codekey,$codevalue);
        if(strlen($codekey) > 0) {
          $this->css[$key][trim($codekey)] = trim($codevalue);
        }
      }
    }
  }

  private function assignValues($arr,&$val1,&$val2) {
      $n = count($arr);
      if ($n > 0) {
          $val1=$arr[0];
          $val2=($n > 1) ? $arr[1] : '';
      }
  }
  function Get($key, $property) {
    $key = strtolower($key);
    $property = strtolower($property);
    $tag='';$subtag='';$class='';$id='';
    $this->assignValues(explode(":",$key),$tag,$subtag);
    $this->assignValues(explode(".",$tag),$tag,$class);
    $this->assignValues(explode("#",$tag),$tag,$id);
    $result = "";
    $_subtag=''; $_class=''; $_id='';
    foreach($this->css as $_tag => $value) {
      $this->assignValues(explode(":",$_tag),$_tag,$_subtag);
      $this->assignValues(explode(".",$_tag),$_tag,$_class);
      $this->assignValues(explode("#",$_tag),$_tag,$_id);

      $tagmatch = (strcmp($tag, $_tag) == 0) | (strlen($_tag) == 0);
      $subtagmatch = (strcmp($subtag, $_subtag) == 0) | (strlen($_subtag) == 0);
      $classmatch = (strcmp($class, $_class) == 0) | (strlen($_class) == 0);
      $idmatch = (strcmp($id, $_id) == 0);

      if($tagmatch & $subtagmatch & $classmatch & $idmatch) {
        $temp = $_tag;
        if((strlen($temp) > 0) & (strlen($_class) > 0)) {
          $temp .= ".".$_class;
        } elseif(strlen($temp) == 0) {
          $temp = ".".$_class;
        }
        if((strlen($temp) > 0) & (strlen($_subtag) > 0)) {
          $temp .= ":".$_subtag;
        } elseif(strlen($temp) == 0) {
          $temp = ":".$_subtag;
        }
        if(isset($this->css[$temp][$property])) {
          $result = $this->css[$temp][$property];
        }
      }
    }
    return $result;
  }

  function GetSection($key) {
    $key = strtolower($key);
    $tag='';$subtag='';$class='';$id='';
    $_subtag=''; $_class=''; $_id='';

    $this->assignValues(explode(":",$key),$tag,$subtag);
    $this->assignValues(explode(".",$tag),$tag,$class);
    $this->assignValues(explode("#",$tag),$tag,$id);
    $result = array();
    foreach($this->css as $_tag => $value) {
      $this->assignValues(explode(":",$_tag),$_tag,$_subtag);
      $this->assignValues(explode(".",$_tag),$_tag,$_class);
      $this->assignValues(explode("#",$_tag),$_tag,$_id);

      $tagmatch = (strcmp($tag, $_tag) == 0) | (strlen($_tag) == 0);
      $subtagmatch = (strcmp($subtag, $_subtag) == 0) | (strlen($_subtag) == 0);
      $classmatch = (strcmp($class, $_class) == 0) | (strlen($_class) == 0);
      $idmatch = (strcmp($id, $_id) == 0);

      if($tagmatch & $subtagmatch & $classmatch & $idmatch) {
        $temp = $_tag;
        if((strlen($temp) > 0) & (strlen($_class) > 0)) {
          $temp .= ".".$_class;
        } elseif(strlen($temp) == 0) {
          $temp = ".".$_class;
        }
        if((strlen($temp) > 0) & (strlen($_subtag) > 0)) {
          $temp .= ":".$_subtag;
        } elseif(strlen($temp) == 0) {
          $temp = ":".$_subtag;
        }
        foreach($this->css[$temp] as $property => $value) {
          $result[$property] = $value;
        }
      }
    }
    return $result;
  }

  function ParseStr($str) {
    $this->Clear();
    // Remove comments
    $str = preg_replace("/\/\*(.*)?\*\//Usi", "", $str);
    // Parse this damn csscode
    $parts = explode("}",$str);
    if(count($parts) > 0) {
      foreach($parts as $part) {
        $keystr='';$codestr='';
        $this->assignValues(explode("{",$part),$keystr,$codestr);
        $keys = explode(",",trim($keystr));
        if(count($keys) > 0) {
          foreach($keys as $key) {
            if(strlen($key) > 0) {
              $key = str_replace("\n", "", $key);
              $key = str_replace("\\", "", $key);
              $this->Add($key, trim($codestr));
            }
          }
        }
      }
    }
    //
    return (count($this->css) > 0);
  }

  function Parse($filename) {
    $this->Clear();
    if(file_exists($filename)) {
      return $this->ParseStr(file_get_contents($filename));
    } else {
      return false;
    }
  }

  function GetCSS() {
    $result = "";
    foreach($this->css as $key => $values) {
      $result .= $key." {\n";
      foreach($values as $key => $value) {
        $result .= "  $key: $value;\n";
      }
      $result .= "}\n\n";
    }
    return $result;
  }
}