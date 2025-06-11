<?php

interface com_wiris_quizzes_api_QuestionRequest extends com_wiris_quizzes_api_Serializable{
	function prefixVariables($prefix, $variablesToPrefix);
	function isEmpty();
	function addMetaProperty($name, $value);
}
