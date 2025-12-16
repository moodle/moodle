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

namespace Google\Service\CloudComposer;

class DatabaseConfig extends \Google\Model
{
  /**
   * Optional. Cloud SQL machine type used by Airflow database. It has to be one
   * of: db-n1-standard-2, db-n1-standard-4, db-n1-standard-8 or
   * db-n1-standard-16. If not specified, db-n1-standard-2 will be used.
   * Supported for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*.
   *
   * @var string
   */
  public $machineType;
  /**
   * Optional. The Compute Engine zone where the Airflow database is created. If
   * zone is provided, it must be in the region selected for the environment. If
   * zone is not provided, a zone is automatically selected. The zone can only
   * be set during environment creation. Supported for Cloud Composer
   * environments in versions composer-2.*.*-airflow-*.*.*.
   *
   * @var string
   */
  public $zone;

  /**
   * Optional. Cloud SQL machine type used by Airflow database. It has to be one
   * of: db-n1-standard-2, db-n1-standard-4, db-n1-standard-8 or
   * db-n1-standard-16. If not specified, db-n1-standard-2 will be used.
   * Supported for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*.
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * Optional. The Compute Engine zone where the Airflow database is created. If
   * zone is provided, it must be in the region selected for the environment. If
   * zone is not provided, a zone is automatically selected. The zone can only
   * be set during environment creation. Supported for Cloud Composer
   * environments in versions composer-2.*.*-airflow-*.*.*.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseConfig::class, 'Google_Service_CloudComposer_DatabaseConfig');
