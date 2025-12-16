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

namespace Google\Service\TravelImpactModel;

class EasaLabelMetadata extends \Google\Model
{
  protected $labelExpiryDateType = Date::class;
  protected $labelExpiryDateDataType = '';
  protected $labelIssueDateType = Date::class;
  protected $labelIssueDateDataType = '';
  /**
   * Version of the label.
   *
   * @var string
   */
  public $labelVersion;
  /**
   * Sustainable Aviation Fuel (SAF) emissions discount percentage applied to
   * the label. It is a percentage as a decimal. The values are in the interval
   * [0,1]. For example, 0.0021 means 0.21%. This discount and reduction in
   * emissions are reported by the EASA label but they are not included in the
   * CO2e estimates distributed by this API.
   *
   * @var 
   */
  public $safDiscountPercentage;

  /**
   * The date when the label expires. The label can be displayed until the end
   * of this date.
   *
   * @param Date $labelExpiryDate
   */
  public function setLabelExpiryDate(Date $labelExpiryDate)
  {
    $this->labelExpiryDate = $labelExpiryDate;
  }
  /**
   * @return Date
   */
  public function getLabelExpiryDate()
  {
    return $this->labelExpiryDate;
  }
  /**
   * The date when the label was issued.
   *
   * @param Date $labelIssueDate
   */
  public function setLabelIssueDate(Date $labelIssueDate)
  {
    $this->labelIssueDate = $labelIssueDate;
  }
  /**
   * @return Date
   */
  public function getLabelIssueDate()
  {
    return $this->labelIssueDate;
  }
  /**
   * Version of the label.
   *
   * @param string $labelVersion
   */
  public function setLabelVersion($labelVersion)
  {
    $this->labelVersion = $labelVersion;
  }
  /**
   * @return string
   */
  public function getLabelVersion()
  {
    return $this->labelVersion;
  }
  public function setSafDiscountPercentage($safDiscountPercentage)
  {
    $this->safDiscountPercentage = $safDiscountPercentage;
  }
  public function getSafDiscountPercentage()
  {
    return $this->safDiscountPercentage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EasaLabelMetadata::class, 'Google_Service_TravelImpactModel_EasaLabelMetadata');
