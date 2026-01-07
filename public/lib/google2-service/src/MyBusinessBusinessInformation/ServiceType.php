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

namespace Google\Service\MyBusinessBusinessInformation;

class ServiceType extends \Google\Model
{
  /**
   * Output only. The human-readable display name for the service type.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. A stable ID (provided by Google) for this service type.
   *
   * @var string
   */
  public $serviceTypeId;

  /**
   * Output only. The human-readable display name for the service type.
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
   * Output only. A stable ID (provided by Google) for this service type.
   *
   * @param string $serviceTypeId
   */
  public function setServiceTypeId($serviceTypeId)
  {
    $this->serviceTypeId = $serviceTypeId;
  }
  /**
   * @return string
   */
  public function getServiceTypeId()
  {
    return $this->serviceTypeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceType::class, 'Google_Service_MyBusinessBusinessInformation_ServiceType');
