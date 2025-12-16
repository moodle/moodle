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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1OperationConfig extends \Google\Collection
{
  protected $collection_key = 'operations';
  /**
   * Required. Name of the API proxy or remote service with which the resources,
   * methods, and quota are associated.
   *
   * @var string
   */
  public $apiSource;
  protected $attributesType = GoogleCloudApigeeV1Attribute::class;
  protected $attributesDataType = 'array';
  protected $operationsType = GoogleCloudApigeeV1Operation::class;
  protected $operationsDataType = 'array';
  protected $quotaType = GoogleCloudApigeeV1Quota::class;
  protected $quotaDataType = '';

  /**
   * Required. Name of the API proxy or remote service with which the resources,
   * methods, and quota are associated.
   *
   * @param string $apiSource
   */
  public function setApiSource($apiSource)
  {
    $this->apiSource = $apiSource;
  }
  /**
   * @return string
   */
  public function getApiSource()
  {
    return $this->apiSource;
  }
  /**
   * Custom attributes associated with the operation.
   *
   * @param GoogleCloudApigeeV1Attribute[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return GoogleCloudApigeeV1Attribute[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * List of resource/method pairs for the API proxy or remote service to which
   * quota will applied. **Note**: Currently, you can specify only a single
   * resource/method pair. The call will fail if more than one resource/method
   * pair is provided.
   *
   * @param GoogleCloudApigeeV1Operation[] $operations
   */
  public function setOperations($operations)
  {
    $this->operations = $operations;
  }
  /**
   * @return GoogleCloudApigeeV1Operation[]
   */
  public function getOperations()
  {
    return $this->operations;
  }
  /**
   * Quota parameters to be enforced for the resources, methods, and API source
   * combination. If none are specified, quota enforcement will not be done.
   *
   * @param GoogleCloudApigeeV1Quota $quota
   */
  public function setQuota(GoogleCloudApigeeV1Quota $quota)
  {
    $this->quota = $quota;
  }
  /**
   * @return GoogleCloudApigeeV1Quota
   */
  public function getQuota()
  {
    return $this->quota;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1OperationConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1OperationConfig');
