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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2ListLabelLocksResponse extends \Google\Collection
{
  protected $collection_key = 'labelLocks';
  protected $labelLocksType = GoogleAppsDriveLabelsV2LabelLock::class;
  protected $labelLocksDataType = 'array';
  /**
   * The token of the next page in the response.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Label locks.
   *
   * @param GoogleAppsDriveLabelsV2LabelLock[] $labelLocks
   */
  public function setLabelLocks($labelLocks)
  {
    $this->labelLocks = $labelLocks;
  }
  /**
   * @return GoogleAppsDriveLabelsV2LabelLock[]
   */
  public function getLabelLocks()
  {
    return $this->labelLocks;
  }
  /**
   * The token of the next page in the response.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2ListLabelLocksResponse::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2ListLabelLocksResponse');
