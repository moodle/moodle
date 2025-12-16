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

class BasePlan extends \Google\Collection
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The base plan is currently in a draft state, and hasn't been activated. It
   * can be safely deleted at this point.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * The base plan is active and available for new subscribers.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The base plan is inactive and only available for existing subscribers.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  protected $collection_key = 'regionalConfigs';
  protected $autoRenewingBasePlanTypeType = AutoRenewingBasePlanType::class;
  protected $autoRenewingBasePlanTypeDataType = '';
  /**
   * Required. Immutable. The unique identifier of this base plan. Must be
   * unique within the subscription, and conform with RFC-1034. That is, this ID
   * can only contain lower-case letters (a-z), numbers (0-9), and hyphens (-),
   * and be at most 63 characters.
   *
   * @var string
   */
  public $basePlanId;
  protected $installmentsBasePlanTypeType = InstallmentsBasePlanType::class;
  protected $installmentsBasePlanTypeDataType = '';
  protected $offerTagsType = OfferTag::class;
  protected $offerTagsDataType = 'array';
  protected $otherRegionsConfigType = OtherRegionsBasePlanConfig::class;
  protected $otherRegionsConfigDataType = '';
  protected $prepaidBasePlanTypeType = PrepaidBasePlanType::class;
  protected $prepaidBasePlanTypeDataType = '';
  protected $regionalConfigsType = RegionalBasePlanConfig::class;
  protected $regionalConfigsDataType = 'array';
  /**
   * Output only. The state of the base plan, i.e. whether it's active. Draft
   * and inactive base plans can be activated or deleted. Active base plans can
   * be made inactive. Inactive base plans can be canceled. This field cannot be
   * changed by updating the resource. Use the dedicated endpoints instead.
   *
   * @var string
   */
  public $state;

  /**
   * Set when the base plan automatically renews at a regular interval.
   *
   * @param AutoRenewingBasePlanType $autoRenewingBasePlanType
   */
  public function setAutoRenewingBasePlanType(AutoRenewingBasePlanType $autoRenewingBasePlanType)
  {
    $this->autoRenewingBasePlanType = $autoRenewingBasePlanType;
  }
  /**
   * @return AutoRenewingBasePlanType
   */
  public function getAutoRenewingBasePlanType()
  {
    return $this->autoRenewingBasePlanType;
  }
  /**
   * Required. Immutable. The unique identifier of this base plan. Must be
   * unique within the subscription, and conform with RFC-1034. That is, this ID
   * can only contain lower-case letters (a-z), numbers (0-9), and hyphens (-),
   * and be at most 63 characters.
   *
   * @param string $basePlanId
   */
  public function setBasePlanId($basePlanId)
  {
    $this->basePlanId = $basePlanId;
  }
  /**
   * @return string
   */
  public function getBasePlanId()
  {
    return $this->basePlanId;
  }
  /**
   * Set for installments base plans where a user is committed to a specified
   * number of payments.
   *
   * @param InstallmentsBasePlanType $installmentsBasePlanType
   */
  public function setInstallmentsBasePlanType(InstallmentsBasePlanType $installmentsBasePlanType)
  {
    $this->installmentsBasePlanType = $installmentsBasePlanType;
  }
  /**
   * @return InstallmentsBasePlanType
   */
  public function getInstallmentsBasePlanType()
  {
    return $this->installmentsBasePlanType;
  }
  /**
   * List of up to 20 custom tags specified for this base plan, and returned to
   * the app through the billing library. Subscription offers for this base plan
   * will also receive these offer tags in the billing library.
   *
   * @param OfferTag[] $offerTags
   */
  public function setOfferTags($offerTags)
  {
    $this->offerTags = $offerTags;
  }
  /**
   * @return OfferTag[]
   */
  public function getOfferTags()
  {
    return $this->offerTags;
  }
  /**
   * Pricing information for any new locations Play may launch in the future. If
   * omitted, the BasePlan will not be automatically available any new locations
   * Play may launch in the future.
   *
   * @param OtherRegionsBasePlanConfig $otherRegionsConfig
   */
  public function setOtherRegionsConfig(OtherRegionsBasePlanConfig $otherRegionsConfig)
  {
    $this->otherRegionsConfig = $otherRegionsConfig;
  }
  /**
   * @return OtherRegionsBasePlanConfig
   */
  public function getOtherRegionsConfig()
  {
    return $this->otherRegionsConfig;
  }
  /**
   * Set when the base plan does not automatically renew at the end of the
   * billing period.
   *
   * @param PrepaidBasePlanType $prepaidBasePlanType
   */
  public function setPrepaidBasePlanType(PrepaidBasePlanType $prepaidBasePlanType)
  {
    $this->prepaidBasePlanType = $prepaidBasePlanType;
  }
  /**
   * @return PrepaidBasePlanType
   */
  public function getPrepaidBasePlanType()
  {
    return $this->prepaidBasePlanType;
  }
  /**
   * Region-specific information for this base plan.
   *
   * @param RegionalBasePlanConfig[] $regionalConfigs
   */
  public function setRegionalConfigs($regionalConfigs)
  {
    $this->regionalConfigs = $regionalConfigs;
  }
  /**
   * @return RegionalBasePlanConfig[]
   */
  public function getRegionalConfigs()
  {
    return $this->regionalConfigs;
  }
  /**
   * Output only. The state of the base plan, i.e. whether it's active. Draft
   * and inactive base plans can be activated or deleted. Active base plans can
   * be made inactive. Inactive base plans can be canceled. This field cannot be
   * changed by updating the resource. Use the dedicated endpoints instead.
   *
   * Accepted values: STATE_UNSPECIFIED, DRAFT, ACTIVE, INACTIVE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BasePlan::class, 'Google_Service_AndroidPublisher_BasePlan');
