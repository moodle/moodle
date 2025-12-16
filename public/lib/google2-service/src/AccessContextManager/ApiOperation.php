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

class ApiOperation extends \Google\Collection
{
  protected $collection_key = 'methodSelectors';
  protected $methodSelectorsType = MethodSelector::class;
  protected $methodSelectorsDataType = 'array';
  /**
   * The name of the API whose methods or permissions the IngressPolicy or
   * EgressPolicy want to allow. A single ApiOperation with `service_name` field
   * set to `*` will allow all methods AND permissions for all services.
   *
   * @var string
   */
  public $serviceName;

  /**
   * API methods or permissions to allow. Method or permission must belong to
   * the service specified by `service_name` field. A single MethodSelector
   * entry with `*` specified for the `method` field will allow all methods AND
   * permissions for the service specified in `service_name`.
   *
   * @param MethodSelector[] $methodSelectors
   */
  public function setMethodSelectors($methodSelectors)
  {
    $this->methodSelectors = $methodSelectors;
  }
  /**
   * @return MethodSelector[]
   */
  public function getMethodSelectors()
  {
    return $this->methodSelectors;
  }
  /**
   * The name of the API whose methods or permissions the IngressPolicy or
   * EgressPolicy want to allow. A single ApiOperation with `service_name` field
   * set to `*` will allow all methods AND permissions for all services.
   *
   * @param string $serviceName
   */
  public function setServiceName($serviceName)
  {
    $this->serviceName = $serviceName;
  }
  /**
   * @return string
   */
  public function getServiceName()
  {
    return $this->serviceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApiOperation::class, 'Google_Service_AccessContextManager_ApiOperation');
