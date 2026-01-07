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

namespace Google\Service\Iam;

class GoogleIamAdminV1AuditData extends \Google\Model
{
  protected $permissionDeltaType = GoogleIamAdminV1AuditDataPermissionDelta::class;
  protected $permissionDeltaDataType = '';

  /**
   * The permission_delta when when creating or updating a Role.
   *
   * @param GoogleIamAdminV1AuditDataPermissionDelta $permissionDelta
   */
  public function setPermissionDelta(GoogleIamAdminV1AuditDataPermissionDelta $permissionDelta)
  {
    $this->permissionDelta = $permissionDelta;
  }
  /**
   * @return GoogleIamAdminV1AuditDataPermissionDelta
   */
  public function getPermissionDelta()
  {
    return $this->permissionDelta;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIamAdminV1AuditData::class, 'Google_Service_Iam_GoogleIamAdminV1AuditData');
