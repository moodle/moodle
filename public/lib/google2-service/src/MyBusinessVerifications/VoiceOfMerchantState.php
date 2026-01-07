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

namespace Google\Service\MyBusinessVerifications;

class VoiceOfMerchantState extends \Google\Model
{
  protected $complyWithGuidelinesType = ComplyWithGuidelines::class;
  protected $complyWithGuidelinesDataType = '';
  /**
   * Indicates whether the location has the authority (ownership) over the
   * business on Google. If true, another location cannot take over and become
   * the dominant listing on Maps. However, edits will not become live unless
   * Voice of Merchant is gained (i.e. has_voice_of_merchant is true).
   *
   * @var bool
   */
  public $hasBusinessAuthority;
  /**
   * Indicates whether the location is in good standing and has control over the
   * business on Google. Any edits made to the location will propagate to Maps
   * after passing the review phase.
   *
   * @var bool
   */
  public $hasVoiceOfMerchant;
  protected $resolveOwnershipConflictType = ResolveOwnershipConflict::class;
  protected $resolveOwnershipConflictDataType = '';
  protected $verifyType = Verify::class;
  protected $verifyDataType = '';
  protected $waitForVoiceOfMerchantType = WaitForVoiceOfMerchant::class;
  protected $waitForVoiceOfMerchantDataType = '';

  /**
   * The location fails to comply with our
   * [guidelines](https://support.google.com/business/answer/3038177) and
   * requires additional steps for reinstatement. To fix this issue, consult the
   * [Help Center Article](https://support.google.com/business/answer/4569145).
   *
   * @param ComplyWithGuidelines $complyWithGuidelines
   */
  public function setComplyWithGuidelines(ComplyWithGuidelines $complyWithGuidelines)
  {
    $this->complyWithGuidelines = $complyWithGuidelines;
  }
  /**
   * @return ComplyWithGuidelines
   */
  public function getComplyWithGuidelines()
  {
    return $this->complyWithGuidelines;
  }
  /**
   * Indicates whether the location has the authority (ownership) over the
   * business on Google. If true, another location cannot take over and become
   * the dominant listing on Maps. However, edits will not become live unless
   * Voice of Merchant is gained (i.e. has_voice_of_merchant is true).
   *
   * @param bool $hasBusinessAuthority
   */
  public function setHasBusinessAuthority($hasBusinessAuthority)
  {
    $this->hasBusinessAuthority = $hasBusinessAuthority;
  }
  /**
   * @return bool
   */
  public function getHasBusinessAuthority()
  {
    return $this->hasBusinessAuthority;
  }
  /**
   * Indicates whether the location is in good standing and has control over the
   * business on Google. Any edits made to the location will propagate to Maps
   * after passing the review phase.
   *
   * @param bool $hasVoiceOfMerchant
   */
  public function setHasVoiceOfMerchant($hasVoiceOfMerchant)
  {
    $this->hasVoiceOfMerchant = $hasVoiceOfMerchant;
  }
  /**
   * @return bool
   */
  public function getHasVoiceOfMerchant()
  {
    return $this->hasVoiceOfMerchant;
  }
  /**
   * This location duplicates another location that is in good standing. If you
   * have access to the location in good standing, use that location's id to
   * perform operations. Otherwise, request access from the current owner.
   *
   * @param ResolveOwnershipConflict $resolveOwnershipConflict
   */
  public function setResolveOwnershipConflict(ResolveOwnershipConflict $resolveOwnershipConflict)
  {
    $this->resolveOwnershipConflict = $resolveOwnershipConflict;
  }
  /**
   * @return ResolveOwnershipConflict
   */
  public function getResolveOwnershipConflict()
  {
    return $this->resolveOwnershipConflict;
  }
  /**
   * Start or continue the verification process.
   *
   * @param Verify $verify
   */
  public function setVerify(Verify $verify)
  {
    $this->verify = $verify;
  }
  /**
   * @return Verify
   */
  public function getVerify()
  {
    return $this->verify;
  }
  /**
   * Wait to gain Voice of Merchant. The location is under review for quality
   * purposes.
   *
   * @param WaitForVoiceOfMerchant $waitForVoiceOfMerchant
   */
  public function setWaitForVoiceOfMerchant(WaitForVoiceOfMerchant $waitForVoiceOfMerchant)
  {
    $this->waitForVoiceOfMerchant = $waitForVoiceOfMerchant;
  }
  /**
   * @return WaitForVoiceOfMerchant
   */
  public function getWaitForVoiceOfMerchant()
  {
    return $this->waitForVoiceOfMerchant;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VoiceOfMerchantState::class, 'Google_Service_MyBusinessVerifications_VoiceOfMerchantState');
