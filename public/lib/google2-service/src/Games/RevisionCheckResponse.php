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

namespace Google\Service\Games;

class RevisionCheckResponse extends \Google\Model
{
  /**
   * The revision being used is current.
   */
  public const REVISION_STATUS_OK = 'OK';
  /**
   * There is currently a newer version available, but the revision being used
   * still works.
   */
  public const REVISION_STATUS_DEPRECATED = 'DEPRECATED';
  /**
   * The revision being used is not supported in any released version.
   */
  public const REVISION_STATUS_INVALID = 'INVALID';
  /**
   * The version of the API this client revision should use when calling API
   * methods.
   *
   * @var string
   */
  public $apiVersion;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#revisionCheckResponse`.
   *
   * @var string
   */
  public $kind;
  /**
   * The result of the revision check.
   *
   * @var string
   */
  public $revisionStatus;

  /**
   * The version of the API this client revision should use when calling API
   * methods.
   *
   * @param string $apiVersion
   */
  public function setApiVersion($apiVersion)
  {
    $this->apiVersion = $apiVersion;
  }
  /**
   * @return string
   */
  public function getApiVersion()
  {
    return $this->apiVersion;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#revisionCheckResponse`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The result of the revision check.
   *
   * Accepted values: OK, DEPRECATED, INVALID
   *
   * @param self::REVISION_STATUS_* $revisionStatus
   */
  public function setRevisionStatus($revisionStatus)
  {
    $this->revisionStatus = $revisionStatus;
  }
  /**
   * @return self::REVISION_STATUS_*
   */
  public function getRevisionStatus()
  {
    return $this->revisionStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RevisionCheckResponse::class, 'Google_Service_Games_RevisionCheckResponse');
