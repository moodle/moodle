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

namespace Google\Service\GKEHub;

class ConfigManagementConfigSync extends \Google\Collection
{
  protected $collection_key = 'deploymentOverrides';
  protected $deploymentOverridesType = ConfigManagementDeploymentOverride::class;
  protected $deploymentOverridesDataType = 'array';
  /**
   * Optional. Enables the installation of ConfigSync. If set to true,
   * ConfigSync resources will be created and the other ConfigSync fields will
   * be applied if exist. If set to false, all other ConfigSync fields will be
   * ignored, ConfigSync resources will be deleted. If omitted, ConfigSync
   * resources will be managed depends on the presence of the git or oci field.
   *
   * @var bool
   */
  public $enabled;
  protected $gitType = ConfigManagementGitConfig::class;
  protected $gitDataType = '';
  /**
   * Optional. The Email of the Google Cloud Service Account (GSA) used for
   * exporting Config Sync metrics to Cloud Monitoring and Cloud Monarch when
   * Workload Identity is enabled. The GSA should have the Monitoring Metric
   * Writer (roles/monitoring.metricWriter) IAM role. The Kubernetes
   * ServiceAccount `default` in the namespace `config-management-monitoring`
   * should be bound to the GSA. Deprecated: If Workload Identity Federation for
   * GKE is enabled, Google Cloud Service Account is no longer needed for
   * exporting Config Sync metrics: https://cloud.google.com/kubernetes-
   * engine/enterprise/config-sync/docs/how-to/monitor-config-sync-cloud-
   * monitoring#custom-monitoring.
   *
   * @deprecated
   * @var string
   */
  public $metricsGcpServiceAccountEmail;
  protected $ociType = ConfigManagementOciConfig::class;
  protected $ociDataType = '';
  /**
   * Optional. Set to true to enable the Config Sync admission webhook to
   * prevent drifts. If set to `false`, disables the Config Sync admission
   * webhook and does not prevent drifts.
   *
   * @var bool
   */
  public $preventDrift;
  /**
   * Optional. Specifies whether the Config Sync Repo is in "hierarchical" or
   * "unstructured" mode.
   *
   * @var string
   */
  public $sourceFormat;
  /**
   * Optional. Set to true to stop syncing configs for a single cluster. Default
   * to false.
   *
   * @var bool
   */
  public $stopSyncing;

  /**
   * Optional. Configuration for deployment overrides.
   *
   * @param ConfigManagementDeploymentOverride[] $deploymentOverrides
   */
  public function setDeploymentOverrides($deploymentOverrides)
  {
    $this->deploymentOverrides = $deploymentOverrides;
  }
  /**
   * @return ConfigManagementDeploymentOverride[]
   */
  public function getDeploymentOverrides()
  {
    return $this->deploymentOverrides;
  }
  /**
   * Optional. Enables the installation of ConfigSync. If set to true,
   * ConfigSync resources will be created and the other ConfigSync fields will
   * be applied if exist. If set to false, all other ConfigSync fields will be
   * ignored, ConfigSync resources will be deleted. If omitted, ConfigSync
   * resources will be managed depends on the presence of the git or oci field.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Optional. Git repo configuration for the cluster.
   *
   * @param ConfigManagementGitConfig $git
   */
  public function setGit(ConfigManagementGitConfig $git)
  {
    $this->git = $git;
  }
  /**
   * @return ConfigManagementGitConfig
   */
  public function getGit()
  {
    return $this->git;
  }
  /**
   * Optional. The Email of the Google Cloud Service Account (GSA) used for
   * exporting Config Sync metrics to Cloud Monitoring and Cloud Monarch when
   * Workload Identity is enabled. The GSA should have the Monitoring Metric
   * Writer (roles/monitoring.metricWriter) IAM role. The Kubernetes
   * ServiceAccount `default` in the namespace `config-management-monitoring`
   * should be bound to the GSA. Deprecated: If Workload Identity Federation for
   * GKE is enabled, Google Cloud Service Account is no longer needed for
   * exporting Config Sync metrics: https://cloud.google.com/kubernetes-
   * engine/enterprise/config-sync/docs/how-to/monitor-config-sync-cloud-
   * monitoring#custom-monitoring.
   *
   * @deprecated
   * @param string $metricsGcpServiceAccountEmail
   */
  public function setMetricsGcpServiceAccountEmail($metricsGcpServiceAccountEmail)
  {
    $this->metricsGcpServiceAccountEmail = $metricsGcpServiceAccountEmail;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getMetricsGcpServiceAccountEmail()
  {
    return $this->metricsGcpServiceAccountEmail;
  }
  /**
   * Optional. OCI repo configuration for the cluster.
   *
   * @param ConfigManagementOciConfig $oci
   */
  public function setOci(ConfigManagementOciConfig $oci)
  {
    $this->oci = $oci;
  }
  /**
   * @return ConfigManagementOciConfig
   */
  public function getOci()
  {
    return $this->oci;
  }
  /**
   * Optional. Set to true to enable the Config Sync admission webhook to
   * prevent drifts. If set to `false`, disables the Config Sync admission
   * webhook and does not prevent drifts.
   *
   * @param bool $preventDrift
   */
  public function setPreventDrift($preventDrift)
  {
    $this->preventDrift = $preventDrift;
  }
  /**
   * @return bool
   */
  public function getPreventDrift()
  {
    return $this->preventDrift;
  }
  /**
   * Optional. Specifies whether the Config Sync Repo is in "hierarchical" or
   * "unstructured" mode.
   *
   * @param string $sourceFormat
   */
  public function setSourceFormat($sourceFormat)
  {
    $this->sourceFormat = $sourceFormat;
  }
  /**
   * @return string
   */
  public function getSourceFormat()
  {
    return $this->sourceFormat;
  }
  /**
   * Optional. Set to true to stop syncing configs for a single cluster. Default
   * to false.
   *
   * @param bool $stopSyncing
   */
  public function setStopSyncing($stopSyncing)
  {
    $this->stopSyncing = $stopSyncing;
  }
  /**
   * @return bool
   */
  public function getStopSyncing()
  {
    return $this->stopSyncing;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementConfigSync::class, 'Google_Service_GKEHub_ConfigManagementConfigSync');
