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

namespace Google\Service\DiscoveryEngine\Resource;

use Google\Service\DiscoveryEngine\GdataMedia;

/**
 * The "media" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $media = $discoveryengineService->media;
 *  </code>
 */
class Media extends \Google\Service\Resource
{
  /**
   * Downloads a file from the session. (media.download)
   *
   * @param string $name Required. The resource name of the Session. Format: `proj
   * ects/{project}/locations/{location}/collections/{collection}/engines/{engine}
   * /sessions/{session}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fileId Required. The ID of the file to be downloaded.
   * @opt_param string viewId Optional. The ID of the view to be downloaded.
   * @return GdataMedia
   * @throws \Google\Service\Exception
   */
  public function download($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('download', [$params], GdataMedia::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Media::class, 'Google_Service_DiscoveryEngine_Resource_Media');
