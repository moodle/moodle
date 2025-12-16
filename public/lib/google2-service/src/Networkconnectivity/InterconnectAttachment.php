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

namespace Google\Service\Networkconnectivity;

class InterconnectAttachment extends \Google\Model
{
  /**
   * Optional. Cloud region to install this policy-based route on interconnect
   * attachment. Use `all` to install it on all interconnect attachments.
   *
   * @var string
   */
  public $region;

  /**
   * Optional. Cloud region to install this policy-based route on interconnect
   * attachment. Use `all` to install it on all interconnect attachments.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectAttachment::class, 'Google_Service_Networkconnectivity_InterconnectAttachment');
