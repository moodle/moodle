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

class DeliveryAreaPostalCodeRange extends \Google\Model
{
  /**
   * Required. A postal code or a pattern of the form prefix* denoting the
   * inclusive lower bound of the range defining the area. Examples values:
   * `"94108"`, `"9410*"`, `"9*"`.
   *
   * @var string
   */
  public $firstPostalCode;
  /**
   * A postal code or a pattern of the form prefix* denoting the inclusive upper
   * bound of the range defining the area (for example [070* - 078*] results in
   * the range [07000 - 07899]). It must have the same length as
   * `firstPostalCode`: if `firstPostalCode` is a postal code then
   * `lastPostalCode` must be a postal code too; if firstPostalCode is a pattern
   * then `lastPostalCode` must be a pattern with the same prefix length.
   * Ignored if not set, then the area is defined as being all the postal codes
   * matching `firstPostalCode`.
   *
   * @var string
   */
  public $lastPostalCode;

  /**
   * Required. A postal code or a pattern of the form prefix* denoting the
   * inclusive lower bound of the range defining the area. Examples values:
   * `"94108"`, `"9410*"`, `"9*"`.
   *
   * @param string $firstPostalCode
   */
  public function setFirstPostalCode($firstPostalCode)
  {
    $this->firstPostalCode = $firstPostalCode;
  }
  /**
   * @return string
   */
  public function getFirstPostalCode()
  {
    return $this->firstPostalCode;
  }
  /**
   * A postal code or a pattern of the form prefix* denoting the inclusive upper
   * bound of the range defining the area (for example [070* - 078*] results in
   * the range [07000 - 07899]). It must have the same length as
   * `firstPostalCode`: if `firstPostalCode` is a postal code then
   * `lastPostalCode` must be a postal code too; if firstPostalCode is a pattern
   * then `lastPostalCode` must be a pattern with the same prefix length.
   * Ignored if not set, then the area is defined as being all the postal codes
   * matching `firstPostalCode`.
   *
   * @param string $lastPostalCode
   */
  public function setLastPostalCode($lastPostalCode)
  {
    $this->lastPostalCode = $lastPostalCode;
  }
  /**
   * @return string
   */
  public function getLastPostalCode()
  {
    return $this->lastPostalCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeliveryAreaPostalCodeRange::class, 'Google_Service_ShoppingContent_DeliveryAreaPostalCodeRange');
