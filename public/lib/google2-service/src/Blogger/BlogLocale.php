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

namespace Google\Service\Blogger;

class BlogLocale extends \Google\Model
{
  /**
   * The country this blog's locale is set to.
   *
   * @var string
   */
  public $country;
  /**
   * The language this blog is authored in.
   *
   * @var string
   */
  public $language;
  /**
   * The language variant this blog is authored in.
   *
   * @var string
   */
  public $variant;

  /**
   * The country this blog's locale is set to.
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * The language this blog is authored in.
   *
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * The language variant this blog is authored in.
   *
   * @param string $variant
   */
  public function setVariant($variant)
  {
    $this->variant = $variant;
  }
  /**
   * @return string
   */
  public function getVariant()
  {
    return $this->variant;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BlogLocale::class, 'Google_Service_Blogger_BlogLocale');
