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

namespace Google\Service\DriveLabels\Resource;

use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2BatchDeleteLabelPermissionsRequest;
use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2BatchUpdateLabelPermissionsRequest;
use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2BatchUpdateLabelPermissionsResponse;
use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2LabelPermission;
use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2ListLabelPermissionsResponse;
use Google\Service\DriveLabels\GoogleProtobufEmpty;

/**
 * The "permissions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $drivelabelsService = new Google\Service\DriveLabels(...);
 *   $permissions = $drivelabelsService->labels_permissions;
 *  </code>
 */
class LabelsPermissions extends \Google\Service\Resource
{
  /**
   * Deletes label permissions. Permissions affect the label resource as a whole,
   * aren't revisioned, and don't require publishing. (permissions.batchDelete)
   *
   * @param string $parent Required. The parent label resource name shared by all
   * permissions being deleted. Format: `labels/{label}`. If this is set, the
   * parent field in the `UpdateLabelPermissionRequest` messages must either be
   * empty or match this field.
   * @param GoogleAppsDriveLabelsV2BatchDeleteLabelPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function batchDelete($parent, GoogleAppsDriveLabelsV2BatchDeleteLabelPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchDelete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Updates label permissions. If a permission for the indicated principal
   * doesn't exist, a label permission is created, otherwise the existing
   * permission is updated. Permissions affect the label resource as a whole,
   * aren't revisioned, and don't require publishing. (permissions.batchUpdate)
   *
   * @param string $parent Required. The parent label resource name shared by all
   * permissions being updated. Format: `labels/{label}`. If this is set, the
   * parent field in the `UpdateLabelPermissionRequest` messages must either be
   * empty or match this field.
   * @param GoogleAppsDriveLabelsV2BatchUpdateLabelPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAppsDriveLabelsV2BatchUpdateLabelPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function batchUpdate($parent, GoogleAppsDriveLabelsV2BatchUpdateLabelPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchUpdate', [$params], GoogleAppsDriveLabelsV2BatchUpdateLabelPermissionsResponse::class);
  }
  /**
   * Updates a label's permissions. If a permission for the indicated principal
   * doesn't exist, a label permission is created, otherwise the existing
   * permission is updated. Permissions affect the label resource as a whole,
   * aren't revisioned, and don't require publishing. (permissions.create)
   *
   * @param string $parent Required. The parent label resource name on the label
   * permission is created. Format: `labels/{label}`.
   * @param GoogleAppsDriveLabelsV2LabelPermission $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool useAdminAccess Set to `true` in order to use the user's admin
   * credentials. The server will verify the user is an admin for the label before
   * allowing access.
   * @return GoogleAppsDriveLabelsV2LabelPermission
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleAppsDriveLabelsV2LabelPermission $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleAppsDriveLabelsV2LabelPermission::class);
  }
  /**
   * Deletes a label's permission. Permissions affect the label resource as a
   * whole, aren't revisioned, and don't require publishing. (permissions.delete)
   *
   * @param string $name Required. Label permission resource name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool useAdminAccess Set to `true` in order to use the user's admin
   * credentials. The server will verify the user is an admin for the label before
   * allowing access.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Lists a label's permissions. (permissions.listLabelsPermissions)
   *
   * @param string $parent Required. The parent label resource name on which label
   * permissions are listed. Format: `labels/{label}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of permissions to return per page.
   * Default: 50. Max: 200.
   * @opt_param string pageToken The token of the page to return.
   * @opt_param bool useAdminAccess Set to `true` in order to use the user's admin
   * credentials. The server will verify the user is an admin for the label before
   * allowing access.
   * @return GoogleAppsDriveLabelsV2ListLabelPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function listLabelsPermissions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleAppsDriveLabelsV2ListLabelPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LabelsPermissions::class, 'Google_Service_DriveLabels_Resource_LabelsPermissions');
