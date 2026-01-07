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

class GoogleMapsAddressvalidationV1PlusCode extends \Google\Model
{
  /**
   * Place's compound code, such as "33GV+HQ, Ramberg, Norway", containing the
   * suffix of the global code and replacing the prefix with a formatted name of
   * a reference entity.
   *
   * @var string
   */
  public $compoundCode;
  /**
   * Place's global (full) code, such as "9FWM33GV+HQ", representing an 1/8000
   * by 1/8000 degree area (~14 by 14 meters).
   *
   * @var string
   */
  public $globalCode;

  /**
   * Place's compound code, such as "33GV+HQ, Ramberg, Norway", containing the
   * suffix of the global code and replacing the prefix with a formatted name of
   * a reference entity.
   *
   * @param string $compoundCode
   */
  public function setCompoundCode($compoundCode)
  {
    $this->compoundCode = $compoundCode;
  }
  /**
   * @return string
   */
  public function getCompoundCode()
  {
    return $this->compoundCode;
  }
  /**
   * Place's global (full) code, such as "9FWM33GV+HQ", representing an 1/8000
   * by 1/8000 degree area (~14 by 14 meters).
   *
   * @param string $globalCode
   */
  public function setGlobalCode($globalCode)
  {
    $this->globalCode = $globalCode;
  }
  /**
   * @return string
   */
  public function getGlobalCode()
  {
    return $this->globalCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsAddressvalidationV1PlusCode::class, 'Google_Service_AddressValidation_GoogleMapsAddressvalidationV1PlusCode');
