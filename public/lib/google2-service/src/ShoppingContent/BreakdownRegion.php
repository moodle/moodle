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

class BreakdownRegion extends \Google\Model
{
  /**
   * The [CLDR territory code]
   * (http://www.unicode.org/repos/cldr/tags/latest/common/main/en.xml)
   *
   * @var string
   */
  public $code;
  /**
   * The localized name of the region. For region with code='001' the value is
   * 'All countries' or the equivalent in other languages.
   *
   * @var string
   */
  public $name;

  /**
   * The [CLDR territory code]
   * (http://www.unicode.org/repos/cldr/tags/latest/common/main/en.xml)
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * The localized name of the region. For region with code='001' the value is
   * 'All countries' or the equivalent in other languages.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BreakdownRegion::class, 'Google_Service_ShoppingContent_BreakdownRegion');
