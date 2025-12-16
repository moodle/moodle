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

namespace Google\Service\DisplayVideo;

class BulkEditAssignedUserRolesRequest extends \Google\Collection
{
  protected $collection_key = 'deletedAssignedUserRoles';
  protected $createdAssignedUserRolesType = AssignedUserRole::class;
  protected $createdAssignedUserRolesDataType = 'array';
  /**
   * The assigned user roles to delete in batch, specified as a list of
   * assigned_user_role_ids. The format of assigned_user_role_id is `entityType-
   * entityid`, for example `partner-123`.
   *
   * @var string[]
   */
  public $deletedAssignedUserRoles;

  /**
   * The assigned user roles to create in batch, specified as a list of
   * AssignedUserRoles.
   *
   * @param AssignedUserRole[] $createdAssignedUserRoles
   */
  public function setCreatedAssignedUserRoles($createdAssignedUserRoles)
  {
    $this->createdAssignedUserRoles = $createdAssignedUserRoles;
  }
  /**
   * @return AssignedUserRole[]
   */
  public function getCreatedAssignedUserRoles()
  {
    return $this->createdAssignedUserRoles;
  }
  /**
   * The assigned user roles to delete in batch, specified as a list of
   * assigned_user_role_ids. The format of assigned_user_role_id is `entityType-
   * entityid`, for example `partner-123`.
   *
   * @param string[] $deletedAssignedUserRoles
   */
  public function setDeletedAssignedUserRoles($deletedAssignedUserRoles)
  {
    $this->deletedAssignedUserRoles = $deletedAssignedUserRoles;
  }
  /**
   * @return string[]
   */
  public function getDeletedAssignedUserRoles()
  {
    return $this->deletedAssignedUserRoles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulkEditAssignedUserRolesRequest::class, 'Google_Service_DisplayVideo_BulkEditAssignedUserRolesRequest');
