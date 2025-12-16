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

namespace Google\Service\CivicInfo;

class DivisionByAddressResponse extends \Google\Model
{
  protected $divisionsType = GeographicDivision::class;
  protected $divisionsDataType = 'map';
  protected $normalizedInputType = SimpleAddressType::class;
  protected $normalizedInputDataType = '';

  /**
   * @param GeographicDivision[]
   */
  public function setDivisions($divisions)
  {
    $this->divisions = $divisions;
  }
  /**
   * @return GeographicDivision[]
   */
  public function getDivisions()
  {
    return $this->divisions;
  }
  /**
   * @param SimpleAddressType
   */
  public function setNormalizedInput(SimpleAddressType $normalizedInput)
  {
    $this->normalizedInput = $normalizedInput;
  }
  /**
   * @return SimpleAddressType
   */
  public function getNormalizedInput()
  {
    return $this->normalizedInput;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DivisionByAddressResponse::class, 'Google_Service_CivicInfo_DivisionByAddressResponse');
