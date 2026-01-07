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

namespace Google\Service\AndroidManagement;

class ComplianceRule extends \Google\Collection
{
  protected $collection_key = 'packageNamesToDisable';
  protected $apiLevelConditionType = ApiLevelCondition::class;
  protected $apiLevelConditionDataType = '';
  /**
   * If set to true, the rule includes a mitigating action to disable apps so
   * that the device is effectively disabled, but app data is preserved. If the
   * device is running an app in locked task mode, the app will be closed and a
   * UI showing the reason for non-compliance will be displayed.
   *
   * @var bool
   */
  public $disableApps;
  protected $nonComplianceDetailConditionType = NonComplianceDetailCondition::class;
  protected $nonComplianceDetailConditionDataType = '';
  /**
   * If set, the rule includes a mitigating action to disable apps specified in
   * the list, but app data is preserved.
   *
   * @var string[]
   */
  public $packageNamesToDisable;

  /**
   * A condition which is satisfied if the Android Framework API level on the
   * device doesn't meet a minimum requirement.
   *
   * @param ApiLevelCondition $apiLevelCondition
   */
  public function setApiLevelCondition(ApiLevelCondition $apiLevelCondition)
  {
    $this->apiLevelCondition = $apiLevelCondition;
  }
  /**
   * @return ApiLevelCondition
   */
  public function getApiLevelCondition()
  {
    return $this->apiLevelCondition;
  }
  /**
   * If set to true, the rule includes a mitigating action to disable apps so
   * that the device is effectively disabled, but app data is preserved. If the
   * device is running an app in locked task mode, the app will be closed and a
   * UI showing the reason for non-compliance will be displayed.
   *
   * @param bool $disableApps
   */
  public function setDisableApps($disableApps)
  {
    $this->disableApps = $disableApps;
  }
  /**
   * @return bool
   */
  public function getDisableApps()
  {
    return $this->disableApps;
  }
  /**
   * A condition which is satisfied if there exists any matching
   * NonComplianceDetail for the device.
   *
   * @param NonComplianceDetailCondition $nonComplianceDetailCondition
   */
  public function setNonComplianceDetailCondition(NonComplianceDetailCondition $nonComplianceDetailCondition)
  {
    $this->nonComplianceDetailCondition = $nonComplianceDetailCondition;
  }
  /**
   * @return NonComplianceDetailCondition
   */
  public function getNonComplianceDetailCondition()
  {
    return $this->nonComplianceDetailCondition;
  }
  /**
   * If set, the rule includes a mitigating action to disable apps specified in
   * the list, but app data is preserved.
   *
   * @param string[] $packageNamesToDisable
   */
  public function setPackageNamesToDisable($packageNamesToDisable)
  {
    $this->packageNamesToDisable = $packageNamesToDisable;
  }
  /**
   * @return string[]
   */
  public function getPackageNamesToDisable()
  {
    return $this->packageNamesToDisable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComplianceRule::class, 'Google_Service_AndroidManagement_ComplianceRule');
