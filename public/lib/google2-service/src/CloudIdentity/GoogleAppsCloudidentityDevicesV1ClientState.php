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

namespace Google\Service\CloudIdentity;

class GoogleAppsCloudidentityDevicesV1ClientState extends \Google\Collection
{
  /**
   * The compliance state of the resource is unknown or unspecified.
   */
  public const COMPLIANCE_STATE_COMPLIANCE_STATE_UNSPECIFIED = 'COMPLIANCE_STATE_UNSPECIFIED';
  /**
   * Device is compliant with third party policies
   */
  public const COMPLIANCE_STATE_COMPLIANT = 'COMPLIANT';
  /**
   * Device is not compliant with third party policies
   */
  public const COMPLIANCE_STATE_NON_COMPLIANT = 'NON_COMPLIANT';
  /**
   * Default value
   */
  public const HEALTH_SCORE_HEALTH_SCORE_UNSPECIFIED = 'HEALTH_SCORE_UNSPECIFIED';
  /**
   * The object is in very poor health as defined by the caller.
   */
  public const HEALTH_SCORE_VERY_POOR = 'VERY_POOR';
  /**
   * The object is in poor health as defined by the caller.
   */
  public const HEALTH_SCORE_POOR = 'POOR';
  /**
   * The object health is neither good nor poor, as defined by the caller.
   */
  public const HEALTH_SCORE_NEUTRAL = 'NEUTRAL';
  /**
   * The object is in good health as defined by the caller.
   */
  public const HEALTH_SCORE_GOOD = 'GOOD';
  /**
   * The object is in very good health as defined by the caller.
   */
  public const HEALTH_SCORE_VERY_GOOD = 'VERY_GOOD';
  /**
   * The management state of the resource is unknown or unspecified.
   */
  public const MANAGED_MANAGED_STATE_UNSPECIFIED = 'MANAGED_STATE_UNSPECIFIED';
  /**
   * The resource is managed.
   */
  public const MANAGED_MANAGED = 'MANAGED';
  /**
   * The resource is not managed.
   */
  public const MANAGED_UNMANAGED = 'UNMANAGED';
  /**
   * Unknown owner type
   */
  public const OWNER_TYPE_OWNER_TYPE_UNSPECIFIED = 'OWNER_TYPE_UNSPECIFIED';
  /**
   * Customer is the owner
   */
  public const OWNER_TYPE_OWNER_TYPE_CUSTOMER = 'OWNER_TYPE_CUSTOMER';
  /**
   * Partner is the owner
   */
  public const OWNER_TYPE_OWNER_TYPE_PARTNER = 'OWNER_TYPE_PARTNER';
  protected $collection_key = 'assetTags';
  /**
   * The caller can specify asset tags for this resource
   *
   * @var string[]
   */
  public $assetTags;
  /**
   * The compliance state of the resource as specified by the API client.
   *
   * @var string
   */
  public $complianceState;
  /**
   * Output only. The time the client state data was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * This field may be used to store a unique identifier for the API resource
   * within which these CustomAttributes are a field.
   *
   * @var string
   */
  public $customId;
  /**
   * The token that needs to be passed back for concurrency control in updates.
   * Token needs to be passed back in UpdateRequest
   *
   * @var string
   */
  public $etag;
  /**
   * The Health score of the resource. The Health score is the callers
   * specification of the condition of the device from a usability point of
   * view. For example, a third-party device management provider may specify a
   * health score based on its compliance with organizational policies.
   *
   * @var string
   */
  public $healthScore;
  protected $keyValuePairsType = GoogleAppsCloudidentityDevicesV1CustomAttributeValue::class;
  protected $keyValuePairsDataType = 'map';
  /**
   * Output only. The time the client state data was last updated.
   *
   * @var string
   */
  public $lastUpdateTime;
  /**
   * The management state of the resource as specified by the API client.
   *
   * @var string
   */
  public $managed;
  /**
   * Output only. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the
   * ClientState in format:
   * `devices/{device}/deviceUsers/{device_user}/clientState/{partner}`, where
   * partner corresponds to the partner storing the data. For partners belonging
   * to the "BeyondCorp Alliance", this is the partner ID specified to you by
   * Google. For all other callers, this is a string of the form:
   * `{customer}-suffix`, where `customer` is your customer ID. The *suffix* is
   * any string the caller specifies. This string will be displayed verbatim in
   * the administration console. This suffix is used in setting up Custom Access
   * Levels in Context-Aware Access. Your organization's customer ID can be
   * obtained from the URL: `GET
   * https://www.googleapis.com/admin/directory/v1/customers/my_customer` The
   * `id` field in the response contains the customer ID starting with the
   * letter 'C'. The customer ID to be used in this API is the string after the
   * letter 'C' (not including 'C')
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The owner of the ClientState
   *
   * @var string
   */
  public $ownerType;
  /**
   * A descriptive cause of the health score.
   *
   * @var string
   */
  public $scoreReason;

