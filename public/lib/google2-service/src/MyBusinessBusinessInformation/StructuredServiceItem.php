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

class StructuredServiceItem extends \Google\Model
{
  /**
   * Optional. Description of structured service item. The character limit is
   * 300.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The `service_type_id` field is a Google provided unique ID that
   * can be found in `ServiceType`. This information is provided by
   * `BatchGetCategories` rpc service.
   *
   * @var string
   */
  public $serviceTypeId;

  /**
   * Optional. Description of structured service item. The character limit is
   * 300.
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
   * Required. The `service_type_id` field is a Google provided unique ID that
   * can be found in `ServiceType`. This information is provided by
   * `BatchGetCategories` rpc service.
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
class_alias(StructuredServiceItem::class, 'Google_Service_MyBusinessBusinessInformation_StructuredServiceItem');
