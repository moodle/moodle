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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaDataRetentionSettings extends \Google\Model
{
  /**
   * Data retention time duration is not specified.
   */
  public const EVENT_DATA_RETENTION_RETENTION_DURATION_UNSPECIFIED = 'RETENTION_DURATION_UNSPECIFIED';
  /**
   * The data retention time duration is 2 months.
   */
  public const EVENT_DATA_RETENTION_TWO_MONTHS = 'TWO_MONTHS';
  /**
   * The data retention time duration is 14 months.
   */
  public const EVENT_DATA_RETENTION_FOURTEEN_MONTHS = 'FOURTEEN_MONTHS';
  /**
   * The data retention time duration is 26 months. Available to 360 properties
   * only. Available for event data only.
   */
  public const EVENT_DATA_RETENTION_TWENTY_SIX_MONTHS = 'TWENTY_SIX_MONTHS';
  /**
   * The data retention time duration is 38 months. Available to 360 properties
   * only. Available for event data only.
   */
  public const EVENT_DATA_RETENTION_THIRTY_EIGHT_MONTHS = 'THIRTY_EIGHT_MONTHS';
  /**
   * The data retention time duration is 50 months. Available to 360 properties
   * only. Available for event data only.
   */
  public const EVENT_DATA_RETENTION_FIFTY_MONTHS = 'FIFTY_MONTHS';
  /**
   * Data retention time duration is not specified.
   */
  public const USER_DATA_RETENTION_RETENTION_DURATION_UNSPECIFIED = 'RETENTION_DURATION_UNSPECIFIED';
  /**
   * The data retention time duration is 2 months.
   */
  public const USER_DATA_RETENTION_TWO_MONTHS = 'TWO_MONTHS';
  /**
   * The data retention time duration is 14 months.
   */
  public const USER_DATA_RETENTION_FOURTEEN_MONTHS = 'FOURTEEN_MONTHS';
  /**
   * The data retention time duration is 26 months. Available to 360 properties
   * only. Available for event data only.
   */
  public const USER_DATA_RETENTION_TWENTY_SIX_MONTHS = 'TWENTY_SIX_MONTHS';
  /**
   * The data retention time duration is 38 months. Available to 360 properties
   * only. Available for event data only.
   */
  public const USER_DATA_RETENTION_THIRTY_EIGHT_MONTHS = 'THIRTY_EIGHT_MONTHS';
  /**
   * The data retention time duration is 50 months. Available to 360 properties
   * only. Available for event data only.
   */
  public const USER_DATA_RETENTION_FIFTY_MONTHS = 'FIFTY_MONTHS';
  /**
   * Required. The length of time that event-level data is retained.
   *
   * @var string
   */
  public $eventDataRetention;
  /**
   * Output only. Resource name for this DataRetentionSetting resource. Format:
   * properties/{property}/dataRetentionSettings
   *
   * @var string
   */
  public $name;
  /**
   * If true, reset the retention period for the user identifier with every
   * event from that user.
   *
   * @var bool
   */
  public $resetUserDataOnNewActivity;
  /**
   * Required. The length of time that user-level data is retained.
   *
   * @var string
   */
  public $userDataRetention;

  /**
   * Required. The length of time that event-level data is retained.
   *
   * Accepted values: RETENTION_DURATION_UNSPECIFIED, TWO_MONTHS,
   * FOURTEEN_MONTHS, TWENTY_SIX_MONTHS, THIRTY_EIGHT_MONTHS, FIFTY_MONTHS
   *
   * @param self::EVENT_DATA_RETENTION_* $eventDataRetention
   */
  public function setEventDataRetention($eventDataRetention)
  {
    $this->eventDataRetention = $eventDataRetention;
  }
  /**
   * @return self::EVENT_DATA_RETENTION_*
   */
  public function getEventDataRetention()
  {
    return $this->eventDataRetention;
  }
  /**
   * Output only. Resource name for this DataRetentionSetting resource. Format:
   * properties/{property}/dataRetentionSettings
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * If true, reset the retention period for the user identifier with every
   * event from that user.
   *
   * @param bool $resetUserDataOnNewActivity
   */
  public function setResetUserDataOnNewActivity($resetUserDataOnNewActivity)
  {
    $this->resetUserDataOnNewActivity = $resetUserDataOnNewActivity;
  }
  /**
   * @return bool
   */
  public function getResetUserDataOnNewActivity()
  {
    return $this->resetUserDataOnNewActivity;
  }
  /**
   * Required. The length of time that user-level data is retained.
   *
   * Accepted values: RETENTION_DURATION_UNSPECIFIED, TWO_MONTHS,
   * FOURTEEN_MONTHS, TWENTY_SIX_MONTHS, THIRTY_EIGHT_MONTHS, FIFTY_MONTHS
   *
   * @param self::USER_DATA_RETENTION_* $userDataRetention
   */
  public function setUserDataRetention($userDataRetention)
  {
    $this->userDataRetention = $userDataRetention;
  }
  /**
   * @return self::USER_DATA_RETENTION_*
   */
  public function getUserDataRetention()
  {
    return $this->userDataRetention;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaDataRetentionSettings::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaDataRetentionSettings');
