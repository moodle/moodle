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

class GoogleIdentityAccesscontextmanagerV1EgressTo extends \Google\Collection
{
  protected $collection_key = 'roles';
  /**
   * A list of external resources that are allowed to be accessed. Only AWS and
   * Azure resources are supported. For Amazon S3, the supported formats are
   * s3://BUCKET_NAME, s3a://BUCKET_NAME, and s3n://BUCKET_NAME. For Azure
   * Storage, the supported format is
   * azure://myaccount.blob.core.windows.net/CONTAINER_NAME. A request matches
   * if it contains an external resource in this list (Example:
   * s3://bucket/path). Currently '*' is not allowed.
   *
   * @var string[]
   */
  public $externalResources;
  protected $operationsType = GoogleIdentityAccesscontextmanagerV1ApiOperation::class;
  protected $operationsDataType = 'array';
  /**
   * A list of resources, currently only projects in the form `projects/`, that
   * are allowed to be accessed by sources defined in the corresponding
   * EgressFrom. A request matches if it contains a resource in this list. If
   * `*` is specified for `resources`, then this EgressTo rule will authorize
   * access to all resources outside the perimeter.
   *
   * @var string[]
   */
  public $resources;
  /**
   * IAM roles that represent the set of operations that the sources specified
   * in the corresponding EgressFrom. are allowed to perform in this
   * ServicePerimeter.
   *
   * @var string[]
   */
  public $roles;

  /**
   * A list of external resources that are allowed to be accessed. Only AWS and
   * Azure resources are supported. For Amazon S3, the supported formats are
   * s3://BUCKET_NAME, s3a://BUCKET_NAME, and s3n://BUCKET_NAME. For Azure
   * Storage, the supported format is
   * azure://myaccount.blob.core.windows.net/CONTAINER_NAME. A request matches
   * if it contains an external resource in this list (Example:
   * s3://bucket/path). Currently '*' is not allowed.
   *
   * @param string[] $externalResources
   */
  public function setExternalResources($externalResources)
  {
    $this->externalResources = $externalResources;
  }
  /**
   * @return string[]
   */
  public function getExternalResources()
  {
    return $this->externalResources;
  }
  /**
   * A list of ApiOperations allowed to be performed by the sources specified in
   * the corresponding EgressFrom. A request matches if it uses an
   * operation/service in this list.
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
   * A list of resources, currently only projects in the form `projects/`, that
   * are allowed to be accessed by sources defined in the corresponding
   * EgressFrom. A request matches if it contains a resource in this list. If
   * `*` is specified for `resources`, then this EgressTo rule will authorize
   * access to all resources outside the perimeter.
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
   * in the corresponding EgressFrom. are allowed to perform in this
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
class_alias(GoogleIdentityAccesscontextmanagerV1EgressTo::class, 'Google_Service_CloudAsset_GoogleIdentityAccesscontextmanagerV1EgressTo');
