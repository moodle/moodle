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

class PlaylistItem extends \Google\Model
{
  protected $contentDetailsType = PlaylistItemContentDetails::class;
  protected $contentDetailsDataType = '';
  /**
   * Etag of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The ID that YouTube uses to uniquely identify the playlist item.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#playlistItem".
   *
   * @var string
   */
  public $kind;
  protected $snippetType = PlaylistItemSnippet::class;
  protected $snippetDataType = '';
  protected $statusType = PlaylistItemStatus::class;
  protected $statusDataType = '';

  /**
   * The contentDetails object is included in the resource if the included item
   * is a YouTube video. The object contains additional information about the
   * video.
   *
   * @param PlaylistItemContentDetails $contentDetails
   */
  public function setContentDetails(PlaylistItemContentDetails $contentDetails)
  {
    $this->contentDetails = $contentDetails;
  }
  /**
   * @return PlaylistItemContentDetails
   */
  public function getContentDetails()
  {
    return $this->contentDetails;
  }
  /**
   * Etag of this resource.
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
   * The ID that YouTube uses to uniquely identify the playlist item.
   *
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#playlistItem".
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
   * The snippet object contains basic details about the playlist item, such as
   * its title and position in the playlist.
   *
   * @param PlaylistItemSnippet $snippet
   */
  public function setSnippet(PlaylistItemSnippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return PlaylistItemSnippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * The status object contains information about the playlist item's privacy
   * status.
   *
   * @param PlaylistItemStatus $status
   */
  public function setStatus(PlaylistItemStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return PlaylistItemStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlaylistItem::class, 'Google_Service_YouTube_PlaylistItem');
