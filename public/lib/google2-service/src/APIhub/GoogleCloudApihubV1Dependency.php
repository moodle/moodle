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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1Dependency extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const DISCOVERY_MODE_DISCOVERY_MODE_UNSPECIFIED = 'DISCOVERY_MODE_UNSPECIFIED';
  /**
   * Manual mode of discovery when the dependency is defined by the user.
   */
  public const DISCOVERY_MODE_MANUAL = 'MANUAL';
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Dependency will be in a proposed state when it is newly identified by the
   * API hub on its own.
   */
  public const STATE_PROPOSED = 'PROPOSED';
  /**
   * Dependency will be in a validated state when it is validated by the admin
   * or manually created in the API hub.
   */
  public const STATE_VALIDATED = 'VALIDATED';
  protected $attributesType = GoogleCloudApihubV1AttributeValues::class;
  protected $attributesDataType = 'map';
  protected $consumerType = GoogleCloudApihubV1DependencyEntityReference::class;
  protected $consumerDataType = '';
  /**
   * Output only. The time at which the dependency was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Human readable description corresponding of the dependency.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Discovery mode of the dependency.
   *
   * @var string
   */
  public $discoveryMode;
  protected $errorDetailType = GoogleCloudApihubV1DependencyErrorDetail::class;
  protected $errorDetailDataType = '';
  /**
   * Identifier. The name of the dependency in the API Hub. Format:
   * `projects/{project}/locations/{location}/dependencies/{dependency}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. State of the dependency.
   *
   * @var string
   */
  public $state;
  protected $supplierType = GoogleCloudApihubV1DependencyEntityReference::class;
  protected $supplierDataType = '';
  /**
   * Output only. The time at which the dependency was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The list of user defined attributes associated with the
   * dependency resource. The key is the attribute name. It will be of the
   * format: `projects/{project}/locations/{location}/attributes/{attribute}`.
   * The value is the attribute values associated with the resource.
   *
   * @param GoogleCloudApihubV1AttributeValues[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Required. Immutable. The entity acting as the consumer in the dependency.
   *
   * @param GoogleCloudApihubV1DependencyEntityReference $consumer
   */
  public function setConsumer(GoogleCloudApihubV1DependencyEntityReference $consumer)
  {
    $this->consumer = $consumer;
  }
  /**
   * @return GoogleCloudApihubV1DependencyEntityReference
   */
  public function getConsumer()
  {
    return $this->consumer;
  }
  /**
   * Output only. The time at which the dependency was created.
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
   * Optional. Human readable description corresponding of the dependency.
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
   * Output only. Discovery mode of the dependency.
   *
   * Accepted values: DISCOVERY_MODE_UNSPECIFIED, MANUAL
   *
   * @param self::DISCOVERY_MODE_* $discoveryMode
   */
  public function setDiscoveryMode($discoveryMode)
  {
    $this->discoveryMode = $discoveryMode;
  }
  /**
   * @return self::DISCOVERY_MODE_*
   */
  public function getDiscoveryMode()
  {
    return $this->discoveryMode;
  }
  /**
   * Output only. Error details of a dependency if the system has detected it
   * internally.
   *
   * @param GoogleCloudApihubV1DependencyErrorDetail $errorDetail
   */
  public function setErrorDetail(GoogleCloudApihubV1DependencyErrorDetail $errorDetail)
  {
    $this->errorDetail = $errorDetail;
  }
  /**
   * @return GoogleCloudApihubV1DependencyErrorDetail
   */
  public function getErrorDetail()
  {
    return $this->errorDetail;
  }
  /**
   * Identifier. The name of the dependency in the API Hub. Format:
   * `projects/{project}/locations/{location}/dependencies/{dependency}`
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
   * Output only. State of the dependency.
   *
   * Accepted values: STATE_UNSPECIFIED, PROPOSED, VALIDATED
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
   * Required. Immutable. The entity acting as the supplier in the dependency.
   *
   * @param GoogleCloudApihubV1DependencyEntityReference $supplier
   */
  public function setSupplier(GoogleCloudApihubV1DependencyEntityReference $supplier)
  {
    $this->supplier = $supplier;
  }
  /**
   * @return GoogleCloudApihubV1DependencyEntityReference
   */
  public function getSupplier()
  {
    return $this->supplier;
  }
  /**
   * Output only. The time at which the dependency was last updated.
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
class_alias(GoogleCloudApihubV1Dependency::class, 'Google_Service_APIhub_GoogleCloudApihubV1Dependency');
