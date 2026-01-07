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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1alpha1Column extends \Google\Model
{
  /**
   * Not used.
   */
  public const DATA_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  /**
   * ReportValues for this column will use string_value.
   */
  public const DATA_TYPE_STRING = 'STRING';
  /**
   * ReportValues for this column will use int_value.
   */
  public const DATA_TYPE_INT = 'INT';
  /**
   * ReportValues for this column will use decimal_value.
   */
  public const DATA_TYPE_DECIMAL = 'DECIMAL';
  /**
   * ReportValues for this column will use money_value.
   */
  public const DATA_TYPE_MONEY = 'MONEY';
  /**
   * ReportValues for this column will use date_value.
   */
  public const DATA_TYPE_DATE = 'DATE';
  /**
   * ReportValues for this column will use date_time_value.
   */
  public const DATA_TYPE_DATE_TIME = 'DATE_TIME';
  /**
   * The unique name of the column (for example, customer_domain,
   * channel_partner, customer_cost). You can use column IDs in
   * RunReportJobRequest.filter. To see all reports and their columns, call
   * CloudChannelReportsService.ListReports.
   *
   * @var string
   */
  public $columnId;
  /**
   * The type of the values for this column.
   *
   * @var string
   */
  public $dataType;
  /**
   * The column's display name.
   *
   * @var string
   */
  public $displayName;

  /**
   * The unique name of the column (for example, customer_domain,
   * channel_partner, customer_cost). You can use column IDs in
   * RunReportJobRequest.filter. To see all reports and their columns, call
   * CloudChannelReportsService.ListReports.
   *
   * @param string $columnId
   */
  public function setColumnId($columnId)
  {
    $this->columnId = $columnId;
  }
  /**
   * @return string
   */
  public function getColumnId()
  {
    return $this->columnId;
  }
  /**
   * The type of the values for this column.
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, STRING, INT, DECIMAL, MONEY, DATE,
   * DATE_TIME
   *
   * @param self::DATA_TYPE_* $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return self::DATA_TYPE_*
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * The column's display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1alpha1Column::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1alpha1Column');
