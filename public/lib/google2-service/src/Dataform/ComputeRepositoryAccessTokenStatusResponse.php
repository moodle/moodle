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

namespace Google\Service\Dataform;

class ComputeRepositoryAccessTokenStatusResponse extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const TOKEN_STATUS_TOKEN_STATUS_UNSPECIFIED = 'TOKEN_STATUS_UNSPECIFIED';
  /**
   * The token could not be found in Secret Manager (or the Dataform Service
   * Account did not have permission to access it).
   */
  public const TOKEN_STATUS_NOT_FOUND = 'NOT_FOUND';
  /**
   * The token could not be used to authenticate against the Git remote.
   */
  public const TOKEN_STATUS_INVALID = 'INVALID';
  /**
   * The token was used successfully to authenticate against the Git remote.
   */
  public const TOKEN_STATUS_VALID = 'VALID';
  /**
   * Indicates the status of the Git access token.
   *
   * @var string
   */
  public $tokenStatus;

  /**
   * Indicates the status of the Git access token.
   *
   * Accepted values: TOKEN_STATUS_UNSPECIFIED, NOT_FOUND, INVALID, VALID
   *
   * @param self::TOKEN_STATUS_* $tokenStatus
   */
  public function setTokenStatus($tokenStatus)
  {
    $this->tokenStatus = $tokenStatus;
  }
  /**
   * @return self::TOKEN_STATUS_*
   */
  public function getTokenStatus()
  {
    return $this->tokenStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputeRepositoryAccessTokenStatusResponse::class, 'Google_Service_Dataform_ComputeRepositoryAccessTokenStatusResponse');
