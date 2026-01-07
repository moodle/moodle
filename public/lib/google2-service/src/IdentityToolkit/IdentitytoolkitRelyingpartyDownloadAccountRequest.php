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

namespace Google\Service\IdentityToolkit;

class IdentitytoolkitRelyingpartyDownloadAccountRequest extends \Google\Model
{
  /**
   * GCP project number of the requesting delegated app. Currently only intended
   * for Firebase V1 migration.
   *
   * @var string
   */
  public $delegatedProjectNumber;
  /**
   * The max number of results to return in the response.
   *
   * @var string
   */
  public $maxResults;
  /**
   * The token for the next page. This should be taken from the previous
   * response.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Specify which project (field value is actually project id) to operate. Only
   * used when provided credential.
   *
   * @var string
   */
  public $targetProjectId;

  /**
   * GCP project number of the requesting delegated app. Currently only intended
   * for Firebase V1 migration.
   *
   * @param string $delegatedProjectNumber
   */
  public function setDelegatedProjectNumber($delegatedProjectNumber)
  {
    $this->delegatedProjectNumber = $delegatedProjectNumber;
  }
  /**
   * @return string
   */
  public function getDelegatedProjectNumber()
  {
    return $this->delegatedProjectNumber;
  }
  /**
   * The max number of results to return in the response.
   *
   * @param string $maxResults
   */
  public function setMaxResults($maxResults)
  {
    $this->maxResults = $maxResults;
  }
  /**
   * @return string
   */
  public function getMaxResults()
  {
    return $this->maxResults;
  }
  /**
   * The token for the next page. This should be taken from the previous
   * response.
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
   * Specify which project (field value is actually project id) to operate. Only
   * used when provided credential.
   *
   * @param string $targetProjectId
   */
  public function setTargetProjectId($targetProjectId)
  {
    $this->targetProjectId = $targetProjectId;
  }
  /**
   * @return string
   */
  public function getTargetProjectId()
  {
    return $this->targetProjectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentitytoolkitRelyingpartyDownloadAccountRequest::class, 'Google_Service_IdentityToolkit_IdentitytoolkitRelyingpartyDownloadAccountRequest');
