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

namespace Google\Service\CloudSearch;

class UnmappedIdentity extends \Google\Model
{
  /**
   * Input-only value. Used to list all unmapped identities regardless of
   * status.
   */
  public const RESOLUTION_STATUS_CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * The unmapped identity was not found in IDaaS, and needs to be provided by
   * the user.
   */
  public const RESOLUTION_STATUS_CODE_NOT_FOUND = 'NOT_FOUND';
  /**
   * The identity source associated with the identity was either not found or
   * deleted.
   */
  public const RESOLUTION_STATUS_CODE_IDENTITY_SOURCE_NOT_FOUND = 'IDENTITY_SOURCE_NOT_FOUND';
  /**
   * IDaaS does not understand the identity source, probably because the schema
   * was modified in a non compatible way.
   */
  public const RESOLUTION_STATUS_CODE_IDENTITY_SOURCE_MISCONFIGURED = 'IDENTITY_SOURCE_MISCONFIGURED';
  /**
   * The number of users associated with the external identity is too large.
   */
  public const RESOLUTION_STATUS_CODE_TOO_MANY_MAPPINGS_FOUND = 'TOO_MANY_MAPPINGS_FOUND';
  /**
   * Internal error.
   */
  public const RESOLUTION_STATUS_CODE_INTERNAL_ERROR = 'INTERNAL_ERROR';
  protected $externalIdentityType = Principal::class;
  protected $externalIdentityDataType = '';
  /**
   * The resolution status for the external identity.
   *
   * @var string
   */
  public $resolutionStatusCode;

  /**
   * The resource name for an external user.
   *
   * @param Principal $externalIdentity
   */
  public function setExternalIdentity(Principal $externalIdentity)
  {
    $this->externalIdentity = $externalIdentity;
  }
  /**
   * @return Principal
   */
  public function getExternalIdentity()
  {
    return $this->externalIdentity;
  }
  /**
   * The resolution status for the external identity.
   *
   * Accepted values: CODE_UNSPECIFIED, NOT_FOUND, IDENTITY_SOURCE_NOT_FOUND,
   * IDENTITY_SOURCE_MISCONFIGURED, TOO_MANY_MAPPINGS_FOUND, INTERNAL_ERROR
   *
   * @param self::RESOLUTION_STATUS_CODE_* $resolutionStatusCode
   */
  public function setResolutionStatusCode($resolutionStatusCode)
  {
    $this->resolutionStatusCode = $resolutionStatusCode;
  }
  /**
   * @return self::RESOLUTION_STATUS_CODE_*
   */
  public function getResolutionStatusCode()
  {
    return $this->resolutionStatusCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UnmappedIdentity::class, 'Google_Service_CloudSearch_UnmappedIdentity');
