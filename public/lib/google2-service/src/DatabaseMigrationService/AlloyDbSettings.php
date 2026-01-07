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

namespace Google\Service\DatabaseMigrationService;

class AlloyDbSettings extends \Google\Model
{
  /**
   * This is an unknown database version.
   */
  public const DATABASE_VERSION_DATABASE_VERSION_UNSPECIFIED = 'DATABASE_VERSION_UNSPECIFIED';
  /**
   * The database version is Postgres 14.
   */
  public const DATABASE_VERSION_POSTGRES_14 = 'POSTGRES_14';
  /**
   * The database version is Postgres 15.
   */
  public const DATABASE_VERSION_POSTGRES_15 = 'POSTGRES_15';
  /**
   * The database version is Postgres 16.
   */
  public const DATABASE_VERSION_POSTGRES_16 = 'POSTGRES_16';
  /**
   * The database version is Postgres 17.
   */
  public const DATABASE_VERSION_POSTGRES_17 = 'POSTGRES_17';
  /**
   * The database version is Postgres 18.
   */
  public const DATABASE_VERSION_POSTGRES_18 = 'POSTGRES_18';
  /**
   * Optional. The database engine major version. This is an optional field. If
   * a database version is not supplied at cluster creation time, then a default
   * database version will be used.
   *
   * @var string
   */
  public $databaseVersion;
  protected $encryptionConfigType = EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  protected $initialUserType = UserPassword::class;
  protected $initialUserDataType = '';
  /**
   * Labels for the AlloyDB cluster created by DMS. An object containing a list
   * of 'key', 'value' pairs.
   *
   * @var string[]
   */
  public $labels;
  protected $primaryInstanceSettingsType = PrimaryInstanceSettings::class;
  protected $primaryInstanceSettingsDataType = '';
  /**
   * Required. The resource link for the VPC network in which cluster resources
   * are created and from which they are accessible via Private IP. The network
   * must belong to the same project as the cluster. It is specified in the
   * form: "projects/{project_number}/global/networks/{network_id}". This is
   * required to create a cluster.
   *
   * @var string
   */
  public $vpcNetwork;

  /**
   * Optional. The database engine major version. This is an optional field. If
   * a database version is not supplied at cluster creation time, then a default
   * database version will be used.
   *
   * Accepted values: DATABASE_VERSION_UNSPECIFIED, POSTGRES_14, POSTGRES_15,
   * POSTGRES_16, POSTGRES_17, POSTGRES_18
   *
   * @param self::DATABASE_VERSION_* $databaseVersion
   */
  public function setDatabaseVersion($databaseVersion)
  {
    $this->databaseVersion = $databaseVersion;
  }
  /**
   * @return self::DATABASE_VERSION_*
   */
  public function getDatabaseVersion()
  {
    return $this->databaseVersion;
  }
  /**
   * Optional. The encryption config can be specified to encrypt the data disks
   * and other persistent data resources of a cluster with a customer-managed
   * encryption key (CMEK). When this field is not specified, the cluster will
   * then use default encryption scheme to protect the user data.
   *
   * @param EncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(EncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return EncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * Required. Input only. Initial user to setup during cluster creation.
   * Required.
   *
   * @param UserPassword $initialUser
   */
  public function setInitialUser(UserPassword $initialUser)
  {
    $this->initialUser = $initialUser;
  }
  /**
   * @return UserPassword
   */
  public function getInitialUser()
  {
    return $this->initialUser;
  }
  /**
   * Labels for the AlloyDB cluster created by DMS. An object containing a list
   * of 'key', 'value' pairs.
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
   * Settings for the cluster's primary instance
   *
   * @param PrimaryInstanceSettings $primaryInstanceSettings
   */
  public function setPrimaryInstanceSettings(PrimaryInstanceSettings $primaryInstanceSettings)
  {
    $this->primaryInstanceSettings = $primaryInstanceSettings;
  }
  /**
   * @return PrimaryInstanceSettings
   */
  public function getPrimaryInstanceSettings()
  {
    return $this->primaryInstanceSettings;
  }
  /**
   * Required. The resource link for the VPC network in which cluster resources
   * are created and from which they are accessible via Private IP. The network
   * must belong to the same project as the cluster. It is specified in the
   * form: "projects/{project_number}/global/networks/{network_id}". This is
   * required to create a cluster.
   *
   * @param string $vpcNetwork
   */
  public function setVpcNetwork($vpcNetwork)
  {
    $this->vpcNetwork = $vpcNetwork;
  }
  /**
   * @return string
   */
  public function getVpcNetwork()
  {
    return $this->vpcNetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AlloyDbSettings::class, 'Google_Service_DatabaseMigrationService_AlloyDbSettings');
