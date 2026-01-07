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

namespace Google\Service\IdentityToolkit;

class EmailTemplate extends \Google\Model
{
  /**
   * Email body.
   *
   * @var string
   */
  public $body;
  /**
   * Email body format.
   *
   * @var string
   */
  public $format;
  /**
   * From address of the email.
   *
   * @var string
   */
  public $from;
  /**
   * From display name.
   *
   * @var string
   */
  public $fromDisplayName;
  /**
   * Reply-to address.
   *
   * @var string
   */
  public $replyTo;
  /**
   * Subject of the email.
   *
   * @var string
   */
  public $subject;

  /**
   * Email body.
   *
   * @param string $body
   */
  public function setBody($body)
  {
    $this->body = $body;
  }
  /**
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * Email body format.
   *
   * @param string $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return string
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * From address of the email.
   *
   * @param string $from
   */
  public function setFrom($from)
  {
    $this->from = $from;
  }
  /**
   * @return string
   */
  public function getFrom()
  {
    return $this->from;
  }
  /**
   * From display name.
   *
   * @param string $fromDisplayName
   */
  public function setFromDisplayName($fromDisplayName)
  {
    $this->fromDisplayName = $fromDisplayName;
  }
  /**
   * @return string
   */
  public function getFromDisplayName()
  {
    return $this->fromDisplayName;
  }
  /**
   * Reply-to address.
   *
   * @param string $replyTo
   */
  public function setReplyTo($replyTo)
  {
    $this->replyTo = $replyTo;
  }
  /**
   * @return string
   */
  public function getReplyTo()
  {
    return $this->replyTo;
  }
  /**
   * Subject of the email.
   *
   * @param string $subject
   */
  public function setSubject($subject)
  {
    $this->subject = $subject;
  }
  /**
   * @return string
   */
  public function getSubject()
  {
    return $this->subject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EmailTemplate::class, 'Google_Service_IdentityToolkit_EmailTemplate');
