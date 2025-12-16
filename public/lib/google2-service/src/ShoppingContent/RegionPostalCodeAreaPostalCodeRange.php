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

class RegionPostalCodeAreaPostalCodeRange extends \Google\Model
{
  /**
   * Required. A postal code or a pattern of the form prefix* denoting the
   * inclusive lower bound of the range defining the area. Examples values:
   * "94108", "9410*", "9*".
   *
   * @var string
   */
  public $begin;
  /**
   * Optional. A postal code or a pattern of the form prefix* denoting the
   * inclusive upper bound of the range defining the area. It must have the same
   * length as postalCodeRangeBegin: if postalCodeRangeBegin is a postal code
   * then postalCodeRangeEnd must be a postal code too; if postalCodeRangeBegin
   * is a pattern then postalCodeRangeEnd must be a pattern with the same prefix
   * length. Optional: if not set, then the area is defined as being all the
   * postal codes matching postalCodeRangeBegin.
   *
   * @var string
   */
  public $end;

  /**
   * Required. A postal code or a pattern of the form prefix* denoting the
   * inclusive lower bound of the range defining the area. Examples values:
   * "94108", "9410*", "9*".
   *
   * @param string $begin
   */
  public function setBegin($begin)
  {
    $this->begin = $begin;
  }
  /**
   * @return string
   */
  public function getBegin()
  {
    return $this->begin;
  }
  /**
   * Optional. A postal code or a pattern of the form prefix* denoting the
   * inclusive upper bound of the range defining the area. It must have the same
   * length as postalCodeRangeBegin: if postalCodeRangeBegin is a postal code
   * then postalCodeRangeEnd must be a postal code too; if postalCodeRangeBegin
   * is a pattern then postalCodeRangeEnd must be a pattern with the same prefix
   * length. Optional: if not set, then the area is defined as being all the
   * postal codes matching postalCodeRangeBegin.
   *
   * @param string $end
   */
  public function setEnd($end)
  {
    $this->end = $end;
  }
  /**
   * @return string
   */
  public function getEnd()
  {
    return $this->end;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RegionPostalCodeAreaPostalCodeRange::class, 'Google_Service_ShoppingContent_RegionPostalCodeAreaPostalCodeRange');
