<?php

interface com_wiris_quizzes_api_Question extends com_wiris_quizzes_api_Serializable{
	function getDeprecationWarnings();
	function setOption($name, $value);
	function addAssertion($name, $correctAnswer, $studentAnswer, $parameters);
	function getCorrectAnswersLength();
	function getCorrectAnswer($index);
	function setCorrectAnswer($index, $answer);
	function getStudentQuestion();
	function getProperty($name);
	function setProperty($name, $value);
	function getAlgorithm();
	function setAlgorithm($session);
	function getAnswerFieldType();
	function setAnswerFieldType($type);
	function removeSlot($slot);
	function addNewSlotFromModel($slot);
	function addNewSlot();
	function getSlots();
	//;
}
