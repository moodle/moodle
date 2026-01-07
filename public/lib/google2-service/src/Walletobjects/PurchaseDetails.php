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

namespace Google\Service\Walletobjects;

class PurchaseDetails extends \Google\Model
{
  /**
   * ID of the account used to purchase the ticket.
   *
   * @var string
   */
  public $accountId;
  /**
   * The confirmation code for the purchase. This may be the same for multiple
   * different tickets and is used to group tickets together.
   *
   * @var string
   */
  public $confirmationCode;
  /**
   * The purchase date/time of the ticket. This is an ISO 8601 extended format
   * date/time, with or without an offset. Time may be specified up to
   * nanosecond precision. Offsets may be specified with seconds precision (even
   * though offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the event
   * were in New York, this would be the equivalent of Eastern Daylight Time
   * (EDT). Remember that offset varies in regions that observe Daylight Saving
   * Time (or Summer Time), depending on the time of the year.
   * `1985-04-12T19:20:50.52` would be 20 minutes and 50.52 seconds after the
   * 19th hour of April 12th, 1985 with no offset information. Without offset
   * information, some rich features may not be available.
   *
   * @var string
   */
  public $purchaseDateTime;
  /**
   * Receipt number/identifier for tracking the ticket purchase via the body
   * that sold the ticket.
   *
   * @var string
   */
  public $purchaseReceiptNumber;
  protected $ticketCostType = TicketCost::class;
  protected $ticketCostDataType = '';

  /**
   * ID of the account used to purchase the ticket.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * The confirmation code for the purchase. This may be the same for multiple
   * different tickets and is used to group tickets together.
   *
   * @param string $confirmationCode
   */
  public function setConfirmationCode($confirmationCode)
  {
    $this->confirmationCode = $confirmationCode;
  }
  /**
   * @return string
   */
  public function getConfirmationCode()
  {
    return $this->confirmationCode;
  }
  /**
   * The purchase date/time of the ticket. This is an ISO 8601 extended format
   * date/time, with or without an offset. Time may be specified up to
   * nanosecond precision. Offsets may be specified with seconds precision (even
   * though offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the event
   * were in New York, this would be the equivalent of Eastern Daylight Time
   * (EDT). Remember that offset varies in regions that observe Daylight Saving
   * Time (or Summer Time), depending on the time of the year.
   * `1985-04-12T19:20:50.52` would be 20 minutes and 50.52 seconds after the
   * 19th hour of April 12th, 1985 with no offset information. Without offset
   * information, some rich features may not be available.
   *
   * @param string $purchaseDateTime
   */
  public function setPurchaseDateTime($purchaseDateTime)
  {
    $this->purchaseDateTime = $purchaseDateTime;
  }
  /**
   * @return string
   */
  public function getPurchaseDateTime()
  {
    return $this->purchaseDateTime;
  }
  /**
   * Receipt number/identifier for tracking the ticket purchase via the body
   * that sold the ticket.
   *
   * @param string $purchaseReceiptNumber
   */
  public function setPurchaseReceiptNumber($purchaseReceiptNumber)
  {
    $this->purchaseReceiptNumber = $purchaseReceiptNumber;
  }
  /**
   * @return string
   */
  public function getPurchaseReceiptNumber()
  {
    return $this->purchaseReceiptNumber;
  }
  /**
   * The cost of the ticket.
   *
   * @param TicketCost $ticketCost
   */
  public function setTicketCost(TicketCost $ticketCost)
  {
    $this->ticketCost = $ticketCost;
  }
  /**
   * @return TicketCost
   */
  public function getTicketCost()
  {
    return $this->ticketCost;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PurchaseDetails::class, 'Google_Service_Walletobjects_PurchaseDetails');
