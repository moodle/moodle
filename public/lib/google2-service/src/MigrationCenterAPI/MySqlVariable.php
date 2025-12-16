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

namespace Google\Service\MigrationCenterAPI;

class MySqlVariable extends \Google\Model
{
  /**
   * Required. The variable category.
   *
   * @var string
   */
  public $category;
  /**
   * Required. The variable value.
   *
   * @var string
   */
  public $value;
  /**
   * Required. The variable name.
   *
   * @var string
   */
  public $variable;

  /**
   * Required. The variable category.
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Required. The variable value.
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
   * Required. The variable name.
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
class_alias(MySqlVariable::class, 'Google_Service_MigrationCenterAPI_MySqlVariable');
