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

class VacationSettings extends \Google\Model
{
  /**
   * Flag that controls whether Gmail automatically replies to messages.
   *
   * @var bool
   */
  public $enableAutoReply;
  /**
   * An optional end time for sending auto-replies (epoch ms). When this is
   * specified, Gmail will automatically reply only to messages that it receives
   * before the end time. If both `startTime` and `endTime` are specified,
   * `startTime` must precede `endTime`.
   *
   * @var string
   */
  public $endTime;
  /**
   * Response body in HTML format. Gmail will sanitize the HTML before storing
   * it. If both `response_body_plain_text` and `response_body_html` are
   * specified, `response_body_html` will be used.
   *
   * @var string
   */
  public $responseBodyHtml;
  /**
   * Response body in plain text format. If both `response_body_plain_text` and
   * `response_body_html` are specified, `response_body_html` will be used.
   *
   * @var string
   */
  public $responseBodyPlainText;
  /**
   * Optional text to prepend to the subject line in vacation responses. In
   * order to enable auto-replies, either the response subject or the response
   * body must be nonempty.
   *
   * @var string
   */
  public $responseSubject;
  /**
   * Flag that determines whether responses are sent to recipients who are not
   * in the user's list of contacts.
   *
   * @var bool
   */
  public $restrictToContacts;
  /**
   * Flag that determines whether responses are sent to recipients who are
   * outside of the user's domain. This feature is only available for Google
   * Workspace users.
   *
   * @var bool
   */
  public $restrictToDomain;
  /**
   * An optional start time for sending auto-replies (epoch ms). When this is
   * specified, Gmail will automatically reply only to messages that it receives
   * after the start time. If both `startTime` and `endTime` are specified,
   * `startTime` must precede `endTime`.
   *
   * @var string
   */
  public $startTime;

  /**
   * Flag that controls whether Gmail automatically replies to messages.
   *
   * @param bool $enableAutoReply
   */
  public function setEnableAutoReply($enableAutoReply)
  {
    $this->enableAutoReply = $enableAutoReply;
  }
  /**
   * @return bool
   */
  public function getEnableAutoReply()
  {
    return $this->enableAutoReply;
  }
  /**
   * An optional end time for sending auto-replies (epoch ms). When this is
   * specified, Gmail will automatically reply only to messages that it receives
   * before the end time. If both `startTime` and `endTime` are specified,
   * `startTime` must precede `endTime`.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Response body in HTML format. Gmail will sanitize the HTML before storing
   * it. If both `response_body_plain_text` and `response_body_html` are
   * specified, `response_body_html` will be used.
   *
   * @param string $responseBodyHtml
   */
  public function setResponseBodyHtml($responseBodyHtml)
  {
    $this->responseBodyHtml = $responseBodyHtml;
  }
  /**
   * @return string
   */
  public function getResponseBodyHtml()
  {
    return $this->responseBodyHtml;
  }
  /**
   * Response body in plain text format. If both `response_body_plain_text` and
   * `response_body_html` are specified, `response_body_html` will be used.
   *
   * @param string $responseBodyPlainText
   */
  public function setResponseBodyPlainText($responseBodyPlainText)
  {
    $this->responseBodyPlainText = $responseBodyPlainText;
  }
  /**
   * @return string
   */
  public function getResponseBodyPlainText()
  {
    return $this->responseBodyPlainText;
  }
  /**
   * Optional text to prepend to the subject line in vacation responses. In
   * order to enable auto-replies, either the response subject or the response
   * body must be nonempty.
   *
   * @param string $responseSubject
   */
  public function setResponseSubject($responseSubject)
  {
    $this->responseSubject = $responseSubject;
  }
  /**
   * @return string
   */
  public function getResponseSubject()
  {
    return $this->responseSubject;
  }
  /**
   * Flag that determines whether responses are sent to recipients who are not
   * in the user's list of contacts.
   *
   * @param bool $restrictToContacts
   */
  public function setRestrictToContacts($restrictToContacts)
  {
    $this->restrictToContacts = $restrictToContacts;
  }
  /**
   * @return bool
   */
  public function getRestrictToContacts()
  {
    return $this->restrictToContacts;
  }
  /**
   * Flag that determines whether responses are sent to recipients who are
   * outside of the user's domain. This feature is only available for Google
   * Workspace users.
   *
   * @param bool $restrictToDomain
   */
  public function setRestrictToDomain($restrictToDomain)
  {
    $this->restrictToDomain = $restrictToDomain;
  }
  /**
   * @return bool
   */
  public function getRestrictToDomain()
  {
    return $this->restrictToDomain;
  }
  /**
   * An optional start time for sending auto-replies (epoch ms). When this is
   * specified, Gmail will automatically reply only to messages that it receives
   * after the start time. If both `startTime` and `endTime` are specified,
   * `startTime` must precede `endTime`.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VacationSettings::class, 'Google_Service_Gmail_VacationSettings');
