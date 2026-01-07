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

namespace Google\Service\Walletobjects;

class ValueAddedModuleData extends \Google\Model
{
  protected $bodyType = LocalizedString::class;
  protected $bodyDataType = '';
  protected $headerType = LocalizedString::class;
  protected $headerDataType = '';
  protected $imageType = Image::class;
  protected $imageDataType = '';
  /**
   * The index for sorting the modules. Modules with a lower sort index are
   * shown before modules with a higher sort index. If unspecified, the sort
   * index is assumed to be INT_MAX. For two modules with the same index, the
   * sorting behavior is undefined.
   *
   * @var int
   */
  public $sortIndex;
  /**
   * URI that the module leads to on click. This can be a web link or a deep
   * link as mentioned in https://developer.android.com/training/app-links/deep-
   * linking.
   *
   * @var string
   */
  public $uri;
  protected $viewConstraintsType = ModuleViewConstraints::class;
  protected $viewConstraintsDataType = '';

  /**
   * Body to be displayed on the module. Character limit is 50 and longer
   * strings will be truncated.
   *
   * @param LocalizedString $body
   */
  public function setBody(LocalizedString $body)
  {
    $this->body = $body;
  }
  /**
   * @return LocalizedString
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * Header to be displayed on the module. Character limit is 60 and longer
   * strings will be truncated.
   *
   * @param LocalizedString $header
   */
  public function setHeader(LocalizedString $header)
  {
    $this->header = $header;
  }
  /**
   * @return LocalizedString
   */
  public function getHeader()
  {
    return $this->header;
  }
  /**
   * Image to be displayed on the module. Recommended image ratio is 1:1. Images
   * will be resized to fit this ratio.
   *
   * @param Image $image
   */
  public function setImage(Image $image)
  {
    $this->image = $image;
  }
  /**
   * @return Image
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * The index for sorting the modules. Modules with a lower sort index are
   * shown before modules with a higher sort index. If unspecified, the sort
   * index is assumed to be INT_MAX. For two modules with the same index, the
   * sorting behavior is undefined.
   *
   * @param int $sortIndex
   */
  public function setSortIndex($sortIndex)
  {
    $this->sortIndex = $sortIndex;
  }
  /**
   * @return int
   */
  public function getSortIndex()
  {
    return $this->sortIndex;
  }
  /**
   * URI that the module leads to on click. This can be a web link or a deep
   * link as mentioned in https://developer.android.com/training/app-links/deep-
   * linking.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
  /**
   * Constraints that all must be met for the module to be shown.
   *
   * @param ModuleViewConstraints $viewConstraints
   */
  public function setViewConstraints(ModuleViewConstraints $viewConstraints)
  {
    $this->viewConstraints = $viewConstraints;
  }
  /**
   * @return ModuleViewConstraints
   */
  public function getViewConstraints()
  {
    return $this->viewConstraints;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValueAddedModuleData::class, 'Google_Service_Walletobjects_ValueAddedModuleData');
