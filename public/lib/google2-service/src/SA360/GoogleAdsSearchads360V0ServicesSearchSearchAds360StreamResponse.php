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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ServicesSearchSearchAds360StreamResponse extends \Google\Collection
{
  protected $collection_key = 'results';
  protected $customColumnHeadersType = GoogleAdsSearchads360V0ServicesCustomColumnHeader::class;
  protected $customColumnHeadersDataType = 'array';
  /**
   * @var string
   */
  public $fieldMask;
  /**
   * @var string
   */
  public $requestId;
  protected $resultsType = GoogleAdsSearchads360V0ServicesSearchAds360Row::class;
  protected $resultsDataType = 'array';
  protected $summaryRowType = GoogleAdsSearchads360V0ServicesSearchAds360Row::class;
  protected $summaryRowDataType = '';

  /**
   * @param GoogleAdsSearchads360V0ServicesCustomColumnHeader[]
   */
  public function setCustomColumnHeaders($customColumnHeaders)
  {
    $this->customColumnHeaders = $customColumnHeaders;
  }
  /**
   * @return GoogleAdsSearchads360V0ServicesCustomColumnHeader[]
   */
  public function getCustomColumnHeaders()
  {
    return $this->customColumnHeaders;
  }
  /**
   * @param string
   */
  public function setFieldMask($fieldMask)
  {
    $this->fieldMask = $fieldMask;
  }
  /**
   * @return string
   */
  public function getFieldMask()
  {
    return $this->fieldMask;
  }
  /**
   * @param string
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * @param GoogleAdsSearchads360V0ServicesSearchAds360Row[]
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return GoogleAdsSearchads360V0ServicesSearchAds360Row[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * @param GoogleAdsSearchads360V0ServicesSearchAds360Row
   */
  public function setSummaryRow(GoogleAdsSearchads360V0ServicesSearchAds360Row $summaryRow)
  {
    $this->summaryRow = $summaryRow;
  }
  /**
   * @return GoogleAdsSearchads360V0ServicesSearchAds360Row
   */
  public function getSummaryRow()
  {
    return $this->summaryRow;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ServicesSearchSearchAds360StreamResponse::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ServicesSearchSearchAds360StreamResponse');
