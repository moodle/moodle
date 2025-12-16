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

class Answer extends \Google\Model
{
  protected $fileUploadAnswersType = FileUploadAnswers::class;
  protected $fileUploadAnswersDataType = '';
  protected $gradeType = Grade::class;
  protected $gradeDataType = '';
  /**
   * Output only. The question's ID. See also Question.question_id.
   *
   * @var string
   */
  public $questionId;
  protected $textAnswersType = TextAnswers::class;
  protected $textAnswersDataType = '';

  /**
   * Output only. The answers to a file upload question.
   *
   * @param FileUploadAnswers $fileUploadAnswers
   */
  public function setFileUploadAnswers(FileUploadAnswers $fileUploadAnswers)
  {
    $this->fileUploadAnswers = $fileUploadAnswers;
  }
  /**
   * @return FileUploadAnswers
   */
  public function getFileUploadAnswers()
  {
    return $this->fileUploadAnswers;
  }
  /**
   * Output only. The grade for the answer if the form was a quiz.
   *
   * @param Grade $grade
   */
  public function setGrade(Grade $grade)
  {
    $this->grade = $grade;
  }
  /**
   * @return Grade
   */
  public function getGrade()
  {
    return $this->grade;
  }
  /**
   * Output only. The question's ID. See also Question.question_id.
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
   * Output only. The specific answers as text.
   *
   * @param TextAnswers $textAnswers
   */
  public function setTextAnswers(TextAnswers $textAnswers)
  {
    $this->textAnswers = $textAnswers;
  }
  /**
   * @return TextAnswers
   */
  public function getTextAnswers()
  {
    return $this->textAnswers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Answer::class, 'Google_Service_Forms_Answer');
