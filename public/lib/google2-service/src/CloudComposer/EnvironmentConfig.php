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

namespace Google\Service\CloudComposer;

class EnvironmentConfig extends \Google\Model
{
  /**
   * The size of the environment is unspecified.
   */
  public const ENVIRONMENT_SIZE_ENVIRONMENT_SIZE_UNSPECIFIED = 'ENVIRONMENT_SIZE_UNSPECIFIED';
  /**
   * The environment size is small.
   */
  public const ENVIRONMENT_SIZE_ENVIRONMENT_SIZE_SMALL = 'ENVIRONMENT_SIZE_SMALL';
  /**
   * The environment size is medium.
   */
  public const ENVIRONMENT_SIZE_ENVIRONMENT_SIZE_MEDIUM = 'ENVIRONMENT_SIZE_MEDIUM';
  /**
   * The environment size is large.
   */
  public const ENVIRONMENT_SIZE_ENVIRONMENT_SIZE_LARGE = 'ENVIRONMENT_SIZE_LARGE';
  /**
   * The environment size is extra large.
   */
  public const ENVIRONMENT_SIZE_ENVIRONMENT_SIZE_EXTRA_LARGE = 'ENVIRONMENT_SIZE_EXTRA_LARGE';
  /**
   * Default mode doesn't change environment parameters.
   */
  public const RESILIENCE_MODE_RESILIENCE_MODE_UNSPECIFIED = 'RESILIENCE_MODE_UNSPECIFIED';
  /**
   * Enabled High Resilience mode, including Cloud SQL HA.
   */
  public const RESILIENCE_MODE_HIGH_RESILIENCE = 'HIGH_RESILIENCE';
  /**
   * Output only. The 'bring your own identity' variant of the URI of the Apache
   * Airflow Web UI hosted within this environment, to be accessed with external
   * identities using workforce identity federation (see [Access environments
   * with workforce identity federation](/composer/docs/composer-2/access-
   * environments-with-workforce-identity-federation)).
   *
   * @var string
   */
  public $airflowByoidUri;
  /**
   * Output only. The URI of the Apache Airflow Web UI hosted within this
   * environment (see [Airflow web interface](/composer/docs/how-
   * to/accessing/airflow-web-interface)).
   *
   * @var string
   */
  public $airflowUri;
  /**
   * Output only. The Cloud Storage prefix of the DAGs for this environment.
   * Although Cloud Storage objects reside in a flat namespace, a hierarchical
   * file tree can be simulated using "/"-delimited object name prefixes. DAG
   * objects for this environment reside in a simulated directory with the given
   * prefix.
   *
   * @var string
   */
  public $dagGcsPrefix;
  protected $dataRetentionConfigType = DataRetentionConfig::class;
  protected $dataRetentionConfigDataType = '';
  protected $databaseConfigType = DatabaseConfig::class;
  protected $databaseConfigDataType = '';
  protected $encryptionConfigType = EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  /**
   * Optional. The size of the Cloud Composer environment. This field is
   * supported for Cloud Composer environments in versions
   * composer-2.*.*-airflow-*.*.* and newer.
   *
   * @var string
   */
  public $environmentSize;
  /**
   * Output only. The Kubernetes Engine cluster used to run this environment.
   *
   * @var string
   */
  public $gkeCluster;
  protected $maintenanceWindowType = MaintenanceWindow::class;
  protected $maintenanceWindowDataType = '';
  protected $masterAuthorizedNetworksConfigType = MasterAuthorizedNetworksConfig::class;
  protected $masterAuthorizedNetworksConfigDataType = '';
  protected $nodeConfigType = NodeConfig::class;
  protected $nodeConfigDataType = '';
  /**
   * The number of nodes in the Kubernetes Engine cluster that will be used to
   * run this environment. This field is supported for Cloud Composer
   * environments in versions composer-1.*.*-airflow-*.*.*.
   *
   * @var int
   */
  public $nodeCount;
  protected $privateEnvironmentConfigType = PrivateEnvironmentConfig::class;
  protected $privateEnvironmentConfigDataType = '';
  protected $recoveryConfigType = RecoveryConfig::class;
  protected $recoveryConfigDataType = '';
  /**
   * Optional. Resilience mode of the Cloud Composer Environment. This field is
   * supported for Cloud Composer environments in versions
   * composer-2.2.0-airflow-*.*.* and newer.
   *
   * @var string
   */
  public $resilienceMode;
  protected $softwareConfigType = SoftwareConfig::class;
  protected $softwareConfigDataType = '';
  protected $webServerConfigType = WebServerConfig::class;
  protected $webServerConfigDataType = '';
  protected $webServerNetworkAccessControlType = WebServerNetworkAccessControl::class;
  protected $webServerNetworkAccessControlDataType = '';
  protected $workloadsConfigType = WorkloadsConfig::class;
  protected $workloadsConfigDataType = '';

