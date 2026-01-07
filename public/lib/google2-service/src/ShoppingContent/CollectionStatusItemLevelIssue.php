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

class CollectionStatusItemLevelIssue extends \Google\Collection
{
  protected $collection_key = 'applicableCountries';
  /**
   * Country codes (ISO 3166-1 alpha-2) where issue applies to the offer.
   *
   * @var string[]
   */
  public $applicableCountries;
  /**
   * The attribute's name, if the issue is caused by a single attribute.
   *
   * @var string
   */
  public $attributeName;
  /**
   * The error code of the issue.
   *
   * @var string
   */
  public $code;
  /**
   * A short issue description in English.
   *
   * @var string
   */
  public $description;
  /**
   * The destination the issue applies to.
   *
   * @var string
   */
  public $destination;
  /**
   * A detailed issue description in English.
   *
   * @var string
   */
  public $detail;
  /**
   * The URL of a web page to help with resolving this issue.
   *
   * @var string
   */
  public $documentation;
  /**
   * Whether the issue can be resolved by the merchant.
   *
   * @var string
   */
  public $resolution;
  /**
   * How this issue affects the serving of the collection.
   *
   * @var string
   */
  public $servability;

  /**
   * Country codes (ISO 3166-1 alpha-2) where issue applies to the offer.
   *
   * @param string[] $applicableCountries
   */
  public function setApplicableCountries($applicableCountries)
  {
    $this->applicableCountries = $applicableCountries;
  }
  /**
   * @return string[]
   */
  public function getApplicableCountries()
  {
    return $this->applicableCountries;
  }
  /**
   * The attribute's name, if the issue is caused by a single attribute.
   *
   * @param string $attributeName
   */
  public function setAttributeName($attributeName)
  {
    $this->attributeName = $attributeName;
  }
  /**
   * @return string
   */
  public function getAttributeName()
  {
    return $this->attributeName;
  }
  /**
   * The error code of the issue.
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
   * A short issue description in English.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The destination the issue applies to.
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * A detailed issue description in English.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * The URL of a web page to help with resolving this issue.
   *
   * @param string $documentation
   */
  public function setDocumentation($documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return string
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Whether the issue can be resolved by the merchant.
   *
   * @param string $resolution
   */
  public function setResolution($resolution)
  {
    $this->resolution = $resolution;
  }
  /**
   * @return string
   */
  public function getResolution()
  {
    return $this->resolution;
  }
  /**
   * How this issue affects the serving of the collection.
   *
   * @param string $servability
   */
  public function setServability($servability)
  {
    $this->servability = $servability;
  }
  /**
   * @return string
   */
  public function getServability()
  {
    return $this->servability;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CollectionStatusItemLevelIssue::class, 'Google_Service_ShoppingContent_CollectionStatusItemLevelIssue');
