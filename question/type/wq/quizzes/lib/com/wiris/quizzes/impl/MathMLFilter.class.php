<?php

class com_wiris_quizzes_impl_MathMLFilter implements com_wiris_quizzes_api_MathFilter{
	public function __construct() { if(!php_Boot::$skip_constructor) {
		if(com_wiris_settings_PlatformSettings::$IS_FLASH || com_wiris_settings_PlatformSettings::$IS_JAVASCRIPT) {
			throw new HException("MathFilter is only available in server technologies.");
		}
	}}
	public function removeWirisPluginImages($html) {
		$start = 0;
		$end = 0;
		$sb = new StringBuf();
		while(($start = _hx_index_of($html, "<img", $end)) !== -1) {
			$sb->add(_hx_substr($html, $end, $start - $end));
			$end = _hx_index_of($html, "/>", $start) + 2;
			$img = _hx_substr($html, $start, $end - $start);
			if(_hx_index_of($img, "class=\"Wirisformula\"", null) !== -1) {
				$pos = _hx_index_of($img, "data-mathml", null);
				$pos = _hx_index_of($img, "\"", $pos) + 1;
				$endpos = _hx_index_of($img, "\"", $pos);
				$img = _hx_substr($img, $pos, $endpos - $pos);
				unset($pos,$endpos);
			}
			$sb->add($img);
			unset($img);
		}
		$sb->add(_hx_substr($html, $end, null));
		return $sb->b;
	}
	public function cacheImage($mathml, $filename) {
		$listener = new com_wiris_quizzes_impl_HttpSyncListener();
		$h = new com_wiris_quizzes_impl_HttpImpl(com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$EDITOR_URL) . "/render", $listener);
		$h->setParameter("mml", $mathml);
		$h->request(true);
		$response = $listener->getData();
		$b = haxe_io_Bytes::ofString($response);
		com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getImagesCache()->set($filename, $b);
	}
	public function mathml2img($mathml) {
		$md5 = haxe_Md5::encode($mathml);
		$filename = $md5 . ".png";
		$this->cacheImage($mathml, $filename);
		$url = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL) . "?service=cache&amp;name=" . $filename;
		return "<img src=\"" . $url . "\" align=\"middle\" />";
	}
	public function filter($html) {
		$h = new com_wiris_quizzes_impl_HTMLTools();
		$html = $this->removeWirisPluginImages($html);
		if($h->isMathMLEncoded($html)) {
			$html = $h->decodeMathML($html);
		}
		$sb = new StringBuf();
		$start = 0;
		$end = 0;
		while(($start = _hx_index_of($html, "<math", $end)) > -1) {
			$sb->add(_hx_substr($html, $end, $start - $end));
			$end = _hx_index_of($html, "</math>", $start);
			if($end === -1) {
				$end = _hx_index_of($html, "/>", $start) + strlen("/>");
			} else {
				$end += strlen("</math>");
			}
			$mathml = _hx_substr($html, $start, $end - $start);
			$img = $this->mathml2img($mathml);
			$sb->add($img);
			unset($mathml,$img);
		}
		$sb->add(_hx_substr($html, $end, null));
		return $sb->b;
	}
	function __toString() { return 'com.wiris.quizzes.impl.MathMLFilter'; }
}
