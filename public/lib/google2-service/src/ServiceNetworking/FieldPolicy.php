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

namespace Google\Service\ServiceNetworking;

class FieldPolicy extends \Google\Model
{
  /**
   * Specifies the required permission(s) for the resource referred to by the
   * field. It requires the field contains a valid resource reference, and the
   * request must pass the permission checks to proceed. For example,
   * "resourcemanager.projects.get".
   *
   * @var string
   */
  public $resourcePermission;
  /**
   * Specifies the resource type for the resource referred to by the field.
   *
   * @var string
   */
  public $resourceType;
  /**
   * Selects one or more request or response message fields to apply this
   * `FieldPolicy`. When a `FieldPolicy` is used in proto annotation, the
   * selector must be left as empty. The service config generator will
   * automatically fill the correct value. When a `FieldPolicy` is used in
   * service config, the selector must be a comma-separated string with valid
   * request or response field paths, such as "foo.bar" or "foo.bar,foo.baz".
   *
   * @var string
   */
  public $selector;

  /**
   * Specifies the required permission(s) for the resource referred to by the
   * field. It requires the field contains a valid resource reference, and the
   * request must pass the permission checks to proceed. For example,
   * "resourcemanager.projects.get".
   *
   * @param string $resourcePermission
   */
  public function setResourcePermission($resourcePermission)
  {
    $this->resourcePermission = $resourcePermission;
  }
  /**
   * @return string
   */
  public function getResourcePermission()
  {
    return $this->resourcePermission;
  }
  /**
   * Specifies the resource type for the resource referred to by the field.
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
  /**
   * Selects one or more request or response message fields to apply this
   * `FieldPolicy`. When a `FieldPolicy` is used in proto annotation, the
   * selector must be left as empty. The service config generator will
   * automatically fill the correct value. When a `FieldPolicy` is used in
   * service config, the selector must be a comma-separated string with valid
   * request or response field paths, such as "foo.bar" or "foo.bar,foo.baz".
   *
   * @param string $selector
   */
  public function setSelector($selector)
  {
    $this->selector = $selector;
  }
  /**
   * @return string
   */
  public function getSelector()
  {
    return $this->selector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FieldPolicy::class, 'Google_Service_ServiceNetworking_FieldPolicy');
