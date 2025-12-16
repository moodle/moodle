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

namespace Google\Service\Dfareporting;

class DimensionFilter extends \Google\Model
{
  /**
   * The name of the dimension to filter.
   *
   * @var string
   */
  public $dimensionName;
  /**
   * The kind of resource this is, in this case dfareporting#dimensionFilter.
   *
   * @var string
   */
  public $kind;
  /**
   * The value of the dimension to filter.
   *
   * @var string
   */
  public $value;

  /**
   * The name of the dimension to filter.
   *
   * @param string $dimensionName
   */
  public function setDimensionName($dimensionName)
  {
    $this->dimensionName = $dimensionName;
  }
  /**
   * @return string
   */
  public function getDimensionName()
  {
    return $this->dimensionName;
  }
  /**
   * The kind of resource this is, in this case dfareporting#dimensionFilter.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The value of the dimension to filter.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DimensionFilter::class, 'Google_Service_Dfareporting_DimensionFilter');
