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

namespace Google\Service\Walletobjects;

class DateTime extends \Google\Model
{
  /**
   * An ISO 8601 extended format date/time. Offset may or may not be required
   * (refer to the parent field's documentation). Time may be specified up to
   * nanosecond precision. Offsets may be specified with seconds precision (even
   * though offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the
   * date/time is intended for a physical location in New York, this would be
   * the equivalent of Eastern Daylight Time (EDT). Remember that offset varies
   * in regions that observe Daylight Saving Time (or Summer Time), depending on
   * the time of the year. `1985-04-12T19:20:50.52` would be 20 minutes and
   * 50.52 seconds after the 19th hour of April 12th, 1985 with no offset
   * information. Providing an offset makes this an absolute instant in time
   * around the world. The date/time will be adjusted based on the user's time
   * zone. For example, a time of `2018-06-19T18:30:00-04:00` will be 18:30:00
   * for a user in New York and 15:30:00 for a user in Los Angeles. Omitting the
   * offset makes this a local date/time, representing several instants in time
   * around the world. The date/time will always be in the user's current time
   * zone. For example, a time of `2018-06-19T18:30:00` will be 18:30:00 for a
   * user in New York and also 18:30:00 for a user in Los Angeles. This is
   * useful when the same local date/time should apply to many physical
   * locations across several time zones.
   *
   * @var string
   */
  public $date;

  /**
   * An ISO 8601 extended format date/time. Offset may or may not be required
   * (refer to the parent field's documentation). Time may be specified up to
   * nanosecond precision. Offsets may be specified with seconds precision (even
   * though offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the
   * date/time is intended for a physical location in New York, this would be
   * the equivalent of Eastern Daylight Time (EDT). Remember that offset varies
   * in regions that observe Daylight Saving Time (or Summer Time), depending on
   * the time of the year. `1985-04-12T19:20:50.52` would be 20 minutes and
   * 50.52 seconds after the 19th hour of April 12th, 1985 with no offset
   * information. Providing an offset makes this an absolute instant in time
   * around the world. The date/time will be adjusted based on the user's time
   * zone. For example, a time of `2018-06-19T18:30:00-04:00` will be 18:30:00
   * for a user in New York and 15:30:00 for a user in Los Angeles. Omitting the
   * offset makes this a local date/time, representing several instants in time
   * around the world. The date/time will always be in the user's current time
   * zone. For example, a time of `2018-06-19T18:30:00` will be 18:30:00 for a
   * user in New York and also 18:30:00 for a user in Los Angeles. This is
   * useful when the same local date/time should apply to many physical
   * locations across several time zones.
   *
   * @param string $date
   */
  public function setDate($date)
  {
    $this->date = $date;
  }
  /**
   * @return string
   */
  public function getDate()
  {
    return $this->date;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DateTime::class, 'Google_Service_Walletobjects_DateTime');
