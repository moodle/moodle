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

class GeneratedApksPerSigningKey extends \Google\Collection
{
  protected $collection_key = 'generatedStandaloneApks';
  /**
   * SHA256 hash of the APK signing public key certificate.
   *
   * @var string
   */
  public $certificateSha256Hash;
  protected $generatedAssetPackSlicesType = GeneratedAssetPackSlice::class;
  protected $generatedAssetPackSlicesDataType = 'array';
  protected $generatedRecoveryModulesType = GeneratedRecoveryApk::class;
  protected $generatedRecoveryModulesDataType = 'array';
  protected $generatedSplitApksType = GeneratedSplitApk::class;
  protected $generatedSplitApksDataType = 'array';
  protected $generatedStandaloneApksType = GeneratedStandaloneApk::class;
  protected $generatedStandaloneApksDataType = 'array';
  protected $generatedUniversalApkType = GeneratedUniversalApk::class;
  protected $generatedUniversalApkDataType = '';
  protected $targetingInfoType = TargetingInfo::class;
  protected $targetingInfoDataType = '';

  /**
   * SHA256 hash of the APK signing public key certificate.
   *
   * @param string $certificateSha256Hash
   */
  public function setCertificateSha256Hash($certificateSha256Hash)
  {
    $this->certificateSha256Hash = $certificateSha256Hash;
  }
  /**
   * @return string
   */
  public function getCertificateSha256Hash()
  {
    return $this->certificateSha256Hash;
  }
  /**
   * List of asset pack slices which will be served for this app bundle, signed
   * with a key corresponding to certificate_sha256_hash.
   *
   * @param GeneratedAssetPackSlice[] $generatedAssetPackSlices
   */
  public function setGeneratedAssetPackSlices($generatedAssetPackSlices)
  {
    $this->generatedAssetPackSlices = $generatedAssetPackSlices;
  }
  /**
   * @return GeneratedAssetPackSlice[]
   */
  public function getGeneratedAssetPackSlices()
  {
    return $this->generatedAssetPackSlices;
  }
  /**
   * Generated recovery apks for recovery actions signed with a key
   * corresponding to certificate_sha256_hash. This includes all generated
   * recovery APKs, also those in draft or cancelled state. This field is not
   * set if no recovery actions were created for this signing key.
   *
   * @param GeneratedRecoveryApk[] $generatedRecoveryModules
   */
  public function setGeneratedRecoveryModules($generatedRecoveryModules)
  {
    $this->generatedRecoveryModules = $generatedRecoveryModules;
  }
  /**
   * @return GeneratedRecoveryApk[]
   */
  public function getGeneratedRecoveryModules()
  {
    return $this->generatedRecoveryModules;
  }
  /**
   * List of generated split APKs, signed with a key corresponding to
   * certificate_sha256_hash.
   *
   * @param GeneratedSplitApk[] $generatedSplitApks
   */
  public function setGeneratedSplitApks($generatedSplitApks)
  {
    $this->generatedSplitApks = $generatedSplitApks;
  }
  /**
   * @return GeneratedSplitApk[]
   */
  public function getGeneratedSplitApks()
  {
    return $this->generatedSplitApks;
  }
  /**
   * List of generated standalone APKs, signed with a key corresponding to
   * certificate_sha256_hash.
   *
   * @param GeneratedStandaloneApk[] $generatedStandaloneApks
   */
  public function setGeneratedStandaloneApks($generatedStandaloneApks)
  {
    $this->generatedStandaloneApks = $generatedStandaloneApks;
  }
  /**
   * @return GeneratedStandaloneApk[]
   */
  public function getGeneratedStandaloneApks()
  {
    return $this->generatedStandaloneApks;
  }
  /**
   * Generated universal APK, signed with a key corresponding to
   * certificate_sha256_hash. This field is not set if no universal APK was
   * generated for this signing key.
   *
   * @param GeneratedUniversalApk $generatedUniversalApk
   */
  public function setGeneratedUniversalApk(GeneratedUniversalApk $generatedUniversalApk)
  {
    $this->generatedUniversalApk = $generatedUniversalApk;
  }
  /**
   * @return GeneratedUniversalApk
   */
  public function getGeneratedUniversalApk()
  {
    return $this->generatedUniversalApk;
  }
  /**
   * Contains targeting information about the generated apks.
   *
   * @param TargetingInfo $targetingInfo
   */
  public function setTargetingInfo(TargetingInfo $targetingInfo)
  {
    $this->targetingInfo = $targetingInfo;
  }
  /**
   * @return TargetingInfo
   */
  public function getTargetingInfo()
  {
    return $this->targetingInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GeneratedApksPerSigningKey::class, 'Google_Service_AndroidPublisher_GeneratedApksPerSigningKey');
