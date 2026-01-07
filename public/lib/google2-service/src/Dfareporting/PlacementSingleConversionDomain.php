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

class PlacementSingleConversionDomain extends \Google\Model
{
  /**
   * @var string
   */
  public $conversionDomainId;
  /**
   * @var string
   */
  public $conversionDomainValue;

  /**
   * @param string $conversionDomainId
   */
  public function setConversionDomainId($conversionDomainId)
  {
    $this->conversionDomainId = $conversionDomainId;
  }
  /**
   * @return string
   */
  public function getConversionDomainId()
  {
    return $this->conversionDomainId;
  }
  /**
   * @param string $conversionDomainValue
   */
  public function setConversionDomainValue($conversionDomainValue)
  {
    $this->conversionDomainValue = $conversionDomainValue;
  }
  /**
   * @return string
   */
  public function getConversionDomainValue()
  {
    return $this->conversionDomainValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlacementSingleConversionDomain::class, 'Google_Service_Dfareporting_PlacementSingleConversionDomain');
