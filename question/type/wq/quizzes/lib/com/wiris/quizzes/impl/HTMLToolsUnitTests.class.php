<?php

class com_wiris_quizzes_impl_HTMLToolsUnitTests {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->h = new com_wiris_quizzes_impl_HTMLTools();
		$this->h->setAnswerKeyword("answer");
	}}
	public function unitTestParameter() {
		$inputs = new _hx_array(array("<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parameter</mi><mo>&nbsp;</mo><mi>answer</mi><mo>&nbsp;</mo><mo>=</mo><mo>&nbsp;</mo><mn>123</mn></math></input></command></group></library></session>", "<wiriscalc version=\"3.1\"><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>UntitledÂ calc</mtext></math></title><properties><property name=\"lang\">en</property></properties><session version=\"3.0\" lang=\"en\"><task><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>SheetÂ 1</mtext></math></title><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parameter</mi><mo>&#xA0;</mo><mi>answer</mi><mo>=</mo><mn>123</mn></math></input></command></group></task></session></wiriscalc>", "<wiriscalc version=\"3.1\"><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>CalcÂ semÂ tÃ­tulo</mtext></math></title><properties><property name=\"lang\">pt</property></properties><session version=\"3.0\" lang=\"pt\"><task><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>HojaÂ 1</mtext></math></title><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parÃ¢metro</mi><mo>&#xA0;</mo><mi>resposta</mi><mo>=</mo><mn>123</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"/></input></command></group></task></session></wiriscalc>", "<wiriscalc version=\"3.1\"><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>CalcÂ semÂ tÃ­tulo</mtext></math></title><properties><property name=\"lang\">pt</property></properties><session version=\"3.0\" lang=\"pt\"><task><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>HojaÂ 1</mtext></math></title><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parametro</mi><mo>&#xA0;</mo><mi>resposta</mi><mo>=</mo><mn>123</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"/></input></command></group></task></session></wiriscalc>", "<wiriscalc version=\"3.1\"><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>CalcÂ sinÂ tÃ­tulo</mtext></math></title><properties><property name=\"lang\">es</property><property name=\"precision\">4</property><property name=\"show_title\">false</property><property name=\"use_degrees\">false</property></properties><session version=\"3.0\" lang=\"es\"><task><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>SheetÂ 1</mtext></math></title><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>par&#xE1;metro</mi><mo>&#xA0;</mo><mi>respuesta</mi><mo>=</mo><mn>0</mn></math></input><output><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>0</mn></math></output></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi mathvariant=\"normal\">a</mi><mo>=</mo><mi>respuesta</mi><mo>+</mo><mn>3</mn></math></input><output><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>3</mn></math></output></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"/></input></command></group></task></session><constructions><construction group=\"1\">{&quot;elements&quot;:[],&quot;constraints&quot;:[],&quot;displays&quot;:[],&quot;handwriting&quot;:[]}</construction></constructions></wiriscalc>", "<wiriscalc version=\"3.1\"><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>UntitledÂ calc</mtext></math></title><properties><property name=\"lang\">en</property></properties><session version=\"3.0\" lang=\"en\"><task><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>SheetÂ 1</mtext></math></title><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parameter</mi><mo>&#xA0;</mo><mi>answer1</mi><mo>=</mo><mo>-</mo><mn>1</mn></math></input><output><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>0</mn></math></output></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parameter</mi><mo>&#xA0;</mo><mi>answer2</mi><mo>=</mo><mo>-</mo><mn>1</mn></math></input><output><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>0</mn></math></output></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi mathvariant=\"normal\">x</mi><mo>=</mo><mi>answer1</mi><mo>+</mo><mi>answer2</mi></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"/></input></command></group></task></session><constructions><construction group=\"1\">{&quot;elements&quot;:[],&quot;constraints&quot;:[],&quot;displays&quot;:[],&quot;handwriting&quot;:[]}</construction></constructions></wiriscalc>"));
		$parameters = new _hx_array(array("parameter", "parameter", "parÃ¢metro", "parÃ¢metro", "parÃ¡metro", "parameter"));
		$answers = new _hx_array(array("answer", "answer", "resposta", "resposta", "respuesta", "answer"));
		$i = null;
		{
			$_g1 = 0; $_g = $inputs->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if(!com_wiris_quizzes_impl_HTMLTools::hasCasSessionParameter($inputs[$i1], $parameters[$i1], $answers[$i1])) {
					throw new HException("Failed test");
				}
				if(com_wiris_quizzes_impl_HTMLTools::hasCasSessionParameter($inputs[$i1], $parameters[$i1], _hx_substr($answers[$i1], 0, 3))) {
					throw new HException("Failed test");
				}
				unset($i1);
			}
		}
	}
	public function unitTestStripRootTag() {
		$inputs = new _hx_array(array("<math><mi>x</mi></math>", "<math><math/></math>", "<math></math><math/>", "<math><mi>e</mi><mo>+</mo><math><mi>i</mi></math></math>", "<math><math></math><math/><math></math></math><math></math>"));
		$outputs = new _hx_array(array("<mi>x</mi>", "<math/>", "<math></math><math/>", "<mi>e</mi><mo>+</mo><math><mi>i</mi></math>", "<math><math></math><math/><math></math></math><math></math>"));
		$i = null;
		{
			$_g1 = 0; $_g = $inputs->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$result = com_wiris_util_xml_MathMLUtils::stripRootTag($inputs[$i1], "math");
				if(!($result === $outputs[$i1])) {
					throw new HException("Failed srtip root tag test: " . _hx_string_rec($i1, "") . "\x0AExpected: " . $outputs[$i1] . "\x0ABut got: " . $result);
				}
				unset($result,$i1);
			}
		}
	}
	public function unitTestUtf8() {
		$s = com_wiris_quizzes_impl_HTMLToolsUnitTests_0($this) . "a" . com_wiris_quizzes_impl_HTMLToolsUnitTests_1($this) . "a";
		$i = com_wiris_system_Utf8::getIterator($s);
		if(!$i->hasNext()) {
			com_wiris_quizzes_impl_HTMLToolsUnitTests::error();
		}
		if($i->next() !== 120120) {
			com_wiris_quizzes_impl_HTMLToolsUnitTests::error();
		}
		if(!$i->hasNext()) {
			com_wiris_quizzes_impl_HTMLToolsUnitTests::error();
		}
		if($i->next() !== 97) {
			com_wiris_quizzes_impl_HTMLToolsUnitTests::error();
		}
		if(!$i->hasNext()) {
			com_wiris_quizzes_impl_HTMLToolsUnitTests::error();
		}
		if($i->next() !== 960) {
			com_wiris_quizzes_impl_HTMLToolsUnitTests::error();
		}
		if(!$i->hasNext()) {
			com_wiris_quizzes_impl_HTMLToolsUnitTests::error();
		}
		if($i->next() !== 97) {
			com_wiris_quizzes_impl_HTMLToolsUnitTests::error();
		}
		if($i->hasNext()) {
			com_wiris_quizzes_impl_HTMLToolsUnitTests::error();
		}
	}
	public function unitTestJoinCompoundAnswer() {
		$inputs = new _hx_array(array(new _hx_array(array(new _hx_array(array("<math><mi>a</mi><mo>=</mo>", "<math><mn>1</mn></math>")), new _hx_array(array("<math><mi>b</mi><mo>=</mo>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\" xmlns:wrs=\"http://www.wiris.com/xml/mathml-extension\"><mn>0</mn><mo>.</mo><mover wrs:positionable=\"false\"><mrow wrs:positionable=\"true\"><mn>3</mn></mrow><mo>&#x23DC;</mo></mover></math>"))))));
		$outputs = new _hx_array(array("<math xmlns=\"http://www.w3.org/1998/Math/MathML\" xmlns:wrs=\"http://www.wiris.com/xml/mathml-extension\"><math><mi>a</mi><mo>=</mo><mn>1</mn><mspace linebreak=\"newline\"/><math><mi>b</mi><mo>=</mo><mn>0</mn><mo>.</mo><mover wrs:positionable=\"false\"><mrow wrs:positionable=\"true\"><mn>3</mn></mrow><mo>&#x23DC;</mo></mover></math>"));
		$i = null;
		{
			$_g1 = 0; $_g = $outputs->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$ans = $inputs[$i1];
				$answers = $ans->copy();
				$a = com_wiris_quizzes_impl_CompoundAnswerParser::joinCompoundAnswer($answers);
				if(!($a->content === $outputs[$i1])) {
					throw new HException("Failed join compound answer: " . _hx_string_rec($i1, "") . "\x0AExpected: " . $outputs[$i1] . "\x0ABut got: " . $a->content);
				}
				unset($i1,$answers,$ans,$a);
			}
		}
	}
	public function unitTestParseCompoundAnswers() {
		$inputs = new _hx_array(array("<math><mi>x</mi><mo>=</mo><semantics><mn>1</mn><annotation encoding=\"application/json\">[...]</annotation></semantics><mspace linebreak=\"newline\"/><mi>y</mi><mo>=</mo><semantics><mrow><mn>1</mn><mo>+</mo><mn>1</mn></mrow><annotation encoding=\"application/json\">[...]</annotation></semantics></math>", "<math><mi>x</mi><mo>=</mo><semantics><mn>1</mn><annotation encoding=\"text/plain\">1</annotation></semantics><mspace linebreak=\"newline\"/><mi>y</mi><mo>=</mo><semantics><mn>2</mn></mrow><annotation encoding=\"text/plain\">2</annotation></semantics></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\" xmlns:wrs=\"http://www.wiris.com/xml/mathml-extension\"><mi>a</mi><mo>=</mo><mn>0</mn><mo>.</mo><mover wrs:positionable=\"false\"><mrow wrs:positionable=\"true\"><mn>3</mn></mrow><mo>&#x23DC;</mo></mover></math>"));
		$outputs = new _hx_array(array(new _hx_array(array(new _hx_array(array("<math><mi>x</mi><mo>=</mo></math>", "<math><semantics><mn>1</mn><annotation encoding=\"application/json\">[...]</annotation></semantics></math>")), new _hx_array(array("<math><mi>y</mi><mo>=</mo></math>", "<math><semantics><mrow><mn>1</mn><mo>+</mo><mn>1</mn></mrow><annotation encoding=\"application/json\">[...]</annotation></semantics></math>")))), new _hx_array(array(new _hx_array(array("<math><mi>x</mi><mo>=</mo></math>", "1")), new _hx_array(array("<math><mi>y</mi><mo>=</mo></math>", "2")))), new _hx_array(array(new _hx_array(array("<math xmlns=\"http://www.w3.org/1998/Math/MathML\" xmlns:wrs=\"http://www.wiris.com/xml/mathml-extension\"><mi>a</mi><mo>=</mo></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\" xmlns:wrs=\"http://www.wiris.com/xml/mathml-extension\"><mn>0</mn><mo>.</mo><mover wrs:positionable=\"false\"><mrow wrs:positionable=\"true\"><mn>3</mn></mrow><mo>&#x23DC;</mo></mover></math>"))))));
		$i = null;
		{
			$_g1 = 0; $_g = $inputs->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$c = new com_wiris_quizzes_impl_MathContent();
				$c->set($inputs[$i1]);
				$result = com_wiris_quizzes_impl_CompoundAnswerParser::parseCompoundAnswer($c);
				if($result->length !== _hx_array_get($outputs, $i1)->length) {
					throw new HException("Compound answer " . _hx_string_rec($i1, "") . " length mismatch");
				}
				$j = null;
				{
					$_g3 = 0; $_g2 = $result->length;
					while($_g3 < $_g2) {
						$j1 = $_g3++;
						$line = $result[$j1];
						if($line->length !== 2) {
							throw new HException("Expecting two parts.");
						}
						if(!($line[0] === $outputs[$i1][$j1][0]) || !($line[1] === $outputs[$i1][$j1][1])) {
							throw new HException("Failed test.\x0A" . "Expected: " . $outputs[$i1][$j1][0] . ";" . $outputs[$i1][$j1][0] . "\x0A" . "Got: " . $line[0] . ";" . $line[1]);
						}
						unset($line,$j1);
					}
					unset($_g3,$_g2);
				}
				unset($result,$j,$i1,$c);
			}
		}
	}
	public function implode($a, $tok) {
		$sb = new StringBuf();
		$i = null;
		{
			$_g1 = 0; $_g = $a->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if($i1 > 0) {
					$sb->add($tok);
				}
				$sb->add($a[$i1]);
				unset($i1);
			}
		}
		return $sb->b;
	}
	public function unitTestTables() {
		$variables = new Hash();
		$t = new Hash();
		$t->set("T", "{1,2,3,4}");
		$t->set("M", "{a,b,c,d}");
		$t->set("r", "10");
		$t->set("x", "{{a,b},{c,d}}");
		$t->set("y", "[[1,2,3],[4,5,6]]");
		$t->set("z", "{{i,j}}");
		$t->set("w", "{i,j}");
		$variables->set(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT, $t);
		$m = new Hash();
		$m->set("a", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfenced open=\"[\" close=\"]\"><mrow><mn>1</mn><mo>,</mo><mn>2</mn><mo>,</mo><mn>3</mn></mrow></mfenced></math>");
		$m->set("b", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfenced open=\"{\" close=\"}\"><mrow><mfenced open=\"{\" close=\"}\"><mrow><mn>1</mn><mo>,</mo><mn>1</mn></mrow></mfenced><mo>,</mo><mfenced open=\"{\" close=\"}\"><mrow><mn>2</mn><mo>,</mo><mn>4</mn></mrow></mfenced><mo>,</mo><mfenced open=\"{\" close=\"}\"><mrow><mn>3</mn><mo>,</mo><mn>9</mn></mrow></mfenced></mrow></mfenced></math>");
		$m->set("G", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfenced><mtable><mtr><mtd><mn>1</mn></mtd><mtd><mn>0</mn></mtd></mtr><mtr><mtd><mn>0</mn></mtd><mtd><mn>1</mn></mtd></mtr></mtable></mfenced></math>");
		$m->set("X", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfenced open=\"{\" close=\"}\"><mrow><mfenced open=\"{\" close=\"}\"><mrow><mn>1</mn><mo>,</mo><mn>2</mn></mrow></mfenced><mo>,</mo><mfenced open=\"{\" close=\"}\"><mrow><mn>3</mn><mo>,</mo><mn>4</mn></mrow></mfenced></mrow></mfenced></math>");
		$m->set("Î±", "<math><mrow><mfenced close=\"}\" open=\"{\"><mrow><mn>1</mn><mo>,</mo><mn>2</mn><mo>,</mo><mn>3</mn></mrow></mfenced></mrow></math>");
		$variables->set(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML, $m);
		$html = new _hx_array(array("<table><tbody><tr><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>#</mo><mi>&#945;</mi></math></td></tr></tbody></table>", "<table><tr><td></table>", "<table><tr><td>X</td><td>#X</td><td></td></tr><tr><td>Â«mathÂ»Â«/mathÂ»</td><td></td><td></td></tr></table>", "<div><table class=\"table\"><tr class=\"tr1\"><td class=\"td1\">Header</td></tr><tr class=\"tr2\"><td class=\"td2\">#M</td></tr></table>", "<div><table><thead><tr><th>Name</th><th>Value</th></tr></thead><tbody><tr><td>#M</td><td>#T</td></tr></tbody></table></div>", "<div><table><thead><tr><td>Label:</td><td>#M</td><td></td></tr></thead><tbody><tr><td>Value:</td><td>#T</td><td>#r</td></tr></tbody></table></div>", "<div><table><tr><td>#T</td></tr></table></div>", "<div><table><tr><td>Values:</td><td>#T</td><td>#r</td></tr></table></div>", "", "<table><tr></tr></table>", "<table><tr><td>#a</td></tr></table>", "<table><tr><th>title</th></tr><tr><td>#b</td></tr></table>", "<table><tbody><tr><td>numbers</td><td> </td><td> </td></tr><tr><td>#a</td><td> </td><td> </td></tr><tr><td> </td><td> </td><td> </td></tr><tr><td> </td><td> </td><td> </td></tr></tbody></table>", "<table><tr><td>#x</td><td>#y</td></tr></table>", "<table><tr><td>#x</td><td></td><td>#y</td><td> &nbsp; </td><td>  &#xa0;</td></tr></table>", "<table><tr><td>#x</td></tr> <tr><td></td></tr> <tr><td>#z</td></tr></table>", "<table><tr><td>#w</td><td>#x</td><td></td></tr> <tr><td></td><td></td><td></td></tr></table>", "<table><thead><tr><th>A</th><th>B</th></tr></thead><tbody><tr><td>#G</td><td> </td></tr></tbody></table>", "<table><tbody><tr><td>A</td><td>#X</td></tr><tr><td>B</td><td></td></tr></tbody></table>"));
		$res = new _hx_array(array("<table class=\"wiristable\"><tbody><tr><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>1</mn></math></td><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>2</mn></math></td><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>3</mn></math></td></tr></tbody></table>", "<table><tr><td></table>", "<table class=\"wiristable\"><tr><td>X</td><td>Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«mnÂ»1Â«/mnÂ»Â«/mathÂ»</td><td>Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«mnÂ»2Â«/mnÂ»Â«/mathÂ»</td></tr><tr><td>Â«mathÂ»Â«/mathÂ»</td><td>Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«mnÂ»3Â«/mnÂ»Â«/mathÂ»</td><td>Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«mnÂ»4Â«/mnÂ»Â«/mathÂ»</td></tr></table>", "<div><table class=\"table\"><tr class=\"tr1\"><td class=\"td1\">Header</td></tr><tr class=\"tr2\"><td class=\"td2\">a</td></tr><tr class=\"tr2\"><td class=\"td2\">b</td></tr><tr class=\"tr2\"><td class=\"td2\">c</td></tr><tr class=\"tr2\"><td class=\"td2\">d</td></tr></table>", "<div><table class=\"wiristable\"><thead><tr><th>Name</th><th>Value</th></tr></thead><tbody><tr><td>a</td><td>1</td></tr><tr><td>b</td><td>2</td></tr><tr><td>c</td><td>3</td></tr><tr><td>d</td><td>4</td></tr></tbody></table></div>", "<div><table class=\"wiristable\"><thead><tr><td>Label:</td><td>a</td><td>b</td><td>c</td><td>d</td><td></td></tr></thead><tbody><tr><td>Value:</td><td>1</td><td>2</td><td>3</td><td>4</td><td>10</td></tr></tbody></table></div>", "<div><table class=\"wiristable\"><tr><td>1</td><td>2</td><td>3</td><td>4</td></tr></table></div>", "<div><table class=\"wiristable\"><tr><td>Values:</td><td>1</td><td>2</td><td>3</td><td>4</td><td>10</td></tr></table></div>", "", "<table><tr></tr></table>", "<table class=\"wiristable\"><tr><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>1</mn></math></td><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>2</mn></math></td><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>3</mn></math></td></tr></table>", "<table class=\"wiristable\"><tr><th>title</th></tr><tr><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfenced open=\"{\" close=\"}\"><mrow><mn>1</mn><mo>,</mo><mn>1</mn></mrow></mfenced></math></td></tr><tr><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfenced open=\"{\" close=\"}\"><mrow><mn>2</mn><mo>,</mo><mn>4</mn></mrow></mfenced></math></td></tr><tr><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfenced open=\"{\" close=\"}\"><mrow><mn>3</mn><mo>,</mo><mn>9</mn></mrow></mfenced></math></td></tr></table>", "<table class=\"wiristable\"><tbody><tr><td>numbers</td><td> </td><td> </td></tr><tr><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>1</mn></math></td><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>2</mn></math></td><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>3</mn></math></td></tr><tr><td> </td><td> </td><td> </td></tr><tr><td> </td><td> </td><td> </td></tr></tbody></table>", "<table class=\"wiristable\"><tr><td>a</td><td>b</td><td>1</td><td>2</td><td>3</td></tr><tr><td>c</td><td>d</td><td>4</td><td>5</td><td>6</td></tr></table>", "<table class=\"wiristable\"><tr><td>a</td><td>b</td><td>1</td><td>2</td><td>3</td></tr><tr><td>c</td><td>d</td><td>4</td><td>5</td><td>6</td></tr></table>", "<table class=\"wiristable\"><tr><td>a</td><td>b</td></tr> <tr><td>c</td><td>d</td></tr> <tr><td>i</td><td>j</td></tr></table>", "<table class=\"wiristable\"><tr><td>i</td><td>a</td><td>b</td></tr> <tr><td>j</td><td>c</td><td>d</td></tr></table>", "<table class=\"wiristable\"><thead><tr><th>A</th><th>B</th></tr></thead><tbody><tr><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>1</mn></math></td><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>0</mn></math></td></tr><tr><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>0</mn></math></td><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>1</mn></math></td></tr></tbody></table>", "<table class=\"wiristable\"><tbody><tr><td>A</td><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>1</mn></math></td><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>2</mn></math></td></tr><tr><td>B</td><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>3</mn></math></td><td><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>4</mn></math></td></tr></tbody></table>"));
		$i = null;
		{
			$_g1 = 0; $_g = $html->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$computed = $this->h->expandVariables($html[$i1], $variables);
				if(!($computed === $res[$i1])) {
					throw new HException("Failed table test " . _hx_string_rec($i1, "") . ".\x0A" . "Expected: " . $res[$i1] . "\x0A" . "Computed: " . $computed);
				}
				unset($i1,$computed);
			}
		}
	}
	public function unitTestVariableNames() {
		$html = new _hx_array(array("<math><mi>#a1</mi></math>", "<p>Calcular la corriente que circula por el condensador D del circuito adjunto. Los valores de los compenentes son: A = #A Î©;  B = #B Î©;  C = #C Î©;  D = #D Î©.</p>\x0A" . "<p><img src=\"@@PLUGINFILE@@/Circuito1.gif\" alt=\"\" width=\"424\" height=\"170\" /></p> <p>#x</p> <p>#x1</p> <p>#x2</p> <p>#x3</p> ", "<span style=\"color: #aaffaa;\">&#x32;#x_11</span>", "123 #a<ol>\x0A" . "<li><span style=\"background-color: #ffff00; font-family: 'times new roman', times, serif;\">Use inverse operations to undo Addition and Subtraction (add and/or subtract all terms from left to right)</span></li>\x0A" . "<li><span style=\"background-color: #ffff00; font-family: 'times new roman', times, serif;\">Use inverse operations to undo Multiplication and Division (multiply and/or divide all factors from left to right)</span></li>\x0A" . "</ol>", "Î©Î©Î©Î©Î©Î©Î©<div>Î©Î©Î©<p>Î©Î©#aÎ©Î©</p>Î©Î©Î©</div>Î©Î©Î©Î©Î©Î©Î©", "<math><mlongdiv longdivstyle=\"shortstackedrightright\" charalign=\"center\" charspacing=\"0px\" stackalign=\"left\"><mrow><mo>#</mo><mi>d</mi></mrow><mrow><mo>#</mo><mi>q</mi></mrow><msgroup><msrow><mo>#</mo><mi>n</mi></msrow></msgroup></mlongdiv></math>", "<math><mi>#mat_1_2</mi></math>", "<math><mi>#mat_3_2</mi></math>", "<math><mi>#mat_5</mi></math>", "<math><mi>#Clist_2</mi></math>", "<math><mi>#Clist_3</mi></math>", "<math><mi>#ListOfLists_1</mi></math>", "<math><mi>#ListOfLists_2_2</mi></math>", "<math><mi>#mat_1_e</mi></math>", "<math><mi>#mat_1__</mi></math>", "<math><mi>#Clist_</mi></math>"));
		$vars = new _hx_array(array(new _hx_array(array("a1")), new _hx_array(array("A", "B", "C", "D", "x", "x1", "x2", "x3")), new _hx_array(array("x", "x_11")), new _hx_array(array("a")), new _hx_array(array("aÎ©Î©")), new _hx_array(array("d", "n", "q")), new _hx_array(array("mat", "mat_1_2")), new _hx_array(array("mat", "mat_3_2")), new _hx_array(array("mat", "mat_5")), new _hx_array(array("Clist", "Clist_2")), new _hx_array(array("Clist", "Clist_3")), new _hx_array(array("ListOfLists", "ListOfLists_1")), new _hx_array(array("ListOfLists", "ListOfLists_2_2")), new _hx_array(array("mat", "mat_1_e")), new _hx_array(array("mat_1__")), new _hx_array(array("Clist_"))));
		$i = null;
		{
			$_g1 = 0; $_g = $html->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$v = $this->h->extractVariableNames($html[$i1]);
				$a1 = $this->implode($vars[$i1], ", ");
				$a2 = $this->implode($v, ", ");
				if(!($a1 === $a2)) {
					throw new HException("Expected: " . $a1 . ". Found: " . $a2 . ".");
				}
				unset($v,$i1,$a2,$a1);
			}
		}
	}
	public function unitTestMathMLToText() {
		$mathml = new _hx_array(array("<math><mi>sin</mi><mi>x</mi></math>", "<math><mi>sin</mi><msup><mi>x</mi><mn>2</mn></msup></math>", "<math><mi>sin</mi><msup><mi>x</mi><mn>2</mn></msup><mi>y</mi></math>", "<math><mi>sin</mi><mi>y</mi><msup><mi>x</mi><mn>2</mn></msup></math>", "<math><mi>sin</mi><mrow><mi>y</mi><msup><mi>x</mi><mn>2</mn></msup></mrow></math>", "<math><mi>sin</mi><mrow><mi>y</mi><msup><mrow><mi>x</mi></mrow><mn>2</mn></msup></mrow></math>", "<math><mrow><mi>sin</mi></mrow><mrow><mi>y</mi><msup><mi>x</mi><mn>2</mn></msup></mrow></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>cosh</mi><msqrt><mi>sin</mi><mfrac><mn>1</mn><mi>x</mi></mfrac></msqrt></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>sinh</mi><mfrac><mrow><mi>x</mi><mo>-</mo><mn>1</mn></mrow><mrow><mi>x</mi><mo>+</mo><mn>1</mn></mrow></mfrac></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>atan</mi><mroot><mi>y</mi><mn>6</mn></mroot></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msqrt><mi>ln</mi><mi>x</mi></msqrt></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>sin</mi><mo>(</mo><mi>x</mi><mo>)</mo><mo>+</mo><mn>1</mn></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>#</mo><mi>F</mi><mo>+</mo><mi>C</mi></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>2</mn><mo>.</mo><mn>0</mn><mo>Â·</mo><mi>x</mi><mi>y</mi></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi mathvariant=\"normal\">sin</mi><mi>x</mi></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfenced><mrow><mn>1</mn><mo>,</mo><mn>0</mn></mrow></mfenced></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfrac><mn>1</mn><mn>3</mn></mfrac></math>", "<math><mfenced open=\"|\" close = \"|\"><mtable><mtr><mtd><mn>1</mn></mtd><mtd><mn>0</mn></mtd></mtr><mtr><mtd><mn>0</mn></mtd><mtd><mn>1</mn></mtd></mtr></mtable></mfenced></math>", "<math><mfenced><mtable><mtr><mtd><mn>1</mn></mtd><mtd><mn>0</mn></mtd></mtr><mtr><mtd><mn>0</mn></mtd><mtd><mn>1</mn></mtd></mtr></mtable></mfenced></math>", "<math><msqrt><mn>2</mn></msqrt><mo>+</mo><mroot><mi>x</mi><mn>3</mn></mroot></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Î±</mi><mo>+</mo><mn>1</mn></math>", "<math><mtable><mtr/></mtable></math>", "<math><mtable/></math>", "<math><mtable><mtr><mtd/></mtr></mtable></math>", "<math><mtable><mtr><mtd><mn>2</mn></mtd><mtd/></mtr><mtr/></mtable></math>"));
		$responses = new _hx_array(array("sinx", "sin(x^2)", "sin(x^2y)", "sin(yx^2)", "sin(yx^2)", "sin(yx^2)", "sin(yx^2)", "cosh(sqrt(sin(1/x)))", "sinh((x-1)/(x+1))", "atan(root(y,6))", "sqrt(lnx)", "sin(x)+1", "#F+C", "2.0Â·xy", "sinx", "(1,0)", "1/3", "|((1,0),(0,1))|", "((1,0),(0,1))", "sqrt(2)+root(x,3)", "Î±+1", "(())", "()", "(())", "((2,),())"));
		$i = null;
		{
			$_g1 = 0; $_g = $mathml->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$res = $this->h->mathMLToText($mathml[$i1]);
				if(!($res === $responses[$i1])) {
					throw new HException("Expected: '" . $responses[$i1] . "' but got: '" . $res . "'.");
				}
				unset($res,$i1);
			}
		}
	}
	public function unitTestTextToMathML() {
		$texts = new _hx_array(array("sin(x)+1", "#F +C", "2.0Â·xy", "x<3", "\x0A" . " #resposta\x0A" . "  "));
		$responses = new _hx_array(array("<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>sin</mi><mo>(</mo><mi>x</mi><mo>)</mo><mo>+</mo><mn>1</mn></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>#</mo><mi>F</mi><mo>&#xA0;</mo><mo>+</mo><mi>C</mi></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>2</mn><mo>.</mo><mn>0</mn><mo>Â·</mo><mi>x</mi><mi>y</mi></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>x</mi><mo>&lt;</mo><mn>3</mn></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mspace linebreak=\"newline\"/><mo>&#xA0;</mo><mo>#</mo><mi>r</mi><mi>e</mi><mi>s</mi><mi>p</mi><mi>o</mi><mi>s</mi><mi>t</mi><mi>a</mi><mspace linebreak=\"newline\"/><mo>&#xA0;</mo><mo>&#xA0;</mo></math>"));
		$i = null;
		{
			$_g1 = 0; $_g = $texts->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$res = $this->h->textToMathML($texts[$i1]);
				if(!($res === $responses[$i1])) {
					throw new HException("Expected: '" . $responses[$i1] . "' but got: '" . $res . "'.");
				}
				unset($res,$i1);
			}
		}
	}
	public function unitTestParameterAnswer() {
		$texts = new _hx_array(array("<math><mi>#a</mi></math>", "<math><mi>#answer</mi></math>"));
		$responses = new _hx_array(array("<math><mrow><mn>2</mn></mrow></math>", "<math><mi>#answer</mi></math>"));
		$mml = new Hash();
		$mml->set("a", "<math><mn>2</mn></math>");
		$txt = new Hash();
		$txt->set("a", "2");
		$v = new Hash();
		$v->set(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML, $mml);
		$v->set(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT, $txt);
		{
			$_g1 = 0; $_g = $texts->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$res = $this->h->expandVariables($texts[$i], $v);
				if(!($res === $responses[$i])) {
					throw new HException("Expected: '" . $responses[$i] . "' but got: '" . $res . "'.");
				}
				unset($res,$i);
			}
		}
		$texts = $responses;
		$responses = new _hx_array(array("<math><mrow><mn>2</mn></mrow></math>", "<math><mrow><mn>3</mn></mrow></math>"));
		$answers = new _hx_array(array());
		$answer = new com_wiris_quizzes_impl_Answer();
		$answer->type = com_wiris_quizzes_impl_MathContent::$TYPE_MATHML;
		$answer->content = "<math><mn>3</mn></math>";
		$answers->push($answer);
		{
			$_g1 = 0; $_g = $texts->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$res = $this->h->expandAnswers($texts[$i], $answers, false);
				if(!($res === $responses[$i])) {
					throw new HException("Expected: '" . $responses[$i] . "' but got: '" . $res . "'.");
				}
				unset($res,$i);
			}
		}
	}
	public function unitTestReplaceVariablesInHTML() {
		$texts = new _hx_array(array("<p><br></p><table id=\"yui_05\"><caption id=\"yui_06\"></caption><thead id=\"yui_07\"><tr id=\"yui_08\"><th scope=\"col\" id=\"yui_09\">Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«moÂ»#Â«/moÂ»Â«miÂ»cÂ«/miÂ»Â«/mathÂ»</th></tr></thead><tbody></tbody></table><br>", "<table><tr><td>X</td><td>#X</td></tr><tr><td>Â«mathÂ»Â«/mathÂ»</td><td></td></tr></table>", "<table><tr><td>X</td><td><math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨><msqrt><mo>#</mo><mi>X</mi></msqrt></math></td></tr><tr><td></td><td></td></tr></table>", "<p>Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«mo mathcolor=Â¨#FF0000Â¨Â»#Â«/moÂ»Â«mi mathcolor=Â¨#FF0000Â¨Â»aÂ«/miÂ»Â«mo mathcolor=Â¨#FF0000Â¨Â»+Â«/moÂ»Â«mn mathcolor=Â¨#FF0000Â¨Â»3Â«/mnÂ»Â«/mathÂ»</p>", "<math><mi>#a1</mi></math>", "<math><mo>#</mo><mi>a</mi><mn>1</mn></math>", "<p><img align=\"middle\" src=\"http://localhost/moodle21/lib/editor/tinymce/tiny_mce/3.4.2/plugins/tiny_mce_wiris/integration/showimage.php?formula=cb550f21cbc30fac59e4f2bba550693d.png\" /> + #dif</p>", "a  Â«math xmlns=&quot;http://www.w3.org/1998/Math/MathML&quot;Â»Â«mfracÂ»Â«mrowÂ»Â«miÂ»#Â«/miÂ»Â«miÂ»aÂ«/miÂ»Â«moÂ»+Â«/moÂ»Â«mnÂ»1Â«/mnÂ»Â«/mrowÂ»Â«mrowÂ»Â«miÂ»#Â«/miÂ»Â«miÂ»bÂ«/miÂ»Â«moÂ»-Â«/moÂ»Â«mnÂ»1Â«/mnÂ»Â«/mrowÂ»Â«/mfracÂ»Â«/mathÂ» a", "<p>#a&#xa1;<script type=\"text/javascript\"> <!-- #a1 will be replaced by Wiris Quizzes --> <![CDATA[ a = #a1; ]]> </script> <select><option>#a</option><option>#a1</option></select></p>", "<math><mo>#</mo><mi>a</mi><mn>0</mn></math>", "<p><img align=\"middle\" alt=\"# alpha\" class=\"Wirisformula\" data-mathml=\"Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«moÂ»#Â«/moÂ»Â«miÂ»Â§#945;Â«/miÂ»Â«/mathÂ»\" height=\"13\" src=\"/pluginwiris_engine/app/showimage?formula=4f52b56f431aac53cad4c548ef47e646&amp;cw=26&amp;ch=13&amp;cb=12\" style=\"vertical-align: -1px;\" width=\"26\" />&nbsp;i #&alpha;.</p>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msub><mo>#mat</mo><mn>1</mn></msub><mspace linebreak=\"newline\"/><msub><mo>#mat</mo><mrow><mn>2</mn><mo>,</mo><mn>3</mn></mrow></msub></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msub><mo>#vect</mo><mn>3</mn></msub></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msub><mo>#Amat</mo><mn>1</mn></msub><mspace linebreak=\"newline\"/><msub><mo>#Bvect</mo><mn>1</mn></msub><mspace linebreak=\"newline\"/><msub><mo>#Amat</mo><mrow><mn>1</mn><mo>,</mo><mn>1</mn></mrow></msub><mspace linebreak=\"newline\"/><msub><mo>#Bvect</mo><mn>1</mn></msub><mspace linebreak=\"newline\"/><msub><mo>#Clist</mo><mn>3</mn></msub><mspace linebreak=\"newline\"/><msub><mo>#Amat</mo><mn>3</mn></msub><mspace linebreak=\"newline\"/><msub><mo>#Bvect</mo><mn>4</mn></msub><mspace linebreak=\"newline\"/><mo>#Clist</mo></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>#Bvect</mo><mspace linebreak=\"newline\"/><msub><mo>#Clist</mo><mn>3</mn></msub><mspace linebreak=\"newline\"/><msub><mo>#Clist</mo><mn>4</mn></msub><mspace linebreak=\"newline\"/><msub><mo>#Amat</mo><mrow><mn>2</mn><mo>,</mo><mn>3</mn></mrow></msub></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msub><mo>#Amat</mo><mrow><mn>2</mn><mo>,</mo><mn>3</mn></mrow></msub><mspace linebreak=\"newline\"/><mo>#Amat</mo><mspace linebreak=\"newline\"/><msub><mo>#Bvect</mo><mn>1</mn></msub><mspace linebreak=\"newline\"/><msub><mo>#Bvect</mo><mn>2</mn></msub><mspace linebreak=\"newline\"/><msub><mo>#Clist</mo><mn>5</mn></msub><mspace linebreak=\"newline\"/><msub><mo>#Amat</mo><mrow><mn>1</mn><mo>,</mo><mn>3</mn></mrow></msub><mspace linebreak=\"newline\"/><msub><mo>#Amat</mo><mn>2</mn></msub><mspace linebreak=\"newline\"/><mo>#Clist</mo></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msub><mo>#vect</mo><mn>3</mn></msub><mspace linebreak=\"newline\"/><mo>#Amatv2</mo><mspace linebreak=\"newline\"/><mo>#vect</mo><mspace linebreak=\"newline\"/><msub><mo>#vect</mo><mn>6</mn></msub><mspace linebreak=\"newline\"/><msub><mo>#Amatv2</mo><mrow><mn>1</mn><mo>,</mo><mn>3</mn></mrow></msub><mspace linebreak=\"newline\"/><msub><mo>#Amatv2</mo><mrow><mn>3</mn><mo>,</mo><mn>1</mn></mrow></msub><mspace linebreak=\"newline\"/><msub><mo>#vect</mo><mn>7</mn></msub><mspace linebreak=\"newline\"/><msub><mo>#Amatv2</mo><mn>3</mn></msub></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msub><mi>#Mix</mi><mrow><mn>2</mn><mo>,</mo><mn>3</mn></mrow></msub><mspace linebreak=\"newline\"/><msub><mi>#ListOfLists</mi><mrow><mn>1</mn><mo>,</mo><mn>3</mn></mrow></msub><mspace linebreak=\"newline\"/><mo>#</mo><mi>V</mi><mi>e</mi><mi>c</mi><mi>t</mi><mi>o</mi><mi>r</mi><mi>O</mi><mi>f</mi><mi>L</mi><mi>i</mi><mi>s</mi><mi>t</mi><msub><mi>s</mi><mrow><mn>1</mn><mo>,</mo><mn>4</mn></mrow></msub><mspace linebreak=\"newline\"/><msub><mi>#VectorOfLists</mi><mn>1</mn></msub><mspace linebreak=\"newline\"/><mo>#</mo><mi>M</mi><mi>i</mi><msub><mi>x</mi><mn>1</mn></msub><mspace linebreak=\"newline\"/><msub><mi>#Mix</mi><mn>2</mn></msub></math>", "<p>#vect_4</p>", "<p>#mat_3_2</p>", "<p>#mat_6 #mat_5</p>", "<p>#ListOfLists_2_3 #ListOfLists_1 #ListOfLists_1_3</p>", "<p>#mat_2 #mat_3</p>", "<p>#vect_4 #Amat_1_3 #a_3</p>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><semantics><mrow><mo>#</mo><mi>a</mi></mrow><annotation encoding=\"text/plain\">#a</annotation></semantics></math>"));
		$mml = new Hash();
		$mml->set("dif", "<math><mn>0</mn></math>");
		$mml->set("a", "<math><mi>x</mi></math>");
		$mml->set("b", "<math><mi>y</mi></math>");
		$mml->set("c", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfenced open=\"[\" close=\"]\"><mrow><mn>1</mn><mo>,</mo><mn>2</mn><mo>,</mo><mn>3</mn></mrow></mfenced></math>");
		$mml->set("a1", "<math><mi>z</mi></math>");
		$mml->set("Î±", "<math><mn>2</mn></math>");
		$mml->set("X", "<math><mfenced open=\"{\" close=\"}\"><mrow><mn>1</mn><mo>,</mo><mi>i</mi></mrow></mfenced></math>");
		$mml->set("mat", "<math><mfenced><mtable><mtr><mtd><mn>1</mn></mtd><mtd><mn>2</mn></mtd><mtd><mn>3</mn></mtd></mtr><mtr><mtd><mi>m</mi></mtd><mtd><mi>n</mi></mtd><mtd><mi>l</mi></mtd></mtr></mtable></mfenced></math>");
		$mml->set("vect", "<math><mfenced close=\"]\" open=\"[\"><mrow><mi>m</mi><mo>,</mo><mn>1</mn><mo>,</mo><mi>n</mi><mo>,</mo><mn>2</mn><mo>,</mo><mi>l</mi><mo>,</mo><mn>3</mn></mrow></mfenced></math>");
		$mml->set("llista", "<math><mfenced close=\"}\" open=\"{\"><mrow><mi>m</mi><mo>,</mo><mn>1</mn><mo>,</mo><mi>n</mi><mo>,</mo><mn>2</mn><mo>,</mo><mi>l</mi><mo>,</mo><mn>3</mn></mrow></mfenced></math>");
		$mml->set("Clist", "<math><mfenced close=\"}\" open=\"{\"><mrow><mn>6</mn><mo>,</mo><mi>k</mi><mo>+</mo><mn>2</mn><mo>,</mo><msup><mi>g</mi><mn>2</mn></msup><mo>&#183;</mo><mi>t</mi></mrow></mfenced></math>");
		$mml->set("Bvect", "<math><mfenced close=\"]\" open=\"[\"><mrow><mi mathvariant=\"normal\">&#960;</mi><mo>,</mo><mi>n</mi><mo>+</mo><mn>4</mn></mrow></mfenced></math>");
		$mml->set("Amat", "<math><mfenced><mtable><mtr><mtd><mi>x</mi><mo>+</mo><mn>1</mn></mtd><mtd><mi>y</mi><mo>+</mo><mn>2</mn></mtd><mtd><mi>p</mi></mtd></mtr><mtr><mtd><mn>13</mn></mtd><mtd><mn>6</mn><mo>&#183;</mo><mi>g</mi><mo>+</mo><mn>5</mn></mtd><mtd><mi>m</mi><mo>-</mo><mn>6</mn></mtd></mtr></mtable></mfenced></math>");
		$mml->set("Amatv2", "<math><mfenced><mtable><mtr><mtd><mn>8</mn></mtd><mtd><mn>6</mn><mo>&#183;</mo><mi>p</mi><mo>+</mo><mn>9</mn></mtd></mtr><mtr><mtd><mi>d</mi><mo>&#183;</mo><mi>f</mi><mo>&#183;</mo><mi>s</mi></mtd><mtd><mn>5</mn><mo>&#183;</mo><mi>t</mi><mo>+</mo><mn>2</mn></mtd></mtr></mtable></mfenced></math>");
		$mml->set("ListOfLists", "<math><mrow><mfenced close=\"]\" open=\"[\"><mrow><mfenced close=\"}\" open=\"{\"><mrow><mn>1</mn><mo>,</mo><mn>2</mn><mo>,</mo><mi>p</mi><mo>,</mo><mn>12</mn></mrow></mfenced><mo>,</mo><mfenced close=\"}\" open=\"{\"><mrow><mn>10</mn><mo>&#183;</mo><mi>k</mi></mrow></mfenced></mrow></mfenced></mrow></math>]]></variable>");
		$mml->set("VectorOfLists", "<math><mrow><mfenced close=\"]\" open=\"[\"><mrow><mfenced close=\"}\" open=\"{\"><mrow><mn>1</mn><mo>,</mo><mn>2</mn><mo>,</mo><mi>p</mi><mo>,</mo><mn>12</mn></mrow></mfenced><mo>,</mo><mfenced close=\"}\" open=\"{\"><mrow><mn>10</mn><mo>&#183;</mo><mi>k</mi></mrow></mfenced></mrow></mfenced></mrow></math>");
		$mml->set("Mix", "<math><mrow><mfenced close=\"]\" open=\"[\"><mrow><mfenced close=\"}\" open=\"{\"><mrow><mn>1</mn><mo>,</mo><mn>2</mn><mo>,</mo><mi>p</mi><mo>,</mo><mn>12</mn></mrow></mfenced><mo>,</mo><mfenced close=\"]\" open=\"[\"><mrow><mn>4</mn><mo>,</mo><mn>6</mn><mo>&#183;</mo><mi>&#948;</mi><mo>,</mo><mi>&#949;</mi><mo>,</mo><mn>0</mn><mo>.</mo><mn>6021</mn></mrow></mfenced></mrow></mfenced></mrow></math>");
		$mml->set("vect_3", "<math><mrow><mi>vect_3</mi></mrow></math>");
		$mml->set("vect_4", "<math><mrow><mo>-</mo><mn>0</mn><mo>.</mo><mn>7568</mn></mrow></math>");
		$mml->set("mat_6", "<math><mrow><mn>777</mn></mrow></math>");
		$mml->set("mat_1_2", "<math><mrow><mi>mat_1_2</mi></mrow></math>");
		$mml->set("mat_3_2", "<math><mrow><mi>mat_3_2</mi></mrow></math>");
		$mml->set("mat_1_e", "<math><mrow><mi>mat_1_e</mi></mrow></math>");
		$mml->set("mat_1", "<math><mrow><mi>mat_1</mi></mrow></math>");
		$mml->set("mat_2", "<math><mrow><mi>mat_2</mi></mrow></math>");
		$mml->set("mat_3", "<math><mrow><mi>mat_3</mi></mrow></math>");
		$mml->set("mat_5", "<math><mrow><mi>mat_5</mi></mrow></math>");
		$mml->set("mat_6", "<math><mrow><mn>777</mn></mrow></math>");
		$mml->set("mat_e", "<math><mrow><mn>mat_e</mn></mrow></math>");
		$mml->set("mat__", "<math><mrow><mn>mat__</mn></mrow></math>");
		$mml->set("mat_1__", "<math><mrow><mn>mat_1__</mn></mrow></math>");
		$mml->set("ListOfLists_1", "<math><mrow><mi>ListOfLists_1</mi></mrow></math>");
		$mml->set("ListOfLists_1_3", "<math><mrow><mi>ListOfLists_1_3</mi></mrow></math>");
		$mml->set("ListOfLists_2_1", "<math><mrow><mi>ListOfLists_2_1</mi></mrow></math>");
		$mml->set("ListOfLists_2_2", "<math><mrow><mi>ListOfLists_2_2</mi></mrow></math>");
		$mml->set("ListOfLists_2_3", "<math><mrow><mi>ListOfLists_2_3</mi></mrow></math>");
		$mml->set("Clist_", "<math><mrow><mi>Clist_</mi></mrow></math>");
		$mml->set("Clist_1", "<math><mrow><mi>Clist_1</mi></mrow></math>");
		$mml->set("Clist_2", "<math><mrow><mi>Clist_2</mi></mrow></math>");
		$mml->set("Clist_3", "<math><mrow><mi>Clist_3</mi></mrow></math>");
		$mml->set("Amat_2_2", "<math><mrow><mi>Amat_2_2</mi></mrow></math>");
		$mml->set("Amat_1_3", "<math><mrow><mi>Amat_1_3</mi></mrow></math>");
		$mml->set("a_3", "<math><mrow><mi>a_3</mi></mrow></math>");
		$mml->set("x_1", "<math><mrow><mn>m</mn><mn>n</mn></mrow></math>");
		$txt = new Hash();
		$txt->set("dif", "0");
		$txt->set("a", "x");
		$txt->set("b", "y");
		$txt->set("c", "[1,2,3]");
		$txt->set("a1", "z");
		$txt->set("Î±", "2");
		$txt->set("X", "{1,i}");
		$txt->set("mat", "[[1,2,3],[m,n,l]]");
		$txt->set("vect", "[m,1,n,2,l,3]");
		$txt->set("llista", "{m,1,n,2,l,3}");
		$txt->set("Clist", "{6,2+k,gtg}");
		$txt->set("Bvect", "[<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi class=\"content\">&#x3C0;</mi></math>,n+4]");
		$txt->set("Amat", "[[x+1,y+2,p],[13,6g+5,m-6]]");
		$txt->set("Amatv2", "[[8,6p+9],[sdf,5t+2]]");
		$txt->set("ListOfLists", "{{1,2,p,12},{10k}}");
		$txt->set("VectorOfLists", "[{1,2,p,12},{10k}]");
		$txt->set("Mix", "[{1,2,p,12},[4,6<math xmlns=\\\"http://www.w3.org/1998/Math/MathML\\\"><mi class=\\\"content\\\">&#x3B4;</mi></math>,\" +\x0A" . "\"<math xmlns=\\\"http://www.w3.org/1998/Math/MathML\\\"><mi class=\\\"content\\\"><mi>&#x3B5;</mi></math>,0.60206]]");
		$txt->set("vect_4", "-0.7568");
		$txt->set("mat_6", "777");
		$txt->set("x_1", "mn");
		$v = new Hash();
		$v->set(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML, $mml);
		$v->set(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT, $txt);
		$responses = new _hx_array(array("<p><br></p><table id=\"yui_05\" class=\"wiristable\"><caption id=\"yui_06\"></caption><thead id=\"yui_07\"><tr id=\"yui_08\"><th scope=\"col\" id=\"yui_09\">Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«mnÂ»1Â«/mnÂ»Â«/mathÂ»</th><th scope=\"col\" id=\"yui_09\">Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«mnÂ»2Â«/mnÂ»Â«/mathÂ»</th><th scope=\"col\" id=\"yui_09\">Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«mnÂ»3Â«/mnÂ»Â«/mathÂ»</th></tr></thead><tbody></tbody></table><br>", "<table class=\"wiristable\"><tr><td>X</td><td>Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«mnÂ»1Â«/mnÂ»Â«/mathÂ»</td></tr><tr><td>Â«mathÂ»Â«/mathÂ»</td><td>Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«miÂ»iÂ«/miÂ»Â«/mathÂ»</td></tr></table>", "<table><tr><td>X</td><td><math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨><msqrt><mrow><mfenced open=\"{\" close=\"}\"><mrow><mn>1</mn><mo>,</mo><mi>i</mi></mrow></mfenced></mrow></msqrt></math></td></tr><tr><td></td><td></td></tr></table>", "<p>Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«mstyle mathcolor=Â¨#FF0000Â¨Â»Â«mrowÂ»Â«miÂ»xÂ«/miÂ»Â«/mrowÂ»Â«/mstyleÂ»Â«mo mathcolor=Â¨#FF0000Â¨Â»+Â«/moÂ»Â«mn mathcolor=Â¨#FF0000Â¨Â»3Â«/mnÂ»Â«/mathÂ»</p>", "<math><mrow><mi>z</mi></mrow></math>", "<math><mrow><mi>z</mi></mrow></math>", "<p><img align=\"middle\" src=\"http://localhost/moodle21/lib/editor/tinymce/tiny_mce/3.4.2/plugins/tiny_mce_wiris/integration/showimage.php?formula=cb550f21cbc30fac59e4f2bba550693d.png\" /> + <math><mn>0</mn></math></p>", "a  Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«mfracÂ»Â«mrowÂ»Â«mrowÂ»Â«miÂ»xÂ«/miÂ»Â«/mrowÂ»Â«moÂ»+Â«/moÂ»Â«mnÂ»1Â«/mnÂ»Â«/mrowÂ»Â«mrowÂ»Â«mrowÂ»Â«miÂ»yÂ«/miÂ»Â«/mrowÂ»Â«moÂ»-Â«/moÂ»Â«mnÂ»1Â«/mnÂ»Â«/mrowÂ»Â«/mfracÂ»Â«/mathÂ» a", "<p><math><mi>x</mi></math>Â¡<script type=\"text/javascript\"> <!-- #a1 will be replaced by Wiris Quizzes --> <![CDATA[ a = z; ]]> </script> <select><option>x</option><option>z</option></select></p>", "<math><mrow><mrow><mi>x</mi></mrow><mo>0</mo></mrow></math>", "<p><img align=\"middle\" alt=\"# alpha\" class=\"Wirisformula\" data-mathml=\"Â«math xmlns=Â¨http://www.w3.org/1998/Math/MathMLÂ¨Â»Â«mrowÂ»Â«mnÂ»2Â«/mnÂ»Â«/mrowÂ»Â«/mathÂ»\" height=\"13\" src=\"/pluginwiris_engine/app/showimage?formula=4f52b56f431aac53cad4c548ef47e646&amp;cw=26&amp;ch=13&amp;cb=12\" style=\"vertical-align: -1px;\" width=\"26\" />Â i Â«mathÂ»Â«mnÂ»2Â«/mnÂ»Â«/mathÂ».</p>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mfenced><mtable><mtr><mtd><mn>1</mn></mtd><mtd><mn>2</mn></mtd><mtd><mn>3</mn></mtd></mtr></mtable></mfenced></mrow><mspace linebreak=\"newline\"/><mrow><mi>l</mi></mrow></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mi>n</mi></mrow></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mfenced><mtable><mtr><mtd><mi>x</mi><mo>+</mo><mn>1</mn></mtd><mtd><mi>y</mi><mo>+</mo><mn>2</mn></mtd><mtd><mi>p</mi></mtd></mtr></mtable></mfenced></mrow><mspace linebreak=\"newline\"/><mrow><mi mathvariant=\"normal\">&#960;</mi></mrow><mspace linebreak=\"newline\"/><mrow><mi>x</mi><mo>+</mo><mn>1</mn></mrow><mspace linebreak=\"newline\"/><mrow><mi mathvariant=\"normal\">&#960;</mi></mrow><mspace linebreak=\"newline\"/><mrow><msup><mi>g</mi><mn>2</mn></msup><mo>&#183;</mo><mi>t</mi></mrow><mspace linebreak=\"newline\"/><msub><mrow><mfenced><mtable><mtr><mtd><mi>x</mi><mo>+</mo><mn>1</mn></mtd><mtd><mi>y</mi><mo>+</mo><mn>2</mn></mtd><mtd><mi>p</mi></mtd></mtr><mtr><mtd><mn>13</mn></mtd><mtd><mn>6</mn><mo>&#183;</mo><mi>g</mi><mo>+</mo><mn>5</mn></mtd><mtd><mi>m</mi><mo>-</mo><mn>6</mn></mtd></mtr></mtable></mfenced></mrow><mn>3</mn></msub><mspace linebreak=\"newline\"/><msub><mrow><mfenced close=\"]\" open=\"[\"><mrow><mi mathvariant=\"normal\">&#960;</mi><mo>,</mo><mi>n</mi><mo>+</mo><mn>4</mn></mrow></mfenced></mrow><mn>4</mn></msub><mspace linebreak=\"newline\"/><mrow><mfenced close=\"}\" open=\"{\"><mrow><mn>6</mn><mo>,</mo><mi>k</mi><mo>+</mo><mn>2</mn><mo>,</mo><msup><mi>g</mi><mn>2</mn></msup><mo>&#183;</mo><mi>t</mi></mrow></mfenced></mrow></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mfenced close=\"]\" open=\"[\"><mrow><mi mathvariant=\"normal\">&#960;</mi><mo>,</mo><mi>n</mi><mo>+</mo><mn>4</mn></mrow></mfenced></mrow><mspace linebreak=\"newline\"/><mrow><msup><mi>g</mi><mn>2</mn></msup><mo>&#183;</mo><mi>t</mi></mrow><mspace linebreak=\"newline\"/><msub><mrow><mfenced close=\"}\" open=\"{\"><mrow><mn>6</mn><mo>,</mo><mi>k</mi><mo>+</mo><mn>2</mn><mo>,</mo><msup><mi>g</mi><mn>2</mn></msup><mo>&#183;</mo><mi>t</mi></mrow></mfenced></mrow><mn>4</mn></msub><mspace linebreak=\"newline\"/><mrow><mi>m</mi><mo>-</mo><mn>6</mn></mrow></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mi>m</mi><mo>-</mo><mn>6</mn></mrow><mspace linebreak=\"newline\"/><mrow><mfenced><mtable><mtr><mtd><mi>x</mi><mo>+</mo><mn>1</mn></mtd><mtd><mi>y</mi><mo>+</mo><mn>2</mn></mtd><mtd><mi>p</mi></mtd></mtr><mtr><mtd><mn>13</mn></mtd><mtd><mn>6</mn><mo>&#183;</mo><mi>g</mi><mo>+</mo><mn>5</mn></mtd><mtd><mi>m</mi><mo>-</mo><mn>6</mn></mtd></mtr></mtable></mfenced></mrow><mspace linebreak=\"newline\"/><mrow><mi mathvariant=\"normal\">&#960;</mi></mrow><mspace linebreak=\"newline\"/><mrow><mi>n</mi><mo>+</mo><mn>4</mn></mrow><mspace linebreak=\"newline\"/><msub><mrow><mfenced close=\"}\" open=\"{\"><mrow><mn>6</mn><mo>,</mo><mi>k</mi><mo>+</mo><mn>2</mn><mo>,</mo><msup><mi>g</mi><mn>2</mn></msup><mo>&#183;</mo><mi>t</mi></mrow></mfenced></mrow><mn>5</mn></msub><mspace linebreak=\"newline\"/><mrow><mi>p</mi></mrow><mspace linebreak=\"newline\"/><mrow><mfenced><mtable><mtr><mtd><mn>13</mn></mtd><mtd><mn>6</mn><mo>&#183;</mo><mi>g</mi><mo>+</mo><mn>5</mn></mtd><mtd><mi>m</mi><mo>-</mo><mn>6</mn></mtd></mtr></mtable></mfenced></mrow><mspace linebreak=\"newline\"/><mrow><mfenced close=\"}\" open=\"{\"><mrow><mn>6</mn><mo>,</mo><mi>k</mi><mo>+</mo><mn>2</mn><mo>,</mo><msup><mi>g</mi><mn>2</mn></msup><mo>&#183;</mo><mi>t</mi></mrow></mfenced></mrow></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mi>n</mi></mrow><mspace linebreak=\"newline\"/><mrow><mfenced><mtable><mtr><mtd><mn>8</mn></mtd><mtd><mn>6</mn><mo>&#183;</mo><mi>p</mi><mo>+</mo><mn>9</mn></mtd></mtr><mtr><mtd><mi>d</mi><mo>&#183;</mo><mi>f</mi><mo>&#183;</mo><mi>s</mi></mtd><mtd><mn>5</mn><mo>&#183;</mo><mi>t</mi><mo>+</mo><mn>2</mn></mtd></mtr></mtable></mfenced></mrow><mspace linebreak=\"newline\"/><mrow><mfenced close=\"]\" open=\"[\"><mrow><mi>m</mi><mo>,</mo><mn>1</mn><mo>,</mo><mi>n</mi><mo>,</mo><mn>2</mn><mo>,</mo><mi>l</mi><mo>,</mo><mn>3</mn></mrow></mfenced></mrow><mspace linebreak=\"newline\"/><mrow><mn>3</mn></mrow><mspace linebreak=\"newline\"/><msub><mrow><mfenced><mtable><mtr><mtd><mn>8</mn></mtd><mtd><mn>6</mn><mo>&#183;</mo><mi>p</mi><mo>+</mo><mn>9</mn></mtd></mtr><mtr><mtd><mi>d</mi><mo>&#183;</mo><mi>f</mi><mo>&#183;</mo><mi>s</mi></mtd><mtd><mn>5</mn><mo>&#183;</mo><mi>t</mi><mo>+</mo><mn>2</mn></mtd></mtr></mtable></mfenced></mrow><mrow><mn>1</mn><mo>,</mo><mn>3</mn></mrow></msub><mspace linebreak=\"newline\"/><msub><mrow><mfenced><mtable><mtr><mtd><mn>8</mn></mtd><mtd><mn>6</mn><mo>&#183;</mo><mi>p</mi><mo>+</mo><mn>9</mn></mtd></mtr><mtr><mtd><mi>d</mi><mo>&#183;</mo><mi>f</mi><mo>&#183;</mo><mi>s</mi></mtd><mtd><mn>5</mn><mo>&#183;</mo><mi>t</mi><mo>+</mo><mn>2</mn></mtd></mtr></mtable></mfenced></mrow><mrow><mn>3</mn><mo>,</mo><mn>1</mn></mrow></msub><mspace linebreak=\"newline\"/><msub><mrow><mfenced close=\"]\" open=\"[\"><mrow><mi>m</mi><mo>,</mo><mn>1</mn><mo>,</mo><mi>n</mi><mo>,</mo><mn>2</mn><mo>,</mo><mi>l</mi><mo>,</mo><mn>3</mn></mrow></mfenced></mrow><mn>7</mn></msub><mspace linebreak=\"newline\"/><msub><mrow><mfenced><mtable><mtr><mtd><mn>8</mn></mtd><mtd><mn>6</mn><mo>&#183;</mo><mi>p</mi><mo>+</mo><mn>9</mn></mtd></mtr><mtr><mtd><mi>d</mi><mo>&#183;</mo><mi>f</mi><mo>&#183;</mo><mi>s</mi></mtd><mtd><mn>5</mn><mo>&#183;</mo><mi>t</mi><mo>+</mo><mn>2</mn></mtd></mtr></mtable></mfenced></mrow><mn>3</mn></msub></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mi>&#949;</mi></mrow><mrow><mspace linebreak=\"newline\"/><mrow><mi>p</mi></mrow><mrow><mspace linebreak=\"newline\"/><mrow><mn>12</mn></mrow><mrow><mspace linebreak=\"newline\"/><mrow><mrow><mfenced close=\"}\" open=\"{\"><mrow><mn>1</mn><mo>,</mo><mn>2</mn><mo>,</mo><mi>p</mi><mo>,</mo><mn>12</mn></mrow></mfenced></mrow></mrow><mspace linebreak=\"newline\"/><mrow><mrow><mfenced close=\"}\" open=\"{\"><mrow><mn>1</mn><mo>,</mo><mn>2</mn><mo>,</mo><mi>p</mi><mo>,</mo><mn>12</mn></mrow></mfenced></mrow></mrow><mspace linebreak=\"newline\"/><msub><mrow><mrow><mfenced close=\"]\" open=\"[\"><mrow><mfenced close=\"}\" open=\"{\"><mrow><mn>1</mn><mo>,</mo><mn>2</mn><mo>,</mo><mi>p</mi><mo>,</mo><mn>12</mn></mrow></mfenced><mo>,</mo><mfenced close=\"]\" open=\"[\"><mrow><mn>4</mn><mo>,</mo><mn>6</mn><mo>&#183;</mo><mi>&#948;</mi><mo>,</mo><mi>&#949;</mi><mo>,</mo><mn>0</mn><mo>.</mo><mn>6021</mn></mrow></mfenced></mrow></mfenced></mrow></mrow><mn>2</mn></msub></math>", "<p><math><mrow><mo>-</mo><mn>0</mn><mo>.</mo><mn>7568</mn></mrow></math></p>", "<p><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msub><mrow><mfenced><mtable><mtr><mtd><mn>1</mn></mtd><mtd><mn>2</mn></mtd><mtd><mn>3</mn></mtd></mtr><mtr><mtd><mi>m</mi></mtd><mtd><mi>n</mi></mtd><mtd><mi>l</mi></mtd></mtr></mtable></mfenced></mrow><mrow><mn>3</mn><mo>,</mo><mn>2</mn></mrow></msub></math></p>", "<p><math><mrow><mn>777</mn></mrow></math> <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msub><mrow><mfenced><mtable><mtr><mtd><mn>1</mn></mtd><mtd><mn>2</mn></mtd><mtd><mn>3</mn></mtd></mtr><mtr><mtd><mi>m</mi></mtd><mtd><mi>n</mi></mtd><mtd><mi>l</mi></mtd></mtr></mtable></mfenced></mrow><mn>5</mn></msub></math></p>", "<p><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msub><mrow><mrow><mfenced close=\"]\" open=\"[\"><mrow><mfenced close=\"}\" open=\"{\"><mrow><mn>1</mn><mo>,</mo><mn>2</mn><mo>,</mo><mi>p</mi><mo>,</mo><mn>12</mn></mrow></mfenced><mo>,</mo><mfenced close=\"}\" open=\"{\"><mrow><mn>10</mn><mo>&#183;</mo><mi>k</mi></mrow></mfenced></mrow></mfenced></mrow></math>]]></va</mrow><mrow><mn>2</mn><mo>,</mo><mn>3</mn></mrow></msub></math> <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mrow><mfenced close=\"}\" open=\"{\"><mrow><mn>1</mn><mo>,</mo><mn>2</mn><mo>,</mo><mi>p</mi><mo>,</mo><mn>12</mn></mrow></mfenced></mrow></mrow></math> <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mi>p</mi></mrow><mrow></math></p>", "<p><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mfenced><mtable><mtr><mtd><mi>m</mi></mtd><mtd><mi>n</mi></mtd><mtd><mi>l</mi></mtd></mtr></mtable></mfenced></mrow></math> <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msub><mrow><mfenced><mtable><mtr><mtd><mn>1</mn></mtd><mtd><mn>2</mn></mtd><mtd><mn>3</mn></mtd></mtr><mtr><mtd><mi>m</mi></mtd><mtd><mi>n</mi></mtd><mtd><mi>l</mi></mtd></mtr></mtable></mfenced></mrow><mn>3</mn></msub></math></p>", "<p><math><mrow><mo>-</mo><mn>0</mn><mo>.</mo><mn>7568</mn></mrow></math> <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mi>p</mi></mrow></math> <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msub><mrow><mi>x</mi></mrow><mn>3</mn></msub></math></p>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><semantics><mrow><mrow><mi>x</mi></mrow></mrow><annotation encoding=\"text/plain\">x</annotation></semantics></math>"));
		$i = null;
		{
			$_g1 = 0; $_g = $texts->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$res = $this->h->expandVariables($texts[$i1], $v);
				if(!($res === $responses[$i1])) {
					throw new HException("At case number " . _hx_string_rec($i1, "") . ", expected: '" . $responses[$i1] . "' but got: '" . $res . "'.");
				}
				unset($res,$i1);
			}
		}
	}
	public function unitTestExtractText() {
		$inputs = new _hx_array(array("<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mtext>Cap de les altres</mtext></mrow></math>", "<math><mrow><mtext>La resposta Ã©s </mtext><mfrac><mfrac><mn>1</mn><mn>2</mn></mfrac><mi>x</mi></mfrac><mtext>.</mtext></mrow></math>", "<math><mrow><mo>(</mo><mtext>tiruliru</mtext><mo>)</mo></mrow></math>", "<math><mrow><msqrt><mtext>radicand</mtext></msqrt></mrow></math>", "<math><mtext>&#xa0;a&#xa0;b&#xa0;</mtext></math>", "<math><mtext>&#xa0;</mtext></math>"));
		$outputs = new _hx_array(array("Cap de les altres", "La resposta Ã©s <math><mrow><mfrac><mfrac><mn>1</mn><mn>2</mn></mfrac><mi>x</mi></mfrac></mrow></math>.", "<math><mrow><mo>(</mo></mrow></math>tiruliru<math><mrow><mo>)</mo></mrow></math>", "<math><mrow><msqrt><mtext>radicand</mtext></msqrt></mrow></math>", " aÂ b ", " "));
		$i = null;
		{
			$_g1 = 0; $_g = $inputs->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$coutput = $this->h->extractTextFromMathML($inputs[$i1]);
				if(!($coutput === $outputs[$i1])) {
					throw new HException("Expected: '" . $outputs[$i1] . "' but got: '" . $coutput . "'.");
				}
				unset($i1,$coutput);
			}
		}
	}
	public function unitTestPrepareFormulasAlgorithm() {
		$tests = new _hx_array(array("<math><mo>#</mo><mi>v</mi><mi>a</mi><mi>r</mi><mo>_</mo><mi>k</mi></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>#</mo><mi>a</mi><mo>&#160;</mo><mo>+</mo><mo>#</mo><mi>b</mi></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>p</mi><mo>(</mo><mi>x</mi><mo>)</mo><mo>=</mo><mi>#</mi><mi>p</mi></math>", "<math><mrow><mi>#</mi><mi>f</mi></mrow></math>", "<math><mrow><mi>#</mi><msup><mi>f</mi><mn>2</mn></msup></mrow></math>", "<math><mrow><msqrt><mrow><mn>2</mn><msqrt><mn>3</mn></msqrt></mrow></msqrt><mi>#</mi><mi>a</mi></mrow></math>", "<math><mrow><msub><mi>#</mi><mi>a</mi></msub></mrow></math>", "<math><mrow><mi>#</mi><msub><mi>a</mi><mi>c</mi></msub></mrow></math>", "<math><mrow><msqrt><mrow><mi>#</mi><mi>f</mi><mi>u</mi><mi>n</mi><mi>c</mi></mrow></msqrt></mrow></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>&#8594;</mo><mn>0</mn></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>x</mi><mo>=</mo><mo>#</mo><mi>a</mi><mspace linebreak=\"newline\"/><mi>y</mi><mo>=</mo><mo>#</mo><mi>b</mi></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>#</mo><mi>Î±</mi></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo mathcolor=\"#FF0000\">#</mo><mi mathcolor=\"#FF0000\">a</mi><mo mathcolor=\"#FF0000\">+</mo><mn mathcolor=\"#FF0000\">3</mn></math>", "<p>#plotter1</p>\x0A<math xmlns=\"http://www.w3.org/1998/Math/MathML\"/>"));
		$responses = new _hx_array(array("<math><mo>#var_k</mo></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>#a</mo><mo>&#160;</mo><mo>+</mo><mo>#b</mo></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>p</mi><mo>(</mo><mi>x</mi><mo>)</mo><mo>=</mo><mi>#p</mi></math>", "<math><mrow><mi>#f</mi></mrow></math>", "<math><mrow><msup><mi>#f</mi><mn>2</mn></msup></mrow></math>", "<math><mrow><msqrt><mrow><mn>2</mn><msqrt><mn>3</mn></msqrt></mrow></msqrt><mi>#a</mi></mrow></math>", "<math><mrow><msub><mi>#</mi><mi>a</mi></msub></mrow></math>", "<math><mrow><msub><mi>#a</mi><mi>c</mi></msub></mrow></math>", "<math><mrow><msqrt><mrow><mi>#func</mi></mrow></msqrt></mrow></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>&#8594;</mo><mn>0</mn></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>x</mi><mo>=</mo><mo>#a</mo><mspace linebreak=\"newline\"/><mi>y</mi><mo>=</mo><mo>#b</mo></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>#Î±</mo></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo mathcolor=\"#FF0000\">#a</mo><mo mathcolor=\"#FF0000\">+</mo><mn mathcolor=\"#FF0000\">3</mn></math>", "<p>#plotter1</p>\x0A<math xmlns=\"http://www.w3.org/1998/Math/MathML\"/>"));
		$i = null;
		{
			$_g1 = 0; $_g = $tests->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$res = $this->h->prepareFormulas($tests[$i1]);
				if(!($res === $responses[$i1])) {
					throw new HException("Expected: '" . $responses[$i1] . "' but got: '" . $res . "'.");
				}
				unset($res,$i1);
			}
		}
	}
	public function unitTestUpdateReservedWords() {
		$tests = new _hx_array(array("<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msup><mi>x</mi><mn>3</mn></msup><msup><mi>y</mi><mn>2</mn></msup></math>", "<math><semantics><mrow><mn>6</mn><mi>k</mi><mi>m</mi><mo>+</mo><mn>4</mn><mi>k</mi><mi>m</mi></mrow><annotation encoding=\"application/json\">[[[18,9],...,[4,27]]]</annotation></semantics></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mn>4</mn><mo>.</mo><mn>1</mn><mo>&#xD7;</mo><mn>1</mn><msup><mn>0</mn><mrow><mo>-</mo><mn>4</mn></mrow></msup></mrow></math>", "<math><mi>s</mi><mi>i</mi><mi>n</mi><mi>s</mi><mi>i</mi><mi>n</mi></math>", "<math><mi>s</mi><mi>i</mi><mi>n</mi><mn>1</mn><mi>s</mi><mi>i</mi><mi>n</mi></math>", "<math><msup><mrow><mi>s</mi><mi>i</mi><msup><mi>n</mi><mi>k</mi></msup></mrow><mi>n</mi></msup></math>", "<math><mi>s</mi><msup><mi>i</mi><mn>1</mn></msup><mi>s</mi><msup><mi>i</mi><mn>1</mn></msup></math>", "<math><mi>s</mi><mrow><mi>i</mi><mi>m</mi><mi>x</mi></mrow></math>", "<math><mi>si</mi><mi>n</mi><mi>x</mi></math>", "<math><mn>2</mn><mi>k</mi><mi>m</mi></math>", "<math><mn>2</mn><mi>k</mi><mo>&nbsp;</mo><mi>m</mi></math>", "<math><mn>5</mn><mi>k</mi><msup><mi>m</mi><mn>2</mn></msup></math>"));
		$words = new _hx_array(array());
		$words->push("s");
		$words->push("si");
		$words->push("sin");
		$words->push("m");
		$words->push("km");
		$res = new _hx_array(array("<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msup><mi>x</mi><mn>3</mn></msup><msup><mi>y</mi><mn>2</mn></msup></math>", "<math><semantics><mrow><mn>6</mn><mi>km</mi><mo>+</mo><mn>4</mn><mi>km</mi></mrow><annotation encoding=\"application/json\">[[[18,9],...,[4,27]]]</annotation></semantics></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>4</mn><mo>.</mo><mn>1</mn><mo>&#xD7;</mo><msup><mn>10</mn><mrow><mo>-</mo><mn>4</mn></mrow></msup></math>", "<math><mi>sin</mi><mi>sin</mi></math>", "<math><mi>sin</mi><mn>1</mn><mi>sin</mi></math>", "<math><msup><msup><mi>sin</mi><mi>k</mi></msup><mi>n</mi></msup></math>", "<math><msup><mi>si</mi><mn>1</mn></msup><msup><mi>si</mi><mn>1</mn></msup></math>", "<math><mi>si</mi><mi mathvariant=\"normal\">m</mi><mi>x</mi></math>", "<math><mi>sin</mi><mi>x</mi></math>", "<math><mn>2</mn><mi>km</mi></math>", "<math><mn>2</mn><mi>k</mi><mo>&#xA0;</mo><mi mathvariant=\"normal\">m</mi></math>", "<math><mn>5</mn><msup><mi>km</mi><mn>2</mn></msup></math>"));
		$i = null;
		{
			$_g1 = 0; $_g = $tests->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$u = $this->h->updateReservedWords($tests[$i1], $words);
				if(!($u === $res[$i1])) {
					throw new HException("Expected: " . $res[$i1] . ". Got: " . $u . ".");
				}
				unset($u,$i1);
			}
		}
	}
	public function unitTestExtractActionCommands() {
		$tests = new _hx_array(array(" <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>evaluate</mi><mfenced><mfrac><mrow><mo>#</mo><mi>a</mi></mrow><mrow><mo>#</mo><mi>b</mi></mrow></mfrac></mfenced></math> ", "  <p>evaluate(#a + #b)</p>\x0A", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>evaluate</mi><mo>(</mo><mo>#</mo><mi>a</mi><mo>&#xA0;</mo><mo>+</mo><mo>&#xA0;</mo><mo>#</mo><mi>b</mi><mo>)</mo></math>", " <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>evaluate</mi><mfenced><mrow><mo>#</mo><mi mathvariant=\"normal\">d</mi><mo>&#xB7;</mo><mo>#</mo><mi mathvariant=\"normal\">e</mi></mrow></mfenced></math> <p>evaluate(#d*#e)</p>\x0A", " evaluate(#a+#b) <p>Calculate #a + #b</p>\x0A", " <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>evaluate</mi><mfenced><mrow><mo>#</mo><msup><mi>a</mi><mrow><mo>#</mo><mi>b</mi></mrow></msup><mo>+</mo><mo>#</mo><msup><mi>b</mi><mn>2</mn></msup><mo>-</mo><mfrac><mrow><mo>#</mo><mi>a</mi></mrow><mrow><mo>#</mo><mi>b</mi></mrow></mfrac><mo>&#xB7;</mo><mo>#</mo><msup><mi>c</mi><mrow><mo>#</mo><mi mathvariant=\"normal\">d</mi></mrow></msup><mo>+</mo><msqrt><mi>a</mi></msqrt></mrow></mfenced></math> ", "  <p>write the following number evaluate(#a/#b+(#a + #b))</p>\x0A", " <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfrac><mrow><mi>evaluate</mi><mfenced><mrow><mo>#</mo><mi>a</mi><mo>+</mo><mo>#</mo><mi>b</mi></mrow></mfenced></mrow><mrow><mi>evaluate</mi><mfenced><mrow><mo>#</mo><mi>a</mi><mo>&#xB7;</mo><mo>#</mo><mi>b</mi></mrow></mfenced></mrow></mfrac></math> <p>evaluate(#a+#b) and evaluate(#a*#b)</p>\x0A", " <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>eval&#xFA;a</mi><mfenced><mrow/></mfenced></math> <p>evaluate((#d+#s)</p>\x0A", " <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>eval&#xFA;a</mi><mfenced><mrow/></mfenced></math> <p>evaluate(#q+(#w)))</p>\x0A"));
		$responses = new _hx_array(array(" <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>#_computed_variable_eea520b33b9e56415fe1ca98dcbf6e58</mi></math> ", "  <p>#_computed_variable_150d3e8a7ebdadcd0d9afd52dab6ef12</p>\x0A", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>#_computed_variable_28506e9df521e9b0f6b92546b6510f28</mi></math>", " <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>#_computed_variable_73184bd88a1121c7e49223daaa1bd404</mi></math> <p>#_computed_variable_68108b87db9775cc4796cf0132e4fb64</p>\x0A", " #_computed_variable_a85f9bfc390e5421fb27d6e0eae415ef <p>Calculate #a + #b</p>\x0A", " <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>#_computed_variable_332587bd084524a4442c4b8c4cf9e50e</mi></math> ", "  <p>write the following number #_computed_variable_047f7bd3591f61601d9ee74065609853</p>\x0A", " <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfrac><mrow><mi>#_computed_variable_36ee93ec55e4d3aaa7c976cc7e88fb1d</mi></mrow><mrow><mi>#_computed_variable_ac0564241e64e7d7367a4179e68185e8</mi></mrow></mfrac></math> <p>#_computed_variable_a85f9bfc390e5421fb27d6e0eae415ef and #_computed_variable_64864068544ba2b47b956f1145c38194</p>\x0A", " <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>eval&#xFA;a</mi><mfenced><mrow/></mfenced></math> <p>evaluate((#d+#s)</p>\x0A", " <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>#_computed_variable_de1594c8a0efb9b9eefbf0f1ca39a80e</mi></math> <p>#_computed_variable_a1098406413f95b008430046c784ba33)</p>\x0A"));
		$i = null;
		{
			$_g1 = 0; $_g = $tests->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$res = $this->h->extractActionExpressions($tests[$i1], null);
				if(!($res === $responses[$i1])) {
					throw new HException("Expected: '" . $responses[$i1] . "' but got: '" . $res . "'.");
				}
				unset($res,$i1);
			}
		}
	}
	public function run() {
		$this->unitTestJoinCompoundAnswer();
		$this->unitTestStripRootTag();
		$this->unitTestParseCompoundAnswers();
		$this->unitTestUpdateReservedWords();
		$this->unitTestReplaceVariablesInHTML();
		$this->unitTestVariableNames();
		$this->unitTestExtractText();
		$this->unitTestTextToMathML();
		$this->unitTestPrepareFormulasAlgorithm();
		$this->unitTestMathMLToText();
		$this->unitTestTables();
		$this->unitTestParseCompoundAnswers();
		$this->unitTestUtf8();
		$this->unitTestParameter();
		$this->unitTestExtractActionCommands();
	}
	public $h;
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
	static function main() {
		$argv = Sys::args();
		$t = new com_wiris_quizzes_impl_HTMLToolsUnitTests();
		$t->run();
	}
	static function error() {
		throw new HException("String encoding error.");
	}
	function __toString() { return 'com.wiris.quizzes.impl.HTMLToolsUnitTests'; }
}
function com_wiris_quizzes_impl_HTMLToolsUnitTests_0(&$»this) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(120120);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLToolsUnitTests_1(&$»this) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(960);
		return $s->toString();
	}
}
