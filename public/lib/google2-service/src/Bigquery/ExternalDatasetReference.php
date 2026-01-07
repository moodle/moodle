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

namespace Google\Service\Bigquery;

class ExternalDatasetReference extends \Google\Model
{
  /**
   * Required. The connection id that is used to access the external_source.
   * Format:
   * projects/{project_id}/locations/{location_id}/connections/{connection_id}
   *
   * @var string
   */
  public $connection;
  /**
   * Required. External source that backs this dataset.
   *
   * @var string
   */
  public $externalSource;

  /**
   * Required. The connection id that is used to access the external_source.
   * Format:
   * projects/{project_id}/locations/{location_id}/connections/{connection_id}
   *
   * @param string $connection
   */
  public function setConnection($connection)
  {
    $this->connection = $connection;
  }
  /**
   * @return string
   */
  public function getConnection()
  {
    return $this->connection;
  }
  /**
   * Required. External source that backs this dataset.
   *
   * @param string $externalSource
   */
  public function setExternalSource($externalSource)
  {
    $this->externalSource = $externalSource;
  }
  /**
   * @return string
   */
  public function getExternalSource()
  {
    return $this->externalSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalDatasetReference::class, 'Google_Service_Bigquery_ExternalDatasetReference');
