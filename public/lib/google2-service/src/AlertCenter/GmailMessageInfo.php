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

namespace Google\Service\AlertCenter;

class GmailMessageInfo extends \Google\Collection
{
  protected $collection_key = 'attachmentsSha256Hash';
  /**
   * The `SHA256` hash of email's attachment and all MIME parts.
   *
   * @var string[]
   */
  public $attachmentsSha256Hash;
  /**
   * The date of the event related to this email.
   *
   * @var string
   */
  public $date;
  /**
   * The hash of the message body text.
   *
   * @var string
   */
  public $md5HashMessageBody;
  /**
   * The MD5 Hash of email's subject (only available for reported emails).
   *
   * @var string
   */
  public $md5HashSubject;
  /**
   * The snippet of the message body text (only available for reported emails).
   *
   * @var string
   */
  public $messageBodySnippet;
  /**
   * The message ID.
   *
   * @var string
   */
  public $messageId;
  /**
   * The recipient of this email.
   *
   * @var string
   */
  public $recipient;
  /**
   * The sent time of the email.
   *
   * @var string
   */
  public $sentTime;
  /**
   * The email subject text (only available for reported emails).
   *
   * @var string
   */
  public $subjectText;

  /**
   * The `SHA256` hash of email's attachment and all MIME parts.
   *
   * @param string[] $attachmentsSha256Hash
   */
  public function setAttachmentsSha256Hash($attachmentsSha256Hash)
  {
    $this->attachmentsSha256Hash = $attachmentsSha256Hash;
  }
  /**
   * @return string[]
   */
  public function getAttachmentsSha256Hash()
  {
    return $this->attachmentsSha256Hash;
  }
  /**
   * The date of the event related to this email.
   *
   * @param string $date
   */
  public function setDate($date)
  {
    $this->date = $date;
  }
  /**
   * @return string
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * The hash of the message body text.
   *
   * @param string $md5HashMessageBody
   */
  public function setMd5HashMessageBody($md5HashMessageBody)
  {
    $this->md5HashMessageBody = $md5HashMessageBody;
  }
  /**
   * @return string
   */
  public function getMd5HashMessageBody()
  {
    return $this->md5HashMessageBody;
  }
  /**
   * The MD5 Hash of email's subject (only available for reported emails).
   *
   * @param string $md5HashSubject
   */
  public function setMd5HashSubject($md5HashSubject)
  {
    $this->md5HashSubject = $md5HashSubject;
  }
  /**
   * @return string
   */
  public function getMd5HashSubject()
  {
    return $this->md5HashSubject;
  }
  /**
   * The snippet of the message body text (only available for reported emails).
   *
   * @param string $messageBodySnippet
   */
  public function setMessageBodySnippet($messageBodySnippet)
  {
    $this->messageBodySnippet = $messageBodySnippet;
  }
  /**
   * @return string
   */
  public function getMessageBodySnippet()
  {
    return $this->messageBodySnippet;
  }
  /**
   * The message ID.
   *
   * @param string $messageId
   */
  public function setMessageId($messageId)
  {
    $this->messageId = $messageId;
  }
  /**
   * @return string
   */
  public function getMessageId()
  {
    return $this->messageId;
  }
  /**
   * The recipient of this email.
   *
   * @param string $recipient
   */
  public function setRecipient($recipient)
  {
    $this->recipient = $recipient;
  }
  /**
   * @return string
   */
  public function getRecipient()
  {
    return $this->recipient;
  }
  /**
   * The sent time of the email.
   *
   * @param string $sentTime
   */
  public function setSentTime($sentTime)
  {
    $this->sentTime = $sentTime;
  }
  /**
   * @return string
   */
  public function getSentTime()
  {
    return $this->sentTime;
  }
  /**
   * The email subject text (only available for reported emails).
   *
   * @param string $subjectText
   */
  public function setSubjectText($subjectText)
  {
    $this->subjectText = $subjectText;
  }
  /**
   * @return string
   */
  public function getSubjectText()
  {
    return $this->subjectText;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GmailMessageInfo::class, 'Google_Service_AlertCenter_GmailMessageInfo');
