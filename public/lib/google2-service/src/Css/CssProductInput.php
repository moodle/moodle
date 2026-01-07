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

class CssProductInput extends \Google\Collection
{
  protected $collection_key = 'customAttributes';
  protected $attributesType = Attributes::class;
  protected $attributesDataType = '';
  /**
   * Required. The two-letter [ISO
   * 639-1](http://en.wikipedia.org/wiki/ISO_639-1) language code for the CSS
   * Product.
   *
   * @var string
   */
  public $contentLanguage;
  protected $customAttributesType = CustomAttribute::class;
  protected $customAttributesDataType = 'array';
  /**
   * Required. The [feed label](https://developers.google.com/shopping-
   * content/guides/products/feed-labels) for the CSS Product. Feed Label is
   * synonymous to "target country" and hence should always be a valid region
   * code. For example: 'DE' for Germany, 'FR' for France.
   *
   * @var string
   */
  public $feedLabel;
  /**
   * Output only. The name of the processed CSS Product. Format:
   * `accounts/{account}/cssProducts/{css_product}` "
   *
   * @var string
   */
  public $finalName;
  /**
   * DEPRECATED. Use expiration_date instead. Represents the existing version
   * (freshness) of the CSS Product, which can be used to preserve the right
   * order when multiple updates are done at the same time. This field must not
   * be set to the future time. If set, the update is prevented if a newer
   * version of the item already exists in our system (that is the last update
   * time of the existing CSS products is later than the freshness time set in
   * the update). If the update happens, the last update time is then set to
   * this freshness time. If not set, the update will not be prevented and the
   * last update time will default to when this request was received by the CSS
   * API. If the operation is prevented, the aborted exception will be thrown.
   *
   * @deprecated
   * @var string
   */
  public $freshnessTime;
  /**
   * Identifier. The name of the CSS Product input. Format:
   * `accounts/{account}/cssProductInputs/{css_product_input}`, where the last
   * section `css_product_input` consists of 3 parts:
   * contentLanguage~feedLabel~offerId. Example:
   * accounts/123/cssProductInputs/de~DE~rawProvidedId123
   *
   * @var string
   */
  public $name;
  /**
   * Required. Your unique identifier for the CSS Product. This is the same for
   * the CSS Product input and processed CSS Product. We only allow ids with
   * alphanumerics, underscores and dashes. See the [products feed
   * specification](https://support.google.com/merchants/answer/188494#id) for
   * details.
   *
   * @var string
   */
  public $rawProvidedId;

  /**
   * A list of CSS Product attributes.
   *
   * @param Attributes $attributes
   */
  public function setAttributes(Attributes $attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return Attributes
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Required. The two-letter [ISO
   * 639-1](http://en.wikipedia.org/wiki/ISO_639-1) language code for the CSS
   * Product.
   *
   * @param string $contentLanguage
   */
  public function setContentLanguage($contentLanguage)
  {
    $this->contentLanguage = $contentLanguage;
  }
  /**
   * @return string
   */
  public function getContentLanguage()
  {
    return $this->contentLanguage;
  }
  /**
   * A list of custom (CSS-provided) attributes. It can also be used for
   * submitting any attribute of the feed specification in its generic form (for
   * example: `{ "name": "size type", "value": "regular" }`). This is useful for
   * submitting attributes not explicitly exposed by the API, such as additional
   * attributes used for Buy on Google.
   *
   * @param CustomAttribute[] $customAttributes
   */
  public function setCustomAttributes($customAttributes)
  {
    $this->customAttributes = $customAttributes;
  }
  /**
   * @return CustomAttribute[]
   */
  public function getCustomAttributes()
  {
    return $this->customAttributes;
  }
  /**
   * Required. The [feed label](https://developers.google.com/shopping-
   * content/guides/products/feed-labels) for the CSS Product. Feed Label is
   * synonymous to "target country" and hence should always be a valid region
   * code. For example: 'DE' for Germany, 'FR' for France.
   *
   * @param string $feedLabel
   */
  public function setFeedLabel($feedLabel)
  {
    $this->feedLabel = $feedLabel;
  }
  /**
   * @return string
   */
  public function getFeedLabel()
  {
    return $this->feedLabel;
  }
  /**
   * Output only. The name of the processed CSS Product. Format:
   * `accounts/{account}/cssProducts/{css_product}` "
   *
   * @param string $finalName
   */
  public function setFinalName($finalName)
  {
    $this->finalName = $finalName;
  }
  /**
   * @return string
   */
  public function getFinalName()
  {
    return $this->finalName;
  }
  /**
   * DEPRECATED. Use expiration_date instead. Represents the existing version
   * (freshness) of the CSS Product, which can be used to preserve the right
   * order when multiple updates are done at the same time. This field must not
   * be set to the future time. If set, the update is prevented if a newer
   * version of the item already exists in our system (that is the last update
   * time of the existing CSS products is later than the freshness time set in
   * the update). If the update happens, the last update time is then set to
   * this freshness time. If not set, the update will not be prevented and the
   * last update time will default to when this request was received by the CSS
   * API. If the operation is prevented, the aborted exception will be thrown.
   *
   * @deprecated
   * @param string $freshnessTime
   */
  public function setFreshnessTime($freshnessTime)
  {
    $this->freshnessTime = $freshnessTime;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getFreshnessTime()
  {
    return $this->freshnessTime;
  }
  /**
   * Identifier. The name of the CSS Product input. Format:
   * `accounts/{account}/cssProductInputs/{css_product_input}`, where the last
   * section `css_product_input` consists of 3 parts:
   * contentLanguage~feedLabel~offerId. Example:
   * accounts/123/cssProductInputs/de~DE~rawProvidedId123
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
   * Required. Your unique identifier for the CSS Product. This is the same for
   * the CSS Product input and processed CSS Product. We only allow ids with
   * alphanumerics, underscores and dashes. See the [products feed
   * specification](https://support.google.com/merchants/answer/188494#id) for
   * details.
   *
   * @param string $rawProvidedId
   */
  public function setRawProvidedId($rawProvidedId)
  {
    $this->rawProvidedId = $rawProvidedId;
  }
  /**
   * @return string
   */
  public function getRawProvidedId()
  {
    return $this->rawProvidedId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CssProductInput::class, 'Google_Service_Css_CssProductInput');
