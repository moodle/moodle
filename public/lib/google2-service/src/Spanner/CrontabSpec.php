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

namespace Google\Service\Spanner;

class CrontabSpec extends \Google\Model
{
  /**
   * Output only. Scheduled backups contain an externally consistent copy of the
   * database at the version time specified in `schedule_spec.cron_spec`.
   * However, Spanner might not initiate the creation of the scheduled backups
   * at that version time. Spanner initiates the creation of scheduled backups
   * within the time window bounded by the version_time specified in
   * `schedule_spec.cron_spec` and version_time + `creation_window`.
   *
   * @var string
   */
  public $creationWindow;
  /**
   * Required. Textual representation of the crontab. User can customize the
   * backup frequency and the backup version time using the cron expression. The
   * version time must be in UTC timezone. The backup will contain an externally
   * consistent copy of the database at the version time. Full backups must be
   * scheduled a minimum of 12 hours apart and incremental backups must be
   * scheduled a minimum of 4 hours apart. Examples of valid cron
   * specifications: * `0 2/12 * * *` : every 12 hours at (2, 14) hours past
   * midnight in UTC. * `0 2,14 * * *` : every 12 hours at (2, 14) hours past
   * midnight in UTC. * `0 4 * * *` : (incremental backups only) every 4 hours
   * at (0, 4, 8, 12, 16, 20) hours past midnight in UTC. * `0 2 * * *` : once a
   * day at 2 past midnight in UTC. * `0 2 * * 0` : once a week every Sunday at
   * 2 past midnight in UTC. * `0 2 8 * *` : once a month on 8th day at 2 past
   * midnight in UTC.
   *
   * @var string
   */
  public $text;
  /**
   * Output only. The time zone of the times in `CrontabSpec.text`. Currently,
   * only UTC is supported.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Output only. Scheduled backups contain an externally consistent copy of the
   * database at the version time specified in `schedule_spec.cron_spec`.
   * However, Spanner might not initiate the creation of the scheduled backups
   * at that version time. Spanner initiates the creation of scheduled backups
   * within the time window bounded by the version_time specified in
   * `schedule_spec.cron_spec` and version_time + `creation_window`.
   *
   * @param string $creationWindow
   */
  public function setCreationWindow($creationWindow)
  {
    $this->creationWindow = $creationWindow;
  }
  /**
   * @return string
   */
  public function getCreationWindow()
  {
    return $this->creationWindow;
  }
  /**
   * Required. Textual representation of the crontab. User can customize the
   * backup frequency and the backup version time using the cron expression. The
   * version time must be in UTC timezone. The backup will contain an externally
   * consistent copy of the database at the version time. Full backups must be
   * scheduled a minimum of 12 hours apart and incremental backups must be
   * scheduled a minimum of 4 hours apart. Examples of valid cron
   * specifications: * `0 2/12 * * *` : every 12 hours at (2, 14) hours past
   * midnight in UTC. * `0 2,14 * * *` : every 12 hours at (2, 14) hours past
   * midnight in UTC. * `0 4 * * *` : (incremental backups only) every 4 hours
   * at (0, 4, 8, 12, 16, 20) hours past midnight in UTC. * `0 2 * * *` : once a
   * day at 2 past midnight in UTC. * `0 2 * * 0` : once a week every Sunday at
   * 2 past midnight in UTC. * `0 2 8 * *` : once a month on 8th day at 2 past
   * midnight in UTC.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * Output only. The time zone of the times in `CrontabSpec.text`. Currently,
   * only UTC is supported.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CrontabSpec::class, 'Google_Service_Spanner_CrontabSpec');
