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

namespace Google\Service\Compute;

class CalendarModeAdviceRequest extends \Google\Model
{
  protected $futureResourcesSpecsType = FutureResourcesSpec::class;
  protected $futureResourcesSpecsDataType = 'map';

  /**
   * Specification of resources to create in the future. The key of the map is
   * an arbitrary string specified by the caller. Value of the map is a
   * specification of required resources and their constraints. Currently only
   * one value is allowed in this map.
   *
   * @param FutureResourcesSpec[] $futureResourcesSpecs
   */
  public function setFutureResourcesSpecs($futureResourcesSpecs)
  {
    $this->futureResourcesSpecs = $futureResourcesSpecs;
  }
  /**
   * @return FutureResourcesSpec[]
   */
  public function getFutureResourcesSpecs()
  {
    return $this->futureResourcesSpecs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CalendarModeAdviceRequest::class, 'Google_Service_Compute_CalendarModeAdviceRequest');
