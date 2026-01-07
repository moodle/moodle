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

namespace Google\Service\ShoppingContent;

class ActionInput extends \Google\Collection
{
  protected $collection_key = 'inputValues';
  /**
   * Required. Id of the selected action flow.
   *
   * @var string
   */
  public $actionFlowId;
  protected $inputValuesType = InputValue::class;
  protected $inputValuesDataType = 'array';

  /**
   * Required. Id of the selected action flow.
   *
   * @param string $actionFlowId
   */
  public function setActionFlowId($actionFlowId)
  {
    $this->actionFlowId = $actionFlowId;
  }
  /**
   * @return string
   */
  public function getActionFlowId()
  {
    return $this->actionFlowId;
  }
  /**
   * Required. Values for input fields.
   *
   * @param InputValue[] $inputValues
   */
  public function setInputValues($inputValues)
  {
    $this->inputValues = $inputValues;
  }
  /**
   * @return InputValue[]
   */
  public function getInputValues()
  {
    return $this->inputValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActionInput::class, 'Google_Service_ShoppingContent_ActionInput');
