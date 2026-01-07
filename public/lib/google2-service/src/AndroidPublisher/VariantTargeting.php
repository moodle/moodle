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

class VariantTargeting extends \Google\Model
{
  protected $abiTargetingType = AbiTargeting::class;
  protected $abiTargetingDataType = '';
  protected $multiAbiTargetingType = MultiAbiTargeting::class;
  protected $multiAbiTargetingDataType = '';
  protected $screenDensityTargetingType = ScreenDensityTargeting::class;
  protected $screenDensityTargetingDataType = '';
  protected $sdkVersionTargetingType = SdkVersionTargeting::class;
  protected $sdkVersionTargetingDataType = '';
  protected $textureCompressionFormatTargetingType = TextureCompressionFormatTargeting::class;
  protected $textureCompressionFormatTargetingDataType = '';

  /**
   * The abi that the variant targets
   *
   * @param AbiTargeting $abiTargeting
   */
  public function setAbiTargeting(AbiTargeting $abiTargeting)
  {
    $this->abiTargeting = $abiTargeting;
  }
  /**
   * @return AbiTargeting
   */
  public function getAbiTargeting()
  {
    return $this->abiTargeting;
  }
  /**
   * Multi-api-level targeting
   *
   * @param MultiAbiTargeting $multiAbiTargeting
   */
  public function setMultiAbiTargeting(MultiAbiTargeting $multiAbiTargeting)
  {
    $this->multiAbiTargeting = $multiAbiTargeting;
  }
  /**
   * @return MultiAbiTargeting
   */
  public function getMultiAbiTargeting()
  {
    return $this->multiAbiTargeting;
  }
  /**
   * The screen densities that this variant supports
   *
   * @param ScreenDensityTargeting $screenDensityTargeting
   */
  public function setScreenDensityTargeting(ScreenDensityTargeting $screenDensityTargeting)
  {
    $this->screenDensityTargeting = $screenDensityTargeting;
  }
  /**
   * @return ScreenDensityTargeting
   */
  public function getScreenDensityTargeting()
  {
    return $this->screenDensityTargeting;
  }
  /**
   * The sdk version that the variant targets
   *
   * @param SdkVersionTargeting $sdkVersionTargeting
   */
  public function setSdkVersionTargeting(SdkVersionTargeting $sdkVersionTargeting)
  {
    $this->sdkVersionTargeting = $sdkVersionTargeting;
  }
  /**
   * @return SdkVersionTargeting
   */
  public function getSdkVersionTargeting()
  {
    return $this->sdkVersionTargeting;
  }
  /**
   * Texture-compression-format-level targeting
   *
   * @param TextureCompressionFormatTargeting $textureCompressionFormatTargeting
   */
  public function setTextureCompressionFormatTargeting(TextureCompressionFormatTargeting $textureCompressionFormatTargeting)
  {
    $this->textureCompressionFormatTargeting = $textureCompressionFormatTargeting;
  }
  /**
   * @return TextureCompressionFormatTargeting
   */
  public function getTextureCompressionFormatTargeting()
  {
    return $this->textureCompressionFormatTargeting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VariantTargeting::class, 'Google_Service_AndroidPublisher_VariantTargeting');
