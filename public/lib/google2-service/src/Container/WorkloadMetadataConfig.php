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

namespace Google\Service\Container;

class WorkloadMetadataConfig extends \Google\Model
{
  /**
   * Not set.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Expose all Compute Engine metadata to pods.
   */
  public const MODE_GCE_METADATA = 'GCE_METADATA';
  /**
   * Run the GKE Metadata Server on this node. The GKE Metadata Server exposes a
   * metadata API to workloads that is compatible with the V1 Compute Metadata
   * APIs exposed by the Compute Engine and App Engine Metadata Servers. This
   * feature can only be enabled if Workload Identity is enabled at the cluster
   * level.
   */
  public const MODE_GKE_METADATA = 'GKE_METADATA';
  /**
   * Mode is the configuration for how to expose metadata to workloads running
   * on the node pool.
   *
   * @var string
   */
  public $mode;

  /**
   * Mode is the configuration for how to expose metadata to workloads running
   * on the node pool.
   *
   * Accepted values: MODE_UNSPECIFIED, GCE_METADATA, GKE_METADATA
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkloadMetadataConfig::class, 'Google_Service_Container_WorkloadMetadataConfig');
