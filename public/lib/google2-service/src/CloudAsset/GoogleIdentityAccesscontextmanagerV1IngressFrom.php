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

namespace Google\Service\CloudAsset;

class GoogleIdentityAccesscontextmanagerV1IngressFrom extends \Google\Collection
{
  /**
   * No blanket identity group specified.
   */
  public const IDENTITY_TYPE_IDENTITY_TYPE_UNSPECIFIED = 'IDENTITY_TYPE_UNSPECIFIED';
  /**
   * Authorize access from all identities outside the perimeter.
   */
  public const IDENTITY_TYPE_ANY_IDENTITY = 'ANY_IDENTITY';
  /**
   * Authorize access from all human users outside the perimeter.
   */
  public const IDENTITY_TYPE_ANY_USER_ACCOUNT = 'ANY_USER_ACCOUNT';
  /**
   * Authorize access from all service accounts outside the perimeter.
   */
  public const IDENTITY_TYPE_ANY_SERVICE_ACCOUNT = 'ANY_SERVICE_ACCOUNT';
  protected $collection_key = 'sources';
  /**
   * A list of identities that are allowed access through [IngressPolicy].
   * Identities can be an individual user, service account, Google group, or
   * third-party identity. For third-party identity, only single identities are
   * supported and other identity types are not supported. The `v1` identities
   * that have the prefix `user`, `group`, `serviceAccount`, and `principal` in
   * https://cloud.google.com/iam/docs/principal-identifiers#v1 are supported.
   *
   * @var string[]
   */
  public $identities;
  /**
   * Specifies the type of identities that are allowed access from outside the
   * perimeter. If left unspecified, then members of `identities` field will be
   * allowed access.
   *
   * @var string
   */
  public $identityType;
  protected $sourcesType = GoogleIdentityAccesscontextmanagerV1IngressSource::class;
  protected $sourcesDataType = 'array';

  /**
   * A list of identities that are allowed access through [IngressPolicy].
   * Identities can be an individual user, service account, Google group, or
   * third-party identity. For third-party identity, only single identities are
   * supported and other identity types are not supported. The `v1` identities
   * that have the prefix `user`, `group`, `serviceAccount`, and `principal` in
   * https://cloud.google.com/iam/docs/principal-identifiers#v1 are supported.
   *
   * @param string[] $identities
   */
  public function setIdentities($identities)
  {
    $this->identities = $identities;
  }
  /**
   * @return string[]
   */
  public function getIdentities()
  {
    return $this->identities;
  }
  /**
   * Specifies the type of identities that are allowed access from outside the
   * perimeter. If left unspecified, then members of `identities` field will be
   * allowed access.
   *
   * Accepted values: IDENTITY_TYPE_UNSPECIFIED, ANY_IDENTITY, ANY_USER_ACCOUNT,
   * ANY_SERVICE_ACCOUNT
   *
   * @param self::IDENTITY_TYPE_* $identityType
   */
  public function setIdentityType($identityType)
  {
    $this->identityType = $identityType;
  }
  /**
   * @return self::IDENTITY_TYPE_*
   */
  public function getIdentityType()
  {
    return $this->identityType;
  }
  /**
   * Sources that this IngressPolicy authorizes access from.
   *
   * @param GoogleIdentityAccesscontextmanagerV1IngressSource[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return GoogleIdentityAccesscontextmanagerV1IngressSource[]
   */
  public function getSources()
  {
    return $this->sources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIdentityAccesscontextmanagerV1IngressFrom::class, 'Google_Service_CloudAsset_GoogleIdentityAccesscontextmanagerV1IngressFrom');
