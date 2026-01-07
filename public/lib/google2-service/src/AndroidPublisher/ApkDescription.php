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

class ApkDescription extends \Google\Model
{
  protected $assetSliceMetadataType = SplitApkMetadata::class;
  protected $assetSliceMetadataDataType = '';
  protected $instantApkMetadataType = SplitApkMetadata::class;
  protected $instantApkMetadataDataType = '';
  /**
   * Path of the Apk, will be in the following format: .apk where DownloadId is
   * the ID used to download the apk using GeneratedApks.Download API.
   *
   * @var string
   */
  public $path;
  protected $splitApkMetadataType = SplitApkMetadata::class;
  protected $splitApkMetadataDataType = '';
  protected $standaloneApkMetadataType = StandaloneApkMetadata::class;
  protected $standaloneApkMetadataDataType = '';
  protected $targetingType = ApkTargeting::class;
  protected $targetingDataType = '';

  /**
   * Set only for asset slices.
   *
   * @param SplitApkMetadata $assetSliceMetadata
   */
  public function setAssetSliceMetadata(SplitApkMetadata $assetSliceMetadata)
  {
    $this->assetSliceMetadata = $assetSliceMetadata;
  }
  /**
   * @return SplitApkMetadata
   */
  public function getAssetSliceMetadata()
  {
    return $this->assetSliceMetadata;
  }
  /**
   * Set only for Instant split APKs.
   *
   * @param SplitApkMetadata $instantApkMetadata
   */
  public function setInstantApkMetadata(SplitApkMetadata $instantApkMetadata)
  {
    $this->instantApkMetadata = $instantApkMetadata;
  }
  /**
   * @return SplitApkMetadata
   */
  public function getInstantApkMetadata()
  {
    return $this->instantApkMetadata;
  }
  /**
   * Path of the Apk, will be in the following format: .apk where DownloadId is
   * the ID used to download the apk using GeneratedApks.Download API.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Set only for Split APKs.
   *
   * @param SplitApkMetadata $splitApkMetadata
   */
  public function setSplitApkMetadata(SplitApkMetadata $splitApkMetadata)
  {
    $this->splitApkMetadata = $splitApkMetadata;
  }
  /**
   * @return SplitApkMetadata
   */
  public function getSplitApkMetadata()
  {
    return $this->splitApkMetadata;
  }
  /**
   * Set only for standalone APKs.
   *
   * @param StandaloneApkMetadata $standaloneApkMetadata
   */
  public function setStandaloneApkMetadata(StandaloneApkMetadata $standaloneApkMetadata)
  {
    $this->standaloneApkMetadata = $standaloneApkMetadata;
  }
  /**
   * @return StandaloneApkMetadata
   */
  public function getStandaloneApkMetadata()
  {
    return $this->standaloneApkMetadata;
  }
  /**
   * Apk-level targeting.
   *
   * @param ApkTargeting $targeting
   */
  public function setTargeting(ApkTargeting $targeting)
  {
    $this->targeting = $targeting;
  }
  /**
   * @return ApkTargeting
   */
  public function getTargeting()
  {
    return $this->targeting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApkDescription::class, 'Google_Service_AndroidPublisher_ApkDescription');
