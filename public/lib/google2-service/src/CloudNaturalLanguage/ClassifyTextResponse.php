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

namespace Google\Service\CloudNaturalLanguage;

class ClassifyTextResponse extends \Google\Collection
{
  protected $collection_key = 'categories';
  protected $categoriesType = ClassificationCategory::class;
  protected $categoriesDataType = 'array';
  /**
   * The language of the text, which will be the same as the language specified
   * in the request or, if not specified, the automatically-detected language.
   * See Document.language_code field for more details.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Whether the language is officially supported. The API may still return a
   * response when the language is not supported, but it is on a best effort
   * basis.
   *
   * @var bool
   */
  public $languageSupported;

  /**
   * Categories representing the input document.
   *
   * @param ClassificationCategory[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return ClassificationCategory[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * The language of the text, which will be the same as the language specified
   * in the request or, if not specified, the automatically-detected language.
   * See Document.language_code field for more details.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Whether the language is officially supported. The API may still return a
   * response when the language is not supported, but it is on a best effort
   * basis.
   *
   * @param bool $languageSupported
   */
  public function setLanguageSupported($languageSupported)
  {
    $this->languageSupported = $languageSupported;
  }
  /**
   * @return bool
   */
  public function getLanguageSupported()
  {
    return $this->languageSupported;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClassifyTextResponse::class, 'Google_Service_CloudNaturalLanguage_ClassifyTextResponse');
