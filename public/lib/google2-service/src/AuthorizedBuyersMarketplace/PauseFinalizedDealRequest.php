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

class PauseFinalizedDealRequest extends \Google\Model
{
  /**
   * The reason to pause the finalized deal, will be displayed to the seller.
   * Maximum length is 1000 characters.
   *
   * @var string
   */
  public $reason;

  /**
   * The reason to pause the finalized deal, will be displayed to the seller.
   * Maximum length is 1000 characters.
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
class_alias(PauseFinalizedDealRequest::class, 'Google_Service_AuthorizedBuyersMarketplace_PauseFinalizedDealRequest');
