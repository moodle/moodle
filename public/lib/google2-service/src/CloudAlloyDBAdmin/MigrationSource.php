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

namespace Google\Service\CloudAlloyDBAdmin;

class MigrationSource extends \Google\Model
{
  /**
   * Migration source is unknown.
   */
  public const SOURCE_TYPE_MIGRATION_SOURCE_TYPE_UNSPECIFIED = 'MIGRATION_SOURCE_TYPE_UNSPECIFIED';
  /**
   * DMS source means the cluster was created via DMS migration job.
   */
  public const SOURCE_TYPE_DMS = 'DMS';
  /**
   * Output only. The host and port of the on-premises instance in host:port
   * format
   *
   * @var string
   */
  public $hostPort;
  /**
   * Output only. Place holder for the external source identifier(e.g DMS job
   * name) that created the cluster.
   *
   * @var string
   */
  public $referenceId;
  /**
   * Output only. Type of migration source.
   *
   * @var string
   */
  public $sourceType;

  /**
   * Output only. The host and port of the on-premises instance in host:port
   * format
   *
   * @param string $hostPort
   */
  public function setHostPort($hostPort)
  {
    $this->hostPort = $hostPort;
  }
  /**
   * @return string
   */
  public function getHostPort()
  {
    return $this->hostPort;
  }
  /**
   * Output only. Place holder for the external source identifier(e.g DMS job
   * name) that created the cluster.
   *
   * @param string $referenceId
   */
  public function setReferenceId($referenceId)
  {
    $this->referenceId = $referenceId;
  }
  /**
   * @return string
   */
  public function getReferenceId()
  {
    return $this->referenceId;
  }
  /**
   * Output only. Type of migration source.
   *
   * Accepted values: MIGRATION_SOURCE_TYPE_UNSPECIFIED, DMS
   *
   * @param self::SOURCE_TYPE_* $sourceType
   */
  public function setSourceType($sourceType)
  {
    $this->sourceType = $sourceType;
  }
  /**
   * @return self::SOURCE_TYPE_*
   */
  public function getSourceType()
  {
    return $this->sourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MigrationSource::class, 'Google_Service_CloudAlloyDBAdmin_MigrationSource');
