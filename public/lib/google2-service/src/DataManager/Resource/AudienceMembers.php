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

namespace Google\Service\DataManager\Resource;

use Google\Service\DataManager\IngestAudienceMembersRequest;
use Google\Service\DataManager\IngestAudienceMembersResponse;
use Google\Service\DataManager\RemoveAudienceMembersRequest;
use Google\Service\DataManager\RemoveAudienceMembersResponse;

/**
 * The "audienceMembers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $datamanagerService = new Google\Service\DataManager(...);
 *   $audienceMembers = $datamanagerService->audienceMembers;
 *  </code>
 */
class AudienceMembers extends \Google\Service\Resource
{
  /**
   * Uploads a list of AudienceMember resources to the provided Destination.
   * (audienceMembers.ingest)
   *
   * @param IngestAudienceMembersRequest $postBody
   * @param array $optParams Optional parameters.
   * @return IngestAudienceMembersResponse
   * @throws \Google\Service\Exception
   */
  public function ingest(IngestAudienceMembersRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('ingest', [$params], IngestAudienceMembersResponse::class);
  }
  /**
   * Removes a list of AudienceMember resources from the provided Destination.
   * (audienceMembers.remove)
   *
   * @param RemoveAudienceMembersRequest $postBody
   * @param array $optParams Optional parameters.
   * @return RemoveAudienceMembersResponse
   * @throws \Google\Service\Exception
   */
  public function remove(RemoveAudienceMembersRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('remove', [$params], RemoveAudienceMembersResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AudienceMembers::class, 'Google_Service_DataManager_Resource_AudienceMembers');
