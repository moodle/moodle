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

namespace Google\Service\AccessContextManager;

class SupportedService extends \Google\Collection
{
  /**
   * Do not use this default value.
   */
  public const SERVICE_SUPPORT_STAGE_SERVICE_SUPPORT_STAGE_UNSPECIFIED = 'SERVICE_SUPPORT_STAGE_UNSPECIFIED';
  /**
   * GA features are open to all developers and are considered stable and fully
   * qualified for production use.
   */
  public const SERVICE_SUPPORT_STAGE_GA = 'GA';
  /**
   * PREVIEW indicates a pre-release stage where the product is functionally
   * complete but undergoing real-world testing.
   */
  public const SERVICE_SUPPORT_STAGE_PREVIEW = 'PREVIEW';
  /**
   * Deprecated features are scheduled to be shut down and removed.
   */
  public const SERVICE_SUPPORT_STAGE_DEPRECATED = 'DEPRECATED';
  /**
   * Do not use this default value.
   */
  public const SUPPORT_STAGE_LAUNCH_STAGE_UNSPECIFIED = 'LAUNCH_STAGE_UNSPECIFIED';
  /**
   * The feature is not yet implemented. Users can not use it.
   */
  public const SUPPORT_STAGE_UNIMPLEMENTED = 'UNIMPLEMENTED';
  /**
   * Prelaunch features are hidden from users and are only visible internally.
   */
  public const SUPPORT_STAGE_PRELAUNCH = 'PRELAUNCH';
  /**
   * Early Access features are limited to a closed group of testers. To use
   * these features, you must sign up in advance and sign a Trusted Tester
   * agreement (which includes confidentiality provisions). These features may
   * be unstable, changed in backward-incompatible ways, and are not guaranteed
   * to be released.
   */
  public const SUPPORT_STAGE_EARLY_ACCESS = 'EARLY_ACCESS';
  /**
   * Alpha is a limited availability test for releases before they are cleared
   * for widespread use. By Alpha, all significant design issues are resolved
   * and we are in the process of verifying functionality. Alpha customers need
   * to apply for access, agree to applicable terms, and have their projects
   * allowlisted. Alpha releases don't have to be feature complete, no SLAs are
   * provided, and there are no technical support obligations, but they will be
   * far enough along that customers can actually use them in test environments
   * or for limited-use tests -- just like they would in normal production
   * cases.
   */
  public const SUPPORT_STAGE_ALPHA = 'ALPHA';
  /**
   * Beta is the point at which we are ready to open a release for any customer
   * to use. There are no SLA or technical support obligations in a Beta
   * release. Products will be complete from a feature perspective, but may have
   * some open outstanding issues. Beta releases are suitable for limited
   * production use cases.
   */
  public const SUPPORT_STAGE_BETA = 'BETA';
  /**
   * GA features are open to all developers and are considered stable and fully
   * qualified for production use.
   */
  public const SUPPORT_STAGE_GA = 'GA';
  /**
   * Deprecated features are scheduled to be shut down and removed. For more
   * information, see the "Deprecation Policy" section of our [Terms of
   * Service](https://cloud.google.com/terms/) and the [Google Cloud Platform
   * Subject to the Deprecation
   * Policy](https://cloud.google.com/terms/deprecation) documentation.
   */
  public const SUPPORT_STAGE_DEPRECATED = 'DEPRECATED';
  protected $collection_key = 'supportedMethods';
  /**
   * True if the service is available on the restricted VIP. Services on the
   * restricted VIP typically either support VPC Service Controls or are core
   * infrastructure services required for the functioning of Google Cloud.
   *
   * @var bool
   */
  public $availableOnRestrictedVip;
  /**
   * True if the service is supported with some limitations. Check
   * [documentation](https://cloud.google.com/vpc-service-
   * controls/docs/supported-products) for details.
   *
   * @var bool
   */
  public $knownLimitations;
  /**
   * The service name or address of the supported service, such as
   * `service.googleapis.com`.
   *
   * @var string
   */
  public $name;
  /**
   * The support stage of the service.
   *
   * @var string
   */
  public $serviceSupportStage;
  /**
   * The support stage of the service.
   *
   * @var string
   */
  public $supportStage;
  protected $supportedMethodsType = MethodSelector::class;
  protected $supportedMethodsDataType = 'array';
  /**
   * The name of the supported product, such as 'Cloud Product API'.
   *
   * @var string
   */
  public $title;

  /**
   * True if the service is available on the restricted VIP. Services on the
   * restricted VIP typically either support VPC Service Controls or are core
   * infrastructure services required for the functioning of Google Cloud.
   *
   * @param bool $availableOnRestrictedVip
   */
  public function setAvailableOnRestrictedVip($availableOnRestrictedVip)
  {
    $this->availableOnRestrictedVip = $availableOnRestrictedVip;
  }
  /**
   * @return bool
   */
  public function getAvailableOnRestrictedVip()
  {
    return $this->availableOnRestrictedVip;
  }
  /**
   * True if the service is supported with some limitations. Check
   * [documentation](https://cloud.google.com/vpc-service-
   * controls/docs/supported-products) for details.
   *
   * @param bool $knownLimitations
   */
  public function setKnownLimitations($knownLimitations)
  {
    $this->knownLimitations = $knownLimitations;
  }
  /**
   * @return bool
   */
  public function getKnownLimitations()
  {
    return $this->knownLimitations;
  }
  /**
   * The service name or address of the supported service, such as
   * `service.googleapis.com`.
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
  /**
   * The support stage of the service.
   *
   * Accepted values: SERVICE_SUPPORT_STAGE_UNSPECIFIED, GA, PREVIEW, DEPRECATED
   *
   * @param self::SERVICE_SUPPORT_STAGE_* $serviceSupportStage
   */
  public function setServiceSupportStage($serviceSupportStage)
  {
    $this->serviceSupportStage = $serviceSupportStage;
  }
  /**
   * @return self::SERVICE_SUPPORT_STAGE_*
   */
  public function getServiceSupportStage()
  {
    return $this->serviceSupportStage;
  }
  /**
   * The support stage of the service.
   *
   * Accepted values: LAUNCH_STAGE_UNSPECIFIED, UNIMPLEMENTED, PRELAUNCH,
   * EARLY_ACCESS, ALPHA, BETA, GA, DEPRECATED
   *
   * @param self::SUPPORT_STAGE_* $supportStage
   */
  public function setSupportStage($supportStage)
  {
    $this->supportStage = $supportStage;
  }
  /**
   * @return self::SUPPORT_STAGE_*
   */
  public function getSupportStage()
  {
    return $this->supportStage;
  }
  /**
   * The list of the supported methods. This field exists only in response to
   * GetSupportedService
   *
   * @param MethodSelector[] $supportedMethods
   */
  public function setSupportedMethods($supportedMethods)
  {
    $this->supportedMethods = $supportedMethods;
  }
  /**
   * @return MethodSelector[]
   */
  public function getSupportedMethods()
  {
    return $this->supportedMethods;
  }
  /**
   * The name of the supported product, such as 'Cloud Product API'.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SupportedService::class, 'Google_Service_AccessContextManager_SupportedService');
