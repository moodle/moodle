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

class GoogleCloudChannelV1RunReportJobRequest extends \Google\Model
{
  protected $dateRangeType = GoogleCloudChannelV1DateRange::class;
  protected $dateRangeDataType = '';
  /**
   * Optional. A structured string that defines conditions on dimension columns
   * to restrict the report output. Filters support logical operators (AND, OR,
   * NOT) and conditional operators (=, !=, <, >, <=, and >=) using `column_id`
   * as keys. For example: `(customer:"accounts/C123abc/customers/S456def" OR
   * customer:"accounts/C123abc/customers/S789ghi") AND invoice_start_date.year
   * >= 2022`
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. The BCP-47 language code, such as "en-US". If specified, the
   * response is localized to the corresponding language code if the original
   * data sources support it. Default is "en-US".
   *
   * @var string
   */
  public $languageCode;

  /**
   * Optional. The range of usage or invoice dates to include in the result.
   *
   * @param GoogleCloudChannelV1DateRange $dateRange
   */
  public function setDateRange(GoogleCloudChannelV1DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }
  /**
   * @return GoogleCloudChannelV1DateRange
   */
  public function getDateRange()
  {
    return $this->dateRange;
  }
  /**
   * Optional. A structured string that defines conditions on dimension columns
   * to restrict the report output. Filters support logical operators (AND, OR,
   * NOT) and conditional operators (=, !=, <, >, <=, and >=) using `column_id`
   * as keys. For example: `(customer:"accounts/C123abc/customers/S456def" OR
   * customer:"accounts/C123abc/customers/S789ghi") AND invoice_start_date.year
   * >= 2022`
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Optional. The BCP-47 language code, such as "en-US". If specified, the
   * response is localized to the corresponding language code if the original
   * data sources support it. Default is "en-US".
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1RunReportJobRequest::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1RunReportJobRequest');
