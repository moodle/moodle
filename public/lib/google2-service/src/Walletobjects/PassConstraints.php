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

namespace Google\Service\Walletobjects;

class PassConstraints extends \Google\Collection
{
  /**
   * Default value, same as ELIGIBLE.
   */
  public const SCREENSHOT_ELIGIBILITY_SCREENSHOT_ELIGIBILITY_UNSPECIFIED = 'SCREENSHOT_ELIGIBILITY_UNSPECIFIED';
  /**
   * Default behavior for all existing Passes if ScreenshotEligibility is not
   * set. Allows screenshots to be taken on Android devices.
   */
  public const SCREENSHOT_ELIGIBILITY_ELIGIBLE = 'ELIGIBLE';
  /**
   * Disallows screenshots to be taken on Android devices. Note that older
   * versions of Wallet may still allow screenshots to be taken.
   */
  public const SCREENSHOT_ELIGIBILITY_INELIGIBLE = 'INELIGIBLE';
  protected $collection_key = 'nfcConstraint';
  /**
   * The NFC constraints for the pass.
   *
   * @var string[]
   */
  public $nfcConstraint;
  /**
   * The screenshot eligibility for the pass.
   *
   * @var string
   */
  public $screenshotEligibility;

  /**
   * The NFC constraints for the pass.
   *
   * @param string[] $nfcConstraint
   */
  public function setNfcConstraint($nfcConstraint)
  {
    $this->nfcConstraint = $nfcConstraint;
  }
  /**
   * @return string[]
   */
  public function getNfcConstraint()
  {
    return $this->nfcConstraint;
  }
  /**
   * The screenshot eligibility for the pass.
   *
   * Accepted values: SCREENSHOT_ELIGIBILITY_UNSPECIFIED, ELIGIBLE, INELIGIBLE
   *
   * @param self::SCREENSHOT_ELIGIBILITY_* $screenshotEligibility
   */
  public function setScreenshotEligibility($screenshotEligibility)
  {
    $this->screenshotEligibility = $screenshotEligibility;
  }
  /**
   * @return self::SCREENSHOT_ELIGIBILITY_*
   */
  public function getScreenshotEligibility()
  {
    return $this->screenshotEligibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PassConstraints::class, 'Google_Service_Walletobjects_PassConstraints');
