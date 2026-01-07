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

use Google\Service\YouTube\BatchGetStatsResponse;

/**
 * The "videos" collection of methods.
 * Typical usage is:
 *  <code>
 *   $youtubeService = new Google\Service\YouTube(...);
 *   $videos = $youtubeService->youtube_v3_videos;
 *  </code>
 */
class YoutubeV3Videos extends \Google\Service\Resource
{
  /**
   * Retrieves a batch of VideoStat resources, possibly filtered.
   * (videos.batchGetStats)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string onBehalfOfContentOwner Optional. **Note:** This parameter
   * is intended exclusively for YouTube content partners. The
   * `onBehalfOfContentOwner` parameter indicates that the request's authorization
   * credentials identify a YouTube CMS user who is acting on behalf of the
   * content owner specified in the parameter value. This parameter is intended
   * for YouTube content partners that own and manage many different YouTube
   * channels. It allows content owners to authenticate once and get access to all
   * their video and channel data, without having to provide authentication
   * credentials for each individual channel. The CMS account that the user
   * authenticates with must be linked to the specified YouTube content owner.
   * @opt_param string part Required. The `**part**` parameter specifies a comma-
   * separated list of one or more `videoStat` resource properties that the API
   * response will include. If the parameter identifies a property that contains
   * child properties, the child properties will be included in the response. For
   * example, in a `videoStat` resource, the `statistics` property contains
   * `view_count` and `like_count`. As such, if you set `**part=snippet**`, the
   * API response will contain all of those properties.
   * @opt_param string videoIds Required. Return videos with the given ids.
   * @return BatchGetStatsResponse
   * @throws \Google\Service\Exception
   */
  public function batchGetStats($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('batchGetStats', [$params], BatchGetStatsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubeV3Videos::class, 'Google_Service_YouTube_Resource_YoutubeV3Videos');
