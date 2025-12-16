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

namespace Google\Service\CloudSupport;

class CloudsupportCase extends \Google\Collection
{
  /**
   * Priority is undefined or has not been set yet.
   */
  public const PRIORITY_PRIORITY_UNSPECIFIED = 'PRIORITY_UNSPECIFIED';
  /**
   * Extreme impact on a production service. Service is hard down.
   */
  public const PRIORITY_P0 = 'P0';
  /**
   * Critical impact on a production service. Service is currently unusable.
   */
  public const PRIORITY_P1 = 'P1';
  /**
   * Severe impact on a production service. Service is usable but greatly
   * impaired.
   */
  public const PRIORITY_P2 = 'P2';
  /**
   * Medium impact on a production service. Service is available, but moderately
   * impaired.
   */
  public const PRIORITY_P3 = 'P3';
  /**
   * General questions or minor issues. Production service is fully available.
   */
  public const PRIORITY_P4 = 'P4';
  /**
   * Case is in an unknown state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The case has been created but no one is assigned to work on it yet.
   */
  public const STATE_NEW = 'NEW';
  /**
   * The case is currently being handled by Google support.
   */
  public const STATE_IN_PROGRESS_GOOGLE_SUPPORT = 'IN_PROGRESS_GOOGLE_SUPPORT';
  /**
   * Google is waiting for a response.
   */
  public const STATE_ACTION_REQUIRED = 'ACTION_REQUIRED';
  /**
   * A solution has been offered for the case, but it isn't yet closed.
   */
  public const STATE_SOLUTION_PROVIDED = 'SOLUTION_PROVIDED';
  /**
   * The case has been resolved.
   */
  public const STATE_CLOSED = 'CLOSED';
  protected $collection_key = 'subscriberEmailAddresses';
  protected $classificationType = CaseClassification::class;
  protected $classificationDataType = '';
  /**
   * A user-supplied email address to send case update notifications for. This
   * should only be used in BYOID flows, where we cannot infer the user's email
   * address directly from their EUCs.
   *
   * @var string
   */
  public $contactEmail;
  /**
   * Output only. The time this case was created.
   *
   * @var string
   */
  public $createTime;
  protected $creatorType = Actor::class;
  protected $creatorDataType = '';
  /**
   * A broad description of the issue.
   *
   * @var string
   */
  public $description;
  /**
   * The short summary of the issue reported in this case.
   *
   * @var string
   */
  public $displayName;
  /**
   * Whether the case is currently escalated.
   *
   * @var bool
   */
  public $escalated;
  /**
   * The language the user has requested to receive support in. This should be a
   * BCP 47 language code (e.g., `"en"`, `"zh-CN"`, `"zh-TW"`, `"ja"`, `"ko"`).
   * If no language or an unsupported language is specified, this field defaults
   * to English (en). Language selection during case creation may affect your
   * available support options. For a list of supported languages and their
   * support working hours, see: https://cloud.google.com/support/docs/language-
   * working-hours
   *
   * @var string
   */
  public $languageCode;
  /**
   * Identifier. The resource name for the case.
   *
   * @var string
   */
  public $name;
  /**
   * The priority of this case.
   *
   * @var string
   */
  public $priority;
  /**
   * Output only. The current status of the support case.
   *
   * @var string
   */
  public $state;
  /**
   * The email addresses to receive updates on this case.
   *
   * @var string[]
   */
  public $subscriberEmailAddresses;
  /**
   * Whether this case was created for internal API testing and should not be
   * acted on by the support team.
   *
   * @var bool
   */
  public $testCase;
  /**
   * The timezone of the user who created the support case. It should be in a
   * format IANA recognizes: https://www.iana.org/time-zones. There is no
   * additional validation done by the API.
   *
   * @var string
   */
  public $timeZone;
  /**
   * Output only. The time this case was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The issue classification applicable to this case.
   *
   * @param CaseClassification $classification
   */
  public function setClassification(CaseClassification $classification)
  {
    $this->classification = $classification;
  }
  /**
   * @return CaseClassification
   */
  public function getClassification()
  {
    return $this->classification;
  }
  /**
   * A user-supplied email address to send case update notifications for. This
   * should only be used in BYOID flows, where we cannot infer the user's email
   * address directly from their EUCs.
   *
   * @param string $contactEmail
   */
  public function setContactEmail($contactEmail)
  {
    $this->contactEmail = $contactEmail;
  }
  /**
   * @return string
   */
  public function getContactEmail()
  {
    return $this->contactEmail;
  }
  /**
   * Output only. The time this case was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The user who created the case. Note: The name and email will be obfuscated
   * if the case was created by Google Support.
   *
   * @param Actor $creator
   */
  public function setCreator(Actor $creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return Actor
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * A broad description of the issue.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The short summary of the issue reported in this case.
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
   * Whether the case is currently escalated.
   *
   * @param bool $escalated
   */
  public function setEscalated($escalated)
  {
    $this->escalated = $escalated;
  }
  /**
   * @return bool
   */
  public function getEscalated()
  {
    return $this->escalated;
  }
  /**
   * The language the user has requested to receive support in. This should be a
   * BCP 47 language code (e.g., `"en"`, `"zh-CN"`, `"zh-TW"`, `"ja"`, `"ko"`).
   * If no language or an unsupported language is specified, this field defaults
   * to English (en). Language selection during case creation may affect your
   * available support options. For a list of supported languages and their
   * support working hours, see: https://cloud.google.com/support/docs/language-
   * working-hours
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Identifier. The resource name for the case.
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
   * The priority of this case.
   *
   * Accepted values: PRIORITY_UNSPECIFIED, P0, P1, P2, P3, P4
   *
   * @param self::PRIORITY_* $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return self::PRIORITY_*
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * Output only. The current status of the support case.
   *
   * Accepted values: STATE_UNSPECIFIED, NEW, IN_PROGRESS_GOOGLE_SUPPORT,
   * ACTION_REQUIRED, SOLUTION_PROVIDED, CLOSED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The email addresses to receive updates on this case.
   *
   * @param string[] $subscriberEmailAddresses
   */
  public function setSubscriberEmailAddresses($subscriberEmailAddresses)
  {
    $this->subscriberEmailAddresses = $subscriberEmailAddresses;
  }
  /**
   * @return string[]
   */
  public function getSubscriberEmailAddresses()
  {
    return $this->subscriberEmailAddresses;
  }
  /**
   * Whether this case was created for internal API testing and should not be
   * acted on by the support team.
   *
   * @param bool $testCase
   */
  public function setTestCase($testCase)
  {
    $this->testCase = $testCase;
  }
  /**
   * @return bool
   */
  public function getTestCase()
  {
    return $this->testCase;
  }
  /**
   * The timezone of the user who created the support case. It should be in a
   * format IANA recognizes: https://www.iana.org/time-zones. There is no
   * additional validation done by the API.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * Output only. The time this case was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudsupportCase::class, 'Google_Service_CloudSupport_CloudsupportCase');
