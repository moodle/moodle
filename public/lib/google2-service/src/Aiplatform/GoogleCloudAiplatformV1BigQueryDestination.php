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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1BigQueryDestination extends \Google\Model
{
  /**
   * Required. BigQuery URI to a project or table, up to 2000 characters long.
   * When only the project is specified, the Dataset and Table is created. When
   * the full table reference is specified, the Dataset must exist and table
   * must not exist. Accepted forms: * BigQuery path. For example:
   * `bq://projectId` or `bq://projectId.bqDatasetId` or
   * `bq://projectId.bqDatasetId.bqTableId`.
   *
   * @var string
   */
  public $outputUri;

  /**
   * Required. BigQuery URI to a project or table, up to 2000 characters long.
   * When only the project is specified, the Dataset and Table is created. When
   * the full table reference is specified, the Dataset must exist and table
   * must not exist. Accepted forms: * BigQuery path. For example:
   * `bq://projectId` or `bq://projectId.bqDatasetId` or
   * `bq://projectId.bqDatasetId.bqTableId`.
   *
   * @param string $outputUri
   */
  public function setOutputUri($outputUri)
  {
    $this->outputUri = $outputUri;
  }
  /**
   * @return string
   */
  public function getOutputUri()
  {
    return $this->outputUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1BigQueryDestination::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1BigQueryDestination');
