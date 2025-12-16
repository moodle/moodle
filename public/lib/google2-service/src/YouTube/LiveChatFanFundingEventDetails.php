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

namespace Google\Service\YouTube;

class LiveChatFanFundingEventDetails extends \Google\Model
{
  /**
   * A rendered string that displays the fund amount and currency to the user.
   *
   * @var string
   */
  public $amountDisplayString;
  /**
   * The amount of the fund.
   *
   * @var string
   */
  public $amountMicros;
  /**
   * The currency in which the fund was made.
   *
   * @var string
   */
  public $currency;
  /**
   * The comment added by the user to this fan funding event.
   *
   * @var string
   */
  public $userComment;

  /**
   * A rendered string that displays the fund amount and currency to the user.
   *
   * @param string $amountDisplayString
   */
  public function setAmountDisplayString($amountDisplayString)
  {
    $this->amountDisplayString = $amountDisplayString;
  }
  /**
   * @return string
   */
  public function getAmountDisplayString()
  {
    return $this->amountDisplayString;
  }
  /**
   * The amount of the fund.
   *
   * @param string $amountMicros
   */
  public function setAmountMicros($amountMicros)
  {
    $this->amountMicros = $amountMicros;
  }
  /**
   * @return string
   */
  public function getAmountMicros()
  {
    return $this->amountMicros;
  }
  /**
   * The currency in which the fund was made.
   *
   * @param string $currency
   */
  public function setCurrency($currency)
  {
    $this->currency = $currency;
  }
  /**
   * @return string
   */
  public function getCurrency()
  {
    return $this->currency;
  }
  /**
   * The comment added by the user to this fan funding event.
   *
   * @param string $userComment
   */
  public function setUserComment($userComment)
  {
    $this->userComment = $userComment;
  }
  /**
   * @return string
   */
  public function getUserComment()
  {
    return $this->userComment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveChatFanFundingEventDetails::class, 'Google_Service_YouTube_LiveChatFanFundingEventDetails');
