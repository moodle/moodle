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

class EventReservationInfo extends \Google\Model
{
  /**
   * The confirmation code of the event reservation. This may also take the form
   * of an "order number", "confirmation number", "reservation number", or other
   * equivalent.
   *
   * @var string
   */
  public $confirmationCode;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#eventReservationInfo"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;

  /**
   * The confirmation code of the event reservation. This may also take the form
   * of an "order number", "confirmation number", "reservation number", or other
   * equivalent.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#eventReservationInfo"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventReservationInfo::class, 'Google_Service_Walletobjects_EventReservationInfo');
