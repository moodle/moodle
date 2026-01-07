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

class RRSetRoutingPolicy extends \Google\Model
{
  protected $geoType = GeoPolicy::class;
  protected $geoDataType = '';
  protected $geoPolicyType = GeoPolicy::class;
  protected $geoPolicyDataType = '';
  /**
   * The fully qualified URL of the HealthCheck to use for this
   * RRSetRoutingPolicy. Format this URL like `https://www.googleapis.com/comput
   * e/v1/projects/{project}/global/healthChecks/{healthCheck}`.
   * https://cloud.google.com/compute/docs/reference/rest/v1/healthChecks
   *
   * @var string
   */
  public $healthCheck;
  protected $primaryBackupType = PrimaryBackupPolicy::class;
  protected $primaryBackupDataType = '';
  protected $wrrType = WrrPolicy::class;
  protected $wrrDataType = '';
  protected $wrrPolicyType = WrrPolicy::class;
  protected $wrrPolicyDataType = '';

  /**
   * @param GeoPolicy $geo
   */
  public function setGeo(GeoPolicy $geo)
  {
    $this->geo = $geo;
  }
  /**
   * @return GeoPolicy
   */
  public function getGeo()
  {
    return $this->geo;
  }
  /**
   * @deprecated
   * @param GeoPolicy $geoPolicy
   */
  public function setGeoPolicy(GeoPolicy $geoPolicy)
  {
    $this->geoPolicy = $geoPolicy;
  }
  /**
   * @deprecated
   * @return GeoPolicy
   */
  public function getGeoPolicy()
  {
    return $this->geoPolicy;
  }
  /**
   * The fully qualified URL of the HealthCheck to use for this
   * RRSetRoutingPolicy. Format this URL like `https://www.googleapis.com/comput
   * e/v1/projects/{project}/global/healthChecks/{healthCheck}`.
   * https://cloud.google.com/compute/docs/reference/rest/v1/healthChecks
   *
   * @param string $healthCheck
   */
  public function setHealthCheck($healthCheck)
  {
    $this->healthCheck = $healthCheck;
  }
  /**
   * @return string
   */
  public function getHealthCheck()
  {
    return $this->healthCheck;
  }
  /**
   * @param PrimaryBackupPolicy $primaryBackup
   */
  public function setPrimaryBackup(PrimaryBackupPolicy $primaryBackup)
  {
    $this->primaryBackup = $primaryBackup;
  }
  /**
   * @return PrimaryBackupPolicy
   */
  public function getPrimaryBackup()
  {
    return $this->primaryBackup;
  }
  /**
   * @param WrrPolicy $wrr
   */
  public function setWrr(WrrPolicy $wrr)
  {
    $this->wrr = $wrr;
  }
  /**
   * @return WrrPolicy
   */
  public function getWrr()
  {
    return $this->wrr;
  }
  /**
   * @deprecated
   * @param WrrPolicy $wrrPolicy
   */
  public function setWrrPolicy(WrrPolicy $wrrPolicy)
  {
    $this->wrrPolicy = $wrrPolicy;
  }
  /**
   * @deprecated
   * @return WrrPolicy
   */
  public function getWrrPolicy()
  {
    return $this->wrrPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RRSetRoutingPolicy::class, 'Google_Service_CloudDomains_RRSetRoutingPolicy');
