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

namespace Google\Service\YouTube\Resource;

use Google\Service\YouTube\LiveChatMessageListResponse;

/**
 * The "messages" collection of methods.
 * Typical usage is:
 *  <code>
 *   $youtubeService = new Google\Service\YouTube(...);
 *   $messages = $youtubeService->youtube_v3_liveChat_messages;
 *  </code>
 */
class YoutubeV3LiveChatMessages extends \Google\Service\Resource
{
  /**
   * Allows a user to load live chat through a server-streamed RPC.
   * (messages.stream)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string hl Specifies the localization language in which the system
   * messages should be returned.
   * @opt_param string liveChatId The id of the live chat for which comments
   * should be returned.
   * @opt_param string maxResults The *maxResults* parameter specifies the maximum
   * number of items that should be returned in the result set. Not used in the
   * streaming RPC.
   * @opt_param string pageToken The *pageToken* parameter identifies a specific
   * page in the result set that should be returned. In an API response, the
   * nextPageToken property identify other pages that could be retrieved.
   * @opt_param string part The *part* parameter specifies the liveChatComment
   * resource parts that the API response will include. Supported values are id,
   * snippet, and authorDetails.
   * @opt_param string profileImageSize Specifies the size of the profile image
   * that should be returned for each user.
   * @return LiveChatMessageListResponse
   * @throws \Google\Service\Exception
   */
  public function stream($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('stream', [$params], LiveChatMessageListResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubeV3LiveChatMessages::class, 'Google_Service_YouTube_Resource_YoutubeV3LiveChatMessages');
