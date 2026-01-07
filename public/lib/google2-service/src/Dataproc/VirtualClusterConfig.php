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

namespace Google\Service\Dataproc;

class VirtualClusterConfig extends \Google\Model
{
  protected $auxiliaryServicesConfigType = AuxiliaryServicesConfig::class;
  protected $auxiliaryServicesConfigDataType = '';
  protected $kubernetesClusterConfigType = KubernetesClusterConfig::class;
  protected $kubernetesClusterConfigDataType = '';
  /**
   * Optional. A Cloud Storage bucket used to stage job dependencies, config
   * files, and job driver console output. If you do not specify a staging
   * bucket, Cloud Dataproc will determine a Cloud Storage location (US, ASIA,
   * or EU) for your cluster's staging bucket according to the Compute Engine
   * zone where your cluster is deployed, and then create and manage this
   * project-level, per-location bucket (see Dataproc staging and temp buckets
   * (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/staging-bucket)). This field requires a Cloud Storage bucket name,
   * not a gs://... URI to a Cloud Storage bucket.
   *
   * @var string
   */
  public $stagingBucket;

  /**
   * Optional. Configuration of auxiliary services used by this cluster.
   *
   * @param AuxiliaryServicesConfig $auxiliaryServicesConfig
   */
  public function setAuxiliaryServicesConfig(AuxiliaryServicesConfig $auxiliaryServicesConfig)
  {
    $this->auxiliaryServicesConfig = $auxiliaryServicesConfig;
  }
  /**
   * @return AuxiliaryServicesConfig
   */
  public function getAuxiliaryServicesConfig()
  {
    return $this->auxiliaryServicesConfig;
  }
  /**
   * Required. The configuration for running the Dataproc cluster on Kubernetes.
   *
   * @param KubernetesClusterConfig $kubernetesClusterConfig
   */
  public function setKubernetesClusterConfig(KubernetesClusterConfig $kubernetesClusterConfig)
  {
    $this->kubernetesClusterConfig = $kubernetesClusterConfig;
  }
  /**
   * @return KubernetesClusterConfig
   */
  public function getKubernetesClusterConfig()
  {
    return $this->kubernetesClusterConfig;
  }
  /**
   * Optional. A Cloud Storage bucket used to stage job dependencies, config
   * files, and job driver console output. If you do not specify a staging
   * bucket, Cloud Dataproc will determine a Cloud Storage location (US, ASIA,
   * or EU) for your cluster's staging bucket according to the Compute Engine
   * zone where your cluster is deployed, and then create and manage this
   * project-level, per-location bucket (see Dataproc staging and temp buckets
   * (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/staging-bucket)). This field requires a Cloud Storage bucket name,
   * not a gs://... URI to a Cloud Storage bucket.
   *
   * @param string $stagingBucket
   */
  public function setStagingBucket($stagingBucket)
  {
    $this->stagingBucket = $stagingBucket;
  }
  /**
   * @return string
   */
  public function getStagingBucket()
  {
    return $this->stagingBucket;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VirtualClusterConfig::class, 'Google_Service_Dataproc_VirtualClusterConfig');
