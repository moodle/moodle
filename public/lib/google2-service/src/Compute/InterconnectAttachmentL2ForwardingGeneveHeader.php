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

class InterconnectAttachmentL2ForwardingGeneveHeader extends \Google\Model
{
  /**
   * Optional. VNI is a 24-bit unique virtual network identifier, from 0 to
   * 16,777,215.
   *
   * @var string
   */
  public $vni;

  /**
   * Optional. VNI is a 24-bit unique virtual network identifier, from 0 to
   * 16,777,215.
   *
   * @param string $vni
   */
  public function setVni($vni)
  {
    $this->vni = $vni;
  }
  /**
   * @return string
   */
  public function getVni()
  {
    return $this->vni;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectAttachmentL2ForwardingGeneveHeader::class, 'Google_Service_Compute_InterconnectAttachmentL2ForwardingGeneveHeader');
