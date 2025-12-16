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

class AuthorizedOrgsDesc extends \Google\Collection
{
  /**
   * No asset type specified.
   */
  public const ASSET_TYPE_ASSET_TYPE_UNSPECIFIED = 'ASSET_TYPE_UNSPECIFIED';
  /**
   * Device asset type.
   */
  public const ASSET_TYPE_ASSET_TYPE_DEVICE = 'ASSET_TYPE_DEVICE';
  /**
   * Credential strength asset type.
   */
  public const ASSET_TYPE_ASSET_TYPE_CREDENTIAL_STRENGTH = 'ASSET_TYPE_CREDENTIAL_STRENGTH';
  /**
   * No direction specified.
   */
  public const AUTHORIZATION_DIRECTION_AUTHORIZATION_DIRECTION_UNSPECIFIED = 'AUTHORIZATION_DIRECTION_UNSPECIFIED';
  /**
   * The specified organizations are authorized to evaluate traffic in this
   * organization.
   */
  public const AUTHORIZATION_DIRECTION_AUTHORIZATION_DIRECTION_TO = 'AUTHORIZATION_DIRECTION_TO';
  /**
   * The traffic of the specified organizations can be evaluated by this
   * organization.
   */
  public const AUTHORIZATION_DIRECTION_AUTHORIZATION_DIRECTION_FROM = 'AUTHORIZATION_DIRECTION_FROM';
  /**
   * No authorization type specified.
   */
  public const AUTHORIZATION_TYPE_AUTHORIZATION_TYPE_UNSPECIFIED = 'AUTHORIZATION_TYPE_UNSPECIFIED';
  /**
   * This authorization relationship is "trust".
   */
  public const AUTHORIZATION_TYPE_AUTHORIZATION_TYPE_TRUST = 'AUTHORIZATION_TYPE_TRUST';
  protected $collection_key = 'orgs';
  /**
   * The asset type of this authorized orgs desc. Valid values are
   * `ASSET_TYPE_DEVICE`, and `ASSET_TYPE_CREDENTIAL_STRENGTH`.
   *
   * @var string
   */
  public $assetType;
  /**
   * The direction of the authorization relationship between this organization
   * and the organizations listed in the `orgs` field. The valid values for this
   * field include the following: `AUTHORIZATION_DIRECTION_FROM`: Allows this
   * organization to evaluate traffic in the organizations listed in the `orgs`
   * field. `AUTHORIZATION_DIRECTION_TO`: Allows the organizations listed in the
   * `orgs` field to evaluate the traffic in this organization. For the
   * authorization relationship to take effect, all of the organizations must
   * authorize and specify the appropriate relationship direction. For example,
   * if organization A authorized organization B and C to evaluate its traffic,
   * by specifying `AUTHORIZATION_DIRECTION_TO` as the authorization direction,
   * organizations B and C must specify `AUTHORIZATION_DIRECTION_FROM` as the
   * authorization direction in their `AuthorizedOrgsDesc` resource.
   *
   * @var string
   */
  public $authorizationDirection;
  /**
   * A granular control type for authorization levels. Valid value is
   * `AUTHORIZATION_TYPE_TRUST`.
   *
   * @var string
   */
  public $authorizationType;
  /**
   * Identifier. Resource name for the `AuthorizedOrgsDesc`. Format: `accessPoli
   * cies/{access_policy}/authorizedOrgsDescs/{authorized_orgs_desc}`. The
   * `authorized_orgs_desc` component must begin with a letter, followed by
   * alphanumeric characters or `_`. After you create an `AuthorizedOrgsDesc`,
   * you cannot change its `name`.
   *
   * @var string
   */
  public $name;
  /**
   * The list of organization ids in this AuthorizedOrgsDesc. Format:
   * `organizations/` Example: `organizations/123456`
   *
   * @var string[]
   */
  public $orgs;

  /**
   * The asset type of this authorized orgs desc. Valid values are
   * `ASSET_TYPE_DEVICE`, and `ASSET_TYPE_CREDENTIAL_STRENGTH`.
   *
   * Accepted values: ASSET_TYPE_UNSPECIFIED, ASSET_TYPE_DEVICE,
   * ASSET_TYPE_CREDENTIAL_STRENGTH
   *
   * @param self::ASSET_TYPE_* $assetType
   */
  public function setAssetType($assetType)
  {
    $this->assetType = $assetType;
  }
  /**
   * @return self::ASSET_TYPE_*
   */
  public function getAssetType()
  {
    return $this->assetType;
  }
  /**
   * The direction of the authorization relationship between this organization
   * and the organizations listed in the `orgs` field. The valid values for this
   * field include the following: `AUTHORIZATION_DIRECTION_FROM`: Allows this
   * organization to evaluate traffic in the organizations listed in the `orgs`
   * field. `AUTHORIZATION_DIRECTION_TO`: Allows the organizations listed in the
   * `orgs` field to evaluate the traffic in this organization. For the
   * authorization relationship to take effect, all of the organizations must
   * authorize and specify the appropriate relationship direction. For example,
   * if organization A authorized organization B and C to evaluate its traffic,
   * by specifying `AUTHORIZATION_DIRECTION_TO` as the authorization direction,
   * organizations B and C must specify `AUTHORIZATION_DIRECTION_FROM` as the
   * authorization direction in their `AuthorizedOrgsDesc` resource.
   *
   * Accepted values: AUTHORIZATION_DIRECTION_UNSPECIFIED,
   * AUTHORIZATION_DIRECTION_TO, AUTHORIZATION_DIRECTION_FROM
   *
   * @param self::AUTHORIZATION_DIRECTION_* $authorizationDirection
   */
  public function setAuthorizationDirection($authorizationDirection)
  {
    $this->authorizationDirection = $authorizationDirection;
  }
  /**
   * @return self::AUTHORIZATION_DIRECTION_*
   */
  public function getAuthorizationDirection()
  {
    return $this->authorizationDirection;
  }
  /**
   * A granular control type for authorization levels. Valid value is
   * `AUTHORIZATION_TYPE_TRUST`.
   *
   * Accepted values: AUTHORIZATION_TYPE_UNSPECIFIED, AUTHORIZATION_TYPE_TRUST
   *
   * @param self::AUTHORIZATION_TYPE_* $authorizationType
   */
  public function setAuthorizationType($authorizationType)
  {
    $this->authorizationType = $authorizationType;
  }
  /**
   * @return self::AUTHORIZATION_TYPE_*
   */
  public function getAuthorizationType()
  {
    return $this->authorizationType;
  }
  /**
   * Identifier. Resource name for the `AuthorizedOrgsDesc`. Format: `accessPoli
   * cies/{access_policy}/authorizedOrgsDescs/{authorized_orgs_desc}`. The
   * `authorized_orgs_desc` component must begin with a letter, followed by
   * alphanumeric characters or `_`. After you create an `AuthorizedOrgsDesc`,
   * you cannot change its `name`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The list of organization ids in this AuthorizedOrgsDesc. Format:
   * `organizations/` Example: `organizations/123456`
   *
   * @param string[] $orgs
   */
  public function setOrgs($orgs)
  {
    $this->orgs = $orgs;
  }
  /**
   * @return string[]
   */
  public function getOrgs()
  {
    return $this->orgs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthorizedOrgsDesc::class, 'Google_Service_AccessContextManager_AuthorizedOrgsDesc');
