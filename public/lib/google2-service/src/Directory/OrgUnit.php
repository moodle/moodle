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

class OrgUnit extends \Google\Model
{
  /**
   * This field is deprecated and setting its value has no effect.
   *
   * @deprecated
   * @var bool
   */
  public $blockInheritance;
  /**
   * Description of the organizational unit.
   *
   * @var string
   */
  public $description;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The type of the API resource. For Orgunits resources, the value is
   * `admin#directory#orgUnit`.
   *
   * @var string
   */
  public $kind;
  /**
   * The organizational unit's path name. For example, an organizational unit's
   * name within the /corp/support/sales_support parent path is sales_support.
   * Required.
   *
   * @var string
   */
  public $name;
  /**
   * The unique ID of the organizational unit.
   *
   * @var string
   */
  public $orgUnitId;
  /**
   * The full path to the organizational unit. The `orgUnitPath` is a derived
   * property. When listed, it is derived from `parentOrgunitPath` and
   * organizational unit's `name`. For example, for an organizational unit named
   * 'apps' under parent organization '/engineering', the orgUnitPath is
   * '/engineering/apps'. In order to edit an `orgUnitPath`, either update the
   * name of the organization or the `parentOrgunitPath`. A user's
   * organizational unit determines which Google Workspace services the user has
   * access to. If the user is moved to a new organization, the user's access
   * changes. For more information about organization structures, see the
   * [administration help center](https://support.google.com/a/answer/4352075).
   * For more information about moving a user to a different organization, see
   * [Update a user](https://developers.google.com/workspace/admin/directory/v1/
   * guides/manage-users.html#update_user).
   *
   * @var string
   */
  public $orgUnitPath;
  /**
   * The unique ID of the parent organizational unit. Required, unless
   * `parentOrgUnitPath` is set.
   *
   * @var string
   */
  public $parentOrgUnitId;
  /**
   * The organizational unit's parent path. For example, /corp/sales is the
   * parent path for /corp/sales/sales_support organizational unit. Required,
   * unless `parentOrgUnitId` is set.
   *
   * @var string
   */
  public $parentOrgUnitPath;

  /**
   * This field is deprecated and setting its value has no effect.
   *
   * @deprecated
   * @param bool $blockInheritance
   */
  public function setBlockInheritance($blockInheritance)
  {
    $this->blockInheritance = $blockInheritance;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getBlockInheritance()
  {
    return $this->blockInheritance;
  }
  /**
   * Description of the organizational unit.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
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
   * The type of the API resource. For Orgunits resources, the value is
   * `admin#directory#orgUnit`.
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
   * The organizational unit's path name. For example, an organizational unit's
   * name within the /corp/support/sales_support parent path is sales_support.
   * Required.
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
   * The unique ID of the organizational unit.
   *
   * @param string $orgUnitId
   */
  public function setOrgUnitId($orgUnitId)
  {
    $this->orgUnitId = $orgUnitId;
  }
  /**
   * @return string
   */
  public function getOrgUnitId()
  {
    return $this->orgUnitId;
  }
  /**
   * The full path to the organizational unit. The `orgUnitPath` is a derived
   * property. When listed, it is derived from `parentOrgunitPath` and
   * organizational unit's `name`. For example, for an organizational unit named
   * 'apps' under parent organization '/engineering', the orgUnitPath is
   * '/engineering/apps'. In order to edit an `orgUnitPath`, either update the
   * name of the organization or the `parentOrgunitPath`. A user's
   * organizational unit determines which Google Workspace services the user has
   * access to. If the user is moved to a new organization, the user's access
   * changes. For more information about organization structures, see the
   * [administration help center](https://support.google.com/a/answer/4352075).
   * For more information about moving a user to a different organization, see
   * [Update a user](https://developers.google.com/workspace/admin/directory/v1/
   * guides/manage-users.html#update_user).
   *
   * @param string $orgUnitPath
   */
  public function setOrgUnitPath($orgUnitPath)
  {
    $this->orgUnitPath = $orgUnitPath;
  }
  /**
   * @return string
   */
  public function getOrgUnitPath()
  {
    return $this->orgUnitPath;
  }
  /**
   * The unique ID of the parent organizational unit. Required, unless
   * `parentOrgUnitPath` is set.
   *
   * @param string $parentOrgUnitId
   */
  public function setParentOrgUnitId($parentOrgUnitId)
  {
    $this->parentOrgUnitId = $parentOrgUnitId;
  }
  /**
   * @return string
   */
  public function getParentOrgUnitId()
  {
    return $this->parentOrgUnitId;
  }
  /**
   * The organizational unit's parent path. For example, /corp/sales is the
   * parent path for /corp/sales/sales_support organizational unit. Required,
   * unless `parentOrgUnitId` is set.
   *
   * @param string $parentOrgUnitPath
   */
  public function setParentOrgUnitPath($parentOrgUnitPath)
  {
    $this->parentOrgUnitPath = $parentOrgUnitPath;
  }
  /**
   * @return string
   */
  public function getParentOrgUnitPath()
  {
    return $this->parentOrgUnitPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrgUnit::class, 'Google_Service_Directory_OrgUnit');
