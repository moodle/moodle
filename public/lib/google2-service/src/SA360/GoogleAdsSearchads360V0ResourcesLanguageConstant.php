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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesLanguageConstant extends \Google\Model
{
  /**
   * Output only. The language code, for example, "en_US", "en_AU", "es", "fr",
   * etc.
   *
   * @var string
   */
  public $code;
  /**
   * Output only. The ID of the language constant.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. The full name of the language in English, for example,
   * "English (US)", "Spanish", etc.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The resource name of the language constant. Language constant
   * resource names have the form: `languageConstants/{criterion_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. Whether the language is targetable.
   *
   * @var bool
   */
  public $targetable;

  /**
   * Output only. The language code, for example, "en_US", "en_AU", "es", "fr",
   * etc.
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
   * Output only. The ID of the language constant.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. The full name of the language in English, for example,
   * "English (US)", "Spanish", etc.
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
  /**
   * Output only. The resource name of the language constant. Language constant
   * resource names have the form: `languageConstants/{criterion_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. Whether the language is targetable.
   *
   * @param bool $targetable
   */
  public function setTargetable($targetable)
  {
    $this->targetable = $targetable;
  }
  /**
   * @return bool
   */
  public function getTargetable()
  {
    return $this->targetable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesLanguageConstant::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesLanguageConstant');
