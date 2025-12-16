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

class ResourceStatusScheduling extends \Google\Model
{
  /**
   * Specifies the availability domain to place the instance in. The value must
   * be a number between 1 and the number of availability domains specified in
   * the spread placement policy attached to the instance.
   *
   * @var int
   */
  public $availabilityDomain;

  /**
   * Specifies the availability domain to place the instance in. The value must
   * be a number between 1 and the number of availability domains specified in
   * the spread placement policy attached to the instance.
   *
   * @param int $availabilityDomain
   */
  public function setAvailabilityDomain($availabilityDomain)
  {
    $this->availabilityDomain = $availabilityDomain;
  }
  /**
   * @return int
   */
  public function getAvailabilityDomain()
  {
    return $this->availabilityDomain;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceStatusScheduling::class, 'Google_Service_Compute_ResourceStatusScheduling');
