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

use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2LabelPermission;

/**
 * The "revisions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $drivelabelsService = new Google\Service\DriveLabels(...);
 *   $revisions = $drivelabelsService->labels_revisions;
 *  </code>
 */
class LabelsRevisions extends \Google\Service\Resource
{
  /**
   * Updates a label's permissions. If a permission for the indicated principal
   * doesn't exist, a label permission is created, otherwise the existing
   * permission is updated. Permissions affect the label resource as a whole,
   * aren't revisioned, and don't require publishing.
   * (revisions.updatePermissions)
   *
   * @param string $parent Required. The parent label resource name.
   * @param GoogleAppsDriveLabelsV2LabelPermission $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool useAdminAccess Set to `true` in order to use the user's admin
   * credentials. The server will verify the user is an admin for the label before
   * allowing access.
   * @return GoogleAppsDriveLabelsV2LabelPermission
   * @throws \Google\Service\Exception
   */
  public function updatePermissions($parent, GoogleAppsDriveLabelsV2LabelPermission $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updatePermissions', [$params], GoogleAppsDriveLabelsV2LabelPermission::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LabelsRevisions::class, 'Google_Service_DriveLabels_Resource_LabelsRevisions');
