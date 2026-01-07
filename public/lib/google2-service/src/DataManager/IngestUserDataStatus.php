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

namespace Google\Service\DataManager;

class IngestUserDataStatus extends \Google\Model
{
  /**
   * The match rate range is unknown.
   */
  public const UPLOAD_MATCH_RATE_RANGE_MATCH_RATE_RANGE_UNKNOWN = 'MATCH_RATE_RANGE_UNKNOWN';
  /**
   * The match rate range is not eligible.
   */
  public const UPLOAD_MATCH_RATE_RANGE_MATCH_RATE_RANGE_NOT_ELIGIBLE = 'MATCH_RATE_RANGE_NOT_ELIGIBLE';
  /**
   * The match rate range is less than 20% (in the interval `[0, 20)`).
   */
  public const UPLOAD_MATCH_RATE_RANGE_MATCH_RATE_RANGE_LESS_THAN_20 = 'MATCH_RATE_RANGE_LESS_THAN_20';
  /**
   * The match rate range is between 20% and 30% (in the interval `[20, 31)`).
   */
  public const UPLOAD_MATCH_RATE_RANGE_MATCH_RATE_RANGE_20_TO_30 = 'MATCH_RATE_RANGE_20_TO_30';
  /**
   * The match rate range is between 31% and 40% (in the interval `[31, 41)`).
   */
  public const UPLOAD_MATCH_RATE_RANGE_MATCH_RATE_RANGE_31_TO_40 = 'MATCH_RATE_RANGE_31_TO_40';
  /**
   * The match rate range is between 41% and 50% (in the interval `[41, 51)`).
   */
  public const UPLOAD_MATCH_RATE_RANGE_MATCH_RATE_RANGE_41_TO_50 = 'MATCH_RATE_RANGE_41_TO_50';
  /**
   * The match rate range is between 51% and 60% (in the interval `[51, 61)`.
   */
  public const UPLOAD_MATCH_RATE_RANGE_MATCH_RATE_RANGE_51_TO_60 = 'MATCH_RATE_RANGE_51_TO_60';
  /**
   * The match rate range is between 61% and 70% (in the interval `[61, 71)`).
   */
  public const UPLOAD_MATCH_RATE_RANGE_MATCH_RATE_RANGE_61_TO_70 = 'MATCH_RATE_RANGE_61_TO_70';
  /**
   * The match rate range is between 71% and 80% (in the interval `[71, 81)`).
   */
  public const UPLOAD_MATCH_RATE_RANGE_MATCH_RATE_RANGE_71_TO_80 = 'MATCH_RATE_RANGE_71_TO_80';
  /**
   * The match rate range is between 81% and 90% (in the interval `[81, 91)`).
   */
  public const UPLOAD_MATCH_RATE_RANGE_MATCH_RATE_RANGE_81_TO_90 = 'MATCH_RATE_RANGE_81_TO_90';
  /**
   * The match rate range is between 91% and 100% (in the interval `[91, 100]`).
   */
  public const UPLOAD_MATCH_RATE_RANGE_MATCH_RATE_RANGE_91_TO_100 = 'MATCH_RATE_RANGE_91_TO_100';
  /**
   * The total count of audience members sent in the upload request for the
   * destination. Includes all audience members in the request, regardless of
   * whether they were successfully ingested or not.
   *
   * @var string
   */
  public $recordCount;
  /**
   * The match rate range of the upload.
   *
   * @var string
   */
  public $uploadMatchRateRange;
  /**
   * The total count of user identifiers sent in the upload request for the
   * destination. Includes all user identifiers in the request, regardless of
   * whether they were successfully ingested or not.
   *
   * @var string
   */
  public $userIdentifierCount;

  /**
   * The total count of audience members sent in the upload request for the
   * destination. Includes all audience members in the request, regardless of
   * whether they were successfully ingested or not.
   *
   * @param string $recordCount
   */
  public function setRecordCount($recordCount)
  {
    $this->recordCount = $recordCount;
  }
  /**
   * @return string
   */
  public function getRecordCount()
  {
    return $this->recordCount;
  }
  /**
   * The match rate range of the upload.
   *
   * Accepted values: MATCH_RATE_RANGE_UNKNOWN, MATCH_RATE_RANGE_NOT_ELIGIBLE,
   * MATCH_RATE_RANGE_LESS_THAN_20, MATCH_RATE_RANGE_20_TO_30,
   * MATCH_RATE_RANGE_31_TO_40, MATCH_RATE_RANGE_41_TO_50,
   * MATCH_RATE_RANGE_51_TO_60, MATCH_RATE_RANGE_61_TO_70,
   * MATCH_RATE_RANGE_71_TO_80, MATCH_RATE_RANGE_81_TO_90,
   * MATCH_RATE_RANGE_91_TO_100
   *
   * @param self::UPLOAD_MATCH_RATE_RANGE_* $uploadMatchRateRange
   */
  public function setUploadMatchRateRange($uploadMatchRateRange)
  {
    $this->uploadMatchRateRange = $uploadMatchRateRange;
  }
  /**
   * @return self::UPLOAD_MATCH_RATE_RANGE_*
   */
  public function getUploadMatchRateRange()
  {
    return $this->uploadMatchRateRange;
  }
  /**
   * The total count of user identifiers sent in the upload request for the
   * destination. Includes all user identifiers in the request, regardless of
   * whether they were successfully ingested or not.
   *
   * @param string $userIdentifierCount
   */
  public function setUserIdentifierCount($userIdentifierCount)
  {
    $this->userIdentifierCount = $userIdentifierCount;
  }
  /**
   * @return string
   */
  public function getUserIdentifierCount()
  {
    return $this->userIdentifierCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IngestUserDataStatus::class, 'Google_Service_DataManager_IngestUserDataStatus');
