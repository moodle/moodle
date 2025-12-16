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

namespace Google\Service\ShoppingContent;

class AccountConversionSettings extends \Google\Model
{
  /**
   * When enabled, free listing URLs have a parameter to enable conversion
   * tracking for products owned by the current merchant account. See [auto-
   * tagging](https://support.google.com/merchants/answer/11127659).
   *
   * @var bool
   */
  public $freeListingsAutoTaggingEnabled;

  /**
   * When enabled, free listing URLs have a parameter to enable conversion
   * tracking for products owned by the current merchant account. See [auto-
   * tagging](https://support.google.com/merchants/answer/11127659).
   *
   * @param bool $freeListingsAutoTaggingEnabled
   */
  public function setFreeListingsAutoTaggingEnabled($freeListingsAutoTaggingEnabled)
  {
    $this->freeListingsAutoTaggingEnabled = $freeListingsAutoTaggingEnabled;
  }
  /**
   * @return bool
   */
  public function getFreeListingsAutoTaggingEnabled()
  {
    return $this->freeListingsAutoTaggingEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountConversionSettings::class, 'Google_Service_ShoppingContent_AccountConversionSettings');
