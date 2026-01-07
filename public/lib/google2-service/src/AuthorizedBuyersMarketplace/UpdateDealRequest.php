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

namespace Google\Service\AuthorizedBuyersMarketplace;

class UpdateDealRequest extends \Google\Model
{
  protected $dealType = Deal::class;
  protected $dealDataType = '';
  /**
   * List of fields to be updated. If empty or unspecified, the service will
   * update all fields populated in the update request excluding the output only
   * fields and primitive fields with default value. Note that explicit field
   * mask is required in order to reset a primitive field back to its default
   * value, for example, false for boolean fields, 0 for integer fields. A
   * special field mask consisting of a single path "*" can be used to indicate
   * full replacement(the equivalent of PUT method), updatable fields unset or
   * unspecified in the input will be cleared or set to default value. Output
   * only fields will be ignored regardless of the value of updateMask.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. The deal to update. The deal's `name` field is used to identify
   * the deal to be updated. Note: proposal_revision will have to be provided
   * within the resource or else an error will be thrown. Format:
   * buyers/{accountId}/proposals/{proposalId}/deals/{dealId}
   *
   * @param Deal $deal
   */
  public function setDeal(Deal $deal)
  {
    $this->deal = $deal;
  }
  /**
   * @return Deal
   */
  public function getDeal()
  {
    return $this->deal;
  }
  /**
   * List of fields to be updated. If empty or unspecified, the service will
   * update all fields populated in the update request excluding the output only
   * fields and primitive fields with default value. Note that explicit field
   * mask is required in order to reset a primitive field back to its default
   * value, for example, false for boolean fields, 0 for integer fields. A
   * special field mask consisting of a single path "*" can be used to indicate
   * full replacement(the equivalent of PUT method), updatable fields unset or
   * unspecified in the input will be cleared or set to default value. Output
   * only fields will be ignored regardless of the value of updateMask.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateDealRequest::class, 'Google_Service_AuthorizedBuyersMarketplace_UpdateDealRequest');
