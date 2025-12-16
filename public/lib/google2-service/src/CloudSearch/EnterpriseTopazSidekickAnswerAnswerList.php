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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickAnswerAnswerList extends \Google\Collection
{
  public const TYPE_UNKNOWN = 'UNKNOWN';
  public const TYPE_PERSON_ADDRESS = 'PERSON_ADDRESS';
  public const TYPE_PERSON_BIRTHDAY = 'PERSON_BIRTHDAY';
  public const TYPE_PERSON_DEPARTMENT = 'PERSON_DEPARTMENT';
  public const TYPE_PERSON_DESK_LOCATION = 'PERSON_DESK_LOCATION';
  public const TYPE_PERSON_EMAIL = 'PERSON_EMAIL';
  public const TYPE_PERSON_JOB_TITLE = 'PERSON_JOB_TITLE';
  public const TYPE_PERSON_PHONE = 'PERSON_PHONE';
  protected $collection_key = 'labeledAnswer';
  protected $labeledAnswerType = EnterpriseTopazSidekickAnswerAnswerListLabeledAnswer::class;
  protected $labeledAnswerDataType = 'array';
  /**
   * Answer type.
   *
   * @var string
   */
  public $type;

  /**
   * Answers that have a corresponding label.
   *
   * @param EnterpriseTopazSidekickAnswerAnswerListLabeledAnswer[] $labeledAnswer
   */
  public function setLabeledAnswer($labeledAnswer)
  {
    $this->labeledAnswer = $labeledAnswer;
  }
  /**
   * @return EnterpriseTopazSidekickAnswerAnswerListLabeledAnswer[]
   */
  public function getLabeledAnswer()
  {
    return $this->labeledAnswer;
  }
  /**
   * Answer type.
   *
   * Accepted values: UNKNOWN, PERSON_ADDRESS, PERSON_BIRTHDAY,
   * PERSON_DEPARTMENT, PERSON_DESK_LOCATION, PERSON_EMAIL, PERSON_JOB_TITLE,
   * PERSON_PHONE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickAnswerAnswerList::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickAnswerAnswerList');
