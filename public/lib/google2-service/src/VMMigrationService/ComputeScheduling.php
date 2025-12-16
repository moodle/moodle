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

namespace Google\Service\VMMigrationService;

class ComputeScheduling extends \Google\Collection
{
  /**
   * An unknown, unexpected behavior.
   */
  public const ON_HOST_MAINTENANCE_ON_HOST_MAINTENANCE_UNSPECIFIED = 'ON_HOST_MAINTENANCE_UNSPECIFIED';
  /**
   * Terminate the instance when the host machine undergoes maintenance.
   */
  public const ON_HOST_MAINTENANCE_TERMINATE = 'TERMINATE';
  /**
   * Migrate the instance when the host machine undergoes maintenance.
   */
  public const ON_HOST_MAINTENANCE_MIGRATE = 'MIGRATE';
  /**
   * Unspecified behavior. This will use the default.
   */
  public const RESTART_TYPE_RESTART_TYPE_UNSPECIFIED = 'RESTART_TYPE_UNSPECIFIED';
  /**
   * The Instance should be automatically restarted whenever it is terminated by
   * Compute Engine.
   */
  public const RESTART_TYPE_AUTOMATIC_RESTART = 'AUTOMATIC_RESTART';
  /**
   * The Instance isn't automatically restarted whenever it is terminated by
   * Compute Engine.
   */
  public const RESTART_TYPE_NO_AUTOMATIC_RESTART = 'NO_AUTOMATIC_RESTART';
  protected $collection_key = 'nodeAffinities';
  /**
   * The minimum number of virtual CPUs this instance will consume when running
   * on a sole-tenant node. Ignored if no node_affinites are configured.
   *
   * @var int
   */
  public $minNodeCpus;
  protected $nodeAffinitiesType = SchedulingNodeAffinity::class;
  protected $nodeAffinitiesDataType = 'array';
  /**
   * How the instance should behave when the host machine undergoes maintenance
   * that may temporarily impact instance performance.
   *
   * @var string
   */
  public $onHostMaintenance;
  /**
   * Whether the Instance should be automatically restarted whenever it is
   * terminated by Compute Engine (not terminated by user). This configuration
   * is identical to `automaticRestart` field in Compute Engine create instance
   * under scheduling. It was changed to an enum (instead of a boolean) to match
   * the default value in Compute Engine which is automatic restart.
   *
   * @var string
   */
  public $restartType;

  /**
   * The minimum number of virtual CPUs this instance will consume when running
   * on a sole-tenant node. Ignored if no node_affinites are configured.
   *
   * @param int $minNodeCpus
   */
  public function setMinNodeCpus($minNodeCpus)
  {
    $this->minNodeCpus = $minNodeCpus;
  }
  /**
   * @return int
   */
  public function getMinNodeCpus()
  {
    return $this->minNodeCpus;
  }
  /**
   * A set of node affinity and anti-affinity configurations for sole tenant
   * nodes.
   *
   * @param SchedulingNodeAffinity[] $nodeAffinities
   */
  public function setNodeAffinities($nodeAffinities)
  {
    $this->nodeAffinities = $nodeAffinities;
  }
  /**
   * @return SchedulingNodeAffinity[]
   */
  public function getNodeAffinities()
  {
    return $this->nodeAffinities;
  }
  /**
   * How the instance should behave when the host machine undergoes maintenance
   * that may temporarily impact instance performance.
   *
   * Accepted values: ON_HOST_MAINTENANCE_UNSPECIFIED, TERMINATE, MIGRATE
   *
   * @param self::ON_HOST_MAINTENANCE_* $onHostMaintenance
   */
  public function setOnHostMaintenance($onHostMaintenance)
  {
    $this->onHostMaintenance = $onHostMaintenance;
  }
  /**
   * @return self::ON_HOST_MAINTENANCE_*
   */
  public function getOnHostMaintenance()
  {
    return $this->onHostMaintenance;
  }
  /**
   * Whether the Instance should be automatically restarted whenever it is
   * terminated by Compute Engine (not terminated by user). This configuration
   * is identical to `automaticRestart` field in Compute Engine create instance
   * under scheduling. It was changed to an enum (instead of a boolean) to match
   * the default value in Compute Engine which is automatic restart.
   *
   * Accepted values: RESTART_TYPE_UNSPECIFIED, AUTOMATIC_RESTART,
   * NO_AUTOMATIC_RESTART
   *
   * @param self::RESTART_TYPE_* $restartType
   */
  public function setRestartType($restartType)
  {
    $this->restartType = $restartType;
  }
  /**
   * @return self::RESTART_TYPE_*
   */
  public function getRestartType()
  {
    return $this->restartType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputeScheduling::class, 'Google_Service_VMMigrationService_ComputeScheduling');
