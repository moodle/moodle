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

namespace Google\Service\Dfareporting;

class Recipient extends \Google\Model
{
  public const DELIVERY_TYPE_LINK = 'LINK';
  public const DELIVERY_TYPE_ATTACHMENT = 'ATTACHMENT';
  /**
   * The delivery type for the recipient.
   *
   * @var string
   */
  public $deliveryType;
  /**
   * The email address of the recipient.
   *
   * @var string
   */
  public $email;
  /**
   * The kind of resource this is, in this case dfareporting#recipient.
   *
   * @var string
   */
  public $kind;

  /**
   * The delivery type for the recipient.
   *
   * Accepted values: LINK, ATTACHMENT
   *
   * @param self::DELIVERY_TYPE_* $deliveryType
   */
  public function setDeliveryType($deliveryType)
  {
    $this->deliveryType = $deliveryType;
  }
  /**
   * @return self::DELIVERY_TYPE_*
   */
  public function getDeliveryType()
  {
    return $this->deliveryType;
  }
  /**
   * The email address of the recipient.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * The kind of resource this is, in this case dfareporting#recipient.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Recipient::class, 'Google_Service_Dfareporting_Recipient');
