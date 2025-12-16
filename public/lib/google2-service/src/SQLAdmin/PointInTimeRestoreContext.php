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

namespace Google\Service\SQLAdmin;

class PointInTimeRestoreContext extends \Google\Model
{
  /**
   * Optional. The name of the allocated IP range for the internal IP Cloud SQL
   * instance. For example: "google-managed-services-default". If you set this,
   * then Cloud SQL creates the IP address for the cloned instance in the
   * allocated range. This range must comply with [RFC
   * 1035](https://tools.ietf.org/html/rfc1035) standards. Specifically, the
   * name must be 1-63 characters long and match the regular expression
   * [a-z]([-a-z0-9]*[a-z0-9])?. Reserved for future use.
   *
   * @var string
   */
  public $allocatedIpRange;
  /**
   * The Backup and Disaster Recovery (DR) Service Datasource URI. Format: proje
   * cts/{project}/locations/{region}/backupVaults/{backupvault}/dataSources/{da
   * tasource}.
   *
   * @var string
   */
  public $datasource;
  /**
   * Required. The date and time to which you want to restore the instance.
   *
   * @var string
   */
  public $pointInTime;
  /**
   * Optional. Point-in-time recovery of a regional instance in the specified
   * zones. If not specified, clone to the same secondary zone as the source
   * instance. This value cannot be the same as the preferred_zone field.
   *
   * @var string
   */
  public $preferredSecondaryZone;
  /**
   * Optional. Point-in-time recovery of an instance to the specified zone. If
   * no zone is specified, then clone to the same primary zone as the source
   * instance.
   *
   * @var string
   */
  public $preferredZone;
  /**
   * Optional. The resource link for the VPC network from which the Cloud SQL
   * instance is accessible for private IP. For example,
   * `/projects/myProject/global/networks/default`.
   *
   * @var string
   */
  public $privateNetwork;
  /**
   * Target instance name.
   *
   * @var string
   */
  public $targetInstance;

  /**
   * Optional. The name of the allocated IP range for the internal IP Cloud SQL
   * instance. For example: "google-managed-services-default". If you set this,
   * then Cloud SQL creates the IP address for the cloned instance in the
   * allocated range. This range must comply with [RFC
   * 1035](https://tools.ietf.org/html/rfc1035) standards. Specifically, the
   * name must be 1-63 characters long and match the regular expression
   * [a-z]([-a-z0-9]*[a-z0-9])?. Reserved for future use.
   *
   * @param string $allocatedIpRange
   */
  public function setAllocatedIpRange($allocatedIpRange)
  {
    $this->allocatedIpRange = $allocatedIpRange;
  }
  /**
   * @return string
   */
  public function getAllocatedIpRange()
  {
    return $this->allocatedIpRange;
  }
  /**
   * The Backup and Disaster Recovery (DR) Service Datasource URI. Format: proje
   * cts/{project}/locations/{region}/backupVaults/{backupvault}/dataSources/{da
   * tasource}.
   *
   * @param string $datasource
   */
  public function setDatasource($datasource)
  {
    $this->datasource = $datasource;
  }
  /**
   * @return string
   */
  public function getDatasource()
  {
    return $this->datasource;
  }
  /**
   * Required. The date and time to which you want to restore the instance.
   *
   * @param string $pointInTime
   */
  public function setPointInTime($pointInTime)
  {
    $this->pointInTime = $pointInTime;
  }
  /**
   * @return string
   */
  public function getPointInTime()
  {
    return $this->pointInTime;
  }
  /**
   * Optional. Point-in-time recovery of a regional instance in the specified
   * zones. If not specified, clone to the same secondary zone as the source
   * instance. This value cannot be the same as the preferred_zone field.
   *
   * @param string $preferredSecondaryZone
   */
  public function setPreferredSecondaryZone($preferredSecondaryZone)
  {
    $this->preferredSecondaryZone = $preferredSecondaryZone;
  }
  /**
   * @return string
   */
  public function getPreferredSecondaryZone()
  {
    return $this->preferredSecondaryZone;
  }
  /**
   * Optional. Point-in-time recovery of an instance to the specified zone. If
   * no zone is specified, then clone to the same primary zone as the source
   * instance.
   *
   * @param string $preferredZone
   */
  public function setPreferredZone($preferredZone)
  {
    $this->preferredZone = $preferredZone;
  }
  /**
   * @return string
   */
  public function getPreferredZone()
  {
    return $this->preferredZone;
  }
  /**
   * Optional. The resource link for the VPC network from which the Cloud SQL
   * instance is accessible for private IP. For example,
   * `/projects/myProject/global/networks/default`.
   *
   * @param string $privateNetwork
   */
  public function setPrivateNetwork($privateNetwork)
  {
    $this->privateNetwork = $privateNetwork;
  }
  /**
   * @return string
   */
  public function getPrivateNetwork()
  {
    return $this->privateNetwork;
  }
  /**
   * Target instance name.
   *
   * @param string $targetInstance
   */
  public function setTargetInstance($targetInstance)
  {
    $this->targetInstance = $targetInstance;
  }
  /**
   * @return string
   */
  public function getTargetInstance()
  {
    return $this->targetInstance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PointInTimeRestoreContext::class, 'Google_Service_SQLAdmin_PointInTimeRestoreContext');
