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

namespace Google\Service\BackupforGKE;

class ResourceFilter extends \Google\Collection
{
  protected $collection_key = 'namespaces';
  protected $groupKindsType = GroupKind::class;
  protected $groupKindsDataType = 'array';
  /**
   * Optional. This is a [JSONPath] (https://github.com/json-
   * path/JsonPath/blob/master/README.md) expression that matches specific
   * fields of candidate resources and it operates as a filtering parameter
   * (resources that are not matched with this expression will not be candidates
   * for transformation).
   *
   * @var string
   */
  public $jsonPath;
  /**
   * Optional. (Filtering parameter) Any resource subject to transformation must
   * be contained within one of the listed Kubernetes Namespace in the Backup.
   * If this field is not provided, no namespace filtering will be performed
   * (all resources in all Namespaces, including all cluster-scoped resources,
   * will be candidates for transformation).
   *
   * @var string[]
   */
  public $namespaces;

  /**
   * Optional. (Filtering parameter) Any resource subject to transformation must
   * belong to one of the listed "types". If this field is not provided, no type
   * filtering will be performed (all resources of all types matching previous
   * filtering parameters will be candidates for transformation).
   *
   * @param GroupKind[] $groupKinds
   */
  public function setGroupKinds($groupKinds)
  {
    $this->groupKinds = $groupKinds;
  }
  /**
   * @return GroupKind[]
   */
  public function getGroupKinds()
  {
    return $this->groupKinds;
  }
  /**
   * Optional. This is a [JSONPath] (https://github.com/json-
   * path/JsonPath/blob/master/README.md) expression that matches specific
   * fields of candidate resources and it operates as a filtering parameter
   * (resources that are not matched with this expression will not be candidates
   * for transformation).
   *
   * @param string $jsonPath
   */
  public function setJsonPath($jsonPath)
  {
    $this->jsonPath = $jsonPath;
  }
  /**
   * @return string
   */
  public function getJsonPath()
  {
    return $this->jsonPath;
  }
  /**
   * Optional. (Filtering parameter) Any resource subject to transformation must
   * be contained within one of the listed Kubernetes Namespace in the Backup.
   * If this field is not provided, no namespace filtering will be performed
   * (all resources in all Namespaces, including all cluster-scoped resources,
   * will be candidates for transformation).
   *
   * @param string[] $namespaces
   */
  public function setNamespaces($namespaces)
  {
    $this->namespaces = $namespaces;
  }
  /**
   * @return string[]
   */
  public function getNamespaces()
  {
    return $this->namespaces;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceFilter::class, 'Google_Service_BackupforGKE_ResourceFilter');
