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

namespace Google\Service\NetworkServices;

class ServiceBinding extends \Google\Model
{
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A free-text description of the resource. Max length 1024
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Set of label tags associated with the ServiceBinding resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Name of the ServiceBinding resource. It matches pattern
   * `projects/locations/serviceBindings/`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The full Service Directory Service name of the format
   * `projects/locations/namespaces/services`. This field is for Service
   * Directory integration which will be deprecated soon.
   *
   * @deprecated
   * @var string
   */
  public $service;
  /**
   * Output only. The unique identifier of the Service Directory Service against
   * which the ServiceBinding resource is validated. This is populated when the
   * Service Binding resource is used in another resource (like Backend
   * Service). This is of the UUID4 format. This field is for Service Directory
   * integration which will be deprecated soon.
   *
   * @deprecated
   * @var string
   */
  public $serviceId;
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The timestamp when the resource was created.
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
   * Optional. A free-text description of the resource. Max length 1024
   * characters.
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
   * Optional. Set of label tags associated with the ServiceBinding resource.
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
   * Identifier. Name of the ServiceBinding resource. It matches pattern
   * `projects/locations/serviceBindings/`.
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
   * Optional. The full Service Directory Service name of the format
   * `projects/locations/namespaces/services`. This field is for Service
   * Directory integration which will be deprecated soon.
   *
   * @deprecated
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * Output only. The unique identifier of the Service Directory Service against
   * which the ServiceBinding resource is validated. This is populated when the
   * Service Binding resource is used in another resource (like Backend
   * Service). This is of the UUID4 format. This field is for Service Directory
   * integration which will be deprecated soon.
   *
   * @deprecated
   * @param string $serviceId
   */
  public function setServiceId($serviceId)
  {
    $this->serviceId = $serviceId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getServiceId()
  {
    return $this->serviceId;
  }
  /**
   * Output only. The timestamp when the resource was updated.
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
class_alias(ServiceBinding::class, 'Google_Service_NetworkServices_ServiceBinding');
