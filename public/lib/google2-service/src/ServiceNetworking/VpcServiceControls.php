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

namespace Google\Service\ServiceNetworking;

class VpcServiceControls extends \Google\Model
{
  /**
   * Output only. Indicates whether the VPC Service Controls are enabled or
   * disabled for the connection. If the consumer called the
   * EnableVpcServiceControls method, then this is true. If the consumer called
   * DisableVpcServiceControls, then this is false. The default is false.
   *
   * @var bool
   */
  public $enabled;

  /**
   * Output only. Indicates whether the VPC Service Controls are enabled or
   * disabled for the connection. If the consumer called the
   * EnableVpcServiceControls method, then this is true. If the consumer called
   * DisableVpcServiceControls, then this is false. The default is false.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VpcServiceControls::class, 'Google_Service_ServiceNetworking_VpcServiceControls');
