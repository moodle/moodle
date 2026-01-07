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

namespace Google\Service\OracleDatabase;

class LocationMetadata extends \Google\Collection
{
  protected $collection_key = 'gcpOracleZones';
  /**
   * Output only. Google Cloud Platform Oracle zones in a location.
   *
   * @var string[]
   */
  public $gcpOracleZones;

  /**
   * Output only. Google Cloud Platform Oracle zones in a location.
   *
   * @param string[] $gcpOracleZones
   */
  public function setGcpOracleZones($gcpOracleZones)
  {
    $this->gcpOracleZones = $gcpOracleZones;
  }
  /**
   * @return string[]
   */
  public function getGcpOracleZones()
  {
    return $this->gcpOracleZones;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocationMetadata::class, 'Google_Service_OracleDatabase_LocationMetadata');
