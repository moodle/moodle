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

namespace Google\Service\PostmasterTools;

class Domain extends \Google\Model
{
  /**
   * The default value and should never be used explicitly.
   */
  public const PERMISSION_PERMISSION_UNSPECIFIED = 'PERMISSION_UNSPECIFIED';
  /**
   * User has read access to the domain and can share access with others.
   */
  public const PERMISSION_OWNER = 'OWNER';
  /**
   * User has read access to the domain.
   */
  public const PERMISSION_READER = 'READER';
  /**
   * User doesn't have permission to access information about the domain. User
   * did not verify ownership of domain nor was access granted by other domain
   * owners.
   */
  public const PERMISSION_NONE = 'NONE';
  /**
   * Timestamp when the user registered this domain. Assigned by the server.
   *
   * @var string
   */
  public $createTime;
  /**
   * The resource name of the Domain. Domain names have the form
   * `domains/{domain_name}`, where domain_name is the fully qualified domain
   * name (i.e., mymail.mydomain.com).
   *
   * @var string
   */
  public $name;
  /**
   * User’s permission for this domain. Assigned by the server.
   *
   * @var string
   */
  public $permission;

  /**
   * Timestamp when the user registered this domain. Assigned by the server.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The resource name of the Domain. Domain names have the form
   * `domains/{domain_name}`, where domain_name is the fully qualified domain
   * name (i.e., mymail.mydomain.com).
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
   * User’s permission for this domain. Assigned by the server.
   *
   * Accepted values: PERMISSION_UNSPECIFIED, OWNER, READER, NONE
   *
   * @param self::PERMISSION_* $permission
   */
  public function setPermission($permission)
  {
    $this->permission = $permission;
  }
  /**
   * @return self::PERMISSION_*
   */
  public function getPermission()
  {
    return $this->permission;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Domain::class, 'Google_Service_PostmasterTools_Domain');
