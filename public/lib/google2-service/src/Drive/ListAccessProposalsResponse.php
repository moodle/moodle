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

namespace Google\Service\Drive;

class ListAccessProposalsResponse extends \Google\Collection
{
  protected $collection_key = 'accessProposals';
  protected $accessProposalsType = AccessProposal::class;
  protected $accessProposalsDataType = 'array';
  /**
   * The continuation token for the next page of results. This will be absent if
   * the end of the results list has been reached. If the token is rejected for
   * any reason, it should be discarded, and pagination should be restarted from
   * the first page of results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of access proposals. This field is only populated in Drive API v3.
   *
   * @param AccessProposal[] $accessProposals
   */
  public function setAccessProposals($accessProposals)
  {
    $this->accessProposals = $accessProposals;
  }
  /**
   * @return AccessProposal[]
   */
  public function getAccessProposals()
  {
    return $this->accessProposals;
  }
  /**
   * The continuation token for the next page of results. This will be absent if
   * the end of the results list has been reached. If the token is rejected for
   * any reason, it should be discarded, and pagination should be restarted from
   * the first page of results.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListAccessProposalsResponse::class, 'Google_Service_Drive_ListAccessProposalsResponse');
