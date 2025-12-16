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

namespace Google\Service\BinaryAuthorization;

class Check extends \Google\Model
{
  /**
   * Optional. A special-case check that always denies. Note that this still
   * only applies when the scope of the `CheckSet` applies and the image isn't
   * exempted by an image allowlist. This check is primarily useful for testing,
   * or to set the default behavior for all unmatched scopes to "deny".
   *
   * @var bool
   */
  public $alwaysDeny;
  /**
   * Optional. A user-provided name for this check. This field has no effect on
   * the policy evaluation behavior except to improve readability of messages in
   * evaluation results.
   *
   * @var string
   */
  public $displayName;
  protected $imageAllowlistType = ImageAllowlist::class;
  protected $imageAllowlistDataType = '';
  protected $imageFreshnessCheckType = ImageFreshnessCheck::class;
  protected $imageFreshnessCheckDataType = '';
  protected $sigstoreSignatureCheckType = SigstoreSignatureCheck::class;
  protected $sigstoreSignatureCheckDataType = '';
  protected $simpleSigningAttestationCheckType = SimpleSigningAttestationCheck::class;
  protected $simpleSigningAttestationCheckDataType = '';
  protected $slsaCheckType = SlsaCheck::class;
  protected $slsaCheckDataType = '';
  protected $trustedDirectoryCheckType = TrustedDirectoryCheck::class;
  protected $trustedDirectoryCheckDataType = '';
  protected $vulnerabilityCheckType = VulnerabilityCheck::class;
  protected $vulnerabilityCheckDataType = '';

  /**
   * Optional. A special-case check that always denies. Note that this still
   * only applies when the scope of the `CheckSet` applies and the image isn't
   * exempted by an image allowlist. This check is primarily useful for testing,
   * or to set the default behavior for all unmatched scopes to "deny".
   *
   * @param bool $alwaysDeny
   */
  public function setAlwaysDeny($alwaysDeny)
  {
    $this->alwaysDeny = $alwaysDeny;
  }
  /**
   * @return bool
   */
  public function getAlwaysDeny()
  {
    return $this->alwaysDeny;
  }
  /**
   * Optional. A user-provided name for this check. This field has no effect on
   * the policy evaluation behavior except to improve readability of messages in
   * evaluation results.
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
   * Optional. Images exempted from this check. If any of the patterns match the
   * image url, the check will not be evaluated.
   *
   * @param ImageAllowlist $imageAllowlist
   */
  public function setImageAllowlist(ImageAllowlist $imageAllowlist)
  {
    $this->imageAllowlist = $imageAllowlist;
  }
  /**
   * @return ImageAllowlist
   */
  public function getImageAllowlist()
  {
    return $this->imageAllowlist;
  }
  /**
   * Optional. Require that an image is no older than a configured expiration
   * time. Image age is determined by its upload time.
   *
   * @param ImageFreshnessCheck $imageFreshnessCheck
   */
  public function setImageFreshnessCheck(ImageFreshnessCheck $imageFreshnessCheck)
  {
    $this->imageFreshnessCheck = $imageFreshnessCheck;
  }
  /**
   * @return ImageFreshnessCheck
   */
  public function getImageFreshnessCheck()
  {
    return $this->imageFreshnessCheck;
  }
  /**
   * Optional. Require that an image was signed by Cosign with a trusted key.
   * This check requires that both the image and signature are stored in
   * Artifact Registry.
   *
   * @param SigstoreSignatureCheck $sigstoreSignatureCheck
   */
  public function setSigstoreSignatureCheck(SigstoreSignatureCheck $sigstoreSignatureCheck)
  {
    $this->sigstoreSignatureCheck = $sigstoreSignatureCheck;
  }
  /**
   * @return SigstoreSignatureCheck
   */
  public function getSigstoreSignatureCheck()
  {
    return $this->sigstoreSignatureCheck;
  }
  /**
   * Optional. Require a SimpleSigning-type attestation for every image in the
   * deployment.
   *
   * @param SimpleSigningAttestationCheck $simpleSigningAttestationCheck
   */
  public function setSimpleSigningAttestationCheck(SimpleSigningAttestationCheck $simpleSigningAttestationCheck)
  {
    $this->simpleSigningAttestationCheck = $simpleSigningAttestationCheck;
  }
  /**
   * @return SimpleSigningAttestationCheck
   */
  public function getSimpleSigningAttestationCheck()
  {
    return $this->simpleSigningAttestationCheck;
  }
  /**
   * Optional. Require that an image was built by a trusted builder (such as
   * Google Cloud Build), meets requirements for Supply chain Levels for
   * Software Artifacts (SLSA), and was built from a trusted source code
   * repostitory.
   *
   * @param SlsaCheck $slsaCheck
   */
  public function setSlsaCheck(SlsaCheck $slsaCheck)
  {
    $this->slsaCheck = $slsaCheck;
  }
  /**
   * @return SlsaCheck
   */
  public function getSlsaCheck()
  {
    return $this->slsaCheck;
  }
  /**
   * Optional. Require that an image lives in a trusted directory.
   *
   * @param TrustedDirectoryCheck $trustedDirectoryCheck
   */
  public function setTrustedDirectoryCheck(TrustedDirectoryCheck $trustedDirectoryCheck)
  {
    $this->trustedDirectoryCheck = $trustedDirectoryCheck;
  }
  /**
   * @return TrustedDirectoryCheck
   */
  public function getTrustedDirectoryCheck()
  {
    return $this->trustedDirectoryCheck;
  }
  /**
   * Optional. Require that an image does not contain vulnerabilities that
   * violate the configured rules, such as based on severity levels.
   *
   * @param VulnerabilityCheck $vulnerabilityCheck
   */
  public function setVulnerabilityCheck(VulnerabilityCheck $vulnerabilityCheck)
  {
    $this->vulnerabilityCheck = $vulnerabilityCheck;
  }
  /**
   * @return VulnerabilityCheck
   */
  public function getVulnerabilityCheck()
  {
    return $this->vulnerabilityCheck;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Check::class, 'Google_Service_BinaryAuthorization_Check');
