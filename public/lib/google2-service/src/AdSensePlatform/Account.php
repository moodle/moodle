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

namespace Google\Service\AdSensePlatform;

class Account extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Unchecked.
   */
  public const STATE_UNCHECKED = 'UNCHECKED';
  /**
   * The account is ready to serve ads.
   */
  public const STATE_APPROVED = 'APPROVED';
  /**
   * The account has been blocked from serving ads.
   */
  public const STATE_DISAPPROVED = 'DISAPPROVED';
  /**
   * Output only. Creation time of the account.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. An opaque token that uniquely identifies the account among all
   * the platform's accounts. This string may contain at most 64 non-whitespace
   * ASCII characters, but otherwise has no predefined structure. However, it is
   * expected to be a platform-specific identifier for the user creating the
   * account, so that only a single account can be created for any given user.
   * This field must not contain any information that is recognizable as
   * personally identifiable information. e.g. it should not be an email address
   * or login name. Once an account has been created, a second attempt to create
   * an account using the same creation_request_id will result in an
   * ALREADY_EXISTS error.
   *
   * @var string
   */
  public $creationRequestId;
  /**
   * Display name of this account.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Resource name of the account. Format:
   * platforms/pub-[0-9]+/accounts/pub-[0-9]+
   *
   * @var string
   */
  public $name;
  /**
   * Required. Input only. CLDR region code of the country/region of the
   * address. Set this to country code of the child account if known, otherwise
   * to your own country code.
   *
   * @var string
   */
  public $regionCode;
  /**
   * Output only. Approval state of the account.
   *
   * @var string
   */
  public $state;
  protected $timeZoneType = TimeZone::class;
  protected $timeZoneDataType = '';

  /**
   * Output only. Creation time of the account.
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
   * Required. An opaque token that uniquely identifies the account among all
   * the platform's accounts. This string may contain at most 64 non-whitespace
   * ASCII characters, but otherwise has no predefined structure. However, it is
   * expected to be a platform-specific identifier for the user creating the
   * account, so that only a single account can be created for any given user.
   * This field must not contain any information that is recognizable as
   * personally identifiable information. e.g. it should not be an email address
   * or login name. Once an account has been created, a second attempt to create
   * an account using the same creation_request_id will result in an
   * ALREADY_EXISTS error.
   *
   * @param string $creationRequestId
   */
  public function setCreationRequestId($creationRequestId)
  {
    $this->creationRequestId = $creationRequestId;
  }
  /**
   * @return string
   */
  public function getCreationRequestId()
  {
    return $this->creationRequestId;
  }
  /**
   * Display name of this account.
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
   * Output only. Resource name of the account. Format:
   * platforms/pub-[0-9]+/accounts/pub-[0-9]+
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
   * Required. Input only. CLDR region code of the country/region of the
   * address. Set this to country code of the child account if known, otherwise
   * to your own country code.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
  /**
   * Output only. Approval state of the account.
   *
   * Accepted values: STATE_UNSPECIFIED, UNCHECKED, APPROVED, DISAPPROVED
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
   * Required. The IANA TZ timezone code of this account. For more information,
   * see https://en.wikipedia.org/wiki/List_of_tz_database_time_zones. This
   * field is used for reporting. It is recommended to set it to the same value
   * for all child accounts.
   *
   * @param TimeZone $timeZone
   */
  public function setTimeZone(TimeZone $timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return TimeZone
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Account::class, 'Google_Service_AdSensePlatform_Account');
