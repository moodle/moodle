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

namespace Google\Service\MigrationCenterAPI;

class SqlServerDatabaseDeployment extends \Google\Collection
{
  protected $collection_key = 'traceFlags';
  protected $featuresType = SqlServerFeature::class;
  protected $featuresDataType = 'array';
  protected $serverFlagsType = SqlServerServerFlag::class;
  protected $serverFlagsDataType = 'array';
  protected $traceFlagsType = SqlServerTraceFlag::class;
  protected $traceFlagsDataType = 'array';

  /**
   * Optional. List of SQL Server features.
   *
   * @param SqlServerFeature[] $features
   */
  public function setFeatures($features)
  {
    $this->features = $features;
  }
  /**
   * @return SqlServerFeature[]
   */
  public function getFeatures()
  {
    return $this->features;
  }
  /**
   * Optional. List of SQL Server server flags.
   *
   * @param SqlServerServerFlag[] $serverFlags
   */
  public function setServerFlags($serverFlags)
  {
    $this->serverFlags = $serverFlags;
  }
  /**
   * @return SqlServerServerFlag[]
   */
  public function getServerFlags()
  {
    return $this->serverFlags;
  }
  /**
   * Optional. List of SQL Server trace flags.
   *
   * @param SqlServerTraceFlag[] $traceFlags
   */
  public function setTraceFlags($traceFlags)
  {
    $this->traceFlags = $traceFlags;
  }
  /**
   * @return SqlServerTraceFlag[]
   */
  public function getTraceFlags()
  {
    return $this->traceFlags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlServerDatabaseDeployment::class, 'Google_Service_MigrationCenterAPI_SqlServerDatabaseDeployment');
