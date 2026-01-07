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

namespace Google\Service\CloudTasks;

class AppEngineRouting extends \Google\Model
{
  /**
   * Output only. The host that the task is sent to. The host is constructed
   * from the domain name of the app associated with the queue's project ID (for
   * example .appspot.com), and the service, version, and instance. Tasks which
   * were created using the App Engine SDK might have a custom domain name. For
   * more information, see [How Requests are
   * Routed](https://cloud.google.com/appengine/docs/standard/python/how-
   * requests-are-routed).
   *
   * @var string
   */
  public $host;
  /**
   * App instance. By default, the task is sent to an instance which is
   * available when the task is attempted. Requests can only be sent to a
   * specific instance if [manual scaling is used in App Engine
   * Standard](https://cloud.google.com/appengine/docs/python/an-overview-of-
   * app-engine?hl=en_US#scaling_types_and_instance_classes). App Engine Flex
   * does not support instances. For more information, see [App Engine Standard
   * request
   * routing](https://cloud.google.com/appengine/docs/standard/python/how-
   * requests-are-routed) and [App Engine Flex request
   * routing](https://cloud.google.com/appengine/docs/flexible/python/how-
   * requests-are-routed).
   *
   * @var string
   */
  public $instance;
  /**
   * App service. By default, the task is sent to the service which is the
   * default service when the task is attempted. For some queues or tasks which
   * were created using the App Engine Task Queue API, host is not parsable into
   * service, version, and instance. For example, some tasks which were created
   * using the App Engine SDK use a custom domain name; custom domains are not
   * parsed by Cloud Tasks. If host is not parsable, then service, version, and
   * instance are the empty string.
   *
   * @var string
   */
  public $service;
  /**
   * App version. By default, the task is sent to the version which is the
   * default version when the task is attempted. For some queues or tasks which
   * were created using the App Engine Task Queue API, host is not parsable into
   * service, version, and instance. For example, some tasks which were created
   * using the App Engine SDK use a custom domain name; custom domains are not
   * parsed by Cloud Tasks. If host is not parsable, then service, version, and
   * instance are the empty string.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. The host that the task is sent to. The host is constructed
   * from the domain name of the app associated with the queue's project ID (for
   * example .appspot.com), and the service, version, and instance. Tasks which
   * were created using the App Engine SDK might have a custom domain name. For
   * more information, see [How Requests are
   * Routed](https://cloud.google.com/appengine/docs/standard/python/how-
   * requests-are-routed).
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * App instance. By default, the task is sent to an instance which is
   * available when the task is attempted. Requests can only be sent to a
   * specific instance if [manual scaling is used in App Engine
   * Standard](https://cloud.google.com/appengine/docs/python/an-overview-of-
   * app-engine?hl=en_US#scaling_types_and_instance_classes). App Engine Flex
   * does not support instances. For more information, see [App Engine Standard
   * request
   * routing](https://cloud.google.com/appengine/docs/standard/python/how-
   * requests-are-routed) and [App Engine Flex request
   * routing](https://cloud.google.com/appengine/docs/flexible/python/how-
   * requests-are-routed).
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * App service. By default, the task is sent to the service which is the
   * default service when the task is attempted. For some queues or tasks which
   * were created using the App Engine Task Queue API, host is not parsable into
   * service, version, and instance. For example, some tasks which were created
   * using the App Engine SDK use a custom domain name; custom domains are not
   * parsed by Cloud Tasks. If host is not parsable, then service, version, and
   * instance are the empty string.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * App version. By default, the task is sent to the version which is the
   * default version when the task is attempted. For some queues or tasks which
   * were created using the App Engine Task Queue API, host is not parsable into
   * service, version, and instance. For example, some tasks which were created
   * using the App Engine SDK use a custom domain name; custom domains are not
   * parsed by Cloud Tasks. If host is not parsable, then service, version, and
   * instance are the empty string.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppEngineRouting::class, 'Google_Service_CloudTasks_AppEngineRouting');
