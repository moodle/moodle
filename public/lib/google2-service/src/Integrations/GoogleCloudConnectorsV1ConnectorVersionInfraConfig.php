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

namespace Google\Service\Integrations;

class GoogleCloudConnectorsV1ConnectorVersionInfraConfig extends \Google\Model
{
  /**
   * Deployment model is not specified.
   */
  public const DEPLOYMENT_MODEL_DEPLOYMENT_MODEL_UNSPECIFIED = 'DEPLOYMENT_MODEL_UNSPECIFIED';
  /**
   * Default model gke mst.
   */
  public const DEPLOYMENT_MODEL_GKE_MST = 'GKE_MST';
  /**
   * Cloud run mst.
   */
  public const DEPLOYMENT_MODEL_CLOUD_RUN_MST = 'CLOUD_RUN_MST';
  /**
   * Deployment model migration state is not specified.
   */
  public const DEPLOYMENT_MODEL_MIGRATION_STATE_DEPLOYMENT_MODEL_MIGRATION_STATE_UNSPECIFIED = 'DEPLOYMENT_MODEL_MIGRATION_STATE_UNSPECIFIED';
  /**
   * Deployment model migration is in progress.
   */
  public const DEPLOYMENT_MODEL_MIGRATION_STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Deployment model migration is completed.
   */
  public const DEPLOYMENT_MODEL_MIGRATION_STATE_COMPLETED = 'COMPLETED';
  /**
   * Deployment model migration rolledback.
   */
  public const DEPLOYMENT_MODEL_MIGRATION_STATE_ROLLEDBACK = 'ROLLEDBACK';
  /**
   * Deployment model migration rollback in progress.
   */
  public const DEPLOYMENT_MODEL_MIGRATION_STATE_ROLLBACK_IN_PROGRESS = 'ROLLBACK_IN_PROGRESS';
  /**
   * TLS migration state is not specified.
   */
  public const TLS_MIGRATION_STATE_TLS_MIGRATION_STATE_UNSPECIFIED = 'TLS_MIGRATION_STATE_UNSPECIFIED';
  /**
   * TLS migration is in progress.
   */
  public const TLS_MIGRATION_STATE_TLS_MIGRATION_NOT_STARTED = 'TLS_MIGRATION_NOT_STARTED';
  /**
   * TLS migration is completed.
   */
  public const TLS_MIGRATION_STATE_TLS_MIGRATION_COMPLETED = 'TLS_MIGRATION_COMPLETED';
  /**
   * Output only. The window used for ratelimiting runtime requests to
   * connections.
   *
   * @var string
   */
  public $connectionRatelimitWindowSeconds;
  /**
   * Output only. Indicates whether connector is deployed on GKE/CloudRun
   *
   * @var string
   */
  public $deploymentModel;
  /**
   * Output only. Status of the deployment model migration.
   *
   * @var string
   */
  public $deploymentModelMigrationState;
  protected $hpaConfigType = GoogleCloudConnectorsV1HPAConfig::class;
  protected $hpaConfigDataType = '';
  /**
   * Output only. Max QPS supported for internal requests originating from
   * Connd.
   *
   * @var string
   */
  public $internalclientRatelimitThreshold;
  /**
   * Output only. Max instance request concurrency.
   *
   * @var int
   */
  public $maxInstanceRequestConcurrency;
  /**
   * Output only. Max QPS supported by the connector version before throttling
   * of requests.
   *
   * @var string
   */
  public $ratelimitThreshold;
  protected $resourceLimitsType = GoogleCloudConnectorsV1ResourceLimits::class;
  protected $resourceLimitsDataType = '';
  protected $resourceRequestsType = GoogleCloudConnectorsV1ResourceRequests::class;
  protected $resourceRequestsDataType = '';
  /**
   * Output only. The name of shared connector deployment.
   *
   * @var string
   */
  public $sharedDeployment;
  /**
   * Output only. Status of the TLS migration.
   *
   * @var string
   */
  public $tlsMigrationState;

