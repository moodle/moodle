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

namespace Google\Service\Drive\Resource;

use Google\Service\Drive\Revision;
use Google\Service\Drive\RevisionList;

/**
 * The "revisions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $driveService = new Google\Service\Drive(...);
 *   $revisions = $driveService->revisions;
 *  </code>
 */
class Revisions extends \Google\Service\Resource
{
  /**
   * Permanently deletes a file version. You can only delete revisions for files
   * with binary content in Google Drive, like images or videos. Revisions for
   * other files, like Google Docs or Sheets, and the last remaining file version
   * can't be deleted. For more information, see [Manage file
   * revisions](https://developers.google.com/drive/api/guides/manage-revisions).
   * (revisions.delete)
   *
   * @param string $fileId The ID of the file.
   * @param string $revisionId The ID of the revision.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function delete($fileId, $revisionId, $optParams = [])
  {
    $params = ['fileId' => $fileId, 'revisionId' => $revisionId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Gets a revision's metadata or content by ID. For more information, see
   * [Manage file
   * revisions](https://developers.google.com/workspace/drive/api/guides/manage-
   * revisions). (revisions.get)
   *
   * @param string $fileId The ID of the file.
   * @param string $revisionId The ID of the revision.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool acknowledgeAbuse Whether the user is acknowledging the risk
   * of downloading known malware or other abusive files. This is only applicable
   * when the `alt` parameter is set to `media` and the user is the owner of the
   * file or an organizer of the shared drive in which the file resides.
   * @return Revision
   * @throws \Google\Service\Exception
   */
  public function get($fileId, $revisionId, $optParams = [])
  {
    $params = ['fileId' => $fileId, 'revisionId' => $revisionId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Revision::class);
  }
  /**
   * Lists a file's revisions. For more information, see [Manage file
   * revisions](https://developers.google.com/workspace/drive/api/guides/manage-
   * revisions). **Important:** The list of revisions returned by this method
   * might be incomplete for files with a large revision history, including
   * frequently edited Google Docs, Sheets, and Slides. Older revisions might be
   * omitted from the response, meaning the first revision returned may not be the
   * oldest existing revision. The revision history visible in the Workspace
   * editor user interface might be more complete than the list returned by the
   * API. (revisions.listRevisions)
   *
   * @param string $fileId The ID of the file.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of revisions to return per page.
   * @opt_param string pageToken The token for continuing a previous list request
   * on the next page. This should be set to the value of 'nextPageToken' from the
   * previous response.
   * @return RevisionList
   * @throws \Google\Service\Exception
   */
  public function listRevisions($fileId, $optParams = [])
  {
    $params = ['fileId' => $fileId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], RevisionList::class);
  }
  /**
   * Updates a revision with patch semantics. For more information, see [Manage
   * file
   * revisions](https://developers.google.com/workspace/drive/api/guides/manage-
   * revisions). (revisions.update)
   *
   * @param string $fileId The ID of the file.
   * @param string $revisionId The ID of the revision.
   * @param Revision $postBody
   * @param array $optParams Optional parameters.
   * @return Revision
   * @throws \Google\Service\Exception
   */
  public function update($fileId, $revisionId, Revision $postBody, $optParams = [])
  {
    $params = ['fileId' => $fileId, 'revisionId' => $revisionId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], Revision::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Revisions::class, 'Google_Service_Drive_Resource_Revisions');
