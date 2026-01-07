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

namespace Google\Service\MigrationCenterAPI;

class GenericInsight extends \Google\Collection
{
  protected $collection_key = 'additionalInformation';
  /**
   * Output only. Additional information about the insight, each entry can be a
   * logical entry and must make sense if it is displayed with line breaks
   * between each entry. Text can contain md style links.
   *
   * @var string[]
   */
  public $additionalInformation;
  /**
   * Output only. In case message_code is not yet known by the client
   * default_message will be the message to be used instead.
   *
   * @var string
   */
  public $defaultMessage;
  /**
   * Output only. Represents a globally unique message id for this insight, can
   * be used for localization purposes, in case message_code is not yet known by
   * the client use default_message instead.
   *
   * @var string
   */
  public $messageId;

  /**
   * Output only. Additional information about the insight, each entry can be a
   * logical entry and must make sense if it is displayed with line breaks
   * between each entry. Text can contain md style links.
   *
   * @param string[] $additionalInformation
   */
  public function setAdditionalInformation($additionalInformation)
  {
    $this->additionalInformation = $additionalInformation;
  }
  /**
   * @return string[]
   */
  public function getAdditionalInformation()
  {
    return $this->additionalInformation;
  }
  /**
   * Output only. In case message_code is not yet known by the client
   * default_message will be the message to be used instead.
   *
   * @param string $defaultMessage
   */
  public function setDefaultMessage($defaultMessage)
  {
    $this->defaultMessage = $defaultMessage;
  }
  /**
   * @return string
   */
  public function getDefaultMessage()
  {
    return $this->defaultMessage;
  }
  /**
   * Output only. Represents a globally unique message id for this insight, can
   * be used for localization purposes, in case message_code is not yet known by
   * the client use default_message instead.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenericInsight::class, 'Google_Service_MigrationCenterAPI_GenericInsight');
