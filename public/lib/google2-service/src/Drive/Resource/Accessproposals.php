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

use Google\Service\Drive\AccessProposal;
use Google\Service\Drive\ListAccessProposalsResponse;
use Google\Service\Drive\ResolveAccessProposalRequest;

/**
 * The "accessproposals" collection of methods.
 * Typical usage is:
 *  <code>
 *   $driveService = new Google\Service\Drive(...);
 *   $accessproposals = $driveService->accessproposals;
 *  </code>
 */
class Accessproposals extends \Google\Service\Resource
{
  /**
   * Retrieves an access proposal by ID. For more information, see [Manage pending
   * access
   * proposals](https://developers.google.com/workspace/drive/api/guides/pending-
   * access). (accessproposals.get)
   *
   * @param string $fileId Required. The ID of the item the request is on.
   * @param string $proposalId Required. The ID of the access proposal to resolve.
   * @param array $optParams Optional parameters.
   * @return AccessProposal
   * @throws \Google\Service\Exception
   */
  public function get($fileId, $proposalId, $optParams = [])
  {
    $params = ['fileId' => $fileId, 'proposalId' => $proposalId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AccessProposal::class);
  }
  /**
   * List the access proposals on a file. For more information, see [Manage
   * pending access
   * proposals](https://developers.google.com/workspace/drive/api/guides/pending-
   * access). Note: Only approvers are able to list access proposals on a file. If
   * the user isn't an approver, a 403 error is returned.
   * (accessproposals.listAccessproposals)
   *
   * @param string $fileId Required. The ID of the item the request is on.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The number of results per page.
   * @opt_param string pageToken Optional. The continuation token on the list of
   * access requests.
   * @return ListAccessProposalsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccessproposals($fileId, $optParams = [])
  {
    $params = ['fileId' => $fileId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAccessProposalsResponse::class);
  }
  /**
   * Approves or denies an access proposal. For more information, see [Manage
   * pending access
   * proposals](https://developers.google.com/workspace/drive/api/guides/pending-
   * access). (accessproposals.resolve)
   *
   * @param string $fileId Required. The ID of the item the request is on.
   * @param string $proposalId Required. The ID of the access proposal to resolve.
   * @param ResolveAccessProposalRequest $postBody
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function resolve($fileId, $proposalId, ResolveAccessProposalRequest $postBody, $optParams = [])
  {
    $params = ['fileId' => $fileId, 'proposalId' => $proposalId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resolve', [$params]);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Accessproposals::class, 'Google_Service_Drive_Resource_Accessproposals');
