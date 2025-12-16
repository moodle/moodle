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

class LiveChatSuperStickerDetails extends \Google\Model
{
  /**
   * A rendered string that displays the fund amount and currency to the user.
   *
   * @var string
   */
  public $amountDisplayString;
  /**
   * The amount purchased by the user, in micros (1,750,000 micros = 1.75).
   *
   * @var string
   */
  public $amountMicros;
  /**
   * The currency in which the purchase was made.
   *
   * @var string
   */
  public $currency;
  protected $superStickerMetadataType = SuperStickerMetadata::class;
  protected $superStickerMetadataDataType = '';
  /**
   * The tier in which the amount belongs. Lower amounts belong to lower tiers.
   * The lowest tier is 1.
   *
   * @var string
   */
  public $tier;

  /**
   * A rendered string that displays the fund amount and currency to the user.
   *
   * @param string $amountDisplayString
   */
  public function setAmountDisplayString($amountDisplayString)
  {
    $this->amountDisplayString = $amountDisplayString;
  }
  /**
   * @return string
   */
  public function getAmountDisplayString()
  {
    return $this->amountDisplayString;
  }
  /**
   * The amount purchased by the user, in micros (1,750,000 micros = 1.75).
   *
   * @param string $amountMicros
   */
  public function setAmountMicros($amountMicros)
  {
    $this->amountMicros = $amountMicros;
  }
  /**
   * @return string
   */
  public function getAmountMicros()
  {
    return $this->amountMicros;
  }
  /**
   * The currency in which the purchase was made.
   *
   * @param string $currency
   */
  public function setCurrency($currency)
  {
    $this->currency = $currency;
  }
  /**
   * @return string
   */
  public function getCurrency()
  {
    return $this->currency;
  }
  /**
   * Information about the Super Sticker.
   *
   * @param SuperStickerMetadata $superStickerMetadata
   */
  public function setSuperStickerMetadata(SuperStickerMetadata $superStickerMetadata)
  {
    $this->superStickerMetadata = $superStickerMetadata;
  }
  /**
   * @return SuperStickerMetadata
   */
  public function getSuperStickerMetadata()
  {
    return $this->superStickerMetadata;
  }
  /**
   * The tier in which the amount belongs. Lower amounts belong to lower tiers.
   * The lowest tier is 1.
   *
   * @param string $tier
   */
  public function setTier($tier)
  {
    $this->tier = $tier;
  }
  /**
   * @return string
   */
  public function getTier()
  {
    return $this->tier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveChatSuperStickerDetails::class, 'Google_Service_YouTube_LiveChatSuperStickerDetails');
