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

class ThirdPartyLinkSnippet extends \Google\Model
{
  public const TYPE_linkUnspecified = 'linkUnspecified';
  /**
   * A link that is connecting (or about to connect) a channel with a store on a
   * merchandising platform in order to enable retail commerce capabilities for
   * that channel on YouTube.
   */
  public const TYPE_channelToStoreLink = 'channelToStoreLink';
  protected $channelToStoreLinkType = ChannelToStoreLinkDetails::class;
  protected $channelToStoreLinkDataType = '';
  /**
   * Type of the link named after the entities that are being linked.
   *
   * @var string
   */
  public $type;

  /**
   * Information specific to a link between a channel and a store on a
   * merchandising platform.
   *
   * @param ChannelToStoreLinkDetails $channelToStoreLink
   */
  public function setChannelToStoreLink(ChannelToStoreLinkDetails $channelToStoreLink)
  {
    $this->channelToStoreLink = $channelToStoreLink;
  }
  /**
   * @return ChannelToStoreLinkDetails
   */
  public function getChannelToStoreLink()
  {
    return $this->channelToStoreLink;
  }
  /**
   * Type of the link named after the entities that are being linked.
   *
   * Accepted values: linkUnspecified, channelToStoreLink
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ThirdPartyLinkSnippet::class, 'Google_Service_YouTube_ThirdPartyLinkSnippet');
