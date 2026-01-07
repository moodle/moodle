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

namespace Google\Service\AccessContextManager;

class ListAuthorizedOrgsDescsResponse extends \Google\Collection
{
  protected $collection_key = 'authorizedOrgsDescs';
  protected $authorizedOrgsDescsType = AuthorizedOrgsDesc::class;
  protected $authorizedOrgsDescsDataType = 'array';
  /**
   * The pagination token to retrieve the next page of results. If the value is
   * empty, no further results remain.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * List of all the Authorized Orgs Desc instances.
   *
   * @param AuthorizedOrgsDesc[] $authorizedOrgsDescs
   */
  public function setAuthorizedOrgsDescs($authorizedOrgsDescs)
  {
    $this->authorizedOrgsDescs = $authorizedOrgsDescs;
  }
  /**
   * @return AuthorizedOrgsDesc[]
   */
  public function getAuthorizedOrgsDescs()
  {
    return $this->authorizedOrgsDescs;
  }
  /**
   * The pagination token to retrieve the next page of results. If the value is
   * empty, no further results remain.
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
class_alias(ListAuthorizedOrgsDescsResponse::class, 'Google_Service_AccessContextManager_ListAuthorizedOrgsDescsResponse');
