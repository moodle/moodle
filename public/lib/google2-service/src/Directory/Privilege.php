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

namespace Google\Service\Directory;

class Privilege extends \Google\Collection
{
  protected $collection_key = 'childPrivileges';
  protected $childPrivilegesType = Privilege::class;
  protected $childPrivilegesDataType = 'array';
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * If the privilege can be restricted to an organization unit.
   *
   * @var bool
   */
  public $isOuScopable;
  /**
   * The type of the API resource. This is always `admin#directory#privilege`.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of the privilege.
   *
   * @var string
   */
  public $privilegeName;
  /**
   * The obfuscated ID of the service this privilege is for. This value is
   * returned with [`Privileges.list()`](https://developers.google.com/workspace
   * /admin/directory/v1/reference/privileges/list).
   *
   * @var string
   */
  public $serviceId;
  /**
   * The name of the service this privilege is for.
   *
   * @var string
   */
  public $serviceName;

  /**
   * A list of child privileges. Privileges for a service form a tree. Each
   * privilege can have a list of child privileges; this list is empty for a
   * leaf privilege.
   *
   * @param Privilege[] $childPrivileges
   */
  public function setChildPrivileges($childPrivileges)
  {
    $this->childPrivileges = $childPrivileges;
  }
  /**
   * @return Privilege[]
   */
  public function getChildPrivileges()
  {
    return $this->childPrivileges;
  }
  /**
   * ETag of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * If the privilege can be restricted to an organization unit.
   *
   * @param bool $isOuScopable
   */
  public function setIsOuScopable($isOuScopable)
  {
    $this->isOuScopable = $isOuScopable;
  }
  /**
   * @return bool
   */
  public function getIsOuScopable()
  {
    return $this->isOuScopable;
  }
  /**
   * The type of the API resource. This is always `admin#directory#privilege`.
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
   * The name of the privilege.
   *
   * @param string $privilegeName
   */
  public function setPrivilegeName($privilegeName)
  {
    $this->privilegeName = $privilegeName;
  }
  /**
   * @return string
   */
  public function getPrivilegeName()
  {
    return $this->privilegeName;
  }
  /**
   * The obfuscated ID of the service this privilege is for. This value is
   * returned with [`Privileges.list()`](https://developers.google.com/workspace
   * /admin/directory/v1/reference/privileges/list).
   *
   * @param string $serviceId
   */
  public function setServiceId($serviceId)
  {
    $this->serviceId = $serviceId;
  }
  /**
   * @return string
   */
  public function getServiceId()
  {
    return $this->serviceId;
  }
  /**
   * The name of the service this privilege is for.
   *
   * @param string $serviceName
   */
  public function setServiceName($serviceName)
  {
    $this->serviceName = $serviceName;
  }
  /**
   * @return string
   */
  public function getServiceName()
  {
    return $this->serviceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Privilege::class, 'Google_Service_Directory_Privilege');
