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

namespace Google\Service\Essentialcontacts;

class GoogleCloudEssentialcontactsV1Contact extends \Google\Collection
{
  /**
   * The validation state is unknown or unspecified.
   */
  public const VALIDATION_STATE_VALIDATION_STATE_UNSPECIFIED = 'VALIDATION_STATE_UNSPECIFIED';
  /**
   * The contact is marked as valid. This is usually done manually by the
   * contact admin. All new contacts begin in the valid state.
   */
  public const VALIDATION_STATE_VALID = 'VALID';
  /**
   * The contact is considered invalid. This may become the state if the
   * contact's email is found to be unreachable.
   */
  public const VALIDATION_STATE_INVALID = 'INVALID';
  protected $collection_key = 'notificationCategorySubscriptions';
  /**
   * Required. The email address to send notifications to. The email address
   * does not need to be a Google Account.
   *
   * @var string
   */
  public $email;
  /**
   * Required. The preferred language for notifications, as a ISO 639-1 language
   * code. See [Supported languages](https://cloud.google.com/resource-
   * manager/docs/managing-notification-contacts#supported-languages) for a list
   * of supported languages.
   *
   * @var string
   */
  public $languageTag;
  /**
   * Output only. The identifier for the contact. Format:
   * {resource_type}/{resource_id}/contacts/{contact_id}
   *
   * @var string
   */
  public $name;
  /**
   * Required. The categories of notifications that the contact will receive
   * communications for.
   *
   * @var string[]
   */
  public $notificationCategorySubscriptions;
  /**
   * Output only. The last time the validation_state was updated, either
   * manually or automatically. A contact is considered stale if its validation
   * state was updated more than 1 year ago.
   *
   * @var string
   */
  public $validateTime;
  /**
   * Output only. The validity of the contact. A contact is considered valid if
   * it is the correct recipient for notifications for a particular resource.
   *
   * @var string
   */
  public $validationState;

  /**
   * Required. The email address to send notifications to. The email address
   * does not need to be a Google Account.
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
   * Required. The preferred language for notifications, as a ISO 639-1 language
   * code. See [Supported languages](https://cloud.google.com/resource-
   * manager/docs/managing-notification-contacts#supported-languages) for a list
   * of supported languages.
   *
   * @param string $languageTag
   */
  public function setLanguageTag($languageTag)
  {
    $this->languageTag = $languageTag;
  }
  /**
   * @return string
   */
  public function getLanguageTag()
  {
    return $this->languageTag;
  }
  /**
   * Output only. The identifier for the contact. Format:
   * {resource_type}/{resource_id}/contacts/{contact_id}
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. The categories of notifications that the contact will receive
   * communications for.
   *
   * @param string[] $notificationCategorySubscriptions
   */
  public function setNotificationCategorySubscriptions($notificationCategorySubscriptions)
  {
    $this->notificationCategorySubscriptions = $notificationCategorySubscriptions;
  }
  /**
   * @return string[]
   */
  public function getNotificationCategorySubscriptions()
  {
    return $this->notificationCategorySubscriptions;
  }
  /**
   * Output only. The last time the validation_state was updated, either
   * manually or automatically. A contact is considered stale if its validation
   * state was updated more than 1 year ago.
   *
   * @param string $validateTime
   */
  public function setValidateTime($validateTime)
  {
    $this->validateTime = $validateTime;
  }
  /**
   * @return string
   */
  public function getValidateTime()
  {
    return $this->validateTime;
  }
  /**
   * Output only. The validity of the contact. A contact is considered valid if
   * it is the correct recipient for notifications for a particular resource.
   *
   * Accepted values: VALIDATION_STATE_UNSPECIFIED, VALID, INVALID
   *
   * @param self::VALIDATION_STATE_* $validationState
   */
  public function setValidationState($validationState)
  {
    $this->validationState = $validationState;
  }
  /**
   * @return self::VALIDATION_STATE_*
   */
  public function getValidationState()
  {
    return $this->validationState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudEssentialcontactsV1Contact::class, 'Google_Service_Essentialcontacts_GoogleCloudEssentialcontactsV1Contact');
