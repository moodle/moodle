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

namespace Google\Service\Backupdr;

class ManagementServer extends \Google\Collection
{
  /**
   * State not set.
   */
  public const STATE_INSTANCE_STATE_UNSPECIFIED = 'INSTANCE_STATE_UNSPECIFIED';
  /**
   * The instance is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The instance has been created and is fully usable.
   */
  public const STATE_READY = 'READY';
  /**
   * The instance configuration is being updated. Certain kinds of updates may
   * cause the instance to become unusable while the update is in progress.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The instance is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The instance is being repaired and may be unstable.
   */
  public const STATE_REPAIRING = 'REPAIRING';
  /**
   * Maintenance is being performed on this instance.
   */
  public const STATE_MAINTENANCE = 'MAINTENANCE';
  /**
   * The instance is experiencing an issue and might be unusable. You can get
   * further details from the statusMessage field of Instance resource.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Instance type is not mentioned.
   */
  public const TYPE_INSTANCE_TYPE_UNSPECIFIED = 'INSTANCE_TYPE_UNSPECIFIED';
  /**
   * Instance for backup and restore management (i.e., AGM).
   */
  public const TYPE_BACKUP_RESTORE = 'BACKUP_RESTORE';
  protected $collection_key = 'networks';
  /**
   * Output only. The hostname or ip address of the exposed AGM endpoints, used
   * by BAs to connect to BA proxy.
   *
   * @var string[]
   */
  public $baProxyUri;
  /**
   * Output only. The time when the instance was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The description of the ManagementServer instance (2048 characters
   * or less).
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Server specified ETag for the ManagementServer resource to
   * prevent simultaneous updates from overwiting each other.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Resource labels to represent user provided metadata. Labels
   * currently defined: 1. migrate_from_go= If set to true, the MS is created in
   * migration ready mode.
   *
   * @var string[]
   */
  public $labels;
  protected $managementUriType = ManagementURI::class;
  protected $managementUriDataType = '';
  /**
   * Output only. Identifier. The resource name.
   *
   * @var string
   */
  public $name;
  protected $networksType = NetworkConfig::class;
  protected $networksDataType = 'array';
  /**
   * Output only. The OAuth 2.0 client id is required to make API calls to the
   * BackupDR instance API of this ManagementServer. This is the value that
   * should be provided in the 'aud' field of the OIDC ID Token (see openid
   * specification https://openid.net/specs/openid-connect-
   * core-1_0.html#IDToken).
   *
   * @var string
   */
  public $oauth2ClientId;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. The ManagementServer state.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. The type of the ManagementServer resource.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The time when the instance was updated.
   *
   * @var string
   */
  public $updateTime;
  protected $workforceIdentityBasedManagementUriType = WorkforceIdentityBasedManagementURI::class;
  protected $workforceIdentityBasedManagementUriDataType = '';
  protected $workforceIdentityBasedOauth2ClientIdType = WorkforceIdentityBasedOAuth2ClientID::class;
  protected $workforceIdentityBasedOauth2ClientIdDataType = '';

  /**
   * Output only. The hostname or ip address of the exposed AGM endpoints, used
   * by BAs to connect to BA proxy.
   *
   * @param string[] $baProxyUri
   */
  public function setBaProxyUri($baProxyUri)
  {
    $this->baProxyUri = $baProxyUri;
  }
  /**
   * @return string[]
   */
  public function getBaProxyUri()
  {
    return $this->baProxyUri;
  }
  /**
   * Output only. The time when the instance was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. The description of the ManagementServer instance (2048 characters
   * or less).
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Server specified ETag for the ManagementServer resource to
   * prevent simultaneous updates from overwiting each other.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Resource labels to represent user provided metadata. Labels
   * currently defined: 1. migrate_from_go= If set to true, the MS is created in
   * migration ready mode.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The hostname or ip address of the exposed AGM endpoints, used
   * by clients to connect to AGM/RD graphical user interface and APIs.
   *
   * @param ManagementURI $managementUri
   */
  public function setManagementUri(ManagementURI $managementUri)
  {
    $this->managementUri = $managementUri;
  }
  /**
   * @return ManagementURI
   */
  public function getManagementUri()
  {
    return $this->managementUri;
  }
  /**
   * Output only. Identifier. The resource name.
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
   * Optional. VPC networks to which the ManagementServer instance is connected.
   * For this version, only a single network is supported. This field is
   * optional if MS is created without PSA
   *
   * @param NetworkConfig[] $networks
   */
  public function setNetworks($networks)
  {
    $this->networks = $networks;
  }
  /**
   * @return NetworkConfig[]
   */
  public function getNetworks()
  {
    return $this->networks;
  }
  /**
   * Output only. The OAuth 2.0 client id is required to make API calls to the
   * BackupDR instance API of this ManagementServer. This is the value that
   * should be provided in the 'aud' field of the OIDC ID Token (see openid
   * specification https://openid.net/specs/openid-connect-
   * core-1_0.html#IDToken).
   *
   * @param string $oauth2ClientId
   */
  public function setOauth2ClientId($oauth2ClientId)
  {
    $this->oauth2ClientId = $oauth2ClientId;
  }
  /**
   * @return string
   */
  public function getOauth2ClientId()
  {
    return $this->oauth2ClientId;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. The ManagementServer state.
   *
   * Accepted values: INSTANCE_STATE_UNSPECIFIED, CREATING, READY, UPDATING,
   * DELETING, REPAIRING, MAINTENANCE, ERROR
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. The type of the ManagementServer resource.
   *
   * Accepted values: INSTANCE_TYPE_UNSPECIFIED, BACKUP_RESTORE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. The time when the instance was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. The hostnames of the exposed AGM endpoints for both types of
   * user i.e. 1p and 3p, used to connect AGM/RM UI.
   *
   * @param WorkforceIdentityBasedManagementURI $workforceIdentityBasedManagementUri
   */
  public function setWorkforceIdentityBasedManagementUri(WorkforceIdentityBasedManagementURI $workforceIdentityBasedManagementUri)
  {
    $this->workforceIdentityBasedManagementUri = $workforceIdentityBasedManagementUri;
  }
  /**
   * @return WorkforceIdentityBasedManagementURI
   */
  public function getWorkforceIdentityBasedManagementUri()
  {
    return $this->workforceIdentityBasedManagementUri;
  }
  /**
   * Output only. The OAuth client IDs for both types of user i.e. 1p and 3p.
   *
   * @param WorkforceIdentityBasedOAuth2ClientID $workforceIdentityBasedOauth2ClientId
   */
  public function setWorkforceIdentityBasedOauth2ClientId(WorkforceIdentityBasedOAuth2ClientID $workforceIdentityBasedOauth2ClientId)
  {
    $this->workforceIdentityBasedOauth2ClientId = $workforceIdentityBasedOauth2ClientId;
  }
  /**
   * @return WorkforceIdentityBasedOAuth2ClientID
   */
  public function getWorkforceIdentityBasedOauth2ClientId()
  {
    return $this->workforceIdentityBasedOauth2ClientId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagementServer::class, 'Google_Service_Backupdr_ManagementServer');
