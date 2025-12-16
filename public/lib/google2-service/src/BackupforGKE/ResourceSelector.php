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

class ResourceSelector extends \Google\Model
{
  protected $groupKindType = GroupKind::class;
  protected $groupKindDataType = '';
  /**
   * Optional. Selects resources using Kubernetes
   * [labels](https://kubernetes.io/docs/concepts/overview/working-with-
   * objects/labels/). If specified, a resource will be selected if and only if
   * the resource has all of the provided labels and all the label values match.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Selects resources using their resource names. If specified, only
   * resources with the provided name will be selected.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Selects resources using their namespaces. This only applies to
   * namespace scoped resources and cannot be used for selecting cluster scoped
   * resources. If specified, only resources in the provided namespace will be
   * selected. If not specified, the filter will apply to both cluster scoped
   * and namespace scoped resources (e.g. name or label). The
   * [Namespace](https://pkg.go.dev/k8s.io/api/core/v1#Namespace) resource
   * itself will be restored if and only if any resources within the namespace
   * are restored.
   *
   * @var string
   */
  public $namespace;

  /**
   * Optional. Selects resources using their Kubernetes GroupKinds. If
   * specified, only resources of provided GroupKind will be selected.
   *
   * @param GroupKind $groupKind
   */
  public function setGroupKind(GroupKind $groupKind)
  {
    $this->groupKind = $groupKind;
  }
  /**
   * @return GroupKind
   */
  public function getGroupKind()
  {
    return $this->groupKind;
  }
  /**
   * Optional. Selects resources using Kubernetes
   * [labels](https://kubernetes.io/docs/concepts/overview/working-with-
   * objects/labels/). If specified, a resource will be selected if and only if
   * the resource has all of the provided labels and all the label values match.
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
   * Optional. Selects resources using their resource names. If specified, only
   * resources with the provided name will be selected.
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
   * Optional. Selects resources using their namespaces. This only applies to
   * namespace scoped resources and cannot be used for selecting cluster scoped
   * resources. If specified, only resources in the provided namespace will be
   * selected. If not specified, the filter will apply to both cluster scoped
   * and namespace scoped resources (e.g. name or label). The
   * [Namespace](https://pkg.go.dev/k8s.io/api/core/v1#Namespace) resource
   * itself will be restored if and only if any resources within the namespace
   * are restored.
   *
   * @param string $namespace
   */
  public function setNamespace($namespace)
  {
    $this->namespace = $namespace;
  }
  /**
   * @return string
   */
  public function getNamespace()
  {
    return $this->namespace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceSelector::class, 'Google_Service_BackupforGKE_ResourceSelector');
