<?php

class com_wiris_quizzes_impl_HTML {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->s = new StringBuf();
		$this->tags = new _hx_array(array());
	}}
	public function openTd($className) {
		$this->open("td", new _hx_array(array(new _hx_array(array("class", $className)))));
	}
	public function openTr($className) {
		$this->open("tr", new _hx_array(array(new _hx_array(array("class", $className)))));
	}
	public function openTable($id, $className) {
		$this->open("table", new _hx_array(array(new _hx_array(array("id", $id)), new _hx_array(array("class", $className)))));
	}
	public function jsComponent($id, $className, $arg) {
		$this->input("hidden", $id, null, $arg, null, "wirisjscomponent " . $className);
	}
	public function dd($text) {
		$this->open("dd", null);
		$this->text($text);
		$this->close();
	}
	public function dt($text) {
		$this->open("dt", null);
		$this->text($text);
		$this->close();
	}
	public function openDl($id, $classes) {
		$this->open("dl", new _hx_array(array(new _hx_array(array("id", $id)), new _hx_array(array("class", $classes)))));
	}
	public function formatText($text) {
		$ps = _hx_explode("\x0A", $text);
		$i = null;
		{
			$_g1 = 0; $_g = $ps->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$this->openP();
				$this->text($ps[$i1]);
				$this->close();
				unset($i1);
			}
		}
	}
	public function openPWithClass($className) {
		$this->open("p", new _hx_array(array(new _hx_array(array("class", $className)))));
	}
	public function openP() {
		$this->open("p", null);
	}
	public function help($id, $href, $title) {
		$this->openSpan($id . "span", "wirishelp");
		$this->open("a", new _hx_array(array(new _hx_array(array("id", $id)), new _hx_array(array("href", $href)), new _hx_array(array("class", "wirishelp")), new _hx_array(array("title", $title)), new _hx_array(array("target", "_blank")))));
		$this->close();
		$this->close();
	}
	public function openStrong() {
		$this->open("strong", null);
	}
	public function openA($id, $href, $className, $target) {
		$this->open("a", new _hx_array(array(new _hx_array(array("id", $id)), new _hx_array(array("href", $href)), new _hx_array(array("class", $className)), new _hx_array(array("target", $target)))));
	}
	public function select($id, $name, $options) {
		$this->open("select", new _hx_array(array(new _hx_array(array("id", $id)), new _hx_array(array("name", $name)))));
		$i = null;
		{
			$_g1 = 0; $_g = $options->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$this->open("option", new _hx_array(array(new _hx_array(array("value", $options[$i1][0])))));
				$this->text($options[$i1][1]);
				$this->close();
				unset($i1);
			}
		}
		$this->close();
	}
	public function openFieldset($id, $legend, $classes) {
		$this->openCollapsibleFieldset($id, $legend, $classes, false, false);
	}
	public function openCollapsibleFieldset($id, $legend, $classes, $collapsible, $collapsed) {
		$className = "wirisfieldset";
		if($classes !== null && strlen($classes) > 0) {
			$className .= " " . $classes;
		}
		if($collapsible) {
			$className .= " wiriscollapsible";
			$className .= (($collapsed) ? " wiriscollapsed" : " wirisexpanded");
		}
		$this->open("fieldset", new _hx_array(array(new _hx_array(array("id", $id)), new _hx_array(array("class", $className)))));
		$this->open("legend", new _hx_array(array(new _hx_array(array("class", $classes)))));
		if($collapsible) {
			$className = "wiriscollapsiblea " . ((($collapsed) ? " wiriscollapsed" : " wirisexpanded"));
			$this->open("a", new _hx_array(array(new _hx_array(array("href", "#")), new _hx_array(array("class", $className)))));
		}
		$this->text($legend);
		if($collapsible) {
			$this->close();
		}
		$this->close();
		if($collapsible) {
			$className = "wirisfieldsetwrapper " . ((($collapsed) ? " wiriscollapsed" : " wirisexpanded"));
			$this->openDivClass($id . "-wrapper", $className);
		}
	}
	public function labelTitle($text, $id, $className, $title) {
		$this->open("label", new _hx_array(array(new _hx_array(array("for", $id)), new _hx_array(array("class", $className)), new _hx_array(array("title", $title)))));
		$this->text($text);
		$this->close();
	}
	public function label($text, $id, $className) {
		$this->labelTitle($text, $id, $className, null);
	}
	public function openLiClass($className) {
		$this->open("li", new _hx_array(array(new _hx_array(array("class", $className)))));
	}
	public function openLi() {
		$this->open("li", new _hx_array(array()));
	}
	public function li($content) {
		$this->open("li", new _hx_array(array()));
		$this->text($content);
		$this->close();
	}
	public function openUl($id, $className) {
		$this->open("ul", new _hx_array(array(new _hx_array(array("id", $id)), new _hx_array(array("class", $className)))));
	}
	public function imageClass($src, $title, $className) {
		$this->openclose("img", new _hx_array(array(new _hx_array(array("src", $src)), new _hx_array(array("alt", $title)), new _hx_array(array("title", $title)), new _hx_array(array("class", $className)))));
	}
	public function image($id, $src, $title, $style) {
		$this->openclose("img", new _hx_array(array(new _hx_array(array("id", $id)), new _hx_array(array("src", $src)), new _hx_array(array("alt", $title)), new _hx_array(array("title", $title)), new _hx_array(array("style", $style)))));
	}
	public function textarea($id, $name, $value, $className, $lang) {
		$this->open("textarea", new _hx_array(array(new _hx_array(array("id", $id)), new _hx_array(array("name", $name)), new _hx_array(array("class", $className)), new _hx_array(array("lang", $lang)))));
		$this->text($value);
		$this->close();
	}
	public function input($type, $id, $name, $value, $title, $className) {
		$this->openclose("input", new _hx_array(array(new _hx_array(array("type", $type)), new _hx_array(array("id", $id)), new _hx_array(array("name", $name)), new _hx_array(array("value", $value)), new _hx_array(array("title", $title)), new _hx_array(array("class", $className)))));
	}
	public function js($code) {
		$this->open("script", new _hx_array(array(new _hx_array(array("type", "text/javascript")))));
		$this->text($code);
		$this->close();
	}
	public function openSpan($id, $className) {
		$this->open("span", new _hx_array(array(new _hx_array(array("id", $id)), new _hx_array(array("class", $className)))));
	}
	public function openDivClass($id, $className) {
		$this->open("div", new _hx_array(array(new _hx_array(array("id", $id)), new _hx_array(array("class", $className)))));
	}
	public function openDiv($id) {
		$this->open("div", new _hx_array(array(new _hx_array(array("id", $id)))));
	}
	public function raw($raw) {
		$this->s->add($raw);
	}
	public function getString() {
		if($this->tags->length > 0) {
			throw new HException("Malformed XML: tag " . $this->tags->pop() . " is not closed.");
		}
		return $this->s->b;
	}
	public function close() {
		if($this->tags->length === 0) {
			throw new HException("Malformed XML. No tag to close!");
		}
		$this->s->add("</");
		$this->s->add($this->tags->pop());
		$this->s->add(">");
	}
	public function text($text) {
		if($text !== null) {
			$this->s->add(com_wiris_util_xml_WXmlUtils::htmlEscape($text));
		}
	}
	public function textEm($text) {
		$this->open("em", null);
		$this->text($text);
		$this->close();
	}
	public function openclose($name, $attributes) {
		$this->start($name, $attributes);
		$this->s->add("/>");
	}
	public function open($name, $attributes) {
		$this->tags->push($name);
		$this->start($name, $attributes);
		$this->s->add(">");
	}
	public function start($name, $attributes) {
		$this->s->add("<");
		$this->s->add($name);
		if($attributes !== null) {
			$i = null;
			{
				$_g1 = 0; $_g = $attributes->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					if(_hx_array_get($attributes, $i1)->length === 2 && $attributes[$i1][0] !== null && $attributes[$i1][1] !== null) {
						$this->s->add(" ");
						$this->s->add($attributes[$i1][0]);
						$this->s->add("=\"");
						$this->s->add(com_wiris_util_xml_WXmlUtils::htmlEscape($attributes[$i1][1]));
						$this->s->add("\"");
					}
					unset($i1);
				}
			}
		}
	}
	public $tags;
	public $s;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	function __toString() { return 'com.wiris.quizzes.impl.HTML'; }
}
