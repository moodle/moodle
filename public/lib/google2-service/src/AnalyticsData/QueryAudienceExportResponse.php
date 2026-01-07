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

namespace Google\Service\AnalyticsData;

class QueryAudienceExportResponse extends \Google\Collection
{
  protected $collection_key = 'audienceRows';
  protected $audienceExportType = AudienceExport::class;
  protected $audienceExportDataType = '';
  protected $audienceRowsType = V1betaAudienceRow::class;
  protected $audienceRowsDataType = 'array';
  /**
   * The total number of rows in the AudienceExport result. `rowCount` is
   * independent of the number of rows returned in the response, the `limit`
   * request parameter, and the `offset` request parameter. For example if a
   * query returns 175 rows and includes `limit` of 50 in the API request, the
   * response will contain `rowCount` of 175 but only 50 rows. To learn more
   * about this pagination parameter, see [Pagination](https://developers.google
   * .com/analytics/devguides/reporting/data/v1/basics#pagination).
   *
   * @var int
   */
  public $rowCount;

  /**
   * Configuration data about AudienceExport being queried. Returned to help
   * interpret the audience rows in this response. For example, the dimensions
   * in this AudienceExport correspond to the columns in the AudienceRows.
   *
   * @param AudienceExport $audienceExport
   */
  public function setAudienceExport(AudienceExport $audienceExport)
  {
    $this->audienceExport = $audienceExport;
  }
  /**
   * @return AudienceExport
   */
  public function getAudienceExport()
  {
    return $this->audienceExport;
  }
  /**
   * Rows for each user in an audience export. The number of rows in this
   * response will be less than or equal to request's page size.
   *
   * @param V1betaAudienceRow[] $audienceRows
   */
  public function setAudienceRows($audienceRows)
  {
    $this->audienceRows = $audienceRows;
  }
  /**
   * @return V1betaAudienceRow[]
   */
  public function getAudienceRows()
  {
    return $this->audienceRows;
  }
  /**
   * The total number of rows in the AudienceExport result. `rowCount` is
   * independent of the number of rows returned in the response, the `limit`
   * request parameter, and the `offset` request parameter. For example if a
   * query returns 175 rows and includes `limit` of 50 in the API request, the
   * response will contain `rowCount` of 175 but only 50 rows. To learn more
   * about this pagination parameter, see [Pagination](https://developers.google
   * .com/analytics/devguides/reporting/data/v1/basics#pagination).
   *
   * @param int $rowCount
   */
  public function setRowCount($rowCount)
  {
    $this->rowCount = $rowCount;
  }
  /**
   * @return int
   */
  public function getRowCount()
  {
    return $this->rowCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryAudienceExportResponse::class, 'Google_Service_AnalyticsData_QueryAudienceExportResponse');
