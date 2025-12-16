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

namespace Google\Service\AddressValidation;

class GoogleMapsAddressvalidationV1AddressMetadata extends \Google\Model
{
  /**
   * Indicates that this is the address of a business. If unset, indicates that
   * the value is unknown.
   *
   * @var bool
   */
  public $business;
  /**
   * Indicates that the address of a PO box. If unset, indicates that the value
   * is unknown.
   *
   * @var bool
   */
  public $poBox;
  /**
   * Indicates that this is the address of a residence. If unset, indicates that
   * the value is unknown.
   *
   * @var bool
   */
  public $residential;

  /**
   * Indicates that this is the address of a business. If unset, indicates that
   * the value is unknown.
   *
   * @param bool $business
   */
  public function setBusiness($business)
  {
    $this->business = $business;
  }
  /**
   * @return bool
   */
  public function getBusiness()
  {
    return $this->business;
  }
  /**
   * Indicates that the address of a PO box. If unset, indicates that the value
   * is unknown.
   *
   * @param bool $poBox
   */
  public function setPoBox($poBox)
  {
    $this->poBox = $poBox;
  }
  /**
   * @return bool
   */
  public function getPoBox()
  {
    return $this->poBox;
  }
  /**
   * Indicates that this is the address of a residence. If unset, indicates that
   * the value is unknown.
   *
   * @param bool $residential
   */
  public function setResidential($residential)
  {
    $this->residential = $residential;
  }
  /**
   * @return bool
   */
  public function getResidential()
  {
    return $this->residential;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsAddressvalidationV1AddressMetadata::class, 'Google_Service_AddressValidation_GoogleMapsAddressvalidationV1AddressMetadata');
