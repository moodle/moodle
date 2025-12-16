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

namespace Google\Service\CloudProfiler;

class Deployment extends \Google\Model
{
  /**
   * Labels identify the deployment within the user universe and same target.
   * Validation regex for label names: `^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$`.
   * Value for an individual label must be <= 512 bytes, the total size of all
   * label names and values must be <= 1024 bytes. Label named "language" can be
   * used to record the programming language of the profiled deployment. The
   * standard choices for the value include "java", "go", "python", "ruby",
   * "nodejs", "php", "dotnet". For deployments running on Google Cloud
   * Platform, "zone" or "region" label should be present describing the
   * deployment location. An example of a zone is "us-central1-a", an example of
   * a region is "us-central1" or "us-central".
   *
   * @var string[]
   */
  public $labels;
  /**
   * Project ID is the ID of a cloud project. Validation regex:
   * `^a-z{4,61}[a-z0-9]$`.
   *
   * @var string
   */
  public $projectId;
  /**
   * Target is the service name used to group related deployments: * Service
   * name for App Engine Flex / Standard. * Cluster and container name for GKE.
   * * User-specified string for direct Compute Engine profiling (e.g. Java). *
   * Job name for Dataflow. Validation regex:
   * `^[a-z0-9]([-a-z0-9_.]{0,253}[a-z0-9])?$`.
   *
   * @var string
   */
  public $target;

  /**
   * Labels identify the deployment within the user universe and same target.
   * Validation regex for label names: `^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$`.
   * Value for an individual label must be <= 512 bytes, the total size of all
   * label names and values must be <= 1024 bytes. Label named "language" can be
   * used to record the programming language of the profiled deployment. The
   * standard choices for the value include "java", "go", "python", "ruby",
   * "nodejs", "php", "dotnet". For deployments running on Google Cloud
   * Platform, "zone" or "region" label should be present describing the
   * deployment location. An example of a zone is "us-central1-a", an example of
   * a region is "us-central1" or "us-central".
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
   * Project ID is the ID of a cloud project. Validation regex:
   * `^a-z{4,61}[a-z0-9]$`.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Target is the service name used to group related deployments: * Service
   * name for App Engine Flex / Standard. * Cluster and container name for GKE.
   * * User-specified string for direct Compute Engine profiling (e.g. Java). *
   * Job name for Dataflow. Validation regex:
   * `^[a-z0-9]([-a-z0-9_.]{0,253}[a-z0-9])?$`.
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Deployment::class, 'Google_Service_CloudProfiler_Deployment');
