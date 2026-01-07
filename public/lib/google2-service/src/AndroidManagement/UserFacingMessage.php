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

namespace Google\Service\AndroidManagement;

class UserFacingMessage extends \Google\Model
{
  /**
   * The default message displayed if no localized message is specified or the
   * user's locale doesn't match with any of the localized messages. A default
   * message must be provided if any localized messages are provided.
   *
   * @var string
   */
  public $defaultMessage;
  /**
   * A map containing pairs, where locale is a well-formed BCP 47 language
   * (https://www.w3.org/International/articles/language-tags/) code, such as
   * en-US, es-ES, or fr.
   *
   * @var string[]
   */
  public $localizedMessages;

  /**
   * The default message displayed if no localized message is specified or the
   * user's locale doesn't match with any of the localized messages. A default
   * message must be provided if any localized messages are provided.
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
   * A map containing pairs, where locale is a well-formed BCP 47 language
   * (https://www.w3.org/International/articles/language-tags/) code, such as
   * en-US, es-ES, or fr.
   *
   * @param string[] $localizedMessages
   */
  public function setLocalizedMessages($localizedMessages)
  {
    $this->localizedMessages = $localizedMessages;
  }
  /**
   * @return string[]
   */
  public function getLocalizedMessages()
  {
    return $this->localizedMessages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserFacingMessage::class, 'Google_Service_AndroidManagement_UserFacingMessage');
