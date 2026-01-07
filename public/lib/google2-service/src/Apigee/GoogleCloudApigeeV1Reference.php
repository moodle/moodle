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

class GoogleCloudApigeeV1Reference extends \Google\Model
{
  /**
   * Optional. A human-readable description of this reference.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The resource id of this reference. Values must match the regular
   * expression [\w\s\-.]+.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The id of the resource to which this reference refers. Must be
   * the id of a resource that exists in the parent environment and is of the
   * given resource_type.
   *
   * @var string
   */
  public $refers;
  /**
   * The type of resource referred to by this reference. Valid values are
   * 'KeyStore' or 'TrustStore'.
   *
   * @var string
   */
  public $resourceType;

  /**
   * Optional. A human-readable description of this reference.
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
   * Required. The resource id of this reference. Values must match the regular
   * expression [\w\s\-.]+.
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
   * Required. The id of the resource to which this reference refers. Must be
   * the id of a resource that exists in the parent environment and is of the
   * given resource_type.
   *
   * @param string $refers
   */
  public function setRefers($refers)
  {
    $this->refers = $refers;
  }
  /**
   * @return string
   */
  public function getRefers()
  {
    return $this->refers;
  }
  /**
   * The type of resource referred to by this reference. Valid values are
   * 'KeyStore' or 'TrustStore'.
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1Reference::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Reference');
