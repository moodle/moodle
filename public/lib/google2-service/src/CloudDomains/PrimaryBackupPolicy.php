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

namespace Google\Service\CloudDomains;

class PrimaryBackupPolicy extends \Google\Model
{
  protected $backupGeoTargetsType = GeoPolicy::class;
  protected $backupGeoTargetsDataType = '';
  protected $primaryTargetsType = HealthCheckTargets::class;
  protected $primaryTargetsDataType = '';
  /**
   * When serving state is `PRIMARY`, this field provides the option of sending
   * a small percentage of the traffic to the backup targets.
   *
   * @var 
   */
  public $trickleTraffic;

  /**
   * Backup targets provide a regional failover policy for the otherwise global
   * primary targets. If serving state is set to `BACKUP`, this policy
   * essentially becomes a geo routing policy.
   *
   * @param GeoPolicy $backupGeoTargets
   */
  public function setBackupGeoTargets(GeoPolicy $backupGeoTargets)
  {
    $this->backupGeoTargets = $backupGeoTargets;
  }
  /**
   * @return GeoPolicy
   */
  public function getBackupGeoTargets()
  {
    return $this->backupGeoTargets;
  }
  /**
   * Endpoints that are health checked before making the routing decision.
   * Unhealthy endpoints are omitted from the results. If all endpoints are
   * unhealthy, we serve a response based on the `backup_geo_targets`.
   *
   * @param HealthCheckTargets $primaryTargets
   */
  public function setPrimaryTargets(HealthCheckTargets $primaryTargets)
  {
    $this->primaryTargets = $primaryTargets;
  }
  /**
   * @return HealthCheckTargets
   */
  public function getPrimaryTargets()
  {
    return $this->primaryTargets;
  }
  public function setTrickleTraffic($trickleTraffic)
  {
    $this->trickleTraffic = $trickleTraffic;
  }
  public function getTrickleTraffic()
  {
    return $this->trickleTraffic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrimaryBackupPolicy::class, 'Google_Service_CloudDomains_PrimaryBackupPolicy');