  /**
   * The caller can specify asset tags for this resource
   *
   * @param string[] $assetTags
   */
  public function setAssetTags($assetTags)
  {
    $this->assetTags = $assetTags;
  }
  /**
   * @return string[]
   */
  public function getAssetTags()
  {
    return $this->assetTags;
  }
  /**
   * The compliance state of the resource as specified by the API client.
   *
   * Accepted values: COMPLIANCE_STATE_UNSPECIFIED, COMPLIANT, NON_COMPLIANT
   *
   * @param self::COMPLIANCE_STATE_* $complianceState
   */
  public function setComplianceState($complianceState)
  {
    $this->complianceState = $complianceState;
  }
  /**
   * @return self::COMPLIANCE_STATE_*
   */
  public function getComplianceState()
  {
    return $this->complianceState;
  }
  /**
   * Output only. The time the client state data was created.
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
   * This field may be used to store a unique identifier for the API resource
   * within which these CustomAttributes are a field.
   *
   * @param string $customId
   */
  public function setCustomId($customId)
  {
    $this->customId = $customId;
  }
  /**
   * @return string
   */
  public function getCustomId()
  {
    return $this->customId;
  }
  /**
   * The token that needs to be passed back for concurrency control in updates.
   * Token needs to be passed back in UpdateRequest
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The Health score of the resource. The Health score is the callers
   * specification of the condition of the device from a usability point of
   * view. For example, a third-party device management provider may specify a
   * health score based on its compliance with organizational policies.
   *
   * Accepted values: HEALTH_SCORE_UNSPECIFIED, VERY_POOR, POOR, NEUTRAL, GOOD,
   * VERY_GOOD
   *
   * @param self::HEALTH_SCORE_* $healthScore
   */
  public function setHealthScore($healthScore)
  {
    $this->healthScore = $healthScore;
  }
  /**
   * @return self::HEALTH_SCORE_*
   */
  public function getHealthScore()
  {
    return $this->healthScore;
  }
  /**
   * The map of key-value attributes stored by callers specific to a device. The
   * total serialized length of this map may not exceed 10KB. No limit is placed
   * on the number of attributes in a map.
   *
   * @param GoogleAppsCloudidentityDevicesV1CustomAttributeValue[] $keyValuePairs
   */
  public function setKeyValuePairs($keyValuePairs)
  {
    $this->keyValuePairs = $keyValuePairs;
  }
  /**
   * @return GoogleAppsCloudidentityDevicesV1CustomAttributeValue[]
   */
  public function getKeyValuePairs()
  {
    return $this->keyValuePairs;
  }
  /**
   * Output only. The time the client state data was last updated.
   *
   * @param string $lastUpdateTime
   */
  public function setLastUpdateTime($lastUpdateTime)
  {
    $this->lastUpdateTime = $lastUpdateTime;
  }
  /**
   * @return string
   */
  public function getLastUpdateTime()
  {
    return $this->lastUpdateTime;
  }
  /**
   * The management state of the resource as specified by the API client.
   *
   * Accepted values: MANAGED_STATE_UNSPECIFIED, MANAGED, UNMANAGED
   *
   * @param self::MANAGED_* $managed
   */
  public function setManaged($managed)
  {
    $this->managed = $managed;
  }
  /**
   * @return self::MANAGED_*
   */
  public function getManaged()
  {
    return $this->managed;
  }
  /**
   * Output only. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the
   * ClientState in format:
   * `devices/{device}/deviceUsers/{device_user}/clientState/{partner}`, where
   * partner corresponds to the partner storing the data. For partners belonging
   * to the "BeyondCorp Alliance", this is the partner ID specified to you by
   * Google. For all other callers, this is a string of the form:
   * `{customer}-suffix`, where `customer` is your customer ID. The *suffix* is
   * any string the caller specifies. This string will be displayed verbatim in
   * the administration console. This suffix is used in setting up Custom Access
   * Levels in Context-Aware Access. Your organization's customer ID can be
   * obtained from the URL: `GET
   * https://www.googleapis.com/admin/directory/v1/customers/my_customer` The
   * `id` field in the response contains the customer ID starting with the
   * letter 'C'. The customer ID to be used in this API is the string after the
   * letter 'C' (not including 'C')
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
   * Output only. The owner of the ClientState
   *
   * Accepted values: OWNER_TYPE_UNSPECIFIED, OWNER_TYPE_CUSTOMER,
   * OWNER_TYPE_PARTNER
   *
   * @param self::OWNER_TYPE_* $ownerType
   */
  public function setOwnerType($ownerType)
  {
    $this->ownerType = $ownerType;
  }
  /**
   * @return self::OWNER_TYPE_*
   */
  public function getOwnerType()
  {
    return $this->ownerType;
  }
  /**
   * A descriptive cause of the health score.
   *
   * @param string $scoreReason
   */
  public function setScoreReason($scoreReason)
  {
    $this->scoreReason = $scoreReason;
  }
  /**
   * @return string
   */
  public function getScoreReason()
  {
    return $this->scoreReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCloudidentityDevicesV1ClientState::class, 'Google_Service_CloudIdentity_GoogleAppsCloudidentityDevicesV1ClientState');
