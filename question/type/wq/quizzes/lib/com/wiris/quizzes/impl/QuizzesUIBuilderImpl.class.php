<?php

class com_wiris_quizzes_impl_QuizzesUIBuilderImpl implements com_wiris_quizzes_api_ui_QuizzesUIBuilder{
	public function __construct() { 
	}
	public function getAuthorAnswerFromCorrectAnswerIndex($slot, $correctAnswer) {
		if($correctAnswer < 0) {
			throw new HException("Invalid correct answer!");
		}
		$authorAnswers = $slot->getAuthorAnswers();
		{
			$_g = 0;
			while($_g < $authorAnswers->length) {
				$aa = $authorAnswers[$_g];
				++$_g;
				$aai = $aa;
				if($aai->id !== null && Std::parseInt($aai->id) === $correctAnswer) {
					return $aai;
				}
				unset($aai,$aa);
			}
		}
		while(true) {
			$aa = $slot->addNewAuthorAnswer("");
			$id = Std::parseInt($aa->id);
			if($id === $correctAnswer) {
				return $aa;
			} else {
				if($id > $correctAnswer) {
					throw new HException("Invalid correct answer!");
				}
			}
			unset($id,$aa);
		}
	}
	public function getSlotFromStudentAnswerIndex($question, $studentAnswer) {
		if($studentAnswer < 0) {
			throw new HException("Invalid student answer!");
		}
		$slots = $question->getSlots();
		{
			$_g = 0;
			while($_g < $slots->length) {
				$s = $slots[$_g];
				++$_g;
				$si = $s;
				if($si->id !== null && Std::parseInt($si->id) === $studentAnswer) {
					return $si;
				}
				unset($si,$s);
			}
		}
		while(true) {
			$s = $question->addNewSlot();
			$id = Std::parseInt($s->id);
			if($id === $studentAnswer) {
				return $s;
			} else {
				if($id > $studentAnswer) {
					throw new HException("Invalid student answer!");
				}
			}
			unset($s,$id);
		}
	}
	public function replaceFields($question, $instance, $element) {
		$instanceImpl = $instance;
		$instanceImpl->question = $question;
		com_wiris_quizzes_api_Quizzes::getInstance()->getQuizzesComponentBuilder()->replaceFields($question, $instance, $element);
	}
	public function getMathViewer() {
		return com_wiris_quizzes_api_Quizzes::getInstance()->getQuizzesComponentBuilder()->getMathViewer();
	}
	public function newAuxiliarCasField($question, $instance, $index) {
		$instanceImpl = $instance;
		$instanceImpl->question = $question;
		$slot = $this->getSlotFromStudentAnswerIndex($question, $index);
		return com_wiris_quizzes_api_Quizzes::getInstance()->getQuizzesComponentBuilder()->newAuxiliaryCasField($instance, $slot);
	}
	public function newEmbeddedAnswersEditor($question, $instance) {
		return com_wiris_quizzes_api_Quizzes::getInstance()->getQuizzesComponentBuilder()->newEmbeddedAnswersEditor($question, $instance);
	}
	public function newAuthoringField($question, $instance, $correctAnswer, $userAnswer) {
		$slot = $this->getSlotFromStudentAnswerIndex($question, $userAnswer);
		$authorAnswer = $this->getAuthorAnswerFromCorrectAnswerIndex($slot, $correctAnswer);
		return com_wiris_quizzes_api_Quizzes::getInstance()->getQuizzesComponentBuilder()->newAuthoringField($question, $slot, $authorAnswer);
	}
	public function newAnswerField($question, $instance, $index) {
		$instanceImpl = $instance;
		$instanceImpl->question = $question;
		$slot = $this->getSlotFromStudentAnswerIndex($question, $index);
		return com_wiris_quizzes_api_Quizzes::getInstance()->getQuizzesComponentBuilder()->newAnswerField($instance, $slot);
	}
	public function newAnswerFeedback($question, $instance, $correctAnswer, $studentAnswer) {
		$instanceImpl = $instance;
		$instanceImpl->question = $question;
		$slot = $this->getSlotFromStudentAnswerIndex($question, $studentAnswer);
		$authorAnswer = $this->getAuthorAnswerFromCorrectAnswerIndex($slot, $correctAnswer);
		return com_wiris_quizzes_api_Quizzes::getInstance()->getQuizzesComponentBuilder()->newAnswerFeedback($instance, $slot, $authorAnswer);
	}
	public function setLanguage($lang) {
		com_wiris_quizzes_api_Quizzes::getInstance()->getQuizzesComponentBuilder()->setLanguage($lang);
	}
	static function __meta__() { $»args = func_get_args(); return call_user_func_array(self::$__meta__, $»args); }
	static $__meta__;
	function __toString() { return 'com.wiris.quizzes.impl.QuizzesUIBuilderImpl'; }
}
com_wiris_quizzes_impl_QuizzesUIBuilderImpl::$__meta__ = _hx_anonymous(array("obj" => _hx_anonymous(array("Deprecated" => null))));
