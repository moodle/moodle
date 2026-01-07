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

namespace Google\Service\DisplayVideo;

class FirstPartyAndPartnerAudienceTargetingSetting extends \Google\Model
{
  /**
   * No limit of recency.
   */
  public const RECENCY_RECENCY_NO_LIMIT = 'RECENCY_NO_LIMIT';
  /**
   * Recency is 1 minute.
   */
  public const RECENCY_RECENCY_1_MINUTE = 'RECENCY_1_MINUTE';
  /**
   * Recency is 5 minutes.
   */
  public const RECENCY_RECENCY_5_MINUTES = 'RECENCY_5_MINUTES';
  /**
   * Recency is 10 minutes.
   */
  public const RECENCY_RECENCY_10_MINUTES = 'RECENCY_10_MINUTES';
  /**
   * Recency is 15 minutes.
   */
  public const RECENCY_RECENCY_15_MINUTES = 'RECENCY_15_MINUTES';
  /**
   * Recency is 30 minutes.
   */
  public const RECENCY_RECENCY_30_MINUTES = 'RECENCY_30_MINUTES';
  /**
   * Recency is 1 hour.
   */
  public const RECENCY_RECENCY_1_HOUR = 'RECENCY_1_HOUR';
  /**
   * Recency is 2 hours.
   */
  public const RECENCY_RECENCY_2_HOURS = 'RECENCY_2_HOURS';
  /**
   * Recency is 3 hours.
   */
  public const RECENCY_RECENCY_3_HOURS = 'RECENCY_3_HOURS';
  /**
   * Recency is 6 hours.
   */
  public const RECENCY_RECENCY_6_HOURS = 'RECENCY_6_HOURS';
  /**
   * Recency is 12 hours.
   */
  public const RECENCY_RECENCY_12_HOURS = 'RECENCY_12_HOURS';
  /**
   * Recency is 1 day.
   */
  public const RECENCY_RECENCY_1_DAY = 'RECENCY_1_DAY';
  /**
   * Recency is 2 days.
   */
  public const RECENCY_RECENCY_2_DAYS = 'RECENCY_2_DAYS';
  /**
   * Recency is 3 days.
   */
  public const RECENCY_RECENCY_3_DAYS = 'RECENCY_3_DAYS';
  /**
   * Recency is 5 days.
   */
  public const RECENCY_RECENCY_5_DAYS = 'RECENCY_5_DAYS';
  /**
   * Recency is 7 days.
   */
  public const RECENCY_RECENCY_7_DAYS = 'RECENCY_7_DAYS';
  /**
   * Recency is 10 days.
   */
  public const RECENCY_RECENCY_10_DAYS = 'RECENCY_10_DAYS';
  /**
   * Recency is 14 days.
   */
  public const RECENCY_RECENCY_14_DAYS = 'RECENCY_14_DAYS';
  /**
   * Recency is 15 days.
   */
  public const RECENCY_RECENCY_15_DAYS = 'RECENCY_15_DAYS';
  /**
   * Recency is 21 days.
   */
  public const RECENCY_RECENCY_21_DAYS = 'RECENCY_21_DAYS';
  /**
   * Recency is 28 days.
   */
  public const RECENCY_RECENCY_28_DAYS = 'RECENCY_28_DAYS';
  /**
   * Recency is 30 days.
   */
  public const RECENCY_RECENCY_30_DAYS = 'RECENCY_30_DAYS';
  /**
   * Recency is 40 days.
   */
  public const RECENCY_RECENCY_40_DAYS = 'RECENCY_40_DAYS';
  /**
   * Recency is 45 days.
   */
  public const RECENCY_RECENCY_45_DAYS = 'RECENCY_45_DAYS';
  /**
   * Recency is 60 days.
   */
  public const RECENCY_RECENCY_60_DAYS = 'RECENCY_60_DAYS';
  /**
   * Recency is 90 days.
   */
  public const RECENCY_RECENCY_90_DAYS = 'RECENCY_90_DAYS';
  /**
   * Recency is 120 days.
   */
  public const RECENCY_RECENCY_120_DAYS = 'RECENCY_120_DAYS';
  /**
   * Recency is 180 days.
   */
  public const RECENCY_RECENCY_180_DAYS = 'RECENCY_180_DAYS';
  /**
   * Recency is 270 days.
   */
  public const RECENCY_RECENCY_270_DAYS = 'RECENCY_270_DAYS';
  /**
   * Recency is 365 days.
   */
  public const RECENCY_RECENCY_365_DAYS = 'RECENCY_365_DAYS';
  /**
   * Required. First party and partner audience id of the first party and
   * partner audience targeting setting. This id is
   * first_party_and_partner_audience_id.
   *
   * @var string
   */
  public $firstPartyAndPartnerAudienceId;
  /**
   * Required. The recency of the first party and partner audience targeting
   * setting. Only applicable to first party audiences, otherwise will be
   * ignored. For more info, refer to
   * https://support.google.com/displayvideo/answer/2949947#recency When
   * unspecified, no recency limit will be used.
   *
   * @var string
   */
  public $recency;

  /**
   * Required. First party and partner audience id of the first party and
   * partner audience targeting setting. This id is
   * first_party_and_partner_audience_id.
   *
   * @param string $firstPartyAndPartnerAudienceId
   */
  public function setFirstPartyAndPartnerAudienceId($firstPartyAndPartnerAudienceId)
  {
    $this->firstPartyAndPartnerAudienceId = $firstPartyAndPartnerAudienceId;
  }
  /**
   * @return string
   */
  public function getFirstPartyAndPartnerAudienceId()
  {
    return $this->firstPartyAndPartnerAudienceId;
  }
  /**
   * Required. The recency of the first party and partner audience targeting
   * setting. Only applicable to first party audiences, otherwise will be
   * ignored. For more info, refer to
   * https://support.google.com/displayvideo/answer/2949947#recency When
   * unspecified, no recency limit will be used.
   *
   * Accepted values: RECENCY_NO_LIMIT, RECENCY_1_MINUTE, RECENCY_5_MINUTES,
   * RECENCY_10_MINUTES, RECENCY_15_MINUTES, RECENCY_30_MINUTES, RECENCY_1_HOUR,
   * RECENCY_2_HOURS, RECENCY_3_HOURS, RECENCY_6_HOURS, RECENCY_12_HOURS,
   * RECENCY_1_DAY, RECENCY_2_DAYS, RECENCY_3_DAYS, RECENCY_5_DAYS,
   * RECENCY_7_DAYS, RECENCY_10_DAYS, RECENCY_14_DAYS, RECENCY_15_DAYS,
   * RECENCY_21_DAYS, RECENCY_28_DAYS, RECENCY_30_DAYS, RECENCY_40_DAYS,
   * RECENCY_45_DAYS, RECENCY_60_DAYS, RECENCY_90_DAYS, RECENCY_120_DAYS,
   * RECENCY_180_DAYS, RECENCY_270_DAYS, RECENCY_365_DAYS
   *
   * @param self::RECENCY_* $recency
   */
  public function setRecency($recency)
  {
    $this->recency = $recency;
  }
  /**
   * @return self::RECENCY_*
   */
  public function getRecency()
  {
    return $this->recency;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirstPartyAndPartnerAudienceTargetingSetting::class, 'Google_Service_DisplayVideo_FirstPartyAndPartnerAudienceTargetingSetting');
