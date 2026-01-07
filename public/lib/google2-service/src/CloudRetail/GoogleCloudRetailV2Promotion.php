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

class GoogleCloudRetailV2Promotion extends \Google\Model
{
  /**
   * Promotion identifier, which is the final component of name. For example,
   * this field is "free_gift", if name is
   * `projects/locations/global/catalogs/default_catalog/promotions/free_gift`.
   * The value must be a UTF-8 encoded string with a length limit of 128
   * characters, and match the pattern: `a-zA-Z*`. For example, id0LikeThis or
   * ID_1_LIKE_THIS. Otherwise, an INVALID_ARGUMENT error is returned.
   * Corresponds to Google Merchant Center property
   * [promotion_id](https://support.google.com/merchants/answer/7050148).
   *
   * @var string
   */
  public $promotionId;

  /**
   * Promotion identifier, which is the final component of name. For example,
   * this field is "free_gift", if name is
   * `projects/locations/global/catalogs/default_catalog/promotions/free_gift`.
   * The value must be a UTF-8 encoded string with a length limit of 128
   * characters, and match the pattern: `a-zA-Z*`. For example, id0LikeThis or
   * ID_1_LIKE_THIS. Otherwise, an INVALID_ARGUMENT error is returned.
   * Corresponds to Google Merchant Center property
   * [promotion_id](https://support.google.com/merchants/answer/7050148).
   *
   * @param string $promotionId
   */
  public function setPromotionId($promotionId)
  {
    $this->promotionId = $promotionId;
  }
  /**
   * @return string
   */
  public function getPromotionId()
  {
    return $this->promotionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2Promotion::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2Promotion');
