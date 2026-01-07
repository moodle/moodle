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

namespace Google\Service\Gmail;

class SendAs extends \Google\Model
{
  /**
   * Unspecified verification status.
   */
  public const VERIFICATION_STATUS_verificationStatusUnspecified = 'verificationStatusUnspecified';
  /**
   * The address is ready to use as a send-as alias.
   */
  public const VERIFICATION_STATUS_accepted = 'accepted';
  /**
   * The address is awaiting verification by the owner.
   */
  public const VERIFICATION_STATUS_pending = 'pending';
  /**
   * A name that appears in the "From:" header for mail sent using this alias.
   * For custom "from" addresses, when this is empty, Gmail will populate the
   * "From:" header with the name that is used for the primary address
   * associated with the account. If the admin has disabled the ability for
   * users to update their name format, requests to update this field for the
   * primary login will silently fail.
   *
   * @var string
   */
  public $displayName;
  /**
   * Whether this address is selected as the default "From:" address in
   * situations such as composing a new message or sending a vacation auto-
   * reply. Every Gmail account has exactly one default send-as address, so the
   * only legal value that clients may write to this field is `true`. Changing
   * this from `false` to `true` for an address will result in this field
   * becoming `false` for the other previous default address.
   *
   * @var bool
   */
  public $isDefault;
  /**
   * Whether this address is the primary address used to login to the account.
   * Every Gmail account has exactly one primary address, and it cannot be
   * deleted from the collection of send-as aliases. This field is read-only.
   *
   * @var bool
   */
  public $isPrimary;
  /**
   * An optional email address that is included in a "Reply-To:" header for mail
   * sent using this alias. If this is empty, Gmail will not generate a "Reply-
   * To:" header.
   *
   * @var string
   */
  public $replyToAddress;
  /**
   * The email address that appears in the "From:" header for mail sent using
   * this alias. This is read-only for all operations except create.
   *
   * @var string
   */
  public $sendAsEmail;
  /**
   * An optional HTML signature that is included in messages composed with this
   * alias in the Gmail web UI. This signature is added to new emails only.
   *
   * @var string
   */
  public $signature;
  protected $smtpMsaType = SmtpMsa::class;
  protected $smtpMsaDataType = '';
  /**
   * Whether Gmail should treat this address as an alias for the user's primary
   * email address. This setting only applies to custom "from" aliases.
   *
   * @var bool
   */
  public $treatAsAlias;
  /**
   * Indicates whether this address has been verified for use as a send-as
   * alias. Read-only. This setting only applies to custom "from" aliases.
   *
   * @var string
   */
  public $verificationStatus;

  /**
   * A name that appears in the "From:" header for mail sent using this alias.
   * For custom "from" addresses, when this is empty, Gmail will populate the
   * "From:" header with the name that is used for the primary address
   * associated with the account. If the admin has disabled the ability for
   * users to update their name format, requests to update this field for the
   * primary login will silently fail.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Whether this address is selected as the default "From:" address in
   * situations such as composing a new message or sending a vacation auto-
   * reply. Every Gmail account has exactly one default send-as address, so the
   * only legal value that clients may write to this field is `true`. Changing
   * this from `false` to `true` for an address will result in this field
   * becoming `false` for the other previous default address.
   *
   * @param bool $isDefault
   */
  public function setIsDefault($isDefault)
  {
    $this->isDefault = $isDefault;
  }
  /**
   * @return bool
   */
  public function getIsDefault()
  {
    return $this->isDefault;
  }
  /**
   * Whether this address is the primary address used to login to the account.
   * Every Gmail account has exactly one primary address, and it cannot be
   * deleted from the collection of send-as aliases. This field is read-only.
   *
   * @param bool $isPrimary
   */
  public function setIsPrimary($isPrimary)
  {
    $this->isPrimary = $isPrimary;
  }
  /**
   * @return bool
   */
  public function getIsPrimary()
  {
    return $this->isPrimary;
  }
  /**
   * An optional email address that is included in a "Reply-To:" header for mail
   * sent using this alias. If this is empty, Gmail will not generate a "Reply-
   * To:" header.
   *
   * @param string $replyToAddress
   */
  public function setReplyToAddress($replyToAddress)
  {
    $this->replyToAddress = $replyToAddress;
  }
  /**
   * @return string
   */
  public function getReplyToAddress()
  {
    return $this->replyToAddress;
  }
  /**
   * The email address that appears in the "From:" header for mail sent using
   * this alias. This is read-only for all operations except create.
   *
   * @param string $sendAsEmail
   */
  public function setSendAsEmail($sendAsEmail)
  {
    $this->sendAsEmail = $sendAsEmail;
  }
  /**
   * @return string
   */
  public function getSendAsEmail()
  {
    return $this->sendAsEmail;
  }
  /**
   * An optional HTML signature that is included in messages composed with this
   * alias in the Gmail web UI. This signature is added to new emails only.
   *
   * @param string $signature
   */
  public function setSignature($signature)
  {
    $this->signature = $signature;
  }
  /**
   * @return string
   */
  public function getSignature()
  {
    return $this->signature;
  }
  /**
   * An optional SMTP service that will be used as an outbound relay for mail
   * sent using this alias. If this is empty, outbound mail will be sent
   * directly from Gmail's servers to the destination SMTP service. This setting
   * only applies to custom "from" aliases.
   *
   * @param SmtpMsa $smtpMsa
   */
  public function setSmtpMsa(SmtpMsa $smtpMsa)
  {
    $this->smtpMsa = $smtpMsa;
  }
  /**
   * @return SmtpMsa
   */
  public function getSmtpMsa()
  {
    return $this->smtpMsa;
  }
  /**
   * Whether Gmail should treat this address as an alias for the user's primary
   * email address. This setting only applies to custom "from" aliases.
   *
   * @param bool $treatAsAlias
   */
  public function setTreatAsAlias($treatAsAlias)
  {
    $this->treatAsAlias = $treatAsAlias;
  }
  /**
   * @return bool
   */
  public function getTreatAsAlias()
  {
    return $this->treatAsAlias;
  }
  /**
   * Indicates whether this address has been verified for use as a send-as
   * alias. Read-only. This setting only applies to custom "from" aliases.
   *
   * Accepted values: verificationStatusUnspecified, accepted, pending
   *
   * @param self::VERIFICATION_STATUS_* $verificationStatus
   */
  public function setVerificationStatus($verificationStatus)
  {
    $this->verificationStatus = $verificationStatus;
  }
  /**
   * @return self::VERIFICATION_STATUS_*
   */
  public function getVerificationStatus()
  {
    return $this->verificationStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SendAs::class, 'Google_Service_Gmail_SendAs');
