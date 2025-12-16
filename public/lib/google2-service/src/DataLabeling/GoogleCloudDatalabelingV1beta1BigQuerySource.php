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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1BigQuerySource extends \Google\Model
{
  /**
   * Required. BigQuery URI to a table, up to 2,000 characters long. If you
   * specify the URI of a table that does not exist, Data Labeling Service
   * creates a table at the URI with the correct schema when you create your
   * EvaluationJob. If you specify the URI of a table that already exists, it
   * must have the [correct schema](/ml-engine/docs/continuous-
   * evaluation/create-job#table-schema). Provide the table URI in the following
   * format: "bq://{your_project_id}/ {your_dataset_name}/{your_table_name}"
   * [Learn more](/ml-engine/docs/continuous-evaluation/create-job#table-
   * schema).
   *
   * @var string
   */
  public $inputUri;

  /**
   * Required. BigQuery URI to a table, up to 2,000 characters long. If you
   * specify the URI of a table that does not exist, Data Labeling Service
   * creates a table at the URI with the correct schema when you create your
   * EvaluationJob. If you specify the URI of a table that already exists, it
   * must have the [correct schema](/ml-engine/docs/continuous-
   * evaluation/create-job#table-schema). Provide the table URI in the following
   * format: "bq://{your_project_id}/ {your_dataset_name}/{your_table_name}"
   * [Learn more](/ml-engine/docs/continuous-evaluation/create-job#table-
   * schema).
   *
   * @param string $inputUri
   */
  public function setInputUri($inputUri)
  {
    $this->inputUri = $inputUri;
  }
  /**
   * @return string
   */
  public function getInputUri()
  {
    return $this->inputUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1BigQuerySource::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1BigQuerySource');
