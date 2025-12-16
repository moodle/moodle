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

class ProductStructuredDescription extends \Google\Model
{
  /**
   * Required. The description text. Maximum length is 5000 characters.
   *
   * @var string
   */
  public $content;
  /**
   * Optional. The digital source type. Acceptable values are: -
   * "`trained_algorithmic_media`" - "`default`"
   *
   * @var string
   */
  public $digitalSourceType;

  /**
   * Required. The description text. Maximum length is 5000 characters.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Optional. The digital source type. Acceptable values are: -
   * "`trained_algorithmic_media`" - "`default`"
   *
   * @param string $digitalSourceType
   */
  public function setDigitalSourceType($digitalSourceType)
  {
    $this->digitalSourceType = $digitalSourceType;
  }
  /**
   * @return string
   */
  public function getDigitalSourceType()
  {
    return $this->digitalSourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductStructuredDescription::class, 'Google_Service_ShoppingContent_ProductStructuredDescription');
