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

use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2DeltaUpdateLabelRequest;
use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2DeltaUpdateLabelResponse;
use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2DisableLabelRequest;
use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2EnableLabelRequest;
use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2Label;
use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2LabelPermission;
use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2ListLabelsResponse;
use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2PublishLabelRequest;
use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2UpdateLabelCopyModeRequest;
use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2UpdateLabelEnabledAppSettingsRequest;
use Google\Service\DriveLabels\GoogleProtobufEmpty;

/**
 * The "labels" collection of methods.
 * Typical usage is:
 *  <code>
 *   $drivelabelsService = new Google\Service\DriveLabels(...);
 *   $labels = $drivelabelsService->labels;
 *  </code>
 */
class Labels extends \Google\Service\Resource
{
  /**
   * Creates a label. For more information, see [Create and publish a
   * label](https://developers.google.com/workspace/drive/labels/guides/create-
   * label). (labels.create)
   *
   * @param GoogleAppsDriveLabelsV2Label $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string languageCode The BCP-47 language code to use for evaluating
   * localized field labels in response. When not specified, values in the default
   * configured language will be used.
   * @opt_param bool useAdminAccess Set to `true` in order to use the user's admin
   * privileges. The server will verify the user is an admin before allowing
   * access.
   * @return GoogleAppsDriveLabelsV2Label
   * @throws \Google\Service\Exception
   */
  public function create(GoogleAppsDriveLabelsV2Label $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleAppsDriveLabelsV2Label::class);
  }
  /**
   * Permanently deletes a label and related metadata on Drive items. For more
   * information, see [Disable, enable, and delete a
   * label](https://developers.google.com/workspace/drive/labels/guides/disable-
   * delete-label). Once deleted, the label and related Drive item metadata will
   * be deleted. Only draft labels and disabled labels may be deleted.
   * (labels.delete)
   *
   * @param string $name Required. Label resource name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool useAdminAccess Set to `true` in order to use the user's admin
   * credentials. The server will verify the user is an admin for the label before
   * allowing access.
   * @opt_param string writeControl.requiredRevisionId The revision ID of the
   * label that the write request will be applied to. If this isn't the latest
   * revision of the label, the request will not be processed and will return a
   * 400 Bad Request error.
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
   * Updates a single label by applying a set of update requests resulting in a
   * new draft revision. For more information, see [Update a
   * label](https://developers.google.com/workspace/drive/labels/guides/update-
   * label). The batch update is all-or-nothing: If any of the update requests are
   * invalid, no changes are applied. The resulting draft revision must be
   * published before the changes may be used with Drive items. (labels.delta)
   *
   * @param string $name Required. The resource name of the label to update.
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelResponse
   * @throws \Google\Service\Exception
   */
  public function delta($name, GoogleAppsDriveLabelsV2DeltaUpdateLabelRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('delta', [$params], GoogleAppsDriveLabelsV2DeltaUpdateLabelResponse::class);
  }
  /**
   * Disable a published label. For more information, see [Disable, enable, and
   * delete a
   * label](https://developers.google.com/workspace/drive/labels/guides/disable-
   * delete-label). Disabling a label will result in a new disabled published
   * revision based on the current published revision. If there's a draft
   * revision, a new disabled draft revision will be created based on the latest
   * draft revision. Older draft revisions will be deleted. Once disabled, a label
   * may be deleted with `DeleteLabel`. (labels.disable)
   *
   * @param string $name Required. Label resource name.
   * @param GoogleAppsDriveLabelsV2DisableLabelRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAppsDriveLabelsV2Label
   * @throws \Google\Service\Exception
   */
  public function disable($name, GoogleAppsDriveLabelsV2DisableLabelRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('disable', [$params], GoogleAppsDriveLabelsV2Label::class);
  }
  /**
   * Enable a disabled label and restore it to its published state. For more
   * information, see [Disable, enable, and delete a
   * label](https://developers.google.com/workspace/drive/labels/guides/disable-
   * delete-label). This will result in a new published revision based on the
   * current disabled published revision. If there's an existing disabled draft
   * revision, a new revision will be created based on that draft and will be
   * enabled. (labels.enable)
   *
   * @param string $name Required. Label resource name.
   * @param GoogleAppsDriveLabelsV2EnableLabelRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAppsDriveLabelsV2Label
   * @throws \Google\Service\Exception
   */
  public function enable($name, GoogleAppsDriveLabelsV2EnableLabelRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('enable', [$params], GoogleAppsDriveLabelsV2Label::class);
  }
  /**
   * Get a label by its resource name. For more information, see [Search for
   * labels](https://developers.google.com/workspace/drive/labels/guides/search-
   * label). Resource name may be any of: * `labels/{id}` - See
   * `labels/{id}@latest` * `labels/{id}@latest` - Gets the latest revision of the
   * label. * `labels/{id}@published` - Gets the current published revision of the
   * label. * `labels/{id}@{revision_id}` - Gets the label at the specified
   * revision ID. (labels.get)
   *
   * @param string $name Required. Label resource name. May be any of: *
   * `labels/{id}` (equivalent to labels/{id}@latest) * `labels/{id}@latest` *
   * `labels/{id}@published` * `labels/{id}@{revision_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string languageCode The BCP-47 language code to use for evaluating
   * localized field labels. When not specified, values in the default configured
   * language are used.
   * @opt_param bool useAdminAccess Set to `true` in order to use the user's admin
   * credentials. The server verifies that the user is an admin for the label
   * before allowing access.
   * @opt_param string view When specified, only certain fields belonging to the
   * indicated view are returned.
   * @return GoogleAppsDriveLabelsV2Label
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleAppsDriveLabelsV2Label::class);
  }
  /**
   * List labels. For more information, see [Search for
   * labels](https://developers.google.com/workspace/drive/labels/guides/search-
   * label). (labels.listLabels)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string customer The customer to scope this list request to. For
   * example: `customers/abcd1234`. If unset, will return all labels within the
   * current customer.
   * @opt_param string languageCode The BCP-47 language code to use for evaluating
   * localized field labels. When not specified, values in the default configured
   * language are used.
   * @opt_param string minimumRole Specifies the level of access the user must
   * have on the returned labels. The minimum role a user must have on a label.
   * Defaults to `READER`.
   * @opt_param int pageSize Maximum number of labels to return per page. Default:
   * 50. Max: 200.
   * @opt_param string pageToken The token of the page to return.
   * @opt_param bool publishedOnly Whether to include only published labels in the
   * results. * When `true`, only the current published label revisions are
   * returned. Disabled labels are included. Returned label resource names
   * reference the published revision (`labels/{id}/{revision_id}`). * When
   * `false`, the current label revisions are returned, which might not be
   * published. Returned label resource names don't reference a specific revision
   * (`labels/{id}`).
   * @opt_param bool useAdminAccess Set to `true` in order to use the user's admin
   * credentials. This will return all labels within the customer.
   * @opt_param string view When specified, only certain fields belonging to the
   * indicated view are returned.
   * @return GoogleAppsDriveLabelsV2ListLabelsResponse
   * @throws \Google\Service\Exception
   */
  public function listLabels($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleAppsDriveLabelsV2ListLabelsResponse::class);
  }
  /**
   * Publish all draft changes to the label. Once published, the label may not
   * return to its draft state. For more information, see [Create and publish a
   * label](https://developers.google.com/workspace/drive/labels/guides/create-
   * label). Publishing a label will result in a new published revision. All
   * previous draft revisions will be deleted. Previous published revisions will
   * be kept but are subject to automated deletion as needed. For more
   * information, see [Label
   * lifecycle](https://developers.google.com/workspace/drive/labels/guides/label-
   * lifecycle). Once published, some changes are no longer permitted. Generally,
   * any change that would invalidate or cause new restrictions on existing
   * metadata related to the label will be rejected. For example, the following
   * changes to a label will be rejected after the label is published: * The label
   * cannot be directly deleted. It must be disabled first, then deleted. *
   * `Field.FieldType` cannot be changed. * Changes to field validation options
   * cannot reject something that was previously accepted. * Reducing the maximum
   * entries. (labels.publish)
   *
   * @param string $name Required. Label resource name.
   * @param GoogleAppsDriveLabelsV2PublishLabelRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAppsDriveLabelsV2Label
   * @throws \Google\Service\Exception
   */
  public function publish($name, GoogleAppsDriveLabelsV2PublishLabelRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('publish', [$params], GoogleAppsDriveLabelsV2Label::class);
  }
  /**
   * Updates a label's `CopyMode`. Changes to this policy aren't revisioned, don't
   * require publishing, and take effect immediately. (labels.updateLabelCopyMode)
   *
   * @param string $name Required. The resource name of the label to update.
   * @param GoogleAppsDriveLabelsV2UpdateLabelCopyModeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAppsDriveLabelsV2Label
   * @throws \Google\Service\Exception
   */
  public function updateLabelCopyMode($name, GoogleAppsDriveLabelsV2UpdateLabelCopyModeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateLabelCopyMode', [$params], GoogleAppsDriveLabelsV2Label::class);
  }
  /**
   * Updates a label's `EnabledAppSettings`. Enabling a label in a Google
   * Workspace app allows it to be used in that app. This change isn't revisioned,
   * doesn't require publishing, and takes effect immediately.
   * (labels.updateLabelEnabledAppSettings)
   *
   * @param string $name Required. The resource name of the label to update. The
   * resource name of the label to update.
   * @param GoogleAppsDriveLabelsV2UpdateLabelEnabledAppSettingsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAppsDriveLabelsV2Label
   * @throws \Google\Service\Exception
   */
  public function updateLabelEnabledAppSettings($name, GoogleAppsDriveLabelsV2UpdateLabelEnabledAppSettingsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateLabelEnabledAppSettings', [$params], GoogleAppsDriveLabelsV2Label::class);
  }
  /**
   * Updates a label's permissions. If a permission for the indicated principal
   * doesn't exist, a label permission is created, otherwise the existing
   * permission is updated. Permissions affect the label resource as a whole,
   * aren't revisioned, and don't require publishing. (labels.updatePermissions)
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
class_alias(Labels::class, 'Google_Service_DriveLabels_Resource_Labels');
