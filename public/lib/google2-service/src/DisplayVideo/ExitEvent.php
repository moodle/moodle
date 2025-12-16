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

namespace Google\Service\DisplayVideo;

class ExitEvent extends \Google\Model
{
  /**
   * Exit event type is not specified or is unknown in this version.
   */
  public const TYPE_EXIT_EVENT_TYPE_UNSPECIFIED = 'EXIT_EVENT_TYPE_UNSPECIFIED';
  /**
   * The exit event is the default one.
   */
  public const TYPE_EXIT_EVENT_TYPE_DEFAULT = 'EXIT_EVENT_TYPE_DEFAULT';
  /**
   * The exit event is a backup exit event. There could be multiple backup exit
   * events in a creative.
   */
  public const TYPE_EXIT_EVENT_TYPE_BACKUP = 'EXIT_EVENT_TYPE_BACKUP';
  /**
   * Optional. The name of the click tag of the exit event. The name must be
   * unique within one creative. Leave it empty or unset for creatives
   * containing image assets only.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The name used to identify this event in reports. Leave it empty
   * or unset for creatives containing image assets only.
   *
   * @var string
   */
  public $reportingName;
  /**
   * Required. The type of the exit event.
   *
   * @var string
   */
  public $type;
  /**
   * Required. The click through URL of the exit event. This is required when
   * type is: * `EXIT_EVENT_TYPE_DEFAULT` * `EXIT_EVENT_TYPE_BACKUP`
   *
   * @var string
   */
  public $url;

  /**
   * Optional. The name of the click tag of the exit event. The name must be
   * unique within one creative. Leave it empty or unset for creatives
   * containing image assets only.
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
   * Optional. The name used to identify this event in reports. Leave it empty
   * or unset for creatives containing image assets only.
   *
   * @param string $reportingName
   */
  public function setReportingName($reportingName)
  {
    $this->reportingName = $reportingName;
  }
  /**
   * @return string
   */
  public function getReportingName()
  {
    return $this->reportingName;
  }
  /**
   * Required. The type of the exit event.
   *
   * Accepted values: EXIT_EVENT_TYPE_UNSPECIFIED, EXIT_EVENT_TYPE_DEFAULT,
   * EXIT_EVENT_TYPE_BACKUP
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
  /**
   * Required. The click through URL of the exit event. This is required when
   * type is: * `EXIT_EVENT_TYPE_DEFAULT` * `EXIT_EVENT_TYPE_BACKUP`
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExitEvent::class, 'Google_Service_DisplayVideo_ExitEvent');
