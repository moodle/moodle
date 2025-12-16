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

namespace Google\Service\DisplayVideo;

class Asset extends \Google\Model
{
  /**
   * The asset content. For uploaded assets, the content is the serving path.
   *
   * @var string
   */
  public $content;
  /**
   * Media ID of the uploaded asset. This is a unique identifier for the asset.
   * This ID can be passed to other API calls, e.g. CreateCreative to associate
   * the asset with a creative. The Media ID space updated on **April 5, 2023**.
   * Update media IDs cached before **April 5, 2023** by retrieving the new
   * media ID from associated creative resources or re-uploading the asset.
   *
   * @var string
   */
  public $mediaId;

  /**
   * The asset content. For uploaded assets, the content is the serving path.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Media ID of the uploaded asset. This is a unique identifier for the asset.
   * This ID can be passed to other API calls, e.g. CreateCreative to associate
   * the asset with a creative. The Media ID space updated on **April 5, 2023**.
   * Update media IDs cached before **April 5, 2023** by retrieving the new
   * media ID from associated creative resources or re-uploading the asset.
   *
   * @param string $mediaId
   */
  public function setMediaId($mediaId)
  {
    $this->mediaId = $mediaId;
  }
  /**
   * @return string
   */
  public function getMediaId()
  {
    return $this->mediaId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Asset::class, 'Google_Service_DisplayVideo_Asset');
