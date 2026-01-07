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

namespace Google\Service\Reports;

class ActivityEvents extends \Google\Collection
{
  protected $collection_key = 'resourceIds';
  /**
   * Name of the event. This is the specific name of the activity reported by
   * the API. And each `eventName` is related to a specific Google Workspace
   * service or feature which the API organizes into types of events. For
   * `eventName` request parameters in general: - If no `eventName` is given,
   * the report returns all possible instances of an `eventName`. - When you
   * request an `eventName`, the API's response returns all activities which
   * contain that `eventName`. For more information about `eventName`
   * properties, see the list of event names for various applications above in
   * `applicationName`.
   *
   * @var string
   */
  public $name;
  protected $parametersType = ActivityEventsParameters::class;
  protected $parametersDataType = 'array';
  /**
   * Resource ids associated with the event.
   *
   * @var string[]
   */
  public $resourceIds;
  /**
   * Type of event. The Google Workspace service or feature that an
   * administrator changes is identified in the `type` property which identifies
   * an event using the `eventName` property. For a full list of the API's
   * `type` categories, see the list of event names for various applications
   * above in `applicationName`.
   *
   * @var string
   */
  public $type;

  /**
   * Name of the event. This is the specific name of the activity reported by
   * the API. And each `eventName` is related to a specific Google Workspace
   * service or feature which the API organizes into types of events. For
   * `eventName` request parameters in general: - If no `eventName` is given,
   * the report returns all possible instances of an `eventName`. - When you
   * request an `eventName`, the API's response returns all activities which
   * contain that `eventName`. For more information about `eventName`
   * properties, see the list of event names for various applications above in
   * `applicationName`.
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
   * Parameter value pairs for various applications. For more information about
   * `eventName` parameters, see the list of event names for various
   * applications above in `applicationName`.
   *
   * @param ActivityEventsParameters[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return ActivityEventsParameters[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Resource ids associated with the event.
   *
   * @param string[] $resourceIds
   */
  public function setResourceIds($resourceIds)
  {
    $this->resourceIds = $resourceIds;
  }
  /**
   * @return string[]
   */
  public function getResourceIds()
  {
    return $this->resourceIds;
  }
  /**
   * Type of event. The Google Workspace service or feature that an
   * administrator changes is identified in the `type` property which identifies
   * an event using the `eventName` property. For a full list of the API's
   * `type` categories, see the list of event names for various applications
   * above in `applicationName`.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActivityEvents::class, 'Google_Service_Reports_ActivityEvents');
