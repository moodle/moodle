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

class Role extends \Google\Model
{
  /**
   * Role type is not specified.
   */
  public const KIND_KIND_UNSPECIFIED = 'KIND_UNSPECIFIED';
  /**
   * Kubernetes Role.
   */
  public const KIND_ROLE = 'ROLE';
  /**
   * Kubernetes ClusterRole.
   */
  public const KIND_CLUSTER_ROLE = 'CLUSTER_ROLE';
  /**
   * Role type.
   *
   * @var string
   */
  public $kind;
  /**
   * Role name.
   *
   * @var string
   */
  public $name;
  /**
   * Role namespace.
   *
   * @var string
   */
  public $ns;

  /**
   * Role type.
   *
   * Accepted values: KIND_UNSPECIFIED, ROLE, CLUSTER_ROLE
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Role name.
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
   * Role namespace.
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
class_alias(Role::class, 'Google_Service_SecurityCommandCenter_Role');
