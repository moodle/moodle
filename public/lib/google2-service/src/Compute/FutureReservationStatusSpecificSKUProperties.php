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

class FutureReservationStatusSpecificSKUProperties extends \Google\Model
{
  /**
   * ID of the instance template used to populate the Future Reservation
   * properties.
   *
   * @var string
   */
  public $sourceInstanceTemplateId;

  /**
   * ID of the instance template used to populate the Future Reservation
   * properties.
   *
   * @param string $sourceInstanceTemplateId
   */
  public function setSourceInstanceTemplateId($sourceInstanceTemplateId)
  {
    $this->sourceInstanceTemplateId = $sourceInstanceTemplateId;
  }
  /**
   * @return string
   */
  public function getSourceInstanceTemplateId()
  {
    return $this->sourceInstanceTemplateId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FutureReservationStatusSpecificSKUProperties::class, 'Google_Service_Compute_FutureReservationStatusSpecificSKUProperties');
