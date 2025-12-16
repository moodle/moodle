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

class GlobalAddressesMoveRequest extends \Google\Model
{
  /**
   * An optional destination address description if intended to be different
   * from the source.
   *
   * @var string
   */
  public $description;
  /**
   * The URL of the destination address to move to. This can be a full or
   * partial URL. For example, the following are all valid URLs to a address:
   * - https://www.googleapis.com/compute/v1/projects/project/global/addresses/a
   * ddress     - projects/project/global/addresses/address
   *
   * Note that destination project must be different from the source project.
   * So/global/addresses/address is not valid partial url.
   *
   * @var string
   */
  public $destinationAddress;

  /**
   * An optional destination address description if intended to be different
   * from the source.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The URL of the destination address to move to. This can be a full or
   * partial URL. For example, the following are all valid URLs to a address:
   * - https://www.googleapis.com/compute/v1/projects/project/global/addresses/a
   * ddress     - projects/project/global/addresses/address
   *
   * Note that destination project must be different from the source project.
   * So/global/addresses/address is not valid partial url.
   *
   * @param string $destinationAddress
   */
  public function setDestinationAddress($destinationAddress)
  {
    $this->destinationAddress = $destinationAddress;
  }
  /**
   * @return string
   */
  public function getDestinationAddress()
  {
    return $this->destinationAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GlobalAddressesMoveRequest::class, 'Google_Service_Compute_GlobalAddressesMoveRequest');
