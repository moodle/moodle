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

namespace Google\Service\MyBusinessBusinessInformation;

class Categories extends \Google\Collection
{
  protected $collection_key = 'additionalCategories';
  protected $additionalCategoriesType = Category::class;
  protected $additionalCategoriesDataType = 'array';
  protected $primaryCategoryType = Category::class;
  protected $primaryCategoryDataType = '';

  /**
   * Optional. Additional categories to describe your business. Categories help
   * your customers find accurate, specific results for services they're
   * interested in. To keep your business information accurate and live, make
   * sure that you use as few categories as possible to describe your overall
   * core business. Choose categories that are as specific as possible, but
   * representative of your main business.
   *
   * @param Category[] $additionalCategories
   */
  public function setAdditionalCategories($additionalCategories)
  {
    $this->additionalCategories = $additionalCategories;
  }
  /**
   * @return Category[]
   */
  public function getAdditionalCategories()
  {
    return $this->additionalCategories;
  }
  /**
   * Required. Category that best describes the core business this location
   * engages in.
   *
   * @param Category $primaryCategory
   */
  public function setPrimaryCategory(Category $primaryCategory)
  {
    $this->primaryCategory = $primaryCategory;
  }
  /**
   * @return Category
   */
  public function getPrimaryCategory()
  {
    return $this->primaryCategory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Categories::class, 'Google_Service_MyBusinessBusinessInformation_Categories');