  /**
   * Output only. The 'bring your own identity' variant of the URI of the Apache
   * Airflow Web UI hosted within this environment, to be accessed with external
   * identities using workforce identity federation (see [Access environments
   * with workforce identity federation](/composer/docs/composer-2/access-
   * environments-with-workforce-identity-federation)).
   *
   * @param string $airflowByoidUri
   */
  public function setAirflowByoidUri($airflowByoidUri)
  {
    $this->airflowByoidUri = $airflowByoidUri;
  }
  /**
   * @return string
   */
  public function getAirflowByoidUri()
  {
    return $this->airflowByoidUri;
  }
  /**
   * Output only. The URI of the Apache Airflow Web UI hosted within this
   * environment (see [Airflow web interface](/composer/docs/how-
   * to/accessing/airflow-web-interface)).
   *
   * @param string $airflowUri
   */
  public function setAirflowUri($airflowUri)
  {
    $this->airflowUri = $airflowUri;
  }
  /**
   * @return string
   */
  public function getAirflowUri()
  {
    return $this->airflowUri;
  }
  /**
   * Output only. The Cloud Storage prefix of the DAGs for this environment.
   * Although Cloud Storage objects reside in a flat namespace, a hierarchical
   * file tree can be simulated using "/"-delimited object name prefixes. DAG
   * objects for this environment reside in a simulated directory with the given
   * prefix.
   *
   * @param string $dagGcsPrefix
   */
  public function setDagGcsPrefix($dagGcsPrefix)
  {
    $this->dagGcsPrefix = $dagGcsPrefix;
  }
  /**
   * @return string
   */
  public function getDagGcsPrefix()
  {
    return $this->dagGcsPrefix;
  }
  /**
   * Optional. The configuration setting for Airflow database data retention
   * mechanism.
   *
   * @param DataRetentionConfig $dataRetentionConfig
   */
  public function setDataRetentionConfig(DataRetentionConfig $dataRetentionConfig)
  {
    $this->dataRetentionConfig = $dataRetentionConfig;
  }
  /**
   * @return DataRetentionConfig
   */
  public function getDataRetentionConfig()
  {
    return $this->dataRetentionConfig;
  }
  /**
   * Optional. The configuration settings for Cloud SQL instance used internally
   * by Apache Airflow software.
   *
   * @param DatabaseConfig $databaseConfig
   */
  public function setDatabaseConfig(DatabaseConfig $databaseConfig)
  {
    $this->databaseConfig = $databaseConfig;
  }
  /**
   * @return DatabaseConfig
   */
  public function getDatabaseConfig()
  {
    return $this->databaseConfig;
  }
  /**
   * Optional. The encryption options for the Cloud Composer environment and its
   * dependencies. Cannot be updated.
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
   * Optional. The size of the Cloud Composer environment. This field is
   * supported for Cloud Composer environments in versions
   * composer-2.*.*-airflow-*.*.* and newer.
   *
   * Accepted values: ENVIRONMENT_SIZE_UNSPECIFIED, ENVIRONMENT_SIZE_SMALL,
   * ENVIRONMENT_SIZE_MEDIUM, ENVIRONMENT_SIZE_LARGE,
   * ENVIRONMENT_SIZE_EXTRA_LARGE
   *
   * @param self::ENVIRONMENT_SIZE_* $environmentSize
   */
  public function setEnvironmentSize($environmentSize)
  {
    $this->environmentSize = $environmentSize;
  }
  /**
   * @return self::ENVIRONMENT_SIZE_*
   */
  public function getEnvironmentSize()
  {
    return $this->environmentSize;
  }
  /**
   * Output only. The Kubernetes Engine cluster used to run this environment.
   *
   * @param string $gkeCluster
   */
  public function setGkeCluster($gkeCluster)
  {
    $this->gkeCluster = $gkeCluster;
  }
  /**
   * @return string
   */
  public function getGkeCluster()
  {
    return $this->gkeCluster;
  }
  /**
   * Optional. The maintenance window is the period when Cloud Composer
   * components may undergo maintenance. It is defined so that maintenance is
   * not executed during peak hours or critical time periods. The system will
   * not be under maintenance for every occurrence of this window, but when
   * maintenance is planned, it will be scheduled during the window. The
   * maintenance window period must encompass at least 12 hours per week. This
   * may be split into multiple chunks, each with a size of at least 4 hours. If
   * this value is omitted, the default value for maintenance window is applied.
   * By default, maintenance windows are from 00:00:00 to 04:00:00 (GMT) on
   * Friday, Saturday, and Sunday every week.
   *
   * @param MaintenanceWindow $maintenanceWindow
   */
  public function setMaintenanceWindow(MaintenanceWindow $maintenanceWindow)
  {
    $this->maintenanceWindow = $maintenanceWindow;
  }
  /**
   * @return MaintenanceWindow
   */
  public function getMaintenanceWindow()
  {
    return $this->maintenanceWindow;
  }
  /**
   * Optional. The configuration options for GKE cluster master authorized
   * networks. By default master authorized networks feature is: - in case of
   * private environment: enabled with no external networks allowlisted. - in
   * case of public environment: disabled.
   *
   * @param MasterAuthorizedNetworksConfig $masterAuthorizedNetworksConfig
   */
  public function setMasterAuthorizedNetworksConfig(MasterAuthorizedNetworksConfig $masterAuthorizedNetworksConfig)
  {
    $this->masterAuthorizedNetworksConfig = $masterAuthorizedNetworksConfig;
  }
  /**
   * @return MasterAuthorizedNetworksConfig
   */
  public function getMasterAuthorizedNetworksConfig()
  {
    return $this->masterAuthorizedNetworksConfig;
  }
  /**
   * Optional. The configuration used for the Kubernetes Engine cluster.
   *
   * @param NodeConfig $nodeConfig
   */
  public function setNodeConfig(NodeConfig $nodeConfig)
  {
    $this->nodeConfig = $nodeConfig;
  }
  /**
   * @return NodeConfig
   */
  public function getNodeConfig()
  {
    return $this->nodeConfig;
  }
  /**
   * The number of nodes in the Kubernetes Engine cluster that will be used to
   * run this environment. This field is supported for Cloud Composer
   * environments in versions composer-1.*.*-airflow-*.*.*.
   *
   * @param int $nodeCount
   */
  public function setNodeCount($nodeCount)
  {
    $this->nodeCount = $nodeCount;
  }
  /**
   * @return int
   */
  public function getNodeCount()
  {
    return $this->nodeCount;
  }
  /**
   * Optional. The configuration used for the Private IP Cloud Composer
   * environment.
   *
   * @param PrivateEnvironmentConfig $privateEnvironmentConfig
   */
  public function setPrivateEnvironmentConfig(PrivateEnvironmentConfig $privateEnvironmentConfig)
  {
    $this->privateEnvironmentConfig = $privateEnvironmentConfig;
  }
  /**
   * @return PrivateEnvironmentConfig
   */
  public function getPrivateEnvironmentConfig()
  {
    return $this->privateEnvironmentConfig;
  }
  /**
   * Optional. The Recovery settings configuration of an environment. This field
   * is supported for Cloud Composer environments in versions
   * composer-2.*.*-airflow-*.*.* and newer.
   *
   * @param RecoveryConfig $recoveryConfig
   */
  public function setRecoveryConfig(RecoveryConfig $recoveryConfig)
  {
    $this->recoveryConfig = $recoveryConfig;
  }
  /**
   * @return RecoveryConfig
   */
  public function getRecoveryConfig()
  {
    return $this->recoveryConfig;
  }
  /**
   * Optional. Resilience mode of the Cloud Composer Environment. This field is
   * supported for Cloud Composer environments in versions
   * composer-2.2.0-airflow-*.*.* and newer.
   *
   * Accepted values: RESILIENCE_MODE_UNSPECIFIED, HIGH_RESILIENCE
   *
   * @param self::RESILIENCE_MODE_* $resilienceMode
   */
  public function setResilienceMode($resilienceMode)
  {
    $this->resilienceMode = $resilienceMode;
  }
  /**
   * @return self::RESILIENCE_MODE_*
   */
  public function getResilienceMode()
  {
    return $this->resilienceMode;
  }
  /**
   * Optional. The configuration settings for software inside the environment.
   *
   * @param SoftwareConfig $softwareConfig
   */
  public function setSoftwareConfig(SoftwareConfig $softwareConfig)
  {
    $this->softwareConfig = $softwareConfig;
  }
  /**
   * @return SoftwareConfig
   */
  public function getSoftwareConfig()
  {
    return $this->softwareConfig;
  }
  /**
   * Optional. The configuration settings for the Airflow web server App Engine
   * instance.
   *
   * @param WebServerConfig $webServerConfig
   */
  public function setWebServerConfig(WebServerConfig $webServerConfig)
  {
    $this->webServerConfig = $webServerConfig;
  }
  /**
   * @return WebServerConfig
   */
  public function getWebServerConfig()
  {
    return $this->webServerConfig;
  }
  /**
   * Optional. The network-level access control policy for the Airflow web
   * server. If unspecified, no network-level access restrictions will be
   * applied.
   *
   * @param WebServerNetworkAccessControl $webServerNetworkAccessControl
   */
  public function setWebServerNetworkAccessControl(WebServerNetworkAccessControl $webServerNetworkAccessControl)
  {
    $this->webServerNetworkAccessControl = $webServerNetworkAccessControl;
  }
  /**
   * @return WebServerNetworkAccessControl
   */
  public function getWebServerNetworkAccessControl()
  {
    return $this->webServerNetworkAccessControl;
  }
  /**
   * Optional. The workloads configuration settings for the GKE cluster
   * associated with the Cloud Composer environment. The GKE cluster runs
   * Airflow scheduler, web server and workers workloads. This field is
   * supported for Cloud Composer environments in versions
   * composer-2.*.*-airflow-*.*.* and newer.
   *
   * @param WorkloadsConfig $workloadsConfig
   */
  public function setWorkloadsConfig(WorkloadsConfig $workloadsConfig)
  {
    $this->workloadsConfig = $workloadsConfig;
  }
  /**
   * @return WorkloadsConfig
   */
  public function getWorkloadsConfig()
  {
    return $this->workloadsConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnvironmentConfig::class, 'Google_Service_CloudComposer_EnvironmentConfig');
