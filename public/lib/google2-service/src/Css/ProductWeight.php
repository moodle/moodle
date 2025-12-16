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

namespace Google\Service\Css;

class ProductWeight extends \Google\Model
{
  /**
   * Required. The weight unit. Acceptable values are: * "`g`" * "`kg`" * "`oz`"
   * * "`lb`"
   *
   * @var string
   */
  public $unit;
  /**
   * Required. The weight represented as a number. The weight can have a maximum
   * precision of four decimal places.
   *
   * @var 
   */
  public $value;

  /**
   * Required. The weight unit. Acceptable values are: * "`g`" * "`kg`" * "`oz`"
   * * "`lb`"
   *
   * @param string $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return string
   */
  public function getUnit()
  {
    return $this->unit;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductWeight::class, 'Google_Service_Css_ProductWeight');
