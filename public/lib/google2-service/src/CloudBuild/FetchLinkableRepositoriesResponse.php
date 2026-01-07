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

namespace Google\Service\CloudBuild;

class FetchLinkableRepositoriesResponse extends \Google\Collection
{
  protected $collection_key = 'repositories';
  /**
   * A token identifying a page of results the server should return.
   *
   * @var string
   */
  public $nextPageToken;
  protected $repositoriesType = Repository::class;
  protected $repositoriesDataType = 'array';

  /**
   * A token identifying a page of results the server should return.
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
   * repositories ready to be created.
   *
   * @param Repository[] $repositories
   */
  public function setRepositories($repositories)
  {
    $this->repositories = $repositories;
  }
  /**
   * @return Repository[]
   */
  public function getRepositories()
  {
    return $this->repositories;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FetchLinkableRepositoriesResponse::class, 'Google_Service_CloudBuild_FetchLinkableRepositoriesResponse');
