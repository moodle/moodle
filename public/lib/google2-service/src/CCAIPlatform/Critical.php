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

namespace Google\Service\CCAIPlatform;

class Critical extends \Google\Collection
{
  protected $collection_key = 'peakHours';
  protected $peakHoursType = WeeklySchedule::class;
  protected $peakHoursDataType = 'array';

  /**
   * Required. Hours during which the instance should not be updated.
   *
   * @param WeeklySchedule[] $peakHours
   */
  public function setPeakHours($peakHours)
  {
    $this->peakHours = $peakHours;
  }
  /**
   * @return WeeklySchedule[]
   */
  public function getPeakHours()
  {
    return $this->peakHours;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Critical::class, 'Google_Service_CCAIPlatform_Critical');
