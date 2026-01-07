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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1ChannelPartnerLink extends \Google\Model
{
  /**
   * Not used.
   */
  public const LINK_STATE_CHANNEL_PARTNER_LINK_STATE_UNSPECIFIED = 'CHANNEL_PARTNER_LINK_STATE_UNSPECIFIED';
  /**
   * An invitation has been sent to the reseller to create a channel partner
   * link.
   */
  public const LINK_STATE_INVITED = 'INVITED';
  /**
   * Status when the reseller is active.
   */
  public const LINK_STATE_ACTIVE = 'ACTIVE';
  /**
   * Status when the reseller has been revoked by the distributor.
   */
  public const LINK_STATE_REVOKED = 'REVOKED';
  /**
   * Status when the reseller is suspended by Google or distributor.
   */
  public const LINK_STATE_SUSPENDED = 'SUSPENDED';
  protected $channelPartnerCloudIdentityInfoType = GoogleCloudChannelV1CloudIdentityInfo::class;
  protected $channelPartnerCloudIdentityInfoDataType = '';
  /**
   * Output only. Timestamp of when the channel partner link is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. URI of the web page where partner accepts the link invitation.
   *
   * @var string
   */
  public $inviteLinkUri;
  /**
   * Required. State of the channel partner link.
   *
   * @var string
   */
  public $linkState;
  /**
   * Output only. Resource name for the channel partner link, in the format
   * accounts/{account_id}/channelPartnerLinks/{id}.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Public identifier that a customer must use to generate a
   * transfer token to move to this distributor-reseller combination.
   *
   * @var string
   */
  public $publicId;
  /**
   * Required. Cloud Identity ID of the linked reseller.
   *
   * @var string
   */
  public $resellerCloudIdentityId;
  /**
   * Output only. Timestamp of when the channel partner link is updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Cloud Identity info of the channel partner (IR).
   *
   * @param GoogleCloudChannelV1CloudIdentityInfo $channelPartnerCloudIdentityInfo
   */
  public function setChannelPartnerCloudIdentityInfo(GoogleCloudChannelV1CloudIdentityInfo $channelPartnerCloudIdentityInfo)
  {
    $this->channelPartnerCloudIdentityInfo = $channelPartnerCloudIdentityInfo;
  }
  /**
   * @return GoogleCloudChannelV1CloudIdentityInfo
   */
  public function getChannelPartnerCloudIdentityInfo()
  {
    return $this->channelPartnerCloudIdentityInfo;
  }
  /**
   * Output only. Timestamp of when the channel partner link is created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. URI of the web page where partner accepts the link invitation.
   *
   * @param string $inviteLinkUri
   */
  public function setInviteLinkUri($inviteLinkUri)
  {
    $this->inviteLinkUri = $inviteLinkUri;
  }
  /**
   * @return string
   */
  public function getInviteLinkUri()
  {
    return $this->inviteLinkUri;
  }
  /**
   * Required. State of the channel partner link.
   *
   * Accepted values: CHANNEL_PARTNER_LINK_STATE_UNSPECIFIED, INVITED, ACTIVE,
   * REVOKED, SUSPENDED
   *
   * @param self::LINK_STATE_* $linkState
   */
  public function setLinkState($linkState)
  {
    $this->linkState = $linkState;
  }
  /**
   * @return self::LINK_STATE_*
   */
  public function getLinkState()
  {
    return $this->linkState;
  }
  /**
   * Output only. Resource name for the channel partner link, in the format
   * accounts/{account_id}/channelPartnerLinks/{id}.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. Public identifier that a customer must use to generate a
   * transfer token to move to this distributor-reseller combination.
   *
   * @param string $publicId
   */
  public function setPublicId($publicId)
  {
    $this->publicId = $publicId;
  }
  /**
   * @return string
   */
  public function getPublicId()
  {
    return $this->publicId;
  }
  /**
   * Required. Cloud Identity ID of the linked reseller.
   *
   * @param string $resellerCloudIdentityId
   */
  public function setResellerCloudIdentityId($resellerCloudIdentityId)
  {
    $this->resellerCloudIdentityId = $resellerCloudIdentityId;
  }
  /**
   * @return string
   */
  public function getResellerCloudIdentityId()
  {
    return $this->resellerCloudIdentityId;
  }
  /**
   * Output only. Timestamp of when the channel partner link is updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1ChannelPartnerLink::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1ChannelPartnerLink');
