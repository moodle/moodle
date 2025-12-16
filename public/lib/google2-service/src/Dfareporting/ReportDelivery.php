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

class ReportDelivery extends \Google\Collection
{
  public const EMAIL_OWNER_DELIVERY_TYPE_LINK = 'LINK';
  public const EMAIL_OWNER_DELIVERY_TYPE_ATTACHMENT = 'ATTACHMENT';
  protected $collection_key = 'recipients';
  /**
   * Whether the report should be emailed to the report owner.
   *
   * @var bool
   */
  public $emailOwner;
  /**
   * The type of delivery for the owner to receive, if enabled.
   *
   * @var string
   */
  public $emailOwnerDeliveryType;
  /**
   * The message to be sent with each email.
   *
   * @var string
   */
  public $message;
  protected $recipientsType = Recipient::class;
  protected $recipientsDataType = 'array';

  /**
   * Whether the report should be emailed to the report owner.
   *
   * @param bool $emailOwner
   */
  public function setEmailOwner($emailOwner)
  {
    $this->emailOwner = $emailOwner;
  }
  /**
   * @return bool
   */
  public function getEmailOwner()
  {
    return $this->emailOwner;
  }
  /**
   * The type of delivery for the owner to receive, if enabled.
   *
   * Accepted values: LINK, ATTACHMENT
   *
   * @param self::EMAIL_OWNER_DELIVERY_TYPE_* $emailOwnerDeliveryType
   */
  public function setEmailOwnerDeliveryType($emailOwnerDeliveryType)
  {
    $this->emailOwnerDeliveryType = $emailOwnerDeliveryType;
  }
  /**
   * @return self::EMAIL_OWNER_DELIVERY_TYPE_*
   */
  public function getEmailOwnerDeliveryType()
  {
    return $this->emailOwnerDeliveryType;
  }
  /**
   * The message to be sent with each email.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The list of recipients to which to email the report.
   *
   * @param Recipient[] $recipients
   */
  public function setRecipients($recipients)
  {
    $this->recipients = $recipients;
  }
  /**
   * @return Recipient[]
   */
  public function getRecipients()
  {
    return $this->recipients;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportDelivery::class, 'Google_Service_Dfareporting_ReportDelivery');
