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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2Audience extends \Google\Collection
{
  protected $collection_key = 'genders';
  /**
   * The age groups of the audience. Strongly encouraged to use the standard
   * values: "newborn" (up to 3 months old), "infant" (3–12 months old),
   * "toddler" (1–5 years old), "kids" (5–13 years old), "adult" (typically
   * teens or older). At most 5 values are allowed. Each value must be a UTF-8
   * encoded string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. Google Merchant Center property
   * [age_group](https://support.google.com/merchants/answer/6324463).
   * Schema.org property
   * [Product.audience.suggestedMinAge](https://schema.org/suggestedMinAge) and
   * [Product.audience.suggestedMaxAge](https://schema.org/suggestedMaxAge).
   *
   * @var string[]
   */
  public $ageGroups;
  /**
   * The genders of the audience. Strongly encouraged to use the standard
   * values: "male", "female", "unisex". At most 5 values are allowed. Each
   * value must be a UTF-8 encoded string with a length limit of 128 characters.
   * Otherwise, an INVALID_ARGUMENT error is returned. Google Merchant Center
   * property [gender](https://support.google.com/merchants/answer/6324479).
   * Schema.org property
   * [Product.audience.suggestedGender](https://schema.org/suggestedGender).
   *
   * @var string[]
   */
  public $genders;

  /**
   * The age groups of the audience. Strongly encouraged to use the standard
   * values: "newborn" (up to 3 months old), "infant" (3–12 months old),
   * "toddler" (1–5 years old), "kids" (5–13 years old), "adult" (typically
   * teens or older). At most 5 values are allowed. Each value must be a UTF-8
   * encoded string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. Google Merchant Center property
   * [age_group](https://support.google.com/merchants/answer/6324463).
   * Schema.org property
   * [Product.audience.suggestedMinAge](https://schema.org/suggestedMinAge) and
   * [Product.audience.suggestedMaxAge](https://schema.org/suggestedMaxAge).
   *
   * @param string[] $ageGroups
   */
  public function setAgeGroups($ageGroups)
  {
    $this->ageGroups = $ageGroups;
  }
  /**
   * @return string[]
   */
  public function getAgeGroups()
  {
    return $this->ageGroups;
  }
  /**
   * The genders of the audience. Strongly encouraged to use the standard
   * values: "male", "female", "unisex". At most 5 values are allowed. Each
   * value must be a UTF-8 encoded string with a length limit of 128 characters.
   * Otherwise, an INVALID_ARGUMENT error is returned. Google Merchant Center
   * property [gender](https://support.google.com/merchants/answer/6324479).
   * Schema.org property
   * [Product.audience.suggestedGender](https://schema.org/suggestedGender).
   *
   * @param string[] $genders
   */
  public function setGenders($genders)
  {
    $this->genders = $genders;
  }
  /**
   * @return string[]
   */
  public function getGenders()
  {
    return $this->genders;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2Audience::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2Audience');
