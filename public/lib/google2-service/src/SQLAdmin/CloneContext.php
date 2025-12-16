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

class CloneContext extends \Google\Collection
{
  protected $collection_key = 'databaseNames';
  /**
   * The name of the allocated ip range for the private ip Cloud SQL instance.
   * For example: "google-managed-services-default". If set, the cloned instance
   * ip will be created in the allocated range. The range name must comply with
   * [RFC 1035](https://tools.ietf.org/html/rfc1035). Specifically, the name
   * must be 1-63 characters long and match the regular expression
   * [a-z]([-a-z0-9]*[a-z0-9])?. Reserved for future use.
   *
   * @var string
   */
  public $allocatedIpRange;
  protected $binLogCoordinatesType = BinLogCoordinates::class;
  protected $binLogCoordinatesDataType = '';
  /**
   * (SQL Server only) Clone only the specified databases from the source
   * instance. Clone all databases if empty.
   *
   * @var string[]
   */
  public $databaseNames;
  /**
   * Required. Name of the Cloud SQL instance to be created as a clone.
   *
   * @var string
   */
  public $destinationInstanceName;
  /**
   * This is always `sql#cloneContext`.
   *
   * @var string
   */
  public $kind;
  /**
   * Reserved for future use.
   *
   * @var string
   */
  public $pitrTimestampMs;
  /**
   * Timestamp, if specified, identifies the time to which the source instance
   * is cloned.
   *
   * @var string
   */
  public $pointInTime;
  /**
   * Optional. Copy clone and point-in-time recovery clone of a regional
   * instance in the specified zones. If not specified, clone to the same
   * secondary zone as the source instance. This value cannot be the same as the
   * preferred_zone field. This field applies to all DB types.
   *
   * @var string
   */
  public $preferredSecondaryZone;
  /**
   * Optional. Copy clone and point-in-time recovery clone of an instance to the
   * specified zone. If no zone is specified, clone to the same primary zone as
   * the source instance. This field applies to all DB types.
   *
   * @var string
   */
  public $preferredZone;
  /**
   * The timestamp used to identify the time when the source instance is
   * deleted. If this instance is deleted, then you must set the timestamp.
   *
   * @var string
   */
  public $sourceInstanceDeletionTime;

  /**
   * The name of the allocated ip range for the private ip Cloud SQL instance.
   * For example: "google-managed-services-default". If set, the cloned instance
   * ip will be created in the allocated range. The range name must comply with
   * [RFC 1035](https://tools.ietf.org/html/rfc1035). Specifically, the name
   * must be 1-63 characters long and match the regular expression
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
   * Binary log coordinates, if specified, identify the position up to which the
   * source instance is cloned. If not specified, the source instance is cloned
   * up to the most recent binary log coordinates.
   *
   * @param BinLogCoordinates $binLogCoordinates
   */
  public function setBinLogCoordinates(BinLogCoordinates $binLogCoordinates)
  {
    $this->binLogCoordinates = $binLogCoordinates;
  }
  /**
   * @return BinLogCoordinates
   */
  public function getBinLogCoordinates()
  {
    return $this->binLogCoordinates;
  }
  /**
   * (SQL Server only) Clone only the specified databases from the source
   * instance. Clone all databases if empty.
   *
   * @param string[] $databaseNames
   */
  public function setDatabaseNames($databaseNames)
  {
    $this->databaseNames = $databaseNames;
  }
  /**
   * @return string[]
   */
  public function getDatabaseNames()
  {
    return $this->databaseNames;
  }
  /**
   * Required. Name of the Cloud SQL instance to be created as a clone.
   *
   * @param string $destinationInstanceName
   */
  public function setDestinationInstanceName($destinationInstanceName)
  {
    $this->destinationInstanceName = $destinationInstanceName;
  }
  /**
   * @return string
   */
  public function getDestinationInstanceName()
  {
    return $this->destinationInstanceName;
  }
  /**
   * This is always `sql#cloneContext`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Reserved for future use.
   *
   * @param string $pitrTimestampMs
   */
  public function setPitrTimestampMs($pitrTimestampMs)
  {
    $this->pitrTimestampMs = $pitrTimestampMs;
  }
  /**
   * @return string
   */
  public function getPitrTimestampMs()
  {
    return $this->pitrTimestampMs;
  }
  /**
   * Timestamp, if specified, identifies the time to which the source instance
   * is cloned.
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
   * Optional. Copy clone and point-in-time recovery clone of a regional
   * instance in the specified zones. If not specified, clone to the same
   * secondary zone as the source instance. This value cannot be the same as the
   * preferred_zone field. This field applies to all DB types.
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
   * Optional. Copy clone and point-in-time recovery clone of an instance to the
   * specified zone. If no zone is specified, clone to the same primary zone as
   * the source instance. This field applies to all DB types.
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
   * The timestamp used to identify the time when the source instance is
   * deleted. If this instance is deleted, then you must set the timestamp.
   *
   * @param string $sourceInstanceDeletionTime
   */
  public function setSourceInstanceDeletionTime($sourceInstanceDeletionTime)
  {
    $this->sourceInstanceDeletionTime = $sourceInstanceDeletionTime;
  }
  /**
   * @return string
   */
  public function getSourceInstanceDeletionTime()
  {
    return $this->sourceInstanceDeletionTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloneContext::class, 'Google_Service_SQLAdmin_CloneContext');
