<?php

class com_wiris_quizzes_impl_HTMLTableTools {
	public function __construct($separator) {
		if(!php_Boot::$skip_constructor) {
		$this->separator = $separator;
	}}
	public function removeRootTag($xml, $name) {
		if(StringTools::startsWith($xml, "<" . $name) && StringTools::endsWith($xml, "</" . $name . ">")) {
			$xml = _hx_substr($xml, _hx_index_of($xml, ">", null) + 1, null);
			$xml = _hx_substr($xml, 0, strlen($xml) - (strlen($name) + 3));
		}
		return $xml;
	}
	public function parseTabularVariableMathML($value) {
		$parts = new _hx_array(array());
		$value = $this->removeRootTag($value, "math");
		$value = $this->removeRootTag($value, "mrow");
		$value = $this->removeRootTag($value, "mfenced");
		$value = $this->removeRootTag($value, "mrow");
		$level = 0;
		$end = 0;
		$start = null;
		$lastindex = 0;
		while(($start = _hx_index_of($value, "<", $end)) !== -1) {
			$closing = false;
			$end = _hx_index_of($value, ">", $start);
			if(_hx_char_code_at($value, $start + 1) === _hx_char_code_at("/", 0)) {
				$start++;
				$closing = true;
			}
			$name = _hx_substr($value, $start + 1, $end - $start - 1);
			if(!$closing) {
				$aux = _hx_index_of($name, " ", null);
				if($aux !== -1) {
					$name = _hx_substr($name, 0, $aux);
				}
				if($name === "mo" && !$closing) {
					$op = _hx_substr($value, $end + 1, 1);
					if($op === "{" || $op === "[" || $op === "(") {
						$level++;
					} else {
						if($op === "}" || $op === "]" || $op === ")") {
							$level--;
						} else {
							if($op === $this->separator && $level === 0) {
								$parts->push(com_wiris_quizzes_impl_HTMLTools::addMathTag(_hx_substr($value, $lastindex, $start - $lastindex)));
								$lastindex = $end + 7;
							}
						}
					}
					unset($op);
				}
				unset($aux);
			}
			if($name === "mfenced") {
				$level += (($closing) ? -1 : 1);
			}
			unset($name,$closing);
		}
		$parts->push(com_wiris_quizzes_impl_HTMLTools::addMathTag(_hx_substr($value, $lastindex, null)));
		return $parts;
	}
	public function parseTabularVariableText($value) {
		$parts = new _hx_array(array());
		$value = _hx_substr($value, 1, strlen($value) - 2);
		$s = (($this->separator !== null) ? _hx_char_code_at($this->separator, 0) : _hx_char_code_at(",", 0));
		$i = null;
		$level = 0;
		$token = new StringBuf();
		$open = new _hx_array(array(_hx_char_code_at("{", 0), _hx_char_code_at("[", 0), _hx_char_code_at("(", 0)));
		$close = new _hx_array(array(_hx_char_code_at("}", 0), _hx_char_code_at("]", 0), _hx_char_code_at(")", 0)));
		{
			$_g1 = 0; $_g = strlen($value);
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$c = _hx_char_code_at($value, $i1);
				if($c === $s && $level === 0) {
					$parts->push($token->b);
					$token = new StringBuf();
				} else {
					$token->b .= chr($c);
					if($c === $open[0] || $c === $open[1] || $c === $open[2]) {
						$level++;
					} else {
						if($c === $close[0] || $c === $close[1] || $c === $close[2]) {
							$level--;
						}
					}
				}
				unset($i1,$c);
			}
		}
		$parts->push($token->b);
		return $parts;
	}
	public function parseTabularVariable($name, $variables) {
		$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML);
		if($v !== null && $v->exists($name)) {
			return $this->parseTabularVariableMathML($v->get($name));
		}
		$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT);
		if($v !== null && $v->exists($name)) {
			return $this->parseTabularVariableText($v->get($name));
		}
		return null;
	}
	public function parseMathMLMatrix($mathml) {
		$res = new _hx_array(array());
		$start = null;
		$end = 0;
		while(($start = _hx_index_of($mathml, "<mtr", $end)) !== -1) {
			$start = _hx_index_of($mathml, ">", $start) + 1;
			$end = _hx_index_of($mathml, "</mtr>", $start);
			$row = _hx_substr($mathml, $start, $end - $start);
			$a = new _hx_array(array());
			$rstart = null;
			$rend = 0;
			while(($rstart = _hx_index_of($row, "<mtd", $rend)) !== -1) {
				$rstart = _hx_index_of($row, ">", $rstart) + 1;
				$rend = _hx_index_of($row, "</mtd>", $rstart);
				$cell = com_wiris_quizzes_impl_HTMLTools::addMathTag(_hx_substr($row, $rstart, $rend - $rstart));
				$a->push($cell);
				unset($cell);
			}
			$res->push($a);
			unset($rstart,$row,$rend,$a);
		}
		return $res;
	}
	public function parseTabularVariable2d($name, $variables) {
		$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML);
		if($v !== null && $v->exists($name)) {
			$mathml = $v->get($name);
			if(_hx_index_of($mathml, "<mtable", null) !== -1) {
				return $this->parseMathMLMatrix($mathml);
			} else {
				$res = new _hx_array(array());
				$rows = $this->parseTabularVariableMathML($mathml);
				$i = null;
				{
					$_g1 = 0; $_g = $rows->length;
					while($_g1 < $_g) {
						$i1 = $_g1++;
						$res->push($this->parseTabularVariableMathML($rows[$i1]));
						unset($i1);
					}
				}
				return $res;
			}
		}
		$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT);
		if($v !== null && $v->exists($name)) {
			$res = new _hx_array(array());
			$rows = $this->parseTabularVariableText($v->get($name));
			$i = null;
			{
				$_g1 = 0; $_g = $rows->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$res->push($this->parseTabularVariableText($rows[$i1]));
					unset($i1);
				}
			}
			return $res;
		}
		return null;
	}
	public function isTabularMathMLVariable2d($value) {
		$this->initTabularERegs();
		return $this->mathmllist2d->match($value) || $this->mathmlmatrix->match($value);
	}
	public function isTabularTextVariable2d($value) {
		$this->initTabularERegs();
		return $this->textlist2d->match($value);
	}
	public function isTabularTextVariable($value) {
		$this->initTabularERegs();
		return $this->textlist->match($value);
	}
	public function isTabularMathMLVariable($value) {
		$this->initTabularERegs();
		return $this->mathmllist->match($value);
	}
	public function initMathMLTabularERegs() {
		$om = "(<math[^>]*>)?(<mrow[^>]*>)?";
		$cm = "(</mrow>)?(</math>)?";
		$ol = "<mfenced(\\s+open\\s*=\\s*\"[\\[\\{]\"|\\s+close\\s*=\\s*\"[\\]\\}]\"){2}\\s*><mrow>";
		$cl = "</mrow></mfenced>";
		$s = "<mo>\\" . $this->separator . "</mo>";
		$x = "[^\\" . $this->separator . "]*";
		$list = $ol . "(" . $x . $s . ")*" . $x . $cl;
		$list2d = $ol . "(" . $list . $s . ")*" . $list . $cl;
		$this->mathmllist = new EReg($om . $list . $cm, "m");
		$this->mathmllist2d = new EReg($om . $list2d . $cm, "m");
		$ot = "<mfenced><mtable>";
		$ct = "</mtable></mfenced>";
		$cell = "<mtd>.*?</mtd>";
		$row = "<mtr>" . "(" . $cell . ")+" . "</mtr>";
		$matrix = $ot . "(" . $row . ")+" . $ct;
		$this->mathmlmatrix = new EReg($om . $matrix . $cm, "g");
	}
	public function initTextTabularERegs() {
		$s = "\\" . $this->separator;
		$o = "[\\[\\{]";
		$c = "[\\}\\]]";
		$x = "[^\\[\\{\\}\\]" . $s . "]*";
		$list = $o . "(" . $x . $s . ")*" . $x . $c;
		$list2d = $o . "(" . $list . $s . ")*" . $list . $c;
		$this->textlist = new EReg($list, "g");
		$this->textlist2d = new EReg($list2d, "g");
	}
	public function initTabularERegs() {
		if($this->textlist === null) {
			$this->initTextTabularERegs();
			$this->initMathMLTabularERegs();
		}
	}
	public function isCellExpandableImpl($cell, $variables, $is2d) {
		if(_hx_index_of($cell, "<input", null) !== -1) {
			return false;
		}
		$h = new com_wiris_quizzes_impl_HTMLTools();
		$start = null;
		$end = 0;
		while(($start = _hx_index_of($cell, "<math", $end)) !== -1) {
			$end = _hx_index_of($cell, "</math>", $start) + 7;
			if(!$h->isTokensMathML(_hx_substr($cell, $start, $end - $start))) {
				return false;
			}
		}
		$content = trim(com_wiris_quizzes_impl_HTMLTableTools::stripTags($cell));
		if(StringTools::startsWith($content, "#")) {
			$content = _hx_substr($content, 1, null);
			if(_hx_index_of($cell, "#" . $content, null) !== -1) {
				$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML);
				if($v !== null && $v->exists($content)) {
					if($is2d && $this->isTabularMathMLVariable2d($v->get($content)) || !$is2d && $this->isTabularMathMLVariable($v->get($content))) {
						return true;
					}
				}
				$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT);
				if($v !== null && $v->exists($content)) {
					if($is2d && $this->isTabularTextVariable2d($v->get($content)) || !$is2d && $this->isTabularTextVariable($v->get($content))) {
						return true;
					}
				}
			}
		}
		return false;
	}
	public function isCellExpandable($cell, $variables) {
		return $this->isCellExpandableImpl($cell, $variables, false);
	}
	public function isCellExpandable2d($cell, $variables) {
		return $this->isCellExpandableImpl($cell, $variables, true);
	}
	public function setClass($element, $name) {
		$end = _hx_index_of($element, ">", null);
		if($end !== -1) {
			$tag = _hx_substr($element, 0, $end + 1);
			$e = new EReg("<\\w+[^>]*\\s+class\\s*=\\s*\"[^\"]*\"[^>]*>", "g");
			if(!$e->match($tag)) {
				$tag = _hx_substr($tag, 0, $end) . " class=\"" . $name . "\">";
				$element = $tag . _hx_substr($element, $end + 1, null);
			}
		}
		return $element;
	}
	public function expandVertical($rows, $grid, $variables) {
		$i = null;
		{
			$_g1 = 0; $_g = $grid->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$thisrow = true;
				$j = 0;
				while($thisrow && $j < _hx_array_get($grid, $i1)->length) {
					$thisrow = $this->isCellExpandable($grid[$i1][$j], $variables);
					$j++;
				}
				if($thisrow && $j > 0) {
					$opentds = new _hx_array(array());
					$closetds = new _hx_array(array());
					$vars = new _hx_array(array());
					$n = -1;
					{
						$_g3 = 0; $_g2 = _hx_array_get($grid, $i1)->length;
						while($_g3 < $_g2) {
							$j1 = $_g3++;
							$model = $this->getModel($grid[$i1][$j1]);
							$placeholder = trim(com_wiris_quizzes_impl_HTMLTableTools::stripTags($model));
							$pos = _hx_index_of($model, $placeholder, null);
							$opentds[$j1] = _hx_substr($model, 0, $pos);
							$closetds[$j1] = _hx_substr($model, $pos + strlen($placeholder), null);
							$parsed = $this->parseTabularVariable(_hx_substr($placeholder, 1, null), $variables);
							$vars->push($parsed);
							if($parsed->length > $n) {
								$n = $parsed->length;
							}
							unset($pos,$placeholder,$parsed,$model,$j1);
						}
						unset($_g3,$_g2);
					}
					$original = $rows[2 * $i1 + 1];
					$bounds = $this->rowBounds($original);
					$row = new StringBuf();
					$k = null;
					{
						$_g2 = 0;
						while($_g2 < $n) {
							$k1 = $_g2++;
							$row->add($bounds[0]);
							{
								$_g4 = 0; $_g3 = $opentds->length;
								while($_g4 < $_g3) {
									$j1 = $_g4++;
									$row->add($opentds[$j1]);
									if($k1 < _hx_array_get($vars, $j1)->length) {
										$row->add($vars[$j1][$k1]);
									}
									$row->add($closetds[$j1]);
									unset($j1);
								}
								unset($_g4,$_g3);
							}
							$row->add($bounds[1]);
							unset($k1);
						}
						unset($_g2);
					}
					$rows[2 * $i1 + 1] = $row->b;
					return $rows->join("");
					unset($vars,$row,$original,$opentds,$n,$k,$closetds,$bounds);
				}
				unset($thisrow,$j,$i1);
			}
		}
		return null;
	}
	public function reconstructHorizontalExpand($rows, $grid) {
		$newTable = new StringBuf();
		$i = null;
		{
			$_g1 = 0; $_g = $grid->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$newTable->add($rows[2 * $i1]);
				$row = $rows[2 * $i1 + 1];
				$bounds = $this->rowBounds($row);
				$newTable->add($bounds[0]);
				$j = null;
				{
					$_g3 = 0; $_g2 = _hx_array_get($grid, $i1)->length;
					while($_g3 < $_g2) {
						$j1 = $_g3++;
						$newTable->add($grid[$i1][$j1]);
						unset($j1);
					}
					unset($_g3,$_g2);
				}
				$newTable->add($bounds[1]);
				unset($row,$j,$i1,$bounds);
			}
		}
		$newTable->add($rows[2 * $grid->length]);
		return $newTable->b;
	}
	public function joinTds($model, $values) {
		$placeholder = trim(com_wiris_quizzes_impl_HTMLTableTools::stripTags($model));
		$pos = _hx_index_of($model, $placeholder, null);
		$prefix = _hx_substr($model, 0, $pos);
		$suffix = _hx_substr($model, $pos + strlen($placeholder), null);
		$sb = new StringBuf();
		$k = null;
		{
			$_g1 = 0; $_g = $values->length;
			while($_g1 < $_g) {
				$k1 = $_g1++;
				$sb->add($prefix);
				$sb->add($values[$k1]);
				$sb->add($suffix);
				unset($k1);
			}
		}
		return $sb->b;
	}
	public function getModel($cell) {
		if(_hx_index_of($cell, "<math", null) === -1) {
			return $cell;
		}
		$placeholder = trim(com_wiris_quizzes_impl_HTMLTableTools::stripTags($cell));
		$pos = _hx_index_of($cell, $placeholder, null);
		$prefix = _hx_substr($cell, 0, $pos);
		$suffix = _hx_substr($cell, $pos + strlen($placeholder), null);
		if(($pos = _hx_index_of($prefix, "<math", null)) !== -1) {
			$prefix = _hx_substr($prefix, 0, $pos);
		}
		if(($pos = _hx_index_of($suffix, "</math>", null)) !== -1) {
			$suffix = _hx_substr($suffix, $pos + 7, null);
		}
		return $prefix . $placeholder . $suffix;
	}
	public function expandHorizontal($rows, $grid, $variables) {
		$j = 0;
		$end = false;
		while(!$end) {
			$thiscolumn = true;
			$i = 0;
			while($i < $grid->length && $thiscolumn && !$end) {
				$thiscell = false;
				if($j < _hx_array_get($grid, $i)->length) {
					$thiscell = $this->isCellExpandable($grid[$i][$j], $variables);
				} else {
					$end = true;
				}
				$thiscolumn = $thiscolumn && $thiscell;
				$i++;
				unset($thiscell);
			}
			$end = $end || $i === 0;
			if($thiscolumn && !$end) {
				$end = true;
				{
					$_g1 = 0; $_g = $grid->length;
					while($_g1 < $_g) {
						$i1 = $_g1++;
						$model = $this->getModel($grid[$i1][$j]);
						$parsed = $this->parseTabularVariable(_hx_substr(trim(com_wiris_quizzes_impl_HTMLTableTools::stripTags($model)), 1, null), $variables);
						$tds = $this->joinTds($model, $parsed);
						$grid[$i1][$j] = $tds;
						unset($tds,$parsed,$model,$i1);
					}
					unset($_g1,$_g);
				}
				return $this->reconstructHorizontalExpand($rows, $grid);
			}
			$j++;
			unset($thiscolumn,$i);
		}
		return null;
	}
	public function expand2d($rows, $grid, $variables) {
		$expand = $this->expandVertical2d($rows, $grid, $variables);
		if($expand === null) {
			$expand = $this->expandHorizontal2d($rows, $grid, $variables);
		}
		if($expand === null) {
			$expand = $this->expandBoth($rows, $grid, $variables);
		}
		return $expand;
	}
	public function expandBoth($rows, $grid, $variables) {
		$i = null;
		$j = null;
		$vars = new _hx_array(array());
		$expand = true;
		$i = 0;
		while($expand && $i < $grid->length) {
			$nrows = -1;
			$row = $grid[$i];
			$vars->push(new _hx_array(array()));
			$j = 0;
			while($expand && $j < $row->length) {
				$cell = $row[$j];
				if($this->isCellExpandable2d($cell, $variables)) {
					$name = _hx_substr(trim(com_wiris_quizzes_impl_HTMLTableTools::stripTags($cell)), 1, null);
					$p = $this->parseTabularVariable2d($name, $variables);
					_hx_array_get($vars, $i)->push($p);
					if($nrows === -1) {
						$nrows = $p->length;
					} else {
						if($nrows !== $p->length) {
							$expand = false;
						}
					}
					unset($p,$name);
				} else {
					if($this->isCellExpandable($cell, $variables)) {
						$name = _hx_substr(trim(com_wiris_quizzes_impl_HTMLTableTools::stripTags($cell)), 1, null);
						$p = $this->parseTabularVariable($name, $variables);
						if($nrows === -1) {
							if($row->length === 1) {
								$nrows = 1;
							} else {
								$nrows = $p->length;
							}
						}
						$pp = null;
						if($nrows === 1) {
							$pp = new _hx_array(array());
							$pp->push($p);
							_hx_array_get($vars, $i)->push($pp);
						} else {
							if($nrows === $p->length) {
								$pp = $this->transposeColumn($p);
								_hx_array_get($vars, $i)->push($pp);
							} else {
								$expand = false;
							}
						}
						unset($pp,$p,$name);
					} else {
						$expand = false;
					}
				}
				$j++;
				unset($cell);
			}
			$expand = $expand && $j > 0;
			$i++;
			unset($row,$nrows);
		}
		if($expand && $i > 0) {
			{
				$_g1 = 0; $_g = $grid->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$original = $rows[2 * $i1 + 1];
					$bounds = $this->rowBounds($original);
					$sb = new StringBuf();
					$k = null;
					$first = $vars[$i1][0];
					{
						$_g3 = 0; $_g2 = $first->length;
						while($_g3 < $_g2) {
							$k1 = $_g3++;
							$sb->add($bounds[0]);
							{
								$_g5 = 0; $_g4 = _hx_array_get($grid, $i1)->length;
								while($_g5 < $_g4) {
									$j1 = $_g5++;
									$model = $this->getModel($grid[$i1][$j1]);
									$x = $vars[$i1][$j1];
									$tds = $this->joinTds($model, $x[$k1]);
									$sb->add($tds);
									unset($x,$tds,$model,$j1);
								}
								unset($_g5,$_g4);
							}
							$sb->add($bounds[1]);
							unset($k1);
						}
						unset($_g3,$_g2);
					}
					$rows[2 * $i1 + 1] = $sb->b;
					unset($sb,$original,$k,$i1,$first,$bounds);
				}
			}
			return $rows->join("");
		}
		return null;
	}
	public function expandHorizontal2d($rows, $grid, $variables) {
		$i = null;
		$j = 0;
		$end = $grid->length === 0;
		while(!$end) {
			$thiscolumn = true;
			$i = 0;
			while($i < $grid->length && $thiscolumn && !$end) {
				$thiscell = false;
				if($j < _hx_array_get($grid, $i)->length) {
					$cell = $grid[$i][$j];
					$thiscell = $this->isCellExpandable2d($cell, $variables);
					if($thiscell) {
						$name = _hx_substr(trim(com_wiris_quizzes_impl_HTMLTableTools::stripTags($cell)), 1, null);
						$p = $this->parseTabularVariable2d($name, $variables);
						$thiscell = $this->isSubgridEmpty($grid, $i, $j, $p->length, 1);
						$i += $p->length;
						unset($p,$name);
					}
					unset($cell);
				} else {
					$end = true;
				}
				$thiscolumn = $thiscolumn && $thiscell;
				unset($thiscell);
			}
			if($thiscolumn && !$end) {
				$end = true;
				$i = 0;
				while($i < $grid->length) {
					$model = $grid[$i][$j];
					$name = _hx_substr(trim(com_wiris_quizzes_impl_HTMLTableTools::stripTags($model)), 1, null);
					$p = $this->parseTabularVariable2d($name, $variables);
					$k = null;
					{
						$_g1 = 0; $_g = $p->length;
						while($_g1 < $_g) {
							$k1 = $_g1++;
							$tds = $this->joinTds($model, $p[$k1]);
							$grid[$i + $k1][$j] = $tds;
							unset($tds,$k1);
						}
						unset($_g1,$_g);
					}
					$i += $p->length;
					unset($p,$name,$model,$k);
				}
				return $this->reconstructHorizontalExpand($rows, $grid);
			}
			$j++;
			unset($thiscolumn);
		}
		return null;
	}
	public function expandVertical2d($rows, $grid, $variables) {
		$i = null;
		{
			$_g1 = 0; $_g = $grid->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$row = $grid[$i1];
				$thisrow = true;
				$n = -1;
				$j = 0;
				while($thisrow && $j < $row->length) {
					$cell = $row[$j];
					if($this->isCellExpandable2d($cell, $variables)) {
						$name = _hx_substr(trim(com_wiris_quizzes_impl_HTMLTableTools::stripTags($cell)), 1, null);
						$p = $this->parseTabularVariable2d($name, $variables);
						if($p->length > $n) {
							$n = $p->length;
						}
						if($this->isSubgridEmpty($grid, $i1, $j, 1, _hx_array_get($p, 0)->length)) {
							$j += _hx_array_get($p, 0)->length;
						} else {
							$thisrow = false;
						}
						unset($p,$name);
					} else {
						$thisrow = false;
					}
					unset($cell);
				}
				if($thisrow && $j > 0) {
					$opentds = new _hx_array(array());
					$closetds = new _hx_array(array());
					$vars = new _hx_array(array());
					$k = null;
					{
						$_g2 = 0;
						while($_g2 < $n) {
							$k1 = $_g2++;
							$vars[$k1] = new _hx_array(array());
							unset($k1);
						}
						unset($_g2);
					}
					$j = 0;
					while($j < $row->length) {
						$model = $grid[$i1][$j];
						$placeholder = trim(com_wiris_quizzes_impl_HTMLTableTools::stripTags($model));
						$pos = _hx_index_of($model, $placeholder, null);
						$opentds[$j] = _hx_substr($model, 0, $pos);
						$closetds[$j] = _hx_substr($model, $pos + strlen($placeholder), null);
						$name = _hx_substr($placeholder, 1, null);
						$p = $this->parseTabularVariable2d($name, $variables);
						{
							$_g3 = 0; $_g2 = _hx_array_get($p, 0)->length;
							while($_g3 < $_g2) {
								$k1 = $_g3++;
								$opentds[$j + $k1] = $opentds[$j];
								$closetds[$j + $k1] = $closetds[$j];
								$l = null;
								{
									$_g5 = 0; $_g4 = $p->length;
									while($_g5 < $_g4) {
										$l1 = $_g5++;
										$vars[$l1][$j + $k1] = $p[$l1][$k1];
										unset($l1);
									}
									unset($_g5,$_g4);
								}
								unset($l,$k1);
							}
							unset($_g3,$_g2);
						}
						$j += _hx_array_get($p, 0)->length;
						unset($pos,$placeholder,$p,$name,$model);
					}
					$original = $rows[2 * $i1 + 1];
					$bounds = $this->rowBounds($original);
					$s = new StringBuf();
					{
						$_g2 = 0;
						while($_g2 < $n) {
							$k1 = $_g2++;
							$s->add($bounds[0]);
							{
								$_g4 = 0; $_g3 = $row->length;
								while($_g4 < $_g3) {
									$j1 = $_g4++;
									$s->add($opentds[$j1]);
									$s->add($vars[$k1][$j1]);
									$s->add($closetds[$j1]);
									unset($j1);
								}
								unset($_g4,$_g3);
							}
							$s->add($bounds[1]);
							unset($k1);
						}
						unset($_g2);
					}
					$rows[2 * $i1 + 1] = $s->b;
					return $rows->join("");
					unset($vars,$s,$original,$opentds,$k,$closetds,$bounds);
				}
				unset($thisrow,$row,$n,$j,$i1);
			}
		}
		return null;
	}
	public function expandNoGrow($rows, $grid, $variables) {
		$expanded = false;
		$i = null;
		{
			$_g1 = 0; $_g = $grid->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$j = null;
				{
					$_g3 = 0; $_g2 = _hx_array_get($grid, $i1)->length;
					while($_g3 < $_g2) {
						$j1 = $_g3++;
						$cell = $grid[$i1][$j1];
						if($this->isCellExpandable2d($cell, $variables)) {
							$name = _hx_substr(trim(com_wiris_quizzes_impl_HTMLTableTools::stripTags($cell)), 1, null);
							$p = $this->parseTabularVariable2d($name, $variables);
							if($this->isSubgridEmpty($grid, $i1, $j1, $p->length, _hx_array_get($p, 0)->length)) {
								$this->expandOnEmptySubgrid($grid, $i1, $j1, $p);
								$expanded = true;
							}
							unset($p,$name);
						} else {
							if($this->isCellExpandable($cell, $variables)) {
								$name = _hx_substr(trim(com_wiris_quizzes_impl_HTMLTableTools::stripTags($cell)), 1, null);
								$p = $this->parseTabularVariable($name, $variables);
								if($this->isSubgridEmpty($grid, $i1, $j1, 1, $p->length)) {
									$row = new _hx_array(array());
									$row->push($p);
									$this->expandOnEmptySubgrid($grid, $i1, $j1, $row);
									$expanded = true;
									unset($row);
								} else {
									if($this->isSubgridEmpty($grid, $i1, $j1, $p->length, 1)) {
										$column = $this->transposeColumn($p);
										$this->expandOnEmptySubgrid($grid, $i1, $j1, $column);
										$expanded = true;
										unset($column);
									}
								}
								unset($p,$name);
							}
						}
						unset($j1,$cell);
					}
					unset($_g3,$_g2);
				}
				unset($j,$i1);
			}
		}
		if($expanded) {
			return $this->reconstructHorizontalExpand($rows, $grid);
		}
		return null;
	}
	public function transposeColumn($p) {
		$column = new _hx_array(array());
		$k = null;
		{
			$_g1 = 0; $_g = $p->length;
			while($_g1 < $_g) {
				$k1 = $_g1++;
				$a = new _hx_array(array());
				$a->push($p[$k1]);
				$column->push($a);
				unset($k1,$a);
			}
		}
		return $column;
	}
	public function expandOnEmptySubgrid($grid, $i, $j, $p) {
		$k = null;
		{
			$_g1 = 0; $_g = $p->length;
			while($_g1 < $_g) {
				$k1 = $_g1++;
				$l = null;
				{
					$_g3 = 0; $_g2 = _hx_array_get($p, $k1)->length;
					while($_g3 < $_g2) {
						$l1 = $_g3++;
						$cell = $grid[$i + $k1][$j + $l1];
						$prefix = _hx_substr($cell, 0, _hx_index_of($cell, ">", null) + 1);
						$suffix = _hx_substr($cell, _hx_last_index_of($cell, "<", null), null);
						$grid[$i + $k1][$j + $l1] = $prefix . $p[$k1][$l1] . $suffix;
						unset($suffix,$prefix,$l1,$cell);
					}
					unset($_g3,$_g2);
				}
				unset($l,$k1);
			}
		}
	}
	public function isEmptyCell($cell) {
		$cell = com_wiris_quizzes_impl_HTMLTableTools::stripTags($cell);
		$cell = com_wiris_util_xml_WXmlUtils::htmlUnescape($cell);
		$cell = str_replace("&nbsp;", "", $cell);
		$cell = str_replace(com_wiris_quizzes_impl_HTMLTableTools_0($this, $cell), "", $cell);
		$cell = trim($cell);
		return $cell === "";
	}
	public function isSubgridEmpty($grid, $i, $j, $w, $h) {
		if($i + $w > $grid->length) {
			return false;
		}
		$k = null;
		{
			$_g1 = $i; $_g = $i + $w;
			while($_g1 < $_g) {
				$k1 = $_g1++;
				if($j + $h > _hx_array_get($grid, $k1)->length) {
					return false;
				}
				$l = null;
				{
					$_g3 = $j; $_g2 = $j + $h;
					while($_g3 < $_g2) {
						$l1 = $_g3++;
						if($k1 !== $i || $l1 !== $j) {
							if(!$this->isEmptyCell($grid[$k1][$l1])) {
								return false;
							}
						}
						unset($l1);
					}
					unset($_g3,$_g2);
				}
				unset($l,$k1);
			}
		}
		return true;
	}
	public function parseTableCells($rows) {
		$grid = new _hx_array(array());
		$i = 1;
		while($i < $rows->length) {
			$cells = new _hx_array(array());
			$row = $rows[$i];
			$tdstart = null;
			$tdend = 0;
			while(($tdstart = $this->tdStartPosition($row, $tdend)) !== -1) {
				$tdend = $this->tdEndPosition($row, $tdstart);
				if($tdend === -1) {
					$tdend = strlen($row);
				} else {
					$tdend += 5;
				}
				$cells->push(_hx_substr($row, $tdstart, $tdend - $tdstart));
			}
			$grid->push($cells);
			$i += 2;
			unset($tdstart,$tdend,$row,$cells);
		}
		return $grid;
	}
	public function rowBounds($row) {
		$bounds = new _hx_array(array());
		$bounds[0] = _hx_substr($row, 0, $this->tdStartPosition($row, 0));
		$pos = com_wiris_util_type_IntegerTools::max(_hx_last_index_of($row, "</td>", null), _hx_last_index_of($row, "</th>", null)) + strlen("</th>");
		$bounds[1] = _hx_substr($row, $pos, null);
		return $bounds;
	}
	public function tdEndPosition($row, $offset) {
		$a = _hx_index_of($row, "</td>", $offset);
		$b = _hx_index_of($row, "</th>", $offset);
		return (($b === -1) ? $a : (($a === -1) ? $b : com_wiris_util_type_IntegerTools::min($a, $b)));
	}
	public function tdStartPosition($row, $offset) {
		$pos = -1;
		$start = new _hx_array(array("<td ", "<td>", "<th ", "<th>"));
		$i = null;
		{
			$_g1 = 0; $_g = $start->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$c = _hx_index_of($row, $start[$i1], $offset);
				if($c !== -1 && $c < $pos || $pos === -1) {
					$pos = $c;
				}
				unset($i1,$c);
			}
		}
		return $pos;
	}
	public function splitTableRows($table) {
		$rows = new _hx_array(array());
		$trend = 0;
		$trstart = null;
		while(($trstart = _hx_index_of($table, "<tr", $trend)) !== -1) {
			$rows->push(_hx_substr($table, $trend, $trstart - $trend));
			$trend = _hx_index_of($table, "</tr>", $trstart);
			if($trend === -1) {
				$last = $rows[$rows->length - 1];
				$rows[$rows->length - 1] = $last . _hx_substr($table, $trstart, null);
				$trend = strlen($table);
				unset($last);
			} else {
				$trend += strlen("</tr>");
				$rows->push(_hx_substr($table, $trstart, $trend - $trstart));
			}
		}
		if($trend < strlen($table)) {
			$rows->push(_hx_substr($table, $trend, null));
		}
		return $rows;
	}
	public function replaceVariablesInsideHTMLTables($html, $variables) {
		$tend = 0;
		$tstart = null;
		while(($tstart = _hx_index_of($html, "<table", $tend)) !== -1) {
			$tend = _hx_index_of($html, "</table>", $tstart);
			if($tend === -1) {
				return $html;
			}
			$tend += strlen("</table>");
			$table = _hx_substr($html, $tstart, $tend - $tstart);
			$rows = $this->splitTableRows($table);
			$grid = $this->parseTableCells($rows);
			$expanded = $this->expandNoGrow($rows, $grid, $variables);
			if($expanded === null) {
				$expanded = $this->expand2d($rows, $grid, $variables);
				if($expanded === null) {
					$expanded = $this->expandHorizontal($rows, $grid, $variables);
					if($expanded === null) {
						$expanded = $this->expandVertical($rows, $grid, $variables);
					}
				}
			}
			if($expanded !== null) {
				$expanded = $this->setClass($expanded, "wiristable");
				$html = _hx_substr($html, 0, $tstart) . $expanded . _hx_substr($html, $tend, null);
				$tend = $tstart + strlen($expanded);
			}
			unset($table,$rows,$grid,$expanded);
		}
		return $html;
	}
	public $mathmlmatrix;
	public $mathmllist2d;
	public $textlist2d;
	public $mathmllist;
	public $textlist;
	public $separator;
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
	static function stripTags($html) {
		$e = new EReg("<[^>]*>", "g");
		return $e->replace($html, "");
	}
	function __toString() { return 'com.wiris.quizzes.impl.HTMLTableTools'; }
}
function com_wiris_quizzes_impl_HTMLTableTools_0(&$»this, &$cell) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(160);
		return $s->toString();
	}
}
