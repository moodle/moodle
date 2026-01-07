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

namespace Google\Service\CloudAsset;

class GoogleIdentityAccesscontextmanagerV1IngressTo extends \Google\Collection
{
  protected $collection_key = 'roles';
  protected $operationsType = GoogleIdentityAccesscontextmanagerV1ApiOperation::class;
  protected $operationsDataType = 'array';
  /**
   * A list of resources, currently only projects in the form `projects/`,
   * protected by this ServicePerimeter that are allowed to be accessed by
   * sources defined in the corresponding IngressFrom. If a single `*` is
   * specified, then access to all resources inside the perimeter are allowed.
   *
   * @var string[]
   */
  public $resources;
  /**
   * IAM roles that represent the set of operations that the sources specified
   * in the corresponding IngressFrom are allowed to perform in this
   * ServicePerimeter.
   *
   * @var string[]
   */
  public $roles;

  /**
   * A list of ApiOperations allowed to be performed by the sources specified in
   * corresponding IngressFrom in this ServicePerimeter.
   *
   * @param GoogleIdentityAccesscontextmanagerV1ApiOperation[] $operations
   */
  public function setOperations($operations)
  {
    $this->operations = $operations;
  }
  /**
   * @return GoogleIdentityAccesscontextmanagerV1ApiOperation[]
   */
  public function getOperations()
  {
    return $this->operations;
  }
  /**
   * A list of resources, currently only projects in the form `projects/`,
   * protected by this ServicePerimeter that are allowed to be accessed by
   * sources defined in the corresponding IngressFrom. If a single `*` is
   * specified, then access to all resources inside the perimeter are allowed.
   *
   * @param string[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return string[]
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * IAM roles that represent the set of operations that the sources specified
   * in the corresponding IngressFrom are allowed to perform in this
   * ServicePerimeter.
   *
   * @param string[] $roles
   */
  public function setRoles($roles)
  {
    $this->roles = $roles;
  }
  /**
   * @return string[]
   */
  public function getRoles()
  {
    return $this->roles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIdentityAccesscontextmanagerV1IngressTo::class, 'Google_Service_CloudAsset_GoogleIdentityAccesscontextmanagerV1IngressTo');
