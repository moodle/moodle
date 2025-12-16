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

class GroupKind extends \Google\Model
{
  /**
   * Optional. API group string of a Kubernetes resource, e.g.
   * "apiextensions.k8s.io", "storage.k8s.io", etc. Note: use empty string for
   * core API group.
   *
   * @var string
   */
  public $resourceGroup;
  /**
   * Optional. Kind of a Kubernetes resource, must be in UpperCamelCase
   * (PascalCase) and singular form. E.g. "CustomResourceDefinition",
   * "StorageClass", etc.
   *
   * @var string
   */
  public $resourceKind;

  /**
   * Optional. API group string of a Kubernetes resource, e.g.
   * "apiextensions.k8s.io", "storage.k8s.io", etc. Note: use empty string for
   * core API group.
   *
   * @param string $resourceGroup
   */
  public function setResourceGroup($resourceGroup)
  {
    $this->resourceGroup = $resourceGroup;
  }
  /**
   * @return string
   */
  public function getResourceGroup()
  {
    return $this->resourceGroup;
  }
  /**
   * Optional. Kind of a Kubernetes resource, must be in UpperCamelCase
   * (PascalCase) and singular form. E.g. "CustomResourceDefinition",
   * "StorageClass", etc.
   *
   * @param string $resourceKind
   */
  public function setResourceKind($resourceKind)
  {
    $this->resourceKind = $resourceKind;
  }
  /**
   * @return string
   */
  public function getResourceKind()
  {
    return $this->resourceKind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GroupKind::class, 'Google_Service_BackupforGKE_GroupKind');
