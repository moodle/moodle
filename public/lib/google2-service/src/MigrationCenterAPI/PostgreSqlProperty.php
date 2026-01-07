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

class PostgreSqlProperty extends \Google\Model
{
  /**
   * Required. The property is enabled.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Required. The property numeric value.
   *
   * @var string
   */
  public $numericValue;
  /**
   * Required. The property name.
   *
   * @var string
   */
  public $property;

  /**
   * Required. The property is enabled.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Required. The property numeric value.
   *
   * @param string $numericValue
   */
  public function setNumericValue($numericValue)
  {
    $this->numericValue = $numericValue;
  }
  /**
   * @return string
   */
  public function getNumericValue()
  {
    return $this->numericValue;
  }
  /**
   * Required. The property name.
   *
   * @param string $property
   */
  public function setProperty($property)
  {
    $this->property = $property;
  }
  /**
   * @return string
   */
  public function getProperty()
  {
    return $this->property;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PostgreSqlProperty::class, 'Google_Service_MigrationCenterAPI_PostgreSqlProperty');
