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

class Playlist extends \Google\Model
{
  protected $contentDetailsType = PlaylistContentDetails::class;
  protected $contentDetailsDataType = '';
  /**
   * Etag of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The ID that YouTube uses to uniquely identify the playlist.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#playlist".
   *
   * @var string
   */
  public $kind;
  protected $localizationsType = PlaylistLocalization::class;
  protected $localizationsDataType = 'map';
  protected $playerType = PlaylistPlayer::class;
  protected $playerDataType = '';
  protected $snippetType = PlaylistSnippet::class;
  protected $snippetDataType = '';
  protected $statusType = PlaylistStatus::class;
  protected $statusDataType = '';

  /**
   * The contentDetails object contains information like video count.
   *
   * @param PlaylistContentDetails $contentDetails
   */
  public function setContentDetails(PlaylistContentDetails $contentDetails)
  {
    $this->contentDetails = $contentDetails;
  }
  /**
   * @return PlaylistContentDetails
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
   * The ID that YouTube uses to uniquely identify the playlist.
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
   * "youtube#playlist".
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
   * Localizations for different languages
   *
   * @param PlaylistLocalization[] $localizations
   */
  public function setLocalizations($localizations)
  {
    $this->localizations = $localizations;
  }
  /**
   * @return PlaylistLocalization[]
   */
  public function getLocalizations()
  {
    return $this->localizations;
  }
  /**
   * The player object contains information that you would use to play the
   * playlist in an embedded player.
   *
   * @param PlaylistPlayer $player
   */
  public function setPlayer(PlaylistPlayer $player)
  {
    $this->player = $player;
  }
  /**
   * @return PlaylistPlayer
   */
  public function getPlayer()
  {
    return $this->player;
  }
  /**
   * The snippet object contains basic details about the playlist, such as its
   * title and description.
   *
   * @param PlaylistSnippet $snippet
   */
  public function setSnippet(PlaylistSnippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return PlaylistSnippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * The status object contains status information for the playlist.
   *
   * @param PlaylistStatus $status
   */
  public function setStatus(PlaylistStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return PlaylistStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Playlist::class, 'Google_Service_YouTube_Playlist');
