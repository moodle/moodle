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

namespace Google\Service\Sheets;

class DataValidationRule extends \Google\Model
{
  protected $conditionType = BooleanCondition::class;
  protected $conditionDataType = '';
  /**
   * A message to show the user when adding data to the cell.
   *
   * @var string
   */
  public $inputMessage;
  /**
   * True if the UI should be customized based on the kind of condition. If
   * true, "List" conditions will show a dropdown.
   *
   * @var bool
   */
  public $showCustomUi;
  /**
   * True if invalid data should be rejected.
   *
   * @var bool
   */
  public $strict;

  /**
   * The condition that data in the cell must match.
   *
   * @param BooleanCondition $condition
   */
  public function setCondition(BooleanCondition $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return BooleanCondition
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * A message to show the user when adding data to the cell.
   *
   * @param string $inputMessage
   */
  public function setInputMessage($inputMessage)
  {
    $this->inputMessage = $inputMessage;
  }
  /**
   * @return string
   */
  public function getInputMessage()
  {
    return $this->inputMessage;
  }
  /**
   * True if the UI should be customized based on the kind of condition. If
   * true, "List" conditions will show a dropdown.
   *
   * @param bool $showCustomUi
   */
  public function setShowCustomUi($showCustomUi)
  {
    $this->showCustomUi = $showCustomUi;
  }
  /**
   * @return bool
   */
  public function getShowCustomUi()
  {
    return $this->showCustomUi;
  }
  /**
   * True if invalid data should be rejected.
   *
   * @param bool $strict
   */
  public function setStrict($strict)
  {
    $this->strict = $strict;
  }
  /**
   * @return bool
   */
  public function getStrict()
  {
    return $this->strict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataValidationRule::class, 'Google_Service_Sheets_DataValidationRule');
