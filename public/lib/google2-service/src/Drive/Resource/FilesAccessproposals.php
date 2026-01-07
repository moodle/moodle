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

use Google\Service\Drive\ListAccessProposalsResponse;

/**
 * The "accessproposals" collection of methods.
 * Typical usage is:
 *  <code>
 *   $driveService = new Google\Service\Drive(...);
 *   $accessproposals = $driveService->files_accessproposals;
 *  </code>
 */
class FilesAccessproposals extends \Google\Service\Resource
{
  /**
   * List the AccessProposals on a file. Note: Only approvers are able to list
   * AccessProposals on a file. If the user is not an approver, returns a 403.
   * (accessproposals.listFilesAccessproposals)
   *
   * @param string $fileId Required. The id of the item the request is on.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The number of results per page
   * @opt_param string pageToken Optional. The continuation token on the list of
   * access requests.
   * @return ListAccessProposalsResponse
   * @throws \Google\Service\Exception
   */
  public function listFilesAccessproposals($fileId, $optParams = [])
  {
    $params = ['fileId' => $fileId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAccessProposalsResponse::class);
  }
  /**
   * Used to approve or deny an Access Proposal. (accessproposals.resolve)
   *
   * @param string $fileId Required. The id of the item the request is on.
   * @param string $proposalId Required. The id of the access proposal to resolve.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string action Required. The action to take on the AccessProposal.
   * @opt_param string role Optional. The roles the approver has allowed, if any.
   * Note: This field is required for the `ACCEPT` action.
   * @opt_param bool sendNotification Optional. Whether to send an email to the
   * requester when the AccessProposal is denied or accepted.
   * @opt_param string view Optional. Indicates the view for this access proposal.
   * This should only be set when the proposal belongs to a view. `published` is
   * the only supported value.
   * @throws \Google\Service\Exception
   */
  public function resolve($fileId, $proposalId, $optParams = [])
  {
    $params = ['fileId' => $fileId, 'proposalId' => $proposalId];
    $params = array_merge($params, $optParams);
    return $this->call('resolve', [$params]);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FilesAccessproposals::class, 'Google_Service_Drive_Resource_FilesAccessproposals');
