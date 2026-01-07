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

namespace Google\Service\Drive\Resource;

use Google\Service\Drive\App;
use Google\Service\Drive\AppList;

/**
 * The "apps" collection of methods.
 * Typical usage is:
 *  <code>
 *   $driveService = new Google\Service\Drive(...);
 *   $apps = $driveService->apps;
 *  </code>
 */
class Apps extends \Google\Service\Resource
{
  /**
   * Gets a specific app. For more information, see [Return user
   * info](https://developers.google.com/workspace/drive/api/guides/user-info).
   * (apps.get)
   *
   * @param string $appId The ID of the app.
   * @param array $optParams Optional parameters.
   * @return App
   * @throws \Google\Service\Exception
   */
  public function get($appId, $optParams = [])
  {
    $params = ['appId' => $appId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], App::class);
  }
  /**
   * Lists a user's installed apps. For more information, see [Return user
   * info](https://developers.google.com/workspace/drive/api/guides/user-info).
   * (apps.listApps)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string appFilterExtensions A comma-separated list of file
   * extensions to limit returned results. All results within the given app query
   * scope which can open any of the given file extensions are included in the
   * response. If `appFilterMimeTypes` are provided as well, the result is a union
   * of the two resulting app lists.
   * @opt_param string appFilterMimeTypes A comma-separated list of file
   * extensions to limit returned results. All results within the given app query
   * scope which can open any of the given MIME types will be included in the
   * response. If `appFilterExtensions` are provided as well, the result is a
   * union of the two resulting app lists.
   * @opt_param string languageCode A language or locale code, as defined by BCP
   * 47, with some extensions from Unicode's LDML format
   * (http://www.unicode.org/reports/tr35/).
   * @return AppList
   * @throws \Google\Service\Exception
   */
  public function listApps($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], AppList::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Apps::class, 'Google_Service_Drive_Resource_Apps');
