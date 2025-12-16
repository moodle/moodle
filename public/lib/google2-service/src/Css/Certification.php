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

namespace Google\Service\Css;

class Certification extends \Google\Model
{
  /**
   * The authority or certification body responsible for issuing the
   * certification. At this time, the most common value is "EC" or
   * “European_Commission” for energy labels in the EU.
   *
   * @var string
   */
  public $authority;
  /**
   * The code of the certification. For example, for the EPREL certificate with
   * the link https://eprel.ec.europa.eu/screen/product/dishwashers2019/123456
   * the code is 123456. The code is required for European Energy Labels.
   *
   * @var string
   */
  public $code;
  /**
   * The name of the certification. At this time, the most common value is
   * "EPREL", which represents energy efficiency certifications in the EU
   * European Registry for Energy Labeling (EPREL) database.
   *
   * @var string
   */
  public $name;

  /**
   * The authority or certification body responsible for issuing the
   * certification. At this time, the most common value is "EC" or
   * “European_Commission” for energy labels in the EU.
   *
   * @param string $authority
   */
  public function setAuthority($authority)
  {
    $this->authority = $authority;
  }
  /**
   * @return string
   */
  public function getAuthority()
  {
    return $this->authority;
  }
  /**
   * The code of the certification. For example, for the EPREL certificate with
   * the link https://eprel.ec.europa.eu/screen/product/dishwashers2019/123456
   * the code is 123456. The code is required for European Energy Labels.
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
   * The name of the certification. At this time, the most common value is
   * "EPREL", which represents energy efficiency certifications in the EU
   * European Registry for Energy Labeling (EPREL) database.
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
class_alias(Certification::class, 'Google_Service_Css_Certification');
