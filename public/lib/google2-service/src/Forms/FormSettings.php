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

namespace Google\Service\Forms;

class FormSettings extends \Google\Model
{
  /**
   * Unspecified. This value is unused.
   */
  public const EMAIL_COLLECTION_TYPE_EMAIL_COLLECTION_TYPE_UNSPECIFIED = 'EMAIL_COLLECTION_TYPE_UNSPECIFIED';
  /**
   * The form doesn't collect email addresses. Default value if the form owner
   * uses a Google account.
   */
  public const EMAIL_COLLECTION_TYPE_DO_NOT_COLLECT = 'DO_NOT_COLLECT';
  /**
   * The form collects email addresses automatically based on the account of the
   * signed-in user. Default value if the form owner uses a Google Workspace
   * account.
   */
  public const EMAIL_COLLECTION_TYPE_VERIFIED = 'VERIFIED';
  /**
   * The form collects email addresses using a field that the respondent
   * completes on the form.
   */
  public const EMAIL_COLLECTION_TYPE_RESPONDER_INPUT = 'RESPONDER_INPUT';
  /**
   * Optional. The setting that determines whether the form collects email
   * addresses from respondents.
   *
   * @var string
   */
  public $emailCollectionType;
  protected $quizSettingsType = QuizSettings::class;
  protected $quizSettingsDataType = '';

  /**
   * Optional. The setting that determines whether the form collects email
   * addresses from respondents.
   *
   * Accepted values: EMAIL_COLLECTION_TYPE_UNSPECIFIED, DO_NOT_COLLECT,
   * VERIFIED, RESPONDER_INPUT
   *
   * @param self::EMAIL_COLLECTION_TYPE_* $emailCollectionType
   */
  public function setEmailCollectionType($emailCollectionType)
  {
    $this->emailCollectionType = $emailCollectionType;
  }
  /**
   * @return self::EMAIL_COLLECTION_TYPE_*
   */
  public function getEmailCollectionType()
  {
    return $this->emailCollectionType;
  }
  /**
   * Settings related to quiz forms and grading.
   *
   * @param QuizSettings $quizSettings
   */
  public function setQuizSettings(QuizSettings $quizSettings)
  {
    $this->quizSettings = $quizSettings;
  }
  /**
   * @return QuizSettings
   */
  public function getQuizSettings()
  {
    return $this->quizSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FormSettings::class, 'Google_Service_Forms_FormSettings');
