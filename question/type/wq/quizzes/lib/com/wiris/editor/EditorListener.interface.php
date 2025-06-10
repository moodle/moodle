<?php

interface com_wiris_editor_EditorListener {
	function transformationReceived($source, $transformation);
	function styleChanged($source);
	function contentChanged($source);
	function clipboardChanged($source);
	function caretPositionChanged($source);
}
