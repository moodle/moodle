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

namespace Google\Service\Cloudbilling;

class Service extends \Google\Model
{
  /**
   * The business under which the service is offered. Ex.
   * "businessEntities/GCP", "businessEntities/Maps"
   *
   * @var string
   */
  public $businessEntityName;
  /**
   * A human readable display name for this service.
   *
   * @var string
   */
  public $displayName;
  /**
   * The resource name for the service. Example: "services/6F81-5844-456A"
   *
   * @var string
   */
  public $name;
  /**
   * The identifier for the service. Example: "6F81-5844-456A"
   *
   * @var string
   */
  public $serviceId;

  /**
   * The business under which the service is offered. Ex.
   * "businessEntities/GCP", "businessEntities/Maps"
   *
   * @param string $businessEntityName
   */
  public function setBusinessEntityName($businessEntityName)
  {
    $this->businessEntityName = $businessEntityName;
  }
  /**
   * @return string
   */
  public function getBusinessEntityName()
  {
    return $this->businessEntityName;
  }
  /**
   * A human readable display name for this service.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The resource name for the service. Example: "services/6F81-5844-456A"
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
   * The identifier for the service. Example: "6F81-5844-456A"
   *
   * @param string $serviceId
   */
  public function setServiceId($serviceId)
  {
    $this->serviceId = $serviceId;
  }
  /**
   * @return string
   */
  public function getServiceId()
  {
    return $this->serviceId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Service::class, 'Google_Service_Cloudbilling_Service');
