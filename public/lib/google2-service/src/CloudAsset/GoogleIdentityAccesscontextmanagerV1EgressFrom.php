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

class GoogleIdentityAccesscontextmanagerV1EgressFrom extends \Google\Collection
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
  /**
   * Enforcement preference unspecified, will not enforce traffic restrictions
   * based on `sources` in EgressFrom.
   */
  public const SOURCE_RESTRICTION_SOURCE_RESTRICTION_UNSPECIFIED = 'SOURCE_RESTRICTION_UNSPECIFIED';
  /**
   * Enforcement preference enabled, traffic restrictions will be enforced based
   * on `sources` in EgressFrom.
   */
  public const SOURCE_RESTRICTION_SOURCE_RESTRICTION_ENABLED = 'SOURCE_RESTRICTION_ENABLED';
  /**
   * Enforcement preference disabled, will not enforce traffic restrictions
   * based on `sources` in EgressFrom.
   */
  public const SOURCE_RESTRICTION_SOURCE_RESTRICTION_DISABLED = 'SOURCE_RESTRICTION_DISABLED';
  protected $collection_key = 'sources';
  /**
   * A list of identities that are allowed access through [EgressPolicy].
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
   * Specifies the type of identities that are allowed access to outside the
   * perimeter. If left unspecified, then members of `identities` field will be
   * allowed access.
   *
   * @var string
   */
  public $identityType;
  /**
   * Whether to enforce traffic restrictions based on `sources` field. If the
   * `sources` fields is non-empty, then this field must be set to
   * `SOURCE_RESTRICTION_ENABLED`.
   *
   * @var string
   */
  public $sourceRestriction;
  protected $sourcesType = GoogleIdentityAccesscontextmanagerV1EgressSource::class;
  protected $sourcesDataType = 'array';

  /**
   * A list of identities that are allowed access through [EgressPolicy].
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
   * Specifies the type of identities that are allowed access to outside the
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
   * Whether to enforce traffic restrictions based on `sources` field. If the
   * `sources` fields is non-empty, then this field must be set to
   * `SOURCE_RESTRICTION_ENABLED`.
   *
   * Accepted values: SOURCE_RESTRICTION_UNSPECIFIED,
   * SOURCE_RESTRICTION_ENABLED, SOURCE_RESTRICTION_DISABLED
   *
   * @param self::SOURCE_RESTRICTION_* $sourceRestriction
   */
  public function setSourceRestriction($sourceRestriction)
  {
    $this->sourceRestriction = $sourceRestriction;
  }
  /**
   * @return self::SOURCE_RESTRICTION_*
   */
  public function getSourceRestriction()
  {
    return $this->sourceRestriction;
  }
  /**
   * Sources that this EgressPolicy authorizes access from. If this field is not
   * empty, then `source_restriction` must be set to
   * `SOURCE_RESTRICTION_ENABLED`.
   *
   * @param GoogleIdentityAccesscontextmanagerV1EgressSource[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return GoogleIdentityAccesscontextmanagerV1EgressSource[]
   */
  public function getSources()
  {
    return $this->sources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIdentityAccesscontextmanagerV1EgressFrom::class, 'Google_Service_CloudAsset_GoogleIdentityAccesscontextmanagerV1EgressFrom');
