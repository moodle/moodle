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

namespace Google\Service\YouTube;

class TestItem extends \Google\Model
{
  /**
   * Etag for the resource. See https://en.wikipedia.org/wiki/HTTP_ETag.
   *
   * @var string
   */
  public $etag;
  /**
   * @var bool
   */
  public $featuredPart;
  /**
   * @var string
   */
  public $gaia;
  /**
   * @var string
   */
  public $id;
  protected $snippetType = TestItemTestItemSnippet::class;
  protected $snippetDataType = '';

  /**
   * Etag for the resource. See https://en.wikipedia.org/wiki/HTTP_ETag.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * @param bool $featuredPart
   */
  public function setFeaturedPart($featuredPart)
  {
    $this->featuredPart = $featuredPart;
  }
  /**
   * @return bool
   */
  public function getFeaturedPart()
  {
    return $this->featuredPart;
  }
  /**
   * @param string $gaia
   */
  public function setGaia($gaia)
  {
    $this->gaia = $gaia;
  }
  /**
   * @return string
   */
  public function getGaia()
  {
    return $this->gaia;
  }
  /**
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
   * @param TestItemTestItemSnippet $snippet
   */
  public function setSnippet(TestItemTestItemSnippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return TestItemTestItemSnippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestItem::class, 'Google_Service_YouTube_TestItem');
