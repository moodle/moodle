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

namespace Google\Service\AdExchangeBuyerII;

class PauseProposalDealsRequest extends \Google\Collection
{
  protected $collection_key = 'externalDealIds';
  /**
   * The external_deal_id's of the deals to be paused. If empty, all the deals
   * in the proposal will be paused.
   *
   * @var string[]
   */
  public $externalDealIds;
  /**
   * The reason why the deals are being paused. This human readable message will
   * be displayed in the seller's UI. (Max length: 1000 unicode code units.)
   *
   * @var string
   */
  public $reason;

  /**
   * The external_deal_id's of the deals to be paused. If empty, all the deals
   * in the proposal will be paused.
   *
   * @param string[] $externalDealIds
   */
  public function setExternalDealIds($externalDealIds)
  {
    $this->externalDealIds = $externalDealIds;
  }
  /**
   * @return string[]
   */
  public function getExternalDealIds()
  {
    return $this->externalDealIds;
  }
  /**
   * The reason why the deals are being paused. This human readable message will
   * be displayed in the seller's UI. (Max length: 1000 unicode code units.)
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PauseProposalDealsRequest::class, 'Google_Service_AdExchangeBuyerII_PauseProposalDealsRequest');
