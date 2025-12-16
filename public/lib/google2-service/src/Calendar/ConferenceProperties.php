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

namespace Google\Service\Calendar;

class ConferenceProperties extends \Google\Collection
{
  protected $collection_key = 'allowedConferenceSolutionTypes';
  /**
   * The types of conference solutions that are supported for this calendar. The
   * possible values are:   - "eventHangout"  - "eventNamedHangout"  -
   * "hangoutsMeet"  Optional.
   *
   * @var string[]
   */
  public $allowedConferenceSolutionTypes;

  /**
   * The types of conference solutions that are supported for this calendar. The
   * possible values are:   - "eventHangout"  - "eventNamedHangout"  -
   * "hangoutsMeet"  Optional.
   *
   * @param string[] $allowedConferenceSolutionTypes
   */
  public function setAllowedConferenceSolutionTypes($allowedConferenceSolutionTypes)
  {
    $this->allowedConferenceSolutionTypes = $allowedConferenceSolutionTypes;
  }
  /**
   * @return string[]
   */
  public function getAllowedConferenceSolutionTypes()
  {
    return $this->allowedConferenceSolutionTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConferenceProperties::class, 'Google_Service_Calendar_ConferenceProperties');
