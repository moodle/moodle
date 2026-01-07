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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1TokenProperties extends \Google\Model
{
  /**
   * Default unspecified type.
   */
  public const INVALID_REASON_INVALID_REASON_UNSPECIFIED = 'INVALID_REASON_UNSPECIFIED';
  /**
   * If the failure reason was not accounted for.
   */
  public const INVALID_REASON_UNKNOWN_INVALID_REASON = 'UNKNOWN_INVALID_REASON';
  /**
   * The provided user verification token was malformed.
   */
  public const INVALID_REASON_MALFORMED = 'MALFORMED';
  /**
   * The user verification token had expired.
   */
  public const INVALID_REASON_EXPIRED = 'EXPIRED';
  /**
   * The user verification had already been seen.
   */
  public const INVALID_REASON_DUPE = 'DUPE';
  /**
   * The user verification token was not present.
   */
  public const INVALID_REASON_MISSING = 'MISSING';
  /**
   * A retriable error (such as network failure) occurred on the browser. Could
   * easily be simulated by an attacker.
   */
  public const INVALID_REASON_BROWSER_ERROR = 'BROWSER_ERROR';
  /**
   * The action provided at token generation was different than the
   * `expected_action` in the assessment request. The comparison is case-
   * insensitive. This reason can only be returned if all of the following are
   * true: - your `site_key` has the POLICY_BASED_CHALLENGE integration type -
   * you set an action score threshold higher than 0.0 - you provided a non-
   * empty `expected_action`
   */
  public const INVALID_REASON_UNEXPECTED_ACTION = 'UNEXPECTED_ACTION';
  /**
   * Output only. Action name provided at token generation.
   *
   * @var string
   */
  public $action;
  /**
   * Output only. The name of the Android package with which the token was
   * generated (Android keys only).
   *
   * @var string
   */
  public $androidPackageName;
  /**
   * Output only. The timestamp corresponding to the generation of the token.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The hostname of the page on which the token was generated (Web
   * keys only).
   *
   * @var string
   */
  public $hostname;
  /**
   * Output only. Reason associated with the response when valid = false.
   *
   * @var string
   */
  public $invalidReason;
  /**
   * Output only. The ID of the iOS bundle with which the token was generated
   * (iOS keys only).
   *
   * @var string
   */
  public $iosBundleId;
  /**
   * Output only. Whether the provided user response token is valid. When valid
   * = false, the reason could be specified in invalid_reason or it could also
   * be due to a user failing to solve a challenge or a sitekey mismatch (i.e
   * the sitekey used to generate the token was different than the one specified
   * in the assessment).
   *
   * @var bool
   */
  public $valid;

  /**
   * Output only. Action name provided at token generation.
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Output only. The name of the Android package with which the token was
   * generated (Android keys only).
   *
   * @param string $androidPackageName
   */
  public function setAndroidPackageName($androidPackageName)
  {
    $this->androidPackageName = $androidPackageName;
  }
  /**
   * @return string
   */
  public function getAndroidPackageName()
  {
    return $this->androidPackageName;
  }
  /**
   * Output only. The timestamp corresponding to the generation of the token.
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
   * Output only. The hostname of the page on which the token was generated (Web
   * keys only).
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * Output only. Reason associated with the response when valid = false.
   *
   * Accepted values: INVALID_REASON_UNSPECIFIED, UNKNOWN_INVALID_REASON,
   * MALFORMED, EXPIRED, DUPE, MISSING, BROWSER_ERROR, UNEXPECTED_ACTION
   *
   * @param self::INVALID_REASON_* $invalidReason
   */
  public function setInvalidReason($invalidReason)
  {
    $this->invalidReason = $invalidReason;
  }
  /**
   * @return self::INVALID_REASON_*
   */
  public function getInvalidReason()
  {
    return $this->invalidReason;
  }
  /**
   * Output only. The ID of the iOS bundle with which the token was generated
   * (iOS keys only).
   *
   * @param string $iosBundleId
   */
  public function setIosBundleId($iosBundleId)
  {
    $this->iosBundleId = $iosBundleId;
  }
  /**
   * @return string
   */
  public function getIosBundleId()
  {
    return $this->iosBundleId;
  }
  /**
   * Output only. Whether the provided user response token is valid. When valid
   * = false, the reason could be specified in invalid_reason or it could also
   * be due to a user failing to solve a challenge or a sitekey mismatch (i.e
   * the sitekey used to generate the token was different than the one specified
   * in the assessment).
   *
   * @param bool $valid
   */
  public function setValid($valid)
  {
    $this->valid = $valid;
  }
  /**
   * @return bool
   */
  public function getValid()
  {
    return $this->valid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1TokenProperties::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1TokenProperties');
