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

class CssProduct extends \Google\Collection
{
  protected $collection_key = 'customAttributes';
  protected $attributesType = Attributes::class;
  protected $attributesDataType = '';
  /**
   * Output only. The two-letter [ISO
   * 639-1](http://en.wikipedia.org/wiki/ISO_639-1) language code for the
   * product.
   *
   * @var string
   */
  public $contentLanguage;
  protected $cssProductStatusType = CssProductStatus::class;
  protected $cssProductStatusDataType = '';
  protected $customAttributesType = CustomAttribute::class;
  protected $customAttributesDataType = 'array';
  /**
   * Output only. The feed label for the product.
   *
   * @var string
   */
  public $feedLabel;
  /**
   * The name of the CSS Product. Format:
   * `"accounts/{account}/cssProducts/{css_product}"`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Your unique raw identifier for the product.
   *
   * @var string
   */
  public $rawProvidedId;

  /**
   * Output only. A list of product attributes.
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
   * Output only. The two-letter [ISO
   * 639-1](http://en.wikipedia.org/wiki/ISO_639-1) language code for the
   * product.
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
   * Output only. The status of a product, data validation issues, that is,
   * information about a product computed asynchronously.
   *
   * @param CssProductStatus $cssProductStatus
   */
  public function setCssProductStatus(CssProductStatus $cssProductStatus)
  {
    $this->cssProductStatus = $cssProductStatus;
  }
  /**
   * @return CssProductStatus
   */
  public function getCssProductStatus()
  {
    return $this->cssProductStatus;
  }
  /**
   * Output only. A list of custom (CSS-provided) attributes. It can also be
   * used to submit any attribute of the feed specification in its generic form
   * (for example, `{ "name": "size type", "value": "regular" }`). This is
   * useful for submitting attributes not explicitly exposed by the API, such as
   * additional attributes used for Buy on Google.
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
   * Output only. The feed label for the product.
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
   * The name of the CSS Product. Format:
   * `"accounts/{account}/cssProducts/{css_product}"`
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
   * Output only. Your unique raw identifier for the product.
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
class_alias(CssProduct::class, 'Google_Service_Css_CssProduct');
