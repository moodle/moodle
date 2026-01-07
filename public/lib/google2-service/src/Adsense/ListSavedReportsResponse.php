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

namespace Google\Service\Adsense;

class ListSavedReportsResponse extends \Google\Collection
{
  protected $collection_key = 'savedReports';
  /**
   * Continuation token used to page through reports. To retrieve the next page
   * of the results, set the next request's "page_token" value to this.
   *
   * @var string
   */
  public $nextPageToken;
  protected $savedReportsType = SavedReport::class;
  protected $savedReportsDataType = 'array';

  /**
   * Continuation token used to page through reports. To retrieve the next page
   * of the results, set the next request's "page_token" value to this.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * The reports returned in this list response.
   *
   * @param SavedReport[] $savedReports
   */
  public function setSavedReports($savedReports)
  {
    $this->savedReports = $savedReports;
  }
  /**
   * @return SavedReport[]
   */
  public function getSavedReports()
  {
    return $this->savedReports;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListSavedReportsResponse::class, 'Google_Service_Adsense_ListSavedReportsResponse');
