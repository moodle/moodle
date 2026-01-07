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

namespace Google\Service\YouTube;

class SuperStickerMetadata extends \Google\Model
{
  /**
   * Internationalized alt text that describes the sticker image and any
   * animation associated with it.
   *
   * @var string
   */
  public $altText;
  /**
   * Specifies the localization language in which the alt text is returned.
   *
   * @var string
   */
  public $altTextLanguage;
  /**
   * Unique identifier of the Super Sticker. This is a shorter form of the
   * alt_text that includes pack name and a recognizable characteristic of the
   * sticker.
   *
   * @var string
   */
  public $stickerId;

  /**
   * Internationalized alt text that describes the sticker image and any
   * animation associated with it.
   *
   * @param string $altText
   */
  public function setAltText($altText)
  {
    $this->altText = $altText;
  }
  /**
   * @return string
   */
  public function getAltText()
  {
    return $this->altText;
  }
  /**
   * Specifies the localization language in which the alt text is returned.
   *
   * @param string $altTextLanguage
   */
  public function setAltTextLanguage($altTextLanguage)
  {
    $this->altTextLanguage = $altTextLanguage;
  }
  /**
   * @return string
   */
  public function getAltTextLanguage()
  {
    return $this->altTextLanguage;
  }
  /**
   * Unique identifier of the Super Sticker. This is a shorter form of the
   * alt_text that includes pack name and a recognizable characteristic of the
   * sticker.
   *
   * @param string $stickerId
   */
  public function setStickerId($stickerId)
  {
    $this->stickerId = $stickerId;
  }
  /**
   * @return string
   */
  public function getStickerId()
  {
    return $this->stickerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SuperStickerMetadata::class, 'Google_Service_YouTube_SuperStickerMetadata');
