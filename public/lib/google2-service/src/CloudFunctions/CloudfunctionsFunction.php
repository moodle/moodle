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

namespace Google\Service\CloudFunctions;

class CloudfunctionsFunction extends \Google\Collection
{
  /**
   * Unspecified
   */
  public const ENVIRONMENT_ENVIRONMENT_UNSPECIFIED = 'ENVIRONMENT_UNSPECIFIED';
  /**
   * Gen 1
   */
  public const ENVIRONMENT_GEN_1 = 'GEN_1';
  /**
   * Gen 2
   */
  public const ENVIRONMENT_GEN_2 = 'GEN_2';
  /**
   * Not specified. Invalid state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Function has been successfully deployed and is serving.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Function deployment failed and the function is not serving.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Function is being created or updated.
   */
  public const STATE_DEPLOYING = 'DEPLOYING';
  /**
   * Function is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Function deployment failed and the function serving state is undefined. The
   * function should be updated or deleted to move it out of this state.
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  /**
   * Function is being detached.
   */
  public const STATE_DETACHING = 'DETACHING';
  /**
   * Function detach failed and the function is still serving.
   */
  public const STATE_DETACH_FAILED = 'DETACH_FAILED';
  protected $collection_key = 'stateMessages';
  protected $buildConfigType = BuildConfig::class;
  protected $buildConfigDataType = '';
  /**
   * Output only. The create timestamp of a Cloud Function. This is only
   * applicable to 2nd Gen functions.
   *
   * @var string
   */
  public $createTime;
  /**
   * User-provided description of a function.
   *
   * @var string
   */
  public $description;
  /**
   * Describe whether the function is 1st Gen or 2nd Gen.
   *
   * @var string
   */
  public $environment;
  protected $eventTriggerType = EventTrigger::class;
  protected $eventTriggerDataType = '';
  /**
   * Resource name of a KMS crypto key (managed by the user) used to
   * encrypt/decrypt function resources. It must match the pattern `projects/{pr
   * oject}/locations/{location}/keyRings/{key_ring}/cryptoKeys/{crypto_key}`.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Labels associated with this Cloud Function.
   *
   * @var string[]
   */
  public $labels;
  /**
   * A user-defined name of the function. Function names must be unique globally
   * and match pattern `projects/locations/functions`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $serviceConfigType = ServiceConfig::class;
  protected $serviceConfigDataType = '';
  /**
   * Output only. State of the function.
   *
   * @var string
   */
  public $state;
  protected $stateMessagesType = GoogleCloudFunctionsV2StateMessage::class;
  protected $stateMessagesDataType = 'array';
  /**
   * Output only. The last update timestamp of a Cloud Function.
   *
   * @var string
   */
  public $updateTime;
  protected $upgradeInfoType = UpgradeInfo::class;
  protected $upgradeInfoDataType = '';
  /**
   * Output only. The deployed url for the function.
   *
   * @var string
   */
  public $url;

  /**
   * Describes the Build step of the function that builds a container from the
   * given source.
   *
   * @param BuildConfig $buildConfig
   */
  public function setBuildConfig(BuildConfig $buildConfig)
  {
    $this->buildConfig = $buildConfig;
  }
  /**
   * @return BuildConfig
   */
  public function getBuildConfig()
  {
    return $this->buildConfig;
  }
  /**
   * Output only. The create timestamp of a Cloud Function. This is only
   * applicable to 2nd Gen functions.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * User-provided description of a function.
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
   * Describe whether the function is 1st Gen or 2nd Gen.
   *
   * Accepted values: ENVIRONMENT_UNSPECIFIED, GEN_1, GEN_2
   *
   * @param self::ENVIRONMENT_* $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return self::ENVIRONMENT_*
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * An Eventarc trigger managed by Google Cloud Functions that fires events in
   * response to a condition in another service.
   *
   * @param EventTrigger $eventTrigger
   */
  public function setEventTrigger(EventTrigger $eventTrigger)
  {
    $this->eventTrigger = $eventTrigger;
  }
  /**
   * @return EventTrigger
   */
  public function getEventTrigger()
  {
    return $this->eventTrigger;
  }
  /**
   * Resource name of a KMS crypto key (managed by the user) used to
   * encrypt/decrypt function resources. It must match the pattern `projects/{pr
   * oject}/locations/{location}/keyRings/{key_ring}/cryptoKeys/{crypto_key}`.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Labels associated with this Cloud Function.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * A user-defined name of the function. Function names must be unique globally
   * and match pattern `projects/locations/functions`
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
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Describes the Service being deployed. Currently deploys services to Cloud
   * Run (fully managed).
   *
   * @param ServiceConfig $serviceConfig
   */
  public function setServiceConfig(ServiceConfig $serviceConfig)
  {
    $this->serviceConfig = $serviceConfig;
  }
  /**
   * @return ServiceConfig
   */
  public function getServiceConfig()
  {
    return $this->serviceConfig;
  }
  /**
   * Output only. State of the function.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, FAILED, DEPLOYING, DELETING,
   * UNKNOWN, DETACHING, DETACH_FAILED
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
  /**
   * Output only. State Messages for this Cloud Function.
   *
   * @param GoogleCloudFunctionsV2StateMessage[] $stateMessages
   */
  public function setStateMessages($stateMessages)
  {
    $this->stateMessages = $stateMessages;
  }
  /**
   * @return GoogleCloudFunctionsV2StateMessage[]
   */
  public function getStateMessages()
  {
    return $this->stateMessages;
  }
  /**
   * Output only. The last update timestamp of a Cloud Function.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. UpgradeInfo for this Cloud Function
   *
   * @param UpgradeInfo $upgradeInfo
   */
  public function setUpgradeInfo(UpgradeInfo $upgradeInfo)
  {
    $this->upgradeInfo = $upgradeInfo;
  }
  /**
   * @return UpgradeInfo
   */
  public function getUpgradeInfo()
  {
    return $this->upgradeInfo;
  }
  /**
   * Output only. The deployed url for the function.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudfunctionsFunction::class, 'Google_Service_CloudFunctions_CloudfunctionsFunction');
