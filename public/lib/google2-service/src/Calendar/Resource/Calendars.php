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

namespace Google\Service\Calendar\Resource;

use Google\Service\Calendar\Calendar;

/**
 * The "calendars" collection of methods.
 * Typical usage is:
 *  <code>
 *   $calendarService = new Google\Service\Calendar(...);
 *   $calendars = $calendarService->calendars;
 *  </code>
 */
class Calendars extends \Google\Service\Resource
{
  /**
   * Clears a primary calendar. This operation deletes all events associated with
   * the primary calendar of an account. (calendars.clear)
   *
   * @param string $calendarId Calendar identifier. To retrieve calendar IDs call
   * the calendarList.list method. If you want to access the primary calendar of
   * the currently logged in user, use the "primary" keyword.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function clear($calendarId, $optParams = [])
  {
    $params = ['calendarId' => $calendarId];
    $params = array_merge($params, $optParams);
    return $this->call('clear', [$params]);
  }
  /**
   * Deletes a secondary calendar. Use calendars.clear for clearing all events on
   * primary calendars. (calendars.delete)
   *
   * @param string $calendarId Calendar identifier. To retrieve calendar IDs call
   * the calendarList.list method. If you want to access the primary calendar of
   * the currently logged in user, use the "primary" keyword.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function delete($calendarId, $optParams = [])
  {
    $params = ['calendarId' => $calendarId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Returns metadata for a calendar. (calendars.get)
   *
   * @param string $calendarId Calendar identifier. To retrieve calendar IDs call
   * the calendarList.list method. If you want to access the primary calendar of
   * the currently logged in user, use the "primary" keyword.
   * @param array $optParams Optional parameters.
   * @return Calendar
   * @throws \Google\Service\Exception
   */
  public function get($calendarId, $optParams = [])
  {
    $params = ['calendarId' => $calendarId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Calendar::class);
  }
  /**
   * Creates a secondary calendar. The authenticated user for the request is made
   * the data owner of the new calendar.
   *
   * Note: We recommend to authenticate as the intended data owner of the
   * calendar. You can use domain-wide delegation of authority to allow
   * applications to act on behalf of a specific user. Don't use a service account
   * for authentication. If you use a service account for authentication, the
   * service account is the data owner, which can lead to unexpected behavior. For
   * example, if a service account is the data owner, data ownership cannot be
   * transferred. (calendars.insert)
   *
   * @param Calendar $postBody
   * @param array $optParams Optional parameters.
   * @return Calendar
   * @throws \Google\Service\Exception
   */
  public function insert(Calendar $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], Calendar::class);
  }
  /**
   * Updates metadata for a calendar. This method supports patch semantics.
   * (calendars.patch)
   *
   * @param string $calendarId Calendar identifier. To retrieve calendar IDs call
   * the calendarList.list method. If you want to access the primary calendar of
   * the currently logged in user, use the "primary" keyword.
   * @param Calendar $postBody
   * @param array $optParams Optional parameters.
   * @return Calendar
   * @throws \Google\Service\Exception
   */
  public function patch($calendarId, Calendar $postBody, $optParams = [])
  {
    $params = ['calendarId' => $calendarId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Calendar::class);
  }
  /**
   * Updates metadata for a calendar. (calendars.update)
   *
   * @param string $calendarId Calendar identifier. To retrieve calendar IDs call
   * the calendarList.list method. If you want to access the primary calendar of
   * the currently logged in user, use the "primary" keyword.
   * @param Calendar $postBody
   * @param array $optParams Optional parameters.
   * @return Calendar
   * @throws \Google\Service\Exception
   */
  public function update($calendarId, Calendar $postBody, $optParams = [])
  {
    $params = ['calendarId' => $calendarId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], Calendar::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Calendars::class, 'Google_Service_Calendar_Resource_Calendars');
