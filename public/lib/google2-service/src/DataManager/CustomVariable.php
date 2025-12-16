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

namespace Google\Service\DataManager;

class CustomVariable extends \Google\Collection
{
  protected $collection_key = 'destinationReferences';
  /**
   * Optional. Reference string used to determine which of the
   * Event.destination_references the custom variable should be sent to. If
   * empty, the Event.destination_references will be used.
   *
   * @var string[]
   */
  public $destinationReferences;
  /**
   * Optional. The value to store for the custom variable.
   *
   * @var string
   */
  public $value;
  /**
   * Optional. The name of the custom variable to set. If the variable is not
   * found for the given destination, it will be ignored.
   *
   * @var string
   */
  public $variable;

  /**
   * Optional. Reference string used to determine which of the
   * Event.destination_references the custom variable should be sent to. If
   * empty, the Event.destination_references will be used.
   *
   * @param string[] $destinationReferences
   */
  public function setDestinationReferences($destinationReferences)
  {
    $this->destinationReferences = $destinationReferences;
  }
  /**
   * @return string[]
   */
  public function getDestinationReferences()
  {
    return $this->destinationReferences;
  }
  /**
   * Optional. The value to store for the custom variable.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
  /**
   * Optional. The name of the custom variable to set. If the variable is not
   * found for the given destination, it will be ignored.
   *
   * @param string $variable
   */
  public function setVariable($variable)
  {
    $this->variable = $variable;
  }
  /**
   * @return string
   */
  public function getVariable()
  {
    return $this->variable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomVariable::class, 'Google_Service_DataManager_CustomVariable');
