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

class GoogleCloudRetailV2ConversationalSearchRequestUserAnswerSelectedAnswer extends \Google\Model
{
  protected $productAttributeValueType = GoogleCloudRetailV2ProductAttributeValue::class;
  protected $productAttributeValueDataType = '';

  /**
   * Optional. This field specifies the selected answer which is a attribute
   * key-value.
   *
   * @param GoogleCloudRetailV2ProductAttributeValue $productAttributeValue
   */
  public function setProductAttributeValue(GoogleCloudRetailV2ProductAttributeValue $productAttributeValue)
  {
    $this->productAttributeValue = $productAttributeValue;
  }
  /**
   * @return GoogleCloudRetailV2ProductAttributeValue
   */
  public function getProductAttributeValue()
  {
    return $this->productAttributeValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ConversationalSearchRequestUserAnswerSelectedAnswer::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ConversationalSearchRequestUserAnswerSelectedAnswer');
