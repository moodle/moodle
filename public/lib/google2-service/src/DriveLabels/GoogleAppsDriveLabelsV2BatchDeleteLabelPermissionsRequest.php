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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2BatchDeleteLabelPermissionsRequest extends \Google\Collection
{
  protected $collection_key = 'requests';
  protected $requestsType = GoogleAppsDriveLabelsV2DeleteLabelPermissionRequest::class;
  protected $requestsDataType = 'array';
  /**
   * Set to `true` in order to use the user's admin credentials. The server will
   * verify the user is an admin for the label before allowing access. If this
   * is set, the `use_admin_access` field in the `DeleteLabelPermissionRequest`
   * messages must either be empty or match this field.
   *
   * @var bool
   */
  public $useAdminAccess;

  /**
   * Required. The request message specifying the resources to update.
   *
   * @param GoogleAppsDriveLabelsV2DeleteLabelPermissionRequest[] $requests
   */
  public function setRequests($requests)
  {
    $this->requests = $requests;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeleteLabelPermissionRequest[]
   */
  public function getRequests()
  {
    return $this->requests;
  }
  /**
   * Set to `true` in order to use the user's admin credentials. The server will
   * verify the user is an admin for the label before allowing access. If this
   * is set, the `use_admin_access` field in the `DeleteLabelPermissionRequest`
   * messages must either be empty or match this field.
   *
   * @param bool $useAdminAccess
   */
  public function setUseAdminAccess($useAdminAccess)
  {
    $this->useAdminAccess = $useAdminAccess;
  }
  /**
   * @return bool
   */
  public function getUseAdminAccess()
  {
    return $this->useAdminAccess;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2BatchDeleteLabelPermissionsRequest::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2BatchDeleteLabelPermissionsRequest');
