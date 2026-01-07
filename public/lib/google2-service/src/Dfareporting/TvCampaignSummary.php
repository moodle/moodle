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

class TvCampaignSummary extends \Google\Model
{
  /**
   * Required to exist; do not use.
   */
  public const TYPE_CAMPAIGN_COMPONENT_TYPE_UNSPECIFIED = 'CAMPAIGN_COMPONENT_TYPE_UNSPECIFIED';
  /**
   * Company.
   */
  public const TYPE_COMPANY = 'COMPANY';
  /**
   * Brand.
   */
  public const TYPE_BRAND = 'BRAND';
  /**
   * Product.
   */
  public const TYPE_PRODUCT = 'PRODUCT';
  /**
   * Campaign.
   */
  public const TYPE_CAMPAIGN = 'CAMPAIGN';
  /**
   * The end date of the TV campaign, inclusive. A string of the format: "yyyy-
   * MM-dd".
   *
   * @var string
   */
  public $endDate;
  /**
   * GRP of this TV campaign.
   *
   * @var string
   */
  public $grp;
  /**
   * ID of this TV campaign.
   *
   * @var string
   */
  public $id;
  /**
   * Impressions across the entire TV campaign.
   *
   * @var string
   */
  public $impressions;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#tvCampaignSummary".
   *
   * @var string
   */
  public $kind;
  /**
   * Identifier. Name of this TV campaign.
   *
   * @var string
   */
  public $name;
  /**
   * Spend across the entire TV campaign.
   *
   * @var 
   */
  public $spend;
  /**
   * The start date of the TV campaign, inclusive. A string of the format:
   * "yyyy-MM-dd".
   *
   * @var string
   */
  public $startDate;
  /**
   * "CampaignComponentType" of this TV campaign.
   *
   * @var string
   */
  public $type;

  /**
   * The end date of the TV campaign, inclusive. A string of the format: "yyyy-
   * MM-dd".
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * GRP of this TV campaign.
   *
   * @param string $grp
   */
  public function setGrp($grp)
  {
    $this->grp = $grp;
  }
  /**
   * @return string
   */
  public function getGrp()
  {
    return $this->grp;
  }
  /**
   * ID of this TV campaign.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Impressions across the entire TV campaign.
   *
   * @param string $impressions
   */
  public function setImpressions($impressions)
  {
    $this->impressions = $impressions;
  }
  /**
   * @return string
   */
  public function getImpressions()
  {
    return $this->impressions;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#tvCampaignSummary".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Identifier. Name of this TV campaign.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  public function setSpend($spend)
  {
    $this->spend = $spend;
  }
  public function getSpend()
  {
    return $this->spend;
  }
  /**
   * The start date of the TV campaign, inclusive. A string of the format:
   * "yyyy-MM-dd".
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * "CampaignComponentType" of this TV campaign.
   *
   * Accepted values: CAMPAIGN_COMPONENT_TYPE_UNSPECIFIED, COMPANY, BRAND,
   * PRODUCT, CAMPAIGN
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TvCampaignSummary::class, 'Google_Service_Dfareporting_TvCampaignSummary');
