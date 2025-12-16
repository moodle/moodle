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

class GoogleCloudDataplexV1DataProfileSpecPostScanActions extends \Google\Model
{
  protected $bigqueryExportType = GoogleCloudDataplexV1DataProfileSpecPostScanActionsBigQueryExport::class;
  protected $bigqueryExportDataType = '';

  /**
   * Optional. If set, results will be exported to the provided BigQuery table.
   *
   * @param GoogleCloudDataplexV1DataProfileSpecPostScanActionsBigQueryExport $bigqueryExport
   */
  public function setBigqueryExport(GoogleCloudDataplexV1DataProfileSpecPostScanActionsBigQueryExport $bigqueryExport)
  {
    $this->bigqueryExport = $bigqueryExport;
  }
  /**
   * @return GoogleCloudDataplexV1DataProfileSpecPostScanActionsBigQueryExport
   */
  public function getBigqueryExport()
  {
    return $this->bigqueryExport;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataProfileSpecPostScanActions::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataProfileSpecPostScanActions');
