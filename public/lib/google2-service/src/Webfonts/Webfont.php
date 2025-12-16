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

namespace Google\Service\Webfonts;

class Webfont extends \Google\Collection
{
  protected $collection_key = 'variants';
  protected $axesType = Axis::class;
  protected $axesDataType = 'array';
  /**
   * The category of the font.
   *
   * @var string
   */
  public $category;
  /**
   * The color format(s) available for this family.
   *
   * @var string[]
   */
  public $colorCapabilities;
  /**
   * The name of the font.
   *
   * @var string
   */
  public $family;
  /**
   * The font files (with all supported scripts) for each one of the available
   * variants, as a key : value map.
   *
   * @var string[]
   */
  public $files;
  /**
   * This kind represents a webfont object in the webfonts service.
   *
   * @var string
   */
  public $kind;
  /**
   * The date (format "yyyy-MM-dd") the font was modified for the last time.
   *
   * @var string
   */
  public $lastModified;
  /**
   * Font URL for menu subset, a subset of the font that is enough to display
   * the font name
   *
   * @var string
   */
  public $menu;
  /**
   * The scripts supported by the font.
   *
   * @var string[]
   */
  public $subsets;
  protected $tagsType = Tag::class;
  protected $tagsDataType = 'array';
  /**
   * The available variants for the font.
   *
   * @var string[]
   */
  public $variants;
  /**
   * The font version.
   *
   * @var string
   */
  public $version;

  /**
   * Axis for variable fonts.
   *
   * @param Axis[] $axes
   */
  public function setAxes($axes)
  {
    $this->axes = $axes;
  }
  /**
   * @return Axis[]
   */
  public function getAxes()
  {
    return $this->axes;
  }
  /**
   * The category of the font.
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The color format(s) available for this family.
   *
   * @param string[] $colorCapabilities
   */
  public function setColorCapabilities($colorCapabilities)
  {
    $this->colorCapabilities = $colorCapabilities;
  }
  /**
   * @return string[]
   */
  public function getColorCapabilities()
  {
    return $this->colorCapabilities;
  }
  /**
   * The name of the font.
   *
   * @param string $family
   */
  public function setFamily($family)
  {
    $this->family = $family;
  }
  /**
   * @return string
   */
  public function getFamily()
  {
    return $this->family;
  }
  /**
   * The font files (with all supported scripts) for each one of the available
   * variants, as a key : value map.
   *
   * @param string[] $files
   */
  public function setFiles($files)
  {
    $this->files = $files;
  }
  /**
   * @return string[]
   */
  public function getFiles()
  {
    return $this->files;
  }
  /**
   * This kind represents a webfont object in the webfonts service.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The date (format "yyyy-MM-dd") the font was modified for the last time.
   *
   * @param string $lastModified
   */
  public function setLastModified($lastModified)
  {
    $this->lastModified = $lastModified;
  }
  /**
   * @return string
   */
  public function getLastModified()
  {
    return $this->lastModified;
  }
  /**
   * Font URL for menu subset, a subset of the font that is enough to display
   * the font name
   *
   * @param string $menu
   */
  public function setMenu($menu)
  {
    $this->menu = $menu;
  }
  /**
   * @return string
   */
  public function getMenu()
  {
    return $this->menu;
  }
  /**
   * The scripts supported by the font.
   *
   * @param string[] $subsets
   */
  public function setSubsets($subsets)
  {
    $this->subsets = $subsets;
  }
  /**
   * @return string[]
   */
  public function getSubsets()
  {
    return $this->subsets;
  }
  /**
   * The tags that apply to this family.
   *
   * @param Tag[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return Tag[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * The available variants for the font.
   *
   * @param string[] $variants
   */
  public function setVariants($variants)
  {
    $this->variants = $variants;
  }
  /**
   * @return string[]
   */
  public function getVariants()
  {
    return $this->variants;
  }
  /**
   * The font version.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Webfont::class, 'Google_Service_Webfonts_Webfont');
