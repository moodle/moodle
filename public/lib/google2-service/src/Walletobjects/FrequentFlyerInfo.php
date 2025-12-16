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

namespace Google\Service\Walletobjects;

class FrequentFlyerInfo extends \Google\Model
{
  /**
   * Frequent flyer number. Required for each nested object of kind
   * `walletobjects#frequentFlyerInfo`.
   *
   * @var string
   */
  public $frequentFlyerNumber;
  protected $frequentFlyerProgramNameType = LocalizedString::class;
  protected $frequentFlyerProgramNameDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#frequentFlyerInfo"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;

  /**
   * Frequent flyer number. Required for each nested object of kind
   * `walletobjects#frequentFlyerInfo`.
   *
   * @param string $frequentFlyerNumber
   */
  public function setFrequentFlyerNumber($frequentFlyerNumber)
  {
    $this->frequentFlyerNumber = $frequentFlyerNumber;
  }
  /**
   * @return string
   */
  public function getFrequentFlyerNumber()
  {
    return $this->frequentFlyerNumber;
  }
  /**
   * Frequent flyer program name. eg: "Lufthansa Miles & More"
   *
   * @param LocalizedString $frequentFlyerProgramName
   */
  public function setFrequentFlyerProgramName(LocalizedString $frequentFlyerProgramName)
  {
    $this->frequentFlyerProgramName = $frequentFlyerProgramName;
  }
  /**
   * @return LocalizedString
   */
  public function getFrequentFlyerProgramName()
  {
    return $this->frequentFlyerProgramName;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#frequentFlyerInfo"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FrequentFlyerInfo::class, 'Google_Service_Walletobjects_FrequentFlyerInfo');
