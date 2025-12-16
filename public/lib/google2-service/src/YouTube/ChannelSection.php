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

class ChannelSection extends \Google\Model
{
  protected $contentDetailsType = ChannelSectionContentDetails::class;
  protected $contentDetailsDataType = '';
  /**
   * Etag of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The ID that YouTube uses to uniquely identify the channel section.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#channelSection".
   *
   * @var string
   */
  public $kind;
  protected $localizationsType = ChannelSectionLocalization::class;
  protected $localizationsDataType = 'map';
  protected $snippetType = ChannelSectionSnippet::class;
  protected $snippetDataType = '';
  protected $targetingType = ChannelSectionTargeting::class;
  protected $targetingDataType = '';

  /**
   * The contentDetails object contains details about the channel section
   * content, such as a list of playlists or channels featured in the section.
   *
   * @param ChannelSectionContentDetails $contentDetails
   */
  public function setContentDetails(ChannelSectionContentDetails $contentDetails)
  {
    $this->contentDetails = $contentDetails;
  }
  /**
   * @return ChannelSectionContentDetails
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
   * The ID that YouTube uses to uniquely identify the channel section.
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
   * "youtube#channelSection".
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
   * @deprecated
   * @param ChannelSectionLocalization[] $localizations
   */
  public function setLocalizations($localizations)
  {
    $this->localizations = $localizations;
  }
  /**
   * @deprecated
   * @return ChannelSectionLocalization[]
   */
  public function getLocalizations()
  {
    return $this->localizations;
  }
  /**
   * The snippet object contains basic details about the channel section, such
   * as its type, style and title.
   *
   * @param ChannelSectionSnippet $snippet
   */
  public function setSnippet(ChannelSectionSnippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return ChannelSectionSnippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * The targeting object contains basic targeting settings about the channel
   * section.
   *
   * @deprecated
   * @param ChannelSectionTargeting $targeting
   */
  public function setTargeting(ChannelSectionTargeting $targeting)
  {
    $this->targeting = $targeting;
  }
  /**
   * @deprecated
   * @return ChannelSectionTargeting
   */
  public function getTargeting()
  {
    return $this->targeting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChannelSection::class, 'Google_Service_YouTube_ChannelSection');
