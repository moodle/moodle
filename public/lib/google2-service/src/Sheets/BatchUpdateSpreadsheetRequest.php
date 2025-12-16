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

namespace Google\Service\Sheets;

class BatchUpdateSpreadsheetRequest extends \Google\Collection
{
  protected $collection_key = 'responseRanges';
  /**
   * Determines if the update response should include the spreadsheet resource.
   *
   * @var bool
   */
  public $includeSpreadsheetInResponse;
  protected $requestsType = Request::class;
  protected $requestsDataType = 'array';
  /**
   * True if grid data should be returned. Meaningful only if
   * include_spreadsheet_in_response is 'true'. This parameter is ignored if a
   * field mask was set in the request.
   *
   * @var bool
   */
  public $responseIncludeGridData;
  /**
   * Limits the ranges included in the response spreadsheet. Meaningful only if
   * include_spreadsheet_in_response is 'true'.
   *
   * @var string[]
   */
  public $responseRanges;

  /**
   * Determines if the update response should include the spreadsheet resource.
   *
   * @param bool $includeSpreadsheetInResponse
   */
  public function setIncludeSpreadsheetInResponse($includeSpreadsheetInResponse)
  {
    $this->includeSpreadsheetInResponse = $includeSpreadsheetInResponse;
  }
  /**
   * @return bool
   */
  public function getIncludeSpreadsheetInResponse()
  {
    return $this->includeSpreadsheetInResponse;
  }
  /**
   * A list of updates to apply to the spreadsheet. Requests will be applied in
   * the order they are specified. If any request is not valid, no requests will
   * be applied.
   *
   * @param Request[] $requests
   */
  public function setRequests($requests)
  {
    $this->requests = $requests;
  }
  /**
   * @return Request[]
   */
  public function getRequests()
  {
    return $this->requests;
  }
  /**
   * True if grid data should be returned. Meaningful only if
   * include_spreadsheet_in_response is 'true'. This parameter is ignored if a
   * field mask was set in the request.
   *
   * @param bool $responseIncludeGridData
   */
  public function setResponseIncludeGridData($responseIncludeGridData)
  {
    $this->responseIncludeGridData = $responseIncludeGridData;
  }
  /**
   * @return bool
   */
  public function getResponseIncludeGridData()
  {
    return $this->responseIncludeGridData;
  }
  /**
   * Limits the ranges included in the response spreadsheet. Meaningful only if
   * include_spreadsheet_in_response is 'true'.
   *
   * @param string[] $responseRanges
   */
  public function setResponseRanges($responseRanges)
  {
    $this->responseRanges = $responseRanges;
  }
  /**
   * @return string[]
   */
  public function getResponseRanges()
  {
    return $this->responseRanges;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchUpdateSpreadsheetRequest::class, 'Google_Service_Sheets_BatchUpdateSpreadsheetRequest');
