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

use Google\Service\Drive\Approval;
use Google\Service\Drive\ApprovalList;

/**
 * The "approvals" collection of methods.
 * Typical usage is:
 *  <code>
 *   $driveService = new Google\Service\Drive(...);
 *   $approvals = $driveService->approvals;
 *  </code>
 */
class Approvals extends \Google\Service\Resource
{
  /**
   * Gets an Approval by ID. (approvals.get)
   *
   * @param string $fileId Required. The ID of the file the Approval is on.
   * @param string $approvalId Required. The ID of the Approval.
   * @param array $optParams Optional parameters.
   * @return Approval
   * @throws \Google\Service\Exception
   */
  public function get($fileId, $approvalId, $optParams = [])
  {
    $params = ['fileId' => $fileId, 'approvalId' => $approvalId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Approval::class);
  }
  /**
   * Lists the Approvals on a file. (approvals.listApprovals)
   *
   * @param string $fileId Required. The ID of the file the Approval is on.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of Approvals to return. When not
   * set, at most 100 Approvals will be returned.
   * @opt_param string pageToken The token for continuing a previous list request
   * on the next page. This should be set to the value of nextPageToken from a
   * previous response.
   * @return ApprovalList
   * @throws \Google\Service\Exception
   */
  public function listApprovals($fileId, $optParams = [])
  {
    $params = ['fileId' => $fileId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ApprovalList::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Approvals::class, 'Google_Service_Drive_Resource_Approvals');
