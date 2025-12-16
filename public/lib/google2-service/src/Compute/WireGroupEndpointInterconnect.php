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

class WireGroupEndpointInterconnect extends \Google\Collection
{
  protected $collection_key = 'vlanTags';
  /**
   * Required. An Interconnect connection. You can specify the connection as a
   * partial or full URL. If the connection is in a different project from the
   * cross-site network, use a format that specifies the project. See the
   * following examples of partial and full URLs:
   * global/interconnects/NAME
   * projects/PROJECT_ID/global/interconnects/NAME          -        https://com
   * pute.googleapis.com/compute/projects/PROJECT_ID/global/interconnects/NAME
   *
   * @var string
   */
  public $interconnect;
  /**
   * Required. To configure the wire group for VLAN mode, enter a VLAN tag,
   * which is a number from `2` to `4093`. You can autoallocate a tag by
   * entering `0`. To configure the wire group for port mode, enter `-1`. Review
   * the following guidelines:        - A VLAN tag must be unique for an
   * Interconnect connection across all    attachments and wire groups.    -
   * Both endpoints of a wire must use the same VLAN tag value.    - Single wire
   * and redundant type wire groups must have only one    VLAN tag.    - Port
   * mode pseudowires must have a single VLAN tag with a value of    `-1` for
   * both endpoints.    - Box and cross type wire groups must have two VLAN
   * tags. The first    is for the same-zone pseudowire, and the second is for
   * the cross-zone    pseudowire.
   *
   * @var int[]
   */
  public $vlanTags;

  /**
   * Required. An Interconnect connection. You can specify the connection as a
   * partial or full URL. If the connection is in a different project from the
   * cross-site network, use a format that specifies the project. See the
   * following examples of partial and full URLs:
   * global/interconnects/NAME
   * projects/PROJECT_ID/global/interconnects/NAME          -        https://com
   * pute.googleapis.com/compute/projects/PROJECT_ID/global/interconnects/NAME
   *
   * @param string $interconnect
   */
  public function setInterconnect($interconnect)
  {
    $this->interconnect = $interconnect;
  }
  /**
   * @return string
   */
  public function getInterconnect()
  {
    return $this->interconnect;
  }
  /**
   * Required. To configure the wire group for VLAN mode, enter a VLAN tag,
   * which is a number from `2` to `4093`. You can autoallocate a tag by
   * entering `0`. To configure the wire group for port mode, enter `-1`. Review
   * the following guidelines:        - A VLAN tag must be unique for an
   * Interconnect connection across all    attachments and wire groups.    -
   * Both endpoints of a wire must use the same VLAN tag value.    - Single wire
   * and redundant type wire groups must have only one    VLAN tag.    - Port
   * mode pseudowires must have a single VLAN tag with a value of    `-1` for
   * both endpoints.    - Box and cross type wire groups must have two VLAN
   * tags. The first    is for the same-zone pseudowire, and the second is for
   * the cross-zone    pseudowire.
   *
   * @param int[] $vlanTags
   */
  public function setVlanTags($vlanTags)
  {
    $this->vlanTags = $vlanTags;
  }
  /**
   * @return int[]
   */
  public function getVlanTags()
  {
    return $this->vlanTags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WireGroupEndpointInterconnect::class, 'Google_Service_Compute_WireGroupEndpointInterconnect');
