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

namespace Google\Service\BigQueryConnectionService;

class CloudSpannerProperties extends \Google\Model
{
  /**
   * Cloud Spanner database in the form `project/instance/database'
   *
   * @var string
   */
  public $database;
  /**
   * Optional. Cloud Spanner database role for fine-grained access control. The
   * Cloud Spanner admin should have provisioned the database role with
   * appropriate permissions, such as `SELECT` and `INSERT`. Other users should
   * only use roles provided by their Cloud Spanner admins. For more details,
   * see [About fine-grained access control]
   * (https://cloud.google.com/spanner/docs/fgac-about). REQUIRES: The database
   * role name must start with a letter, and can only contain letters, numbers,
   * and underscores.
   *
   * @var string
   */
  public $databaseRole;
  /**
   * Allows setting max parallelism per query when executing on Spanner
   * independent compute resources. If unspecified, default values of
   * parallelism are chosen that are dependent on the Cloud Spanner instance
   * configuration. REQUIRES: `use_parallelism` must be set. REQUIRES:
   * `use_data_boost` must be set.
   *
   * @var int
   */
  public $maxParallelism;
  /**
   * If set, the request will be executed via Spanner independent compute
   * resources. REQUIRES: `use_parallelism` must be set.
   *
   * @var bool
   */
  public $useDataBoost;
  /**
   * If parallelism should be used when reading from Cloud Spanner
   *
   * @var bool
   */
  public $useParallelism;
  /**
   * Deprecated: prefer use_data_boost instead. If the serverless analytics
   * service should be used to read data from Cloud Spanner. Note:
   * `use_parallelism` must be set when using serverless analytics.
   *
   * @deprecated
   * @var bool
   */
  public $useServerlessAnalytics;

  /**
   * Cloud Spanner database in the form `project/instance/database'
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Optional. Cloud Spanner database role for fine-grained access control. The
   * Cloud Spanner admin should have provisioned the database role with
   * appropriate permissions, such as `SELECT` and `INSERT`. Other users should
   * only use roles provided by their Cloud Spanner admins. For more details,
   * see [About fine-grained access control]
   * (https://cloud.google.com/spanner/docs/fgac-about). REQUIRES: The database
   * role name must start with a letter, and can only contain letters, numbers,
   * and underscores.
   *
   * @param string $databaseRole
   */
  public function setDatabaseRole($databaseRole)
  {
    $this->databaseRole = $databaseRole;
  }
  /**
   * @return string
   */
  public function getDatabaseRole()
  {
    return $this->databaseRole;
  }
  /**
   * Allows setting max parallelism per query when executing on Spanner
   * independent compute resources. If unspecified, default values of
   * parallelism are chosen that are dependent on the Cloud Spanner instance
   * configuration. REQUIRES: `use_parallelism` must be set. REQUIRES:
   * `use_data_boost` must be set.
   *
   * @param int $maxParallelism
   */
  public function setMaxParallelism($maxParallelism)
  {
    $this->maxParallelism = $maxParallelism;
  }
  /**
   * @return int
   */
  public function getMaxParallelism()
  {
    return $this->maxParallelism;
  }
  /**
   * If set, the request will be executed via Spanner independent compute
   * resources. REQUIRES: `use_parallelism` must be set.
   *
   * @param bool $useDataBoost
   */
  public function setUseDataBoost($useDataBoost)
  {
    $this->useDataBoost = $useDataBoost;
  }
  /**
   * @return bool
   */
  public function getUseDataBoost()
  {
    return $this->useDataBoost;
  }
  /**
   * If parallelism should be used when reading from Cloud Spanner
   *
   * @param bool $useParallelism
   */
  public function setUseParallelism($useParallelism)
  {
    $this->useParallelism = $useParallelism;
  }
  /**
   * @return bool
   */
  public function getUseParallelism()
  {
    return $this->useParallelism;
  }
  /**
   * Deprecated: prefer use_data_boost instead. If the serverless analytics
   * service should be used to read data from Cloud Spanner. Note:
   * `use_parallelism` must be set when using serverless analytics.
   *
   * @deprecated
   * @param bool $useServerlessAnalytics
   */
  public function setUseServerlessAnalytics($useServerlessAnalytics)
  {
    $this->useServerlessAnalytics = $useServerlessAnalytics;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getUseServerlessAnalytics()
  {
    return $this->useServerlessAnalytics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudSpannerProperties::class, 'Google_Service_BigQueryConnectionService_CloudSpannerProperties');
