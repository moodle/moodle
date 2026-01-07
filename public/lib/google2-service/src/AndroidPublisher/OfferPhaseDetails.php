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

namespace Google\Service\AndroidPublisher;

class OfferPhaseDetails extends \Google\Model
{
  protected $baseDetailsType = BaseDetails::class;
  protected $baseDetailsDataType = '';
  protected $freeTrialDetailsType = FreeTrialDetails::class;
  protected $freeTrialDetailsDataType = '';
  protected $introductoryPriceDetailsType = IntroductoryPriceDetails::class;
  protected $introductoryPriceDetailsDataType = '';
  protected $prorationPeriodDetailsType = ProrationPeriodDetails::class;
  protected $prorationPeriodDetailsDataType = '';

  /**
   * The order funds a base price period.
   *
   * @param BaseDetails $baseDetails
   */
  public function setBaseDetails(BaseDetails $baseDetails)
  {
    $this->baseDetails = $baseDetails;
  }
  /**
   * @return BaseDetails
   */
  public function getBaseDetails()
  {
    return $this->baseDetails;
  }
  /**
   * The order funds a free trial period.
   *
   * @param FreeTrialDetails $freeTrialDetails
   */
  public function setFreeTrialDetails(FreeTrialDetails $freeTrialDetails)
  {
    $this->freeTrialDetails = $freeTrialDetails;
  }
  /**
   * @return FreeTrialDetails
   */
  public function getFreeTrialDetails()
  {
    return $this->freeTrialDetails;
  }
  /**
   * The order funds an introductory pricing period.
   *
   * @param IntroductoryPriceDetails $introductoryPriceDetails
   */
  public function setIntroductoryPriceDetails(IntroductoryPriceDetails $introductoryPriceDetails)
  {
    $this->introductoryPriceDetails = $introductoryPriceDetails;
  }
  /**
   * @return IntroductoryPriceDetails
   */
  public function getIntroductoryPriceDetails()
  {
    return $this->introductoryPriceDetails;
  }
  /**
   * The order funds a proration period.
   *
   * @param ProrationPeriodDetails $prorationPeriodDetails
   */
  public function setProrationPeriodDetails(ProrationPeriodDetails $prorationPeriodDetails)
  {
    $this->prorationPeriodDetails = $prorationPeriodDetails;
  }
  /**
   * @return ProrationPeriodDetails
   */
  public function getProrationPeriodDetails()
  {
    return $this->prorationPeriodDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OfferPhaseDetails::class, 'Google_Service_AndroidPublisher_OfferPhaseDetails');
