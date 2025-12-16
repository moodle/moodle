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

namespace Google\Service\BigtableAdmin;

class ChangeStreamConfig extends \Google\Model
{
  /**
   * How long the change stream should be retained. Change stream data older
   * than the retention period will not be returned when reading the change
   * stream from the table. Values must be at least 1 day and at most 7 days,
   * and will be truncated to microsecond granularity.
   *
   * @var string
   */
  public $retentionPeriod;

  /**
   * How long the change stream should be retained. Change stream data older
   * than the retention period will not be returned when reading the change
   * stream from the table. Values must be at least 1 day and at most 7 days,
   * and will be truncated to microsecond granularity.
   *
   * @param string $retentionPeriod
   */
  public function setRetentionPeriod($retentionPeriod)
  {
    $this->retentionPeriod = $retentionPeriod;
  }
  /**
   * @return string
   */
  public function getRetentionPeriod()
  {
    return $this->retentionPeriod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChangeStreamConfig::class, 'Google_Service_BigtableAdmin_ChangeStreamConfig');
