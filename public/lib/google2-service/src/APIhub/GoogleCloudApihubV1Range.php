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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1Range extends \Google\Model
{
  protected $endType = GoogleCloudApihubV1Point::class;
  protected $endDataType = '';
  protected $startType = GoogleCloudApihubV1Point::class;
  protected $startDataType = '';

  /**
   * Required. End of the issue.
   *
   * @param GoogleCloudApihubV1Point $end
   */
  public function setEnd(GoogleCloudApihubV1Point $end)
  {
    $this->end = $end;
  }
  /**
   * @return GoogleCloudApihubV1Point
   */
  public function getEnd()
  {
    return $this->end;
  }
  /**
   * Required. Start of the issue.
   *
   * @param GoogleCloudApihubV1Point $start
   */
  public function setStart(GoogleCloudApihubV1Point $start)
  {
    $this->start = $start;
  }
  /**
   * @return GoogleCloudApihubV1Point
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1Range::class, 'Google_Service_APIhub_GoogleCloudApihubV1Range');
