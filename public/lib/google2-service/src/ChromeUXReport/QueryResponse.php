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

namespace Google\Service\ChromeUXReport;

class QueryResponse extends \Google\Model
{
  protected $recordType = Record::class;
  protected $recordDataType = '';
  protected $urlNormalizationDetailsType = UrlNormalization::class;
  protected $urlNormalizationDetailsDataType = '';

  /**
   * The record that was found.
   *
   * @param Record $record
   */
  public function setRecord(Record $record)
  {
    $this->record = $record;
  }
  /**
   * @return Record
   */
  public function getRecord()
  {
    return $this->record;
  }
  /**
   * These are details about automated normalization actions that were taken in
   * order to make the requested `url_pattern` valid.
   *
   * @param UrlNormalization $urlNormalizationDetails
   */
  public function setUrlNormalizationDetails(UrlNormalization $urlNormalizationDetails)
  {
    $this->urlNormalizationDetails = $urlNormalizationDetails;
  }
  /**
   * @return UrlNormalization
   */
  public function getUrlNormalizationDetails()
  {
    return $this->urlNormalizationDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryResponse::class, 'Google_Service_ChromeUXReport_QueryResponse');
