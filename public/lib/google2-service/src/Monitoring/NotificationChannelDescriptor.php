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

namespace Google\Service\Monitoring;

class NotificationChannelDescriptor extends \Google\Collection
{
  /**
   * Do not use this default value.
   */
  public const LAUNCH_STAGE_LAUNCH_STAGE_UNSPECIFIED = 'LAUNCH_STAGE_UNSPECIFIED';
  /**
   * The feature is not yet implemented. Users can not use it.
   */
  public const LAUNCH_STAGE_UNIMPLEMENTED = 'UNIMPLEMENTED';
  /**
   * Prelaunch features are hidden from users and are only visible internally.
   */
  public const LAUNCH_STAGE_PRELAUNCH = 'PRELAUNCH';
  /**
   * Early Access features are limited to a closed group of testers. To use
   * these features, you must sign up in advance and sign a Trusted Tester
   * agreement (which includes confidentiality provisions). These features may
   * be unstable, changed in backward-incompatible ways, and are not guaranteed
   * to be released.
   */
  public const LAUNCH_STAGE_EARLY_ACCESS = 'EARLY_ACCESS';
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
  public const LAUNCH_STAGE_ALPHA = 'ALPHA';
  /**
   * Beta is the point at which we are ready to open a release for any customer
   * to use. There are no SLA or technical support obligations in a Beta
   * release. Products will be complete from a feature perspective, but may have
   * some open outstanding issues. Beta releases are suitable for limited
   * production use cases.
   */
  public const LAUNCH_STAGE_BETA = 'BETA';
  /**
   * GA features are open to all developers and are considered stable and fully
   * qualified for production use.
   */
  public const LAUNCH_STAGE_GA = 'GA';
  /**
   * Deprecated features are scheduled to be shut down and removed. For more
   * information, see the "Deprecation Policy" section of our Terms of Service
   * (https://cloud.google.com/terms/) and the Google Cloud Platform Subject to
   * the Deprecation Policy (https://cloud.google.com/terms/deprecation)
   * documentation.
   */
  public const LAUNCH_STAGE_DEPRECATED = 'DEPRECATED';
  protected $collection_key = 'supportedTiers';
  /**
   * A human-readable description of the notification channel type. The
   * description may include a description of the properties of the channel and
   * pointers to external documentation.
   *
   * @var string
   */
  public $description;
  /**
   * A human-readable name for the notification channel type. This form of the
   * name is suitable for a user interface.
   *
   * @var string
   */
  public $displayName;
  protected $labelsType = LabelDescriptor::class;
  protected $labelsDataType = 'array';
  /**
   * The product launch stage for channels of this type.
   *
   * @var string
   */
  public $launchStage;
  /**
   * The full REST resource name for this descriptor. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/notificationChannelDescriptors/[TYPE] In
   * the above, [TYPE] is the value of the type field.
   *
   * @var string
   */
  public $name;
  /**
   * The tiers that support this notification channel; the project service tier
   * must be one of the supported_tiers.
   *
   * @deprecated
   * @var string[]
   */
  public $supportedTiers;
  /**
   * The type of notification channel, such as "email" and "sms". To view the
   * full list of channels, see Channel descriptors
   * (https://cloud.google.com/monitoring/alerts/using-channels-api#ncd).
   * Notification channel types are globally unique.
   *
   * @var string
   */
  public $type;

  /**
   * A human-readable description of the notification channel type. The
   * description may include a description of the properties of the channel and
   * pointers to external documentation.
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
   * A human-readable name for the notification channel type. This form of the
   * name is suitable for a user interface.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The set of labels that must be defined to identify a particular channel of
   * the corresponding type. Each label includes a description for how that
   * field should be populated.
   *
   * @param LabelDescriptor[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return LabelDescriptor[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The product launch stage for channels of this type.
   *
   * Accepted values: LAUNCH_STAGE_UNSPECIFIED, UNIMPLEMENTED, PRELAUNCH,
   * EARLY_ACCESS, ALPHA, BETA, GA, DEPRECATED
   *
   * @param self::LAUNCH_STAGE_* $launchStage
   */
  public function setLaunchStage($launchStage)
  {
    $this->launchStage = $launchStage;
  }
  /**
   * @return self::LAUNCH_STAGE_*
   */
  public function getLaunchStage()
  {
    return $this->launchStage;
  }
  /**
   * The full REST resource name for this descriptor. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/notificationChannelDescriptors/[TYPE] In
   * the above, [TYPE] is the value of the type field.
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
   * The tiers that support this notification channel; the project service tier
   * must be one of the supported_tiers.
   *
   * @deprecated
   * @param string[] $supportedTiers
   */
  public function setSupportedTiers($supportedTiers)
  {
    $this->supportedTiers = $supportedTiers;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getSupportedTiers()
  {
    return $this->supportedTiers;
  }
  /**
   * The type of notification channel, such as "email" and "sms". To view the
   * full list of channels, see Channel descriptors
   * (https://cloud.google.com/monitoring/alerts/using-channels-api#ncd).
   * Notification channel types are globally unique.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NotificationChannelDescriptor::class, 'Google_Service_Monitoring_NotificationChannelDescriptor');
