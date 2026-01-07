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

namespace Google\Service\Safebrowsing;

class GoogleSecuritySafebrowsingV5FullHashFullHashDetail extends \Google\Collection
{
  /**
   * Unknown threat type. If this is returned by the server, the client shall
   * disregard the enclosing `FullHashDetail` altogether.
   */
  public const THREAT_TYPE_THREAT_TYPE_UNSPECIFIED = 'THREAT_TYPE_UNSPECIFIED';
  /**
   * Malware threat type. Malware is any software or mobile application
   * specifically designed to harm a computer, a mobile device, the software
   * it's running, or its users. Malware exhibits malicious behavior that can
   * include installing software without user consent and installing harmful
   * software such as viruses. More information can be found
   * [here](https://developers.google.com/search/docs/monitor-
   * debug/security/malware).
   */
  public const THREAT_TYPE_MALWARE = 'MALWARE';
  /**
   * Social engineering threat type. Social engineering pages falsely purport to
   * act on behalf of a third party with the intention of confusing viewers into
   * performing an action with which the viewer would only trust a true agent of
   * that third party. Phishing is a type of social engineering that tricks the
   * viewer into performing the specific action of providing information, such
   * as login credentials. More information can be found
   * [here](https://developers.google.com/search/docs/monitor-
   * debug/security/social-engineering).
   */
  public const THREAT_TYPE_SOCIAL_ENGINEERING = 'SOCIAL_ENGINEERING';
  /**
   * Unwanted software threat type. Unwanted software is any software that does
   * not adhere to [Google's Software
   * Principles](https://www.google.com/about/software-principles.html) but
   * isn't malware.
   */
  public const THREAT_TYPE_UNWANTED_SOFTWARE = 'UNWANTED_SOFTWARE';
  /**
   * Potentially harmful application threat type [as used by Google Play Protect
   * for the Play Store](https://developers.google.com/android/play-
   * protect/potentially-harmful-applications).
   */
  public const THREAT_TYPE_POTENTIALLY_HARMFUL_APPLICATION = 'POTENTIALLY_HARMFUL_APPLICATION';
  protected $collection_key = 'attributes';
  /**
   * Unordered list. Additional attributes about those full hashes. This may be
   * empty.
   *
   * @var string[]
   */
  public $attributes;
  /**
   * The type of threat. This field will never be empty.
   *
   * @var string
   */
  public $threatType;

  /**
   * Unordered list. Additional attributes about those full hashes. This may be
   * empty.
   *
   * @param string[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return string[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * The type of threat. This field will never be empty.
   *
   * Accepted values: THREAT_TYPE_UNSPECIFIED, MALWARE, SOCIAL_ENGINEERING,
   * UNWANTED_SOFTWARE, POTENTIALLY_HARMFUL_APPLICATION
   *
   * @param self::THREAT_TYPE_* $threatType
   */
  public function setThreatType($threatType)
  {
    $this->threatType = $threatType;
  }
  /**
   * @return self::THREAT_TYPE_*
   */
  public function getThreatType()
  {
    return $this->threatType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleSecuritySafebrowsingV5FullHashFullHashDetail::class, 'Google_Service_Safebrowsing_GoogleSecuritySafebrowsingV5FullHashFullHashDetail');
