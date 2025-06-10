<?php

interface com_wiris_quizzes_api_Slot extends com_wiris_quizzes_api_Serializable{
	function getGrammarUrl();
	function copy($model);
	function getAnswerFieldType();
	function setAnswerFieldType($answerFieldType);
	function setInitialContent($content);
	function getInitialContent();
	function setSyntax($type);
	function getSyntax();
	function getProperty($name);
	function setProperty($name, $value);
	function removeAuthorAnswer($answer);
	function addNewAuthorAnswer($value);
	function getAuthorAnswers();
}
