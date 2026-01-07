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

class GoogleCloudApigeeV1LlmOperationConfig extends \Google\Collection
{
  protected $collection_key = 'llmOperations';
  /**
   * Required. Name of the API proxy or remote service with which the resources,
   * methods, and quota are associated.
   *
   * @var string
   */
  public $apiSource;
  protected $attributesType = GoogleCloudApigeeV1Attribute::class;
  protected $attributesDataType = 'array';
  protected $llmOperationsType = GoogleCloudApigeeV1LlmOperation::class;
  protected $llmOperationsDataType = 'array';
  protected $llmTokenQuotaType = GoogleCloudApigeeV1LlmTokenQuota::class;
  protected $llmTokenQuotaDataType = '';

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
   * Optional. Custom attributes associated with the operation.
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
   * Required. List of resource/method/model for the API proxy to which quota
   * will applied. **Note**: Currently, you can specify only a single
   * resource/method/model mapping. The call will fail if more than one
   * resource/method/model mappings are provided.
   *
   * @param GoogleCloudApigeeV1LlmOperation[] $llmOperations
   */
  public function setLlmOperations($llmOperations)
  {
    $this->llmOperations = $llmOperations;
  }
  /**
   * @return GoogleCloudApigeeV1LlmOperation[]
   */
  public function getLlmOperations()
  {
    return $this->llmOperations;
  }
  /**
   * Required. LLM token Quota parameters to be enforced for the resources,
   * methods, and API source & LLM model combination. If none are specified,
   * quota enforcement will not be done.
   *
   * @param GoogleCloudApigeeV1LlmTokenQuota $llmTokenQuota
   */
  public function setLlmTokenQuota(GoogleCloudApigeeV1LlmTokenQuota $llmTokenQuota)
  {
    $this->llmTokenQuota = $llmTokenQuota;
  }
  /**
   * @return GoogleCloudApigeeV1LlmTokenQuota
   */
  public function getLlmTokenQuota()
  {
    return $this->llmTokenQuota;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1LlmOperationConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1LlmOperationConfig');
