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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV1ResourceValueConfig extends \Google\Collection
{
  /**
   * The cloud provider is unspecified.
   */
  public const CLOUD_PROVIDER_CLOUD_PROVIDER_UNSPECIFIED = 'CLOUD_PROVIDER_UNSPECIFIED';
  /**
   * The cloud provider is Google Cloud.
   */
  public const CLOUD_PROVIDER_GOOGLE_CLOUD_PLATFORM = 'GOOGLE_CLOUD_PLATFORM';
  /**
   * The cloud provider is Amazon Web Services.
   */
  public const CLOUD_PROVIDER_AMAZON_WEB_SERVICES = 'AMAZON_WEB_SERVICES';
  /**
   * The cloud provider is Microsoft Azure.
   */
  public const CLOUD_PROVIDER_MICROSOFT_AZURE = 'MICROSOFT_AZURE';
  /**
   * Unspecific value
   */
  public const RESOURCE_VALUE_RESOURCE_VALUE_UNSPECIFIED = 'RESOURCE_VALUE_UNSPECIFIED';
  /**
   * High resource value
   */
  public const RESOURCE_VALUE_HIGH = 'HIGH';
  /**
   * Medium resource value
   */
  public const RESOURCE_VALUE_MEDIUM = 'MEDIUM';
  /**
   * Low resource value
   */
  public const RESOURCE_VALUE_LOW = 'LOW';
  /**
   * No resource value, e.g. ignore these resources
   */
  public const RESOURCE_VALUE_NONE = 'NONE';
  protected $collection_key = 'tagValues';
  /**
   * Cloud provider this configuration applies to
   *
   * @var string
   */
  public $cloudProvider;
  /**
   * Output only. Timestamp this resource value configuration was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Description of the resource value configuration.
   *
   * @var string
   */
  public $description;
  /**
   * Name for the resource value configuration
   *
   * @var string
   */
  public $name;
  /**
   * List of resource labels to search for, evaluated with `AND`. For example,
   * `"resource_labels_selector": {"key": "value", "env": "prod"}` will match
   * resources with labels "key": "value" `AND` "env": "prod"
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels
   *
   * @var string[]
   */
  public $resourceLabelsSelector;
  /**
   * Apply resource_value only to resources that match resource_type.
   * resource_type will be checked with `AND` of other resources. For example,
   * "storage.googleapis.com/Bucket" with resource_value "HIGH" will apply
   * "HIGH" value only to "storage.googleapis.com/Bucket" resources.
   *
   * @var string
   */
  public $resourceType;
  /**
   * Required. Resource value level this expression represents
   *
   * @var string
   */
  public $resourceValue;
  /**
   * Project or folder to scope this configuration to. For example,
   * "project/456" would apply this configuration only to resources in
   * "project/456" scope will be checked with `AND` of other resources.
   *
   * @var string
   */
  public $scope;
  protected $sensitiveDataProtectionMappingType = GoogleCloudSecuritycenterV1SensitiveDataProtectionMapping::class;
  protected $sensitiveDataProtectionMappingDataType = '';
  /**
   * Required. Tag values combined with `AND` to check against. For Google Cloud
   * resources, they are tag value IDs in the form of "tagValues/123". Example:
   * `[ "tagValues/123", "tagValues/456", "tagValues/789" ]`
   * https://cloud.google.com/resource-manager/docs/tags/tags-creating-and-
   * managing
   *
   * @var string[]
   */
  public $tagValues;
  /**
   * Output only. Timestamp this resource value configuration was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Cloud provider this configuration applies to
   *
   * Accepted values: CLOUD_PROVIDER_UNSPECIFIED, GOOGLE_CLOUD_PLATFORM,
   * AMAZON_WEB_SERVICES, MICROSOFT_AZURE
   *
   * @param self::CLOUD_PROVIDER_* $cloudProvider
   */
  public function setCloudProvider($cloudProvider)
  {
    $this->cloudProvider = $cloudProvider;
  }
  /**
   * @return self::CLOUD_PROVIDER_*
   */
  public function getCloudProvider()
  {
    return $this->cloudProvider;
  }
  /**
   * Output only. Timestamp this resource value configuration was created.
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
   * Description of the resource value configuration.
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
   * Name for the resource value configuration
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
   * List of resource labels to search for, evaluated with `AND`. For example,
   * `"resource_labels_selector": {"key": "value", "env": "prod"}` will match
   * resources with labels "key": "value" `AND` "env": "prod"
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels
   *
   * @param string[] $resourceLabelsSelector
   */
  public function setResourceLabelsSelector($resourceLabelsSelector)
  {
    $this->resourceLabelsSelector = $resourceLabelsSelector;
  }
  /**
   * @return string[]
   */
  public function getResourceLabelsSelector()
  {
    return $this->resourceLabelsSelector;
  }
  /**
   * Apply resource_value only to resources that match resource_type.
   * resource_type will be checked with `AND` of other resources. For example,
   * "storage.googleapis.com/Bucket" with resource_value "HIGH" will apply
   * "HIGH" value only to "storage.googleapis.com/Bucket" resources.
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * Required. Resource value level this expression represents
   *
   * Accepted values: RESOURCE_VALUE_UNSPECIFIED, HIGH, MEDIUM, LOW, NONE
   *
   * @param self::RESOURCE_VALUE_* $resourceValue
   */
  public function setResourceValue($resourceValue)
  {
    $this->resourceValue = $resourceValue;
  }
  /**
   * @return self::RESOURCE_VALUE_*
   */
  public function getResourceValue()
  {
    return $this->resourceValue;
  }
  /**
   * Project or folder to scope this configuration to. For example,
   * "project/456" would apply this configuration only to resources in
   * "project/456" scope will be checked with `AND` of other resources.
   *
   * @param string $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * A mapping of the sensitivity on Sensitive Data Protection finding to
   * resource values. This mapping can only be used in combination with a
   * resource_type that is related to BigQuery, e.g.
   * "bigquery.googleapis.com/Dataset".
   *
   * @param GoogleCloudSecuritycenterV1SensitiveDataProtectionMapping $sensitiveDataProtectionMapping
   */
  public function setSensitiveDataProtectionMapping(GoogleCloudSecuritycenterV1SensitiveDataProtectionMapping $sensitiveDataProtectionMapping)
  {
    $this->sensitiveDataProtectionMapping = $sensitiveDataProtectionMapping;
  }
  /**
   * @return GoogleCloudSecuritycenterV1SensitiveDataProtectionMapping
   */
  public function getSensitiveDataProtectionMapping()
  {
    return $this->sensitiveDataProtectionMapping;
  }
  /**
   * Required. Tag values combined with `AND` to check against. For Google Cloud
   * resources, they are tag value IDs in the form of "tagValues/123". Example:
   * `[ "tagValues/123", "tagValues/456", "tagValues/789" ]`
   * https://cloud.google.com/resource-manager/docs/tags/tags-creating-and-
   * managing
   *
   * @param string[] $tagValues
   */
  public function setTagValues($tagValues)
  {
    $this->tagValues = $tagValues;
  }
  /**
   * @return string[]
   */
  public function getTagValues()
  {
    return $this->tagValues;
  }
  /**
   * Output only. Timestamp this resource value configuration was last updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV1ResourceValueConfig::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV1ResourceValueConfig');
