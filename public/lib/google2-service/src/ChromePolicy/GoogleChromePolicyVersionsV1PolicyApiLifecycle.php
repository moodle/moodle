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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1PolicyApiLifecycle extends \Google\Collection
{
  /**
   * Policy Api Lifecycle is Unspecified.
   */
  public const POLICY_API_LIFECYCLE_STAGE_API_UNSPECIFIED = 'API_UNSPECIFIED';
  /**
   * Policy is not working yet, but giving developers heads up on format. This
   * stage can transfer to API_DEVELOPEMNT or API_CURRENT.
   */
  public const POLICY_API_LIFECYCLE_STAGE_API_PREVIEW = 'API_PREVIEW';
  /**
   * Policy can change format in backward incompatible way (breaking change).
   * This stage can transfer to API_CURRENT or API_DEPRECATED. This could be
   * used for policies launched only to TTs or launched to selected customers
   * for emergency usage.
   */
  public const POLICY_API_LIFECYCLE_STAGE_API_DEVELOPMENT = 'API_DEVELOPMENT';
  /**
   * Policy in official format. Policy can change format in backward compatible
   * way (non-breaking change). Example: this policy can introduce a new field,
   * which is considered non-breaking change, when field masks are properly
   * utilized. This stage can transfer to API_DEPRECATED.
   */
  public const POLICY_API_LIFECYCLE_STAGE_API_CURRENT = 'API_CURRENT';
  /**
   * Please stop using this policy. This policy is deprecated and may/will be
   * removed in the future. Most likely a new policy was introduced to replace
   * this one.
   */
  public const POLICY_API_LIFECYCLE_STAGE_API_DEPRECATED = 'API_DEPRECATED';
  protected $collection_key = 'scheduledToDeprecatePolicies';
  /**
   * In the event that this policy was deprecated in favor of another policy,
   * the fully qualified namespace(s) of the new policies as they will show in
   * PolicyAPI. Could only be set if policy_api_lifecycle_stage is
   * API_DEPRECATED.
   *
   * @var string[]
   */
  public $deprecatedInFavorOf;
  /**
   * Description about current life cycle.
   *
   * @var string
   */
  public $description;
  protected $endSupportType = GoogleTypeDate::class;
  protected $endSupportDataType = '';
  /**
   * Indicates current life cycle stage of the policy API.
   *
   * @var string
   */
  public $policyApiLifecycleStage;
  /**
   * Corresponding to deprecated_in_favor_of, the fully qualified namespace(s)
   * of the old policies that will be deprecated because of introduction of this
   * policy.
   *
   * @var string[]
   */
  public $scheduledToDeprecatePolicies;

  /**
   * In the event that this policy was deprecated in favor of another policy,
   * the fully qualified namespace(s) of the new policies as they will show in
   * PolicyAPI. Could only be set if policy_api_lifecycle_stage is
   * API_DEPRECATED.
   *
   * @param string[] $deprecatedInFavorOf
   */
  public function setDeprecatedInFavorOf($deprecatedInFavorOf)
  {
    $this->deprecatedInFavorOf = $deprecatedInFavorOf;
  }
  /**
   * @return string[]
   */
  public function getDeprecatedInFavorOf()
  {
    return $this->deprecatedInFavorOf;
  }
  /**
   * Description about current life cycle.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * End supporting date for current policy. Attempting to modify a policy after
   * its end support date will result in a Bad Request (400 error). Could only
   * be set if policy_api_lifecycle_stage is API_DEPRECATED.
   *
   * @param GoogleTypeDate $endSupport
   */
  public function setEndSupport(GoogleTypeDate $endSupport)
  {
    $this->endSupport = $endSupport;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getEndSupport()
  {
    return $this->endSupport;
  }
  /**
   * Indicates current life cycle stage of the policy API.
   *
   * Accepted values: API_UNSPECIFIED, API_PREVIEW, API_DEVELOPMENT,
   * API_CURRENT, API_DEPRECATED
   *
   * @param self::POLICY_API_LIFECYCLE_STAGE_* $policyApiLifecycleStage
   */
  public function setPolicyApiLifecycleStage($policyApiLifecycleStage)
  {
    $this->policyApiLifecycleStage = $policyApiLifecycleStage;
  }
  /**
   * @return self::POLICY_API_LIFECYCLE_STAGE_*
   */
  public function getPolicyApiLifecycleStage()
  {
    return $this->policyApiLifecycleStage;
  }
  /**
   * Corresponding to deprecated_in_favor_of, the fully qualified namespace(s)
   * of the old policies that will be deprecated because of introduction of this
   * policy.
   *
   * @param string[] $scheduledToDeprecatePolicies
   */
  public function setScheduledToDeprecatePolicies($scheduledToDeprecatePolicies)
  {
    $this->scheduledToDeprecatePolicies = $scheduledToDeprecatePolicies;
  }
  /**
   * @return string[]
   */
  public function getScheduledToDeprecatePolicies()
  {
    return $this->scheduledToDeprecatePolicies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1PolicyApiLifecycle::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1PolicyApiLifecycle');
