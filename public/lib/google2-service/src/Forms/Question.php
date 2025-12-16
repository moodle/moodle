<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Forms;

class Question extends \Google\Model
{
  protected $choiceQuestionType = ChoiceQuestion::class;
  protected $choiceQuestionDataType = '';
  protected $dateQuestionType = DateQuestion::class;
  protected $dateQuestionDataType = '';
  protected $fileUploadQuestionType = FileUploadQuestion::class;
  protected $fileUploadQuestionDataType = '';
  protected $gradingType = Grading::class;
  protected $gradingDataType = '';
  /**
   * Read only. The question ID. On creation, it can be provided but the ID must
   * not be already used in the form. If not provided, a new ID is assigned.
   *
   * @var string
   */
  public $questionId;
  protected $ratingQuestionType = RatingQuestion::class;
  protected $ratingQuestionDataType = '';
  /**
   * Whether the question must be answered in order for a respondent to submit
   * their response.
   *
   * @var bool
   */
  public $required;
  protected $rowQuestionType = RowQuestion::class;
  protected $rowQuestionDataType = '';
  protected $scaleQuestionType = ScaleQuestion::class;
  protected $scaleQuestionDataType = '';
  protected $textQuestionType = TextQuestion::class;
  protected $textQuestionDataType = '';
  protected $timeQuestionType = TimeQuestion::class;
  protected $timeQuestionDataType = '';

  /**
   * A respondent can choose from a pre-defined set of options.
   *
   * @param ChoiceQuestion $choiceQuestion
   */
  public function setChoiceQuestion(ChoiceQuestion $choiceQuestion)
  {
    $this->choiceQuestion = $choiceQuestion;
  }
  /**
   * @return ChoiceQuestion
   */
  public function getChoiceQuestion()
  {
    return $this->choiceQuestion;
  }
  /**
   * A respondent can enter a date.
   *
   * @param DateQuestion $dateQuestion
   */
  public function setDateQuestion(DateQuestion $dateQuestion)
  {
    $this->dateQuestion = $dateQuestion;
  }
  /**
   * @return DateQuestion
   */
  public function getDateQuestion()
  {
    return $this->dateQuestion;
  }
  /**
   * A respondent can upload one or more files.
   *
   * @param FileUploadQuestion $fileUploadQuestion
   */
  public function setFileUploadQuestion(FileUploadQuestion $fileUploadQuestion)
  {
    $this->fileUploadQuestion = $fileUploadQuestion;
  }
  /**
   * @return FileUploadQuestion
   */
  public function getFileUploadQuestion()
  {
    return $this->fileUploadQuestion;
  }
  /**
   * Grading setup for the question.
   *
   * @param Grading $grading
   */
  public function setGrading(Grading $grading)
  {
    $this->grading = $grading;
  }
  /**
   * @return Grading
   */
  public function getGrading()
  {
    return $this->grading;
  }
  /**
   * Read only. The question ID. On creation, it can be provided but the ID must
   * not be already used in the form. If not provided, a new ID is assigned.
   *
   * @param string $questionId
   */
  public function setQuestionId($questionId)
  {
    $this->questionId = $questionId;
  }
  /**
   * @return string
   */
  public function getQuestionId()
  {
    return $this->questionId;
  }
  /**
   * A respondent can choose a rating from a pre-defined set of icons.
   *
   * @param RatingQuestion $ratingQuestion
   */
  public function setRatingQuestion(RatingQuestion $ratingQuestion)
  {
    $this->ratingQuestion = $ratingQuestion;
  }
  /**
   * @return RatingQuestion
   */
  public function getRatingQuestion()
  {
    return $this->ratingQuestion;
  }
  /**
   * Whether the question must be answered in order for a respondent to submit
   * their response.
   *
   * @param bool $required
   */
  public function setRequired($required)
  {
    $this->required = $required;
  }
  /**
   * @return bool
   */
  public function getRequired()
  {
    return $this->required;
  }
  /**
   * A row of a QuestionGroupItem.
   *
   * @param RowQuestion $rowQuestion
   */
  public function setRowQuestion(RowQuestion $rowQuestion)
  {
    $this->rowQuestion = $rowQuestion;
  }
  /**
   * @return RowQuestion
   */
  public function getRowQuestion()
  {
    return $this->rowQuestion;
  }
  /**
   * A respondent can choose a number from a range.
   *
   * @param ScaleQuestion $scaleQuestion
   */
  public function setScaleQuestion(ScaleQuestion $scaleQuestion)
  {
    $this->scaleQuestion = $scaleQuestion;
  }
  /**
   * @return ScaleQuestion
   */
  public function getScaleQuestion()
  {
    return $this->scaleQuestion;
  }
  /**
   * A respondent can enter a free text response.
   *
   * @param TextQuestion $textQuestion
   */
  public function setTextQuestion(TextQuestion $textQuestion)
  {
    $this->textQuestion = $textQuestion;
  }
  /**
   * @return TextQuestion
   */
  public function getTextQuestion()
  {
    return $this->textQuestion;
  }
  /**
   * A respondent can enter a time.
   *
   * @param TimeQuestion $timeQuestion
   */
  public function setTimeQuestion(TimeQuestion $timeQuestion)
  {
    $this->timeQuestion = $timeQuestion;
  }
  /**
   * @return TimeQuestion
   */
  public function getTimeQuestion()
  {
    return $this->timeQuestion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Question::class, 'Google_Service_Forms_Question');
