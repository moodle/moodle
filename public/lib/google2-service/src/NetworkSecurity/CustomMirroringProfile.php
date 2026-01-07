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

namespace Google\Service\NetworkSecurity;

class CustomMirroringProfile extends \Google\Model
{
  /**
   * Required. Immutable. The target MirroringEndpointGroup. When a mirroring
   * rule with this security profile attached matches a packet, a replica will
   * be mirrored to the location-local target in this group.
   *
   * @var string
   */
  public $mirroringEndpointGroup;

  /**
   * Required. Immutable. The target MirroringEndpointGroup. When a mirroring
   * rule with this security profile attached matches a packet, a replica will
   * be mirrored to the location-local target in this group.
   *
   * @param string $mirroringEndpointGroup
   */
  public function setMirroringEndpointGroup($mirroringEndpointGroup)
  {
    $this->mirroringEndpointGroup = $mirroringEndpointGroup;
  }
  /**
   * @return string
   */
  public function getMirroringEndpointGroup()
  {
    return $this->mirroringEndpointGroup;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomMirroringProfile::class, 'Google_Service_NetworkSecurity_CustomMirroringProfile');
