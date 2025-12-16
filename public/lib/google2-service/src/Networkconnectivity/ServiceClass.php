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

namespace Google\Service\Networkconnectivity;

class ServiceClass extends \Google\Model
{
  /**
   * Output only. Time when the ServiceClass was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * A description of this resource.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. The etag is computed by the server, and may be sent on update and
   * delete requests to ensure the client has an up-to-date value before
   * proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * User-defined labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. The name of a ServiceClass resource. Format:
   * projects/{project}/locations/{location}/serviceClasses/{service_class} See:
   * https://google.aip.dev/122#fields-representing-resource-names
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The generated service class name. Use this name to refer to
   * the Service class in Service Connection Maps and Service Connection
   * Policies.
   *
   * @var string
   */
  public $serviceClass;
  /**
   * Output only. Time when the ServiceClass was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Time when the ServiceClass was created.
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
   * A description of this resource.
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
   * Optional. The etag is computed by the server, and may be sent on update and
   * delete requests to ensure the client has an up-to-date value before
   * proceeding.
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
   * User-defined labels.
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
   * Immutable. The name of a ServiceClass resource. Format:
   * projects/{project}/locations/{location}/serviceClasses/{service_class} See:
   * https://google.aip.dev/122#fields-representing-resource-names
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
   * Output only. The generated service class name. Use this name to refer to
   * the Service class in Service Connection Maps and Service Connection
   * Policies.
   *
   * @param string $serviceClass
   */
  public function setServiceClass($serviceClass)
  {
    $this->serviceClass = $serviceClass;
  }
  /**
   * @return string
   */
  public function getServiceClass()
  {
    return $this->serviceClass;
  }
  /**
   * Output only. Time when the ServiceClass was updated.
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
class_alias(ServiceClass::class, 'Google_Service_Networkconnectivity_ServiceClass');
