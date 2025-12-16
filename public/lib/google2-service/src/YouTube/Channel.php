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

class Channel extends \Google\Model
{
  protected $auditDetailsType = ChannelAuditDetails::class;
  protected $auditDetailsDataType = '';
  protected $brandingSettingsType = ChannelBrandingSettings::class;
  protected $brandingSettingsDataType = '';
  protected $contentDetailsType = ChannelContentDetails::class;
  protected $contentDetailsDataType = '';
  protected $contentOwnerDetailsType = ChannelContentOwnerDetails::class;
  protected $contentOwnerDetailsDataType = '';
  protected $conversionPingsType = ChannelConversionPings::class;
  protected $conversionPingsDataType = '';
  /**
   * Etag of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The ID that YouTube uses to uniquely identify the channel.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#channel".
   *
   * @var string
   */
  public $kind;
  protected $localizationsType = ChannelLocalization::class;
  protected $localizationsDataType = 'map';
  protected $snippetType = ChannelSnippet::class;
  protected $snippetDataType = '';
  protected $statisticsType = ChannelStatistics::class;
  protected $statisticsDataType = '';
  protected $statusType = ChannelStatus::class;
  protected $statusDataType = '';
  protected $topicDetailsType = ChannelTopicDetails::class;
  protected $topicDetailsDataType = '';

  /**
   * The auditionDetails object encapsulates channel data that is relevant for
   * YouTube Partners during the audition process.
   *
   * @param ChannelAuditDetails $auditDetails
   */
  public function setAuditDetails(ChannelAuditDetails $auditDetails)
  {
    $this->auditDetails = $auditDetails;
  }
  /**
   * @return ChannelAuditDetails
   */
  public function getAuditDetails()
  {
    return $this->auditDetails;
  }
  /**
   * The brandingSettings object encapsulates information about the branding of
   * the channel.
   *
   * @param ChannelBrandingSettings $brandingSettings
   */
  public function setBrandingSettings(ChannelBrandingSettings $brandingSettings)
  {
    $this->brandingSettings = $brandingSettings;
  }
  /**
   * @return ChannelBrandingSettings
   */
  public function getBrandingSettings()
  {
    return $this->brandingSettings;
  }
  /**
   * The contentDetails object encapsulates information about the channel's
   * content.
   *
   * @param ChannelContentDetails $contentDetails
   */
  public function setContentDetails(ChannelContentDetails $contentDetails)
  {
    $this->contentDetails = $contentDetails;
  }
  /**
   * @return ChannelContentDetails
   */
  public function getContentDetails()
  {
    return $this->contentDetails;
  }
  /**
   * The contentOwnerDetails object encapsulates channel data that is relevant
   * for YouTube Partners linked with the channel.
   *
   * @param ChannelContentOwnerDetails $contentOwnerDetails
   */
  public function setContentOwnerDetails(ChannelContentOwnerDetails $contentOwnerDetails)
  {
    $this->contentOwnerDetails = $contentOwnerDetails;
  }
  /**
   * @return ChannelContentOwnerDetails
   */
  public function getContentOwnerDetails()
  {
    return $this->contentOwnerDetails;
  }
  /**
   * The conversionPings object encapsulates information about conversion pings
   * that need to be respected by the channel.
   *
   * @deprecated
   * @param ChannelConversionPings $conversionPings
   */
  public function setConversionPings(ChannelConversionPings $conversionPings)
  {
    $this->conversionPings = $conversionPings;
  }
  /**
   * @deprecated
   * @return ChannelConversionPings
   */
  public function getConversionPings()
  {
    return $this->conversionPings;
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
   * The ID that YouTube uses to uniquely identify the channel.
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
   * "youtube#channel".
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
   * @param ChannelLocalization[] $localizations
   */
  public function setLocalizations($localizations)
  {
    $this->localizations = $localizations;
  }
  /**
   * @return ChannelLocalization[]
   */
  public function getLocalizations()
  {
    return $this->localizations;
  }
  /**
   * The snippet object contains basic details about the channel, such as its
   * title, description, and thumbnail images.
   *
   * @param ChannelSnippet $snippet
   */
  public function setSnippet(ChannelSnippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return ChannelSnippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * The statistics object encapsulates statistics for the channel.
   *
   * @param ChannelStatistics $statistics
   */
  public function setStatistics(ChannelStatistics $statistics)
  {
    $this->statistics = $statistics;
  }
  /**
   * @return ChannelStatistics
   */
  public function getStatistics()
  {
    return $this->statistics;
  }
  /**
   * The status object encapsulates information about the privacy status of the
   * channel.
   *
   * @param ChannelStatus $status
   */
  public function setStatus(ChannelStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return ChannelStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The topicDetails object encapsulates information about Freebase topics
   * associated with the channel.
   *
   * @param ChannelTopicDetails $topicDetails
   */
  public function setTopicDetails(ChannelTopicDetails $topicDetails)
  {
    $this->topicDetails = $topicDetails;
  }
  /**
   * @return ChannelTopicDetails
   */
  public function getTopicDetails()
  {
    return $this->topicDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Channel::class, 'Google_Service_YouTube_Channel');
