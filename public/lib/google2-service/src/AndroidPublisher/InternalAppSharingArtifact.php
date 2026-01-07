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

namespace Google\Service\AndroidPublisher;

class InternalAppSharingArtifact extends \Google\Model
{
  /**
   * The sha256 fingerprint of the certificate used to sign the generated
   * artifact.
   *
   * @var string
   */
  public $certificateFingerprint;
  /**
   * The download URL generated for the uploaded artifact. Users that are
   * authorized to download can follow the link to the Play Store app to install
   * it.
   *
   * @var string
   */
  public $downloadUrl;
  /**
   * The sha256 hash of the artifact represented as a lowercase hexadecimal
   * number, matching the output of the sha256sum command.
   *
   * @var string
   */
  public $sha256;

  /**
   * The sha256 fingerprint of the certificate used to sign the generated
   * artifact.
   *
   * @param string $certificateFingerprint
   */
  public function setCertificateFingerprint($certificateFingerprint)
  {
    $this->certificateFingerprint = $certificateFingerprint;
  }
  /**
   * @return string
   */
  public function getCertificateFingerprint()
  {
    return $this->certificateFingerprint;
  }
  /**
   * The download URL generated for the uploaded artifact. Users that are
   * authorized to download can follow the link to the Play Store app to install
   * it.
   *
   * @param string $downloadUrl
   */
  public function setDownloadUrl($downloadUrl)
  {
    $this->downloadUrl = $downloadUrl;
  }
  /**
   * @return string
   */
  public function getDownloadUrl()
  {
    return $this->downloadUrl;
  }
  /**
   * The sha256 hash of the artifact represented as a lowercase hexadecimal
   * number, matching the output of the sha256sum command.
   *
   * @param string $sha256
   */
  public function setSha256($sha256)
  {
    $this->sha256 = $sha256;
  }
  /**
   * @return string
   */
  public function getSha256()
  {
    return $this->sha256;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InternalAppSharingArtifact::class, 'Google_Service_AndroidPublisher_InternalAppSharingArtifact');
