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

namespace Google\Service\CloudHealthcare;

class ApplyAdminConsentsResponse extends \Google\Model
{
  /**
   * The number of resources (including the Consent resources) that may have
   * consent access change.
   *
   * @var string
   */
  public $affectedResources;
  /**
   * If `validate_only=false` in ApplyAdminConsentsRequest, this counter
   * contains the number of Consent resources that were successfully applied.
   * Otherwise, it is the number of Consent resources that are supported.
   *
   * @var string
   */
  public $consentApplySuccess;
  /**
   * The number of resources (including the Consent resources) that
   * ApplyAdminConsents failed to re-index.
   *
   * @var string
   */
  public $failedResources;

  /**
   * The number of resources (including the Consent resources) that may have
   * consent access change.
   *
   * @param string $affectedResources
   */
  public function setAffectedResources($affectedResources)
  {
    $this->affectedResources = $affectedResources;
  }
  /**
   * @return string
   */
  public function getAffectedResources()
  {
    return $this->affectedResources;
  }
  /**
   * If `validate_only=false` in ApplyAdminConsentsRequest, this counter
   * contains the number of Consent resources that were successfully applied.
   * Otherwise, it is the number of Consent resources that are supported.
   *
   * @param string $consentApplySuccess
   */
  public function setConsentApplySuccess($consentApplySuccess)
  {
    $this->consentApplySuccess = $consentApplySuccess;
  }
  /**
   * @return string
   */
  public function getConsentApplySuccess()
  {
    return $this->consentApplySuccess;
  }
  /**
   * The number of resources (including the Consent resources) that
   * ApplyAdminConsents failed to re-index.
   *
   * @param string $failedResources
   */
  public function setFailedResources($failedResources)
  {
    $this->failedResources = $failedResources;
  }
  /**
   * @return string
   */
  public function getFailedResources()
  {
    return $this->failedResources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplyAdminConsentsResponse::class, 'Google_Service_CloudHealthcare_ApplyAdminConsentsResponse');
