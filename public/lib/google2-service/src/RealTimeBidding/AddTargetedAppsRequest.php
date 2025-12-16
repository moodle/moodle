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

namespace Google\Service\RealTimeBidding;

class AddTargetedAppsRequest extends \Google\Collection
{
  /**
   * Placeholder for undefined targeting mode.
   */
  public const TARGETING_MODE_TARGETING_MODE_UNSPECIFIED = 'TARGETING_MODE_UNSPECIFIED';
  /**
   * The inclusive list type. Inventory must match an item in this list to be
   * targeted.
   */
  public const TARGETING_MODE_INCLUSIVE = 'INCLUSIVE';
  /**
   * The exclusive list type. Inventory must not match any item in this list to
   * be targeted.
   */
  public const TARGETING_MODE_EXCLUSIVE = 'EXCLUSIVE';
  protected $collection_key = 'appIds';
  /**
   * A list of app IDs to target in the pretargeting configuration. These values
   * will be added to the list of targeted app IDs in
   * PretargetingConfig.appTargeting.mobileAppTargeting.values.
   *
   * @var string[]
   */
  public $appIds;
  /**
   * Required. The targeting mode that should be applied to the list of app IDs.
   * If there are existing targeted app IDs, must be equal to the existing
   * PretargetingConfig.appTargeting.mobileAppTargeting.targetingMode or a 400
   * bad request error will be returned.
   *
   * @var string
   */
  public $targetingMode;

  /**
   * A list of app IDs to target in the pretargeting configuration. These values
   * will be added to the list of targeted app IDs in
   * PretargetingConfig.appTargeting.mobileAppTargeting.values.
   *
   * @param string[] $appIds
   */
  public function setAppIds($appIds)
  {
    $this->appIds = $appIds;
  }
  /**
   * @return string[]
   */
  public function getAppIds()
  {
    return $this->appIds;
  }
  /**
   * Required. The targeting mode that should be applied to the list of app IDs.
   * If there are existing targeted app IDs, must be equal to the existing
   * PretargetingConfig.appTargeting.mobileAppTargeting.targetingMode or a 400
   * bad request error will be returned.
   *
   * Accepted values: TARGETING_MODE_UNSPECIFIED, INCLUSIVE, EXCLUSIVE
   *
   * @param self::TARGETING_MODE_* $targetingMode
   */
  public function setTargetingMode($targetingMode)
  {
    $this->targetingMode = $targetingMode;
  }
  /**
   * @return self::TARGETING_MODE_*
   */
  public function getTargetingMode()
  {
    return $this->targetingMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddTargetedAppsRequest::class, 'Google_Service_RealTimeBidding_AddTargetedAppsRequest');
