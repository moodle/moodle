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

namespace Google\Service\DeveloperConnect;

class GKEWorkload extends \Google\Model
{
  /**
   * Required. Immutable. The name of the GKE cluster. Format:
   * `projects/{project}/locations/{location}/clusters/{cluster}`.
   *
   * @var string
   */
  public $cluster;
  /**
   * Output only. The name of the GKE deployment. Format: `projects/{project}/lo
   * cations/{location}/clusters/{cluster}/namespaces/{namespace}/deployments/{d
   * eployment}`.
   *
   * @var string
   */
  public $deployment;

  /**
   * Required. Immutable. The name of the GKE cluster. Format:
   * `projects/{project}/locations/{location}/clusters/{cluster}`.
   *
   * @param string $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return string
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Output only. The name of the GKE deployment. Format: `projects/{project}/lo
   * cations/{location}/clusters/{cluster}/namespaces/{namespace}/deployments/{d
   * eployment}`.
   *
   * @param string $deployment
   */
  public function setDeployment($deployment)
  {
    $this->deployment = $deployment;
  }
  /**
   * @return string
   */
  public function getDeployment()
  {
    return $this->deployment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GKEWorkload::class, 'Google_Service_DeveloperConnect_GKEWorkload');
