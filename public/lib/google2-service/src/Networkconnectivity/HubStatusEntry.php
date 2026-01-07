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

class HubStatusEntry extends \Google\Model
{
  /**
   * The number of propagated Private Service Connect connections with this
   * status. If the `group_by` field was not set in the request message, the
   * value of this field is 1.
   *
   * @var int
   */
  public $count;
  /**
   * The fields that this entry is grouped by. This has the same value as the
   * `group_by` field in the request message.
   *
   * @var string
   */
  public $groupBy;
  protected $pscPropagationStatusType = PscPropagationStatus::class;
  protected $pscPropagationStatusDataType = '';

  /**
   * The number of propagated Private Service Connect connections with this
   * status. If the `group_by` field was not set in the request message, the
   * value of this field is 1.
   *
   * @param int $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return int
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * The fields that this entry is grouped by. This has the same value as the
   * `group_by` field in the request message.
   *
   * @param string $groupBy
   */
  public function setGroupBy($groupBy)
  {
    $this->groupBy = $groupBy;
  }
  /**
   * @return string
   */
  public function getGroupBy()
  {
    return $this->groupBy;
  }
  /**
   * The Private Service Connect propagation status.
   *
   * @param PscPropagationStatus $pscPropagationStatus
   */
  public function setPscPropagationStatus(PscPropagationStatus $pscPropagationStatus)
  {
    $this->pscPropagationStatus = $pscPropagationStatus;
  }
  /**
   * @return PscPropagationStatus
   */
  public function getPscPropagationStatus()
  {
    return $this->pscPropagationStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HubStatusEntry::class, 'Google_Service_Networkconnectivity_HubStatusEntry');
