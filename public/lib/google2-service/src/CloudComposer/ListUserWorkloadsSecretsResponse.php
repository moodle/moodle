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

namespace Google\Service\CloudComposer;

class ListUserWorkloadsSecretsResponse extends \Google\Collection
{
  protected $collection_key = 'userWorkloadsSecrets';
  /**
   * The page token used to query for the next page if one exists.
   *
   * @var string
   */
  public $nextPageToken;
  protected $userWorkloadsSecretsType = UserWorkloadsSecret::class;
  protected $userWorkloadsSecretsDataType = 'array';

  /**
   * The page token used to query for the next page if one exists.
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
  /**
   * The list of Secrets returned by a ListUserWorkloadsSecretsRequest.
   *
   * @param UserWorkloadsSecret[] $userWorkloadsSecrets
   */
  public function setUserWorkloadsSecrets($userWorkloadsSecrets)
  {
    $this->userWorkloadsSecrets = $userWorkloadsSecrets;
  }
  /**
   * @return UserWorkloadsSecret[]
   */
  public function getUserWorkloadsSecrets()
  {
    return $this->userWorkloadsSecrets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListUserWorkloadsSecretsResponse::class, 'Google_Service_CloudComposer_ListUserWorkloadsSecretsResponse');
