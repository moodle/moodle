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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1DateRange extends \Google\Model
{
  /**
   * Required. End date (exclusive) of the data to export in the format `yyyy-
   * mm-dd`. The date range ends at 00:00:00 UTC on the end date- which will not
   * be in the output.
   *
   * @var string
   */
  public $end;
  /**
   * Required. Start date of the data to export in the format `yyyy-mm-dd`. The
   * date range begins at 00:00:00 UTC on the start date.
   *
   * @var string
   */
  public $start;

  /**
   * Required. End date (exclusive) of the data to export in the format `yyyy-
   * mm-dd`. The date range ends at 00:00:00 UTC on the end date- which will not
   * be in the output.
   *
   * @param string $end
   */
  public function setEnd($end)
  {
    $this->end = $end;
  }
  /**
   * @return string
   */
  public function getEnd()
  {
    return $this->end;
  }
  /**
   * Required. Start date of the data to export in the format `yyyy-mm-dd`. The
   * date range begins at 00:00:00 UTC on the start date.
   *
   * @param string $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }
  /**
   * @return string
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1DateRange::class, 'Google_Service_Apigee_GoogleCloudApigeeV1DateRange');
