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

class TextureCompressionFormat extends \Google\Model
{
  /**
   * Unspecified format.
   */
  public const ALIAS_UNSPECIFIED_TEXTURE_COMPRESSION_FORMAT = 'UNSPECIFIED_TEXTURE_COMPRESSION_FORMAT';
  /**
   * ETC1_RGB8 format.
   */
  public const ALIAS_ETC1_RGB8 = 'ETC1_RGB8';
  /**
   * PALETTED format.
   */
  public const ALIAS_PALETTED = 'PALETTED';
  /**
   * THREE_DC format.
   */
  public const ALIAS_THREE_DC = 'THREE_DC';
  /**
   * ATC format.
   */
  public const ALIAS_ATC = 'ATC';
  /**
   * LATC format.
   */
  public const ALIAS_LATC = 'LATC';
  /**
   * DXT1 format.
   */
  public const ALIAS_DXT1 = 'DXT1';
  /**
   * S3TC format.
   */
  public const ALIAS_S3TC = 'S3TC';
  /**
   * PVRTC format.
   */
  public const ALIAS_PVRTC = 'PVRTC';
  /**
   * ASTC format.
   */
  public const ALIAS_ASTC = 'ASTC';
  /**
   * ETC2 format.
   */
  public const ALIAS_ETC2 = 'ETC2';
  /**
   * Alias for texture compression format.
   *
   * @var string
   */
  public $alias;

  /**
   * Alias for texture compression format.
   *
   * Accepted values: UNSPECIFIED_TEXTURE_COMPRESSION_FORMAT, ETC1_RGB8,
   * PALETTED, THREE_DC, ATC, LATC, DXT1, S3TC, PVRTC, ASTC, ETC2
   *
   * @param self::ALIAS_* $alias
   */
  public function setAlias($alias)
  {
    $this->alias = $alias;
  }
  /**
   * @return self::ALIAS_*
   */
  public function getAlias()
  {
    return $this->alias;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextureCompressionFormat::class, 'Google_Service_AndroidPublisher_TextureCompressionFormat');
