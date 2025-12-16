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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2Object extends \Google\Collection
{
  protected $collection_key = 'containers';
  protected $containersType = GoogleCloudSecuritycenterV2Container::class;
  protected $containersDataType = 'array';
  /**
   * Kubernetes object group, such as "policy.k8s.io/v1".
   *
   * @var string
   */
  public $group;
  /**
   * Kubernetes object kind, such as "Namespace".
   *
   * @var string
   */
  public $kind;
  /**
   * Kubernetes object name. For details see
   * https://kubernetes.io/docs/concepts/overview/working-with-objects/names/.
   *
   * @var string
   */
  public $name;
  /**
   * Kubernetes object namespace. Must be a valid DNS label. Named "ns" to avoid
   * collision with C++ namespace keyword. For details see
   * https://kubernetes.io/docs/tasks/administer-cluster/namespaces/.
   *
   * @var string
   */
  public $ns;

  /**
   * Pod containers associated with this finding, if any.
   *
   * @param GoogleCloudSecuritycenterV2Container[] $containers
   */
  public function setContainers($containers)
  {
    $this->containers = $containers;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Container[]
   */
  public function getContainers()
  {
    return $this->containers;
  }
  /**
   * Kubernetes object group, such as "policy.k8s.io/v1".
   *
   * @param string $group
   */
  public function setGroup($group)
  {
    $this->group = $group;
  }
  /**
   * @return string
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * Kubernetes object kind, such as "Namespace".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Kubernetes object name. For details see
   * https://kubernetes.io/docs/concepts/overview/working-with-objects/names/.
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
   * Kubernetes object namespace. Must be a valid DNS label. Named "ns" to avoid
   * collision with C++ namespace keyword. For details see
   * https://kubernetes.io/docs/tasks/administer-cluster/namespaces/.
   *
   * @param string $ns
   */
  public function setNs($ns)
  {
    $this->ns = $ns;
  }
  /**
   * @return string
   */
  public function getNs()
  {
    return $this->ns;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2Object::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2Object');