  /**
   * Output only. The window used for ratelimiting runtime requests to
   * connections.
   *
   * @param string $connectionRatelimitWindowSeconds
   */
  public function setConnectionRatelimitWindowSeconds($connectionRatelimitWindowSeconds)
  {
    $this->connectionRatelimitWindowSeconds = $connectionRatelimitWindowSeconds;
  }
  /**
   * @return string
   */
  public function getConnectionRatelimitWindowSeconds()
  {
    return $this->connectionRatelimitWindowSeconds;
  }
  /**
   * Output only. Indicates whether connector is deployed on GKE/CloudRun
   *
   * Accepted values: DEPLOYMENT_MODEL_UNSPECIFIED, GKE_MST, CLOUD_RUN_MST
   *
   * @param self::DEPLOYMENT_MODEL_* $deploymentModel
   */
  public function setDeploymentModel($deploymentModel)
  {
    $this->deploymentModel = $deploymentModel;
  }
  /**
   * @return self::DEPLOYMENT_MODEL_*
   */
  public function getDeploymentModel()
  {
    return $this->deploymentModel;
  }
  /**
   * Output only. Status of the deployment model migration.
   *
   * Accepted values: DEPLOYMENT_MODEL_MIGRATION_STATE_UNSPECIFIED, IN_PROGRESS,
   * COMPLETED, ROLLEDBACK, ROLLBACK_IN_PROGRESS
   *
   * @param self::DEPLOYMENT_MODEL_MIGRATION_STATE_* $deploymentModelMigrationState
   */
  public function setDeploymentModelMigrationState($deploymentModelMigrationState)
  {
    $this->deploymentModelMigrationState = $deploymentModelMigrationState;
  }
  /**
   * @return self::DEPLOYMENT_MODEL_MIGRATION_STATE_*
   */
  public function getDeploymentModelMigrationState()
  {
    return $this->deploymentModelMigrationState;
  }
  /**
   * Output only. HPA autoscaling config.
   *
   * @param GoogleCloudConnectorsV1HPAConfig $hpaConfig
   */
  public function setHpaConfig(GoogleCloudConnectorsV1HPAConfig $hpaConfig)
  {
    $this->hpaConfig = $hpaConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1HPAConfig
   */
  public function getHpaConfig()
  {
    return $this->hpaConfig;
  }
  /**
   * Output only. Max QPS supported for internal requests originating from
   * Connd.
   *
   * @param string $internalclientRatelimitThreshold
   */
  public function setInternalclientRatelimitThreshold($internalclientRatelimitThreshold)
  {
    $this->internalclientRatelimitThreshold = $internalclientRatelimitThreshold;
  }
  /**
   * @return string
   */
  public function getInternalclientRatelimitThreshold()
  {
    return $this->internalclientRatelimitThreshold;
  }
  /**
   * Output only. Max instance request concurrency.
   *
   * @param int $maxInstanceRequestConcurrency
   */
  public function setMaxInstanceRequestConcurrency($maxInstanceRequestConcurrency)
  {
    $this->maxInstanceRequestConcurrency = $maxInstanceRequestConcurrency;
  }
  /**
   * @return int
   */
  public function getMaxInstanceRequestConcurrency()
  {
    return $this->maxInstanceRequestConcurrency;
  }
  /**
   * Output only. Max QPS supported by the connector version before throttling
   * of requests.
   *
   * @param string $ratelimitThreshold
   */
  public function setRatelimitThreshold($ratelimitThreshold)
  {
    $this->ratelimitThreshold = $ratelimitThreshold;
  }
  /**
   * @return string
   */
  public function getRatelimitThreshold()
  {
    return $this->ratelimitThreshold;
  }
  /**
   * Output only. System resource limits.
   *
   * @param GoogleCloudConnectorsV1ResourceLimits $resourceLimits
   */
  public function setResourceLimits(GoogleCloudConnectorsV1ResourceLimits $resourceLimits)
  {
    $this->resourceLimits = $resourceLimits;
  }
  /**
   * @return GoogleCloudConnectorsV1ResourceLimits
   */
  public function getResourceLimits()
  {
    return $this->resourceLimits;
  }
  /**
   * Output only. System resource requests.
   *
   * @param GoogleCloudConnectorsV1ResourceRequests $resourceRequests
   */
  public function setResourceRequests(GoogleCloudConnectorsV1ResourceRequests $resourceRequests)
  {
    $this->resourceRequests = $resourceRequests;
  }
  /**
   * @return GoogleCloudConnectorsV1ResourceRequests
   */
  public function getResourceRequests()
  {
    return $this->resourceRequests;
  }
  /**
   * Output only. The name of shared connector deployment.
   *
   * @param string $sharedDeployment
   */
  public function setSharedDeployment($sharedDeployment)
  {
    $this->sharedDeployment = $sharedDeployment;
  }
  /**
   * @return string
   */
  public function getSharedDeployment()
  {
    return $this->sharedDeployment;
  }
  /**
   * Output only. Status of the TLS migration.
   *
   * Accepted values: TLS_MIGRATION_STATE_UNSPECIFIED,
   * TLS_MIGRATION_NOT_STARTED, TLS_MIGRATION_COMPLETED
   *
   * @param self::TLS_MIGRATION_STATE_* $tlsMigrationState
   */
  public function setTlsMigrationState($tlsMigrationState)
  {
    $this->tlsMigrationState = $tlsMigrationState;
  }
  /**
   * @return self::TLS_MIGRATION_STATE_*
   */
  public function getTlsMigrationState()
  {
    return $this->tlsMigrationState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1ConnectorVersionInfraConfig::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1ConnectorVersionInfraConfig');
