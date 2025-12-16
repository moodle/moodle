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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataScanEventPostScanActionsResult extends \Google\Model
{
  protected $bigqueryExportResultType = GoogleCloudDataplexV1DataScanEventPostScanActionsResultBigQueryExportResult::class;
  protected $bigqueryExportResultDataType = '';

  /**
   * The result of BigQuery export post scan action.
   *
   * @param GoogleCloudDataplexV1DataScanEventPostScanActionsResultBigQueryExportResult $bigqueryExportResult
   */
  public function setBigqueryExportResult(GoogleCloudDataplexV1DataScanEventPostScanActionsResultBigQueryExportResult $bigqueryExportResult)
  {
    $this->bigqueryExportResult = $bigqueryExportResult;
  }
  /**
   * @return GoogleCloudDataplexV1DataScanEventPostScanActionsResultBigQueryExportResult
   */
  public function getBigqueryExportResult()
  {
    return $this->bigqueryExportResult;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataScanEventPostScanActionsResult::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataScanEventPostScanActionsResult');
