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

namespace Google\Service\AnalyticsData;

class MinuteRange extends \Google\Model
{
  /**
   * The inclusive end minute for the query as a number of minutes before now.
   * Cannot be before `startMinutesAgo`. For example, `"endMinutesAgo": 15`
   * specifies the report should include event data from prior to 15 minutes
   * ago. If unspecified, `endMinutesAgo` is defaulted to 0. Standard Analytics
   * properties can request any minute in the last 30 minutes of event data
   * (`endMinutesAgo <= 29`), and 360 Analytics properties can request any
   * minute in the last 60 minutes of event data (`endMinutesAgo <= 59`).
   *
   * @var int
   */
  public $endMinutesAgo;
  /**
   * Assigns a name to this minute range. The dimension `dateRange` is valued to
   * this name in a report response. If set, cannot begin with `date_range_` or
   * `RESERVED_`. If not set, minute ranges are named by their zero based index
   * in the request: `date_range_0`, `date_range_1`, etc.
   *
   * @var string
   */
  public $name;
  /**
   * The inclusive start minute for the query as a number of minutes before now.
   * For example, `"startMinutesAgo": 29` specifies the report should include
   * event data from 29 minutes ago and after. Cannot be after `endMinutesAgo`.
   * If unspecified, `startMinutesAgo` is defaulted to 29. Standard Analytics
   * properties can request up to the last 30 minutes of event data
   * (`startMinutesAgo <= 29`), and 360 Analytics properties can request up to
   * the last 60 minutes of event data (`startMinutesAgo <= 59`).
   *
   * @var int
   */
  public $startMinutesAgo;

  /**
   * The inclusive end minute for the query as a number of minutes before now.
   * Cannot be before `startMinutesAgo`. For example, `"endMinutesAgo": 15`
   * specifies the report should include event data from prior to 15 minutes
   * ago. If unspecified, `endMinutesAgo` is defaulted to 0. Standard Analytics
   * properties can request any minute in the last 30 minutes of event data
   * (`endMinutesAgo <= 29`), and 360 Analytics properties can request any
   * minute in the last 60 minutes of event data (`endMinutesAgo <= 59`).
   *
   * @param int $endMinutesAgo
   */
  public function setEndMinutesAgo($endMinutesAgo)
  {
    $this->endMinutesAgo = $endMinutesAgo;
  }
  /**
   * @return int
   */
  public function getEndMinutesAgo()
  {
    return $this->endMinutesAgo;
  }
  /**
   * Assigns a name to this minute range. The dimension `dateRange` is valued to
   * this name in a report response. If set, cannot begin with `date_range_` or
   * `RESERVED_`. If not set, minute ranges are named by their zero based index
   * in the request: `date_range_0`, `date_range_1`, etc.
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
   * The inclusive start minute for the query as a number of minutes before now.
   * For example, `"startMinutesAgo": 29` specifies the report should include
   * event data from 29 minutes ago and after. Cannot be after `endMinutesAgo`.
   * If unspecified, `startMinutesAgo` is defaulted to 29. Standard Analytics
   * properties can request up to the last 30 minutes of event data
   * (`startMinutesAgo <= 29`), and 360 Analytics properties can request up to
   * the last 60 minutes of event data (`startMinutesAgo <= 59`).
   *
   * @param int $startMinutesAgo
   */
  public function setStartMinutesAgo($startMinutesAgo)
  {
    $this->startMinutesAgo = $startMinutesAgo;
  }
  /**
   * @return int
   */
  public function getStartMinutesAgo()
  {
    return $this->startMinutesAgo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MinuteRange::class, 'Google_Service_AnalyticsData_MinuteRange');
