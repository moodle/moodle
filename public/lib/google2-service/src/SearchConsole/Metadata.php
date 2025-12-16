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

namespace Google\Service\SearchConsole;

class Metadata extends \Google\Model
{
  /**
   * The first date for which the data is still being collected and processed,
   * presented in `YYYY-MM-DD` format (ISO-8601 extended local date format).
   * This field is populated only when the request's `dataState` is "`all`",
   * data is grouped by "`DATE`", and the requested date range contains
   * incomplete data points. All values after the `first_incomplete_date` may
   * still change noticeably.
   *
   * @var string
   */
  public $firstIncompleteDate;
  /**
   * The first hour for which the data is still being collected and processed,
   * presented in `YYYY-MM-DDThh:mm:ss[+|-]hh:mm` format (ISO-8601 extended
   * offset date-time format). This field is populated only when the request's
   * `dataState` is "`hourly_all`", data is grouped by "`HOUR`" and the
   * requested date range contains incomplete data points. All values after the
   * `first_incomplete_hour` may still change noticeably.
   *
   * @var string
   */
  public $firstIncompleteHour;

  /**
   * The first date for which the data is still being collected and processed,
   * presented in `YYYY-MM-DD` format (ISO-8601 extended local date format).
   * This field is populated only when the request's `dataState` is "`all`",
   * data is grouped by "`DATE`", and the requested date range contains
   * incomplete data points. All values after the `first_incomplete_date` may
   * still change noticeably.
   *
   * @param string $firstIncompleteDate
   */
  public function setFirstIncompleteDate($firstIncompleteDate)
  {
    $this->firstIncompleteDate = $firstIncompleteDate;
  }
  /**
   * @return string
   */
  public function getFirstIncompleteDate()
  {
    return $this->firstIncompleteDate;
  }
  /**
   * The first hour for which the data is still being collected and processed,
   * presented in `YYYY-MM-DDThh:mm:ss[+|-]hh:mm` format (ISO-8601 extended
   * offset date-time format). This field is populated only when the request's
   * `dataState` is "`hourly_all`", data is grouped by "`HOUR`" and the
   * requested date range contains incomplete data points. All values after the
   * `first_incomplete_hour` may still change noticeably.
   *
   * @param string $firstIncompleteHour
   */
  public function setFirstIncompleteHour($firstIncompleteHour)
  {
    $this->firstIncompleteHour = $firstIncompleteHour;
  }
  /**
   * @return string
   */
  public function getFirstIncompleteHour()
  {
    return $this->firstIncompleteHour;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Metadata::class, 'Google_Service_SearchConsole_Metadata');
