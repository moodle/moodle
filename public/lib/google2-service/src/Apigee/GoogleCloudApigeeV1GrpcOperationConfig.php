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

class GoogleCloudApigeeV1GrpcOperationConfig extends \Google\Collection
{
  protected $collection_key = 'methods';
  /**
   * Required. Name of the API proxy with which the gRPC operation and quota are
   * associated.
   *
   * @var string
   */
  public $apiSource;
  protected $attributesType = GoogleCloudApigeeV1Attribute::class;
  protected $attributesDataType = 'array';
  /**
   * List of unqualified gRPC method names for the proxy to which quota will be
   * applied. If this field is empty, the Quota will apply to all operations on
   * the gRPC service defined on the proxy. Example: Given a proxy that is
   * configured to serve com.petstore.PetService, the methods
   * com.petstore.PetService.ListPets and com.petstore.PetService.GetPet would
   * be specified here as simply ["ListPets", "GetPet"].
   *
   * @var string[]
   */
  public $methods;
  protected $quotaType = GoogleCloudApigeeV1Quota::class;
  protected $quotaDataType = '';
  /**
   * Required. gRPC Service name associated to be associated with the API proxy,
   * on which quota rules can be applied upon.
   *
   * @var string
   */
  public $service;

  /**
   * Required. Name of the API proxy with which the gRPC operation and quota are
   * associated.
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
   * List of unqualified gRPC method names for the proxy to which quota will be
   * applied. If this field is empty, the Quota will apply to all operations on
   * the gRPC service defined on the proxy. Example: Given a proxy that is
   * configured to serve com.petstore.PetService, the methods
   * com.petstore.PetService.ListPets and com.petstore.PetService.GetPet would
   * be specified here as simply ["ListPets", "GetPet"].
   *
   * @param string[] $methods
   */
  public function setMethods($methods)
  {
    $this->methods = $methods;
  }
  /**
   * @return string[]
   */
  public function getMethods()
  {
    return $this->methods;
  }
  /**
   * Quota parameters to be enforced for the methods and API source combination.
   * If none are specified, quota enforcement will not be done.
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
  /**
   * Required. gRPC Service name associated to be associated with the API proxy,
   * on which quota rules can be applied upon.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1GrpcOperationConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1GrpcOperationConfig');
