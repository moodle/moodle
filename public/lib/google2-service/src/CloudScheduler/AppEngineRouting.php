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

namespace Google\Service\CloudScheduler;

class AppEngineRouting extends \Google\Model
{
  /**
   * Output only. The host that the job is sent to. For more information about
   * how App Engine requests are routed, see
   * [here](https://cloud.google.com/appengine/docs/standard/python/how-
   * requests-are-routed). The host is constructed as: * `host =
   * [application_domain_name]` `| [service] + '.' + [application_domain_name]`
   * `| [version] + '.' + [application_domain_name]` `| [version_dot_service]+
   * '.' + [application_domain_name]` `| [instance] + '.' +
   * [application_domain_name]` `| [instance_dot_service] + '.' +
   * [application_domain_name]` `| [instance_dot_version] + '.' +
   * [application_domain_name]` `| [instance_dot_version_dot_service] + '.' +
   * [application_domain_name]` * `application_domain_name` = The domain name of
   * the app, for example .appspot.com, which is associated with the job's
   * project ID. * `service =` service * `version =` version *
   * `version_dot_service =` version `+ '.' +` service * `instance =` instance *
   * `instance_dot_service =` instance `+ '.' +` service * `instance_dot_version
   * =` instance `+ '.' +` version * `instance_dot_version_dot_service =`
   * instance `+ '.' +` version `+ '.' +` service If service is empty, then the
   * job will be sent to the service which is the default service when the job
   * is attempted. If version is empty, then the job will be sent to the version
   * which is the default version when the job is attempted. If instance is
   * empty, then the job will be sent to an instance which is available when the
   * job is attempted. If service, version, or instance is invalid, then the job
   * will be sent to the default version of the default service when the job is
   * attempted.
   *
   * @var string
   */
  public $host;
  /**
   * App instance. By default, the job is sent to an instance which is available
   * when the job is attempted. Requests can only be sent to a specific instance
   * if [manual scaling is used in App Engine
   * Standard](https://cloud.google.com/appengine/docs/python/an-overview-of-
   * app-engine?#scaling_types_and_instance_classes). App Engine Flex does not
   * support instances. For more information, see [App Engine Standard request
   * routing](https://cloud.google.com/appengine/docs/standard/python/how-
   * requests-are-routed) and [App Engine Flex request
   * routing](https://cloud.google.com/appengine/docs/flexible/python/how-
   * requests-are-routed).
   *
   * @var string
   */
  public $instance;
  /**
   * App service. By default, the job is sent to the service which is the
   * default service when the job is attempted.
   *
   * @var string
   */
  public $service;
  /**
   * App version. By default, the job is sent to the version which is the
   * default version when the job is attempted.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. The host that the job is sent to. For more information about
   * how App Engine requests are routed, see
   * [here](https://cloud.google.com/appengine/docs/standard/python/how-
   * requests-are-routed). The host is constructed as: * `host =
   * [application_domain_name]` `| [service] + '.' + [application_domain_name]`
   * `| [version] + '.' + [application_domain_name]` `| [version_dot_service]+
   * '.' + [application_domain_name]` `| [instance] + '.' +
   * [application_domain_name]` `| [instance_dot_service] + '.' +
   * [application_domain_name]` `| [instance_dot_version] + '.' +
   * [application_domain_name]` `| [instance_dot_version_dot_service] + '.' +
   * [application_domain_name]` * `application_domain_name` = The domain name of
   * the app, for example .appspot.com, which is associated with the job's
   * project ID. * `service =` service * `version =` version *
   * `version_dot_service =` version `+ '.' +` service * `instance =` instance *
   * `instance_dot_service =` instance `+ '.' +` service * `instance_dot_version
   * =` instance `+ '.' +` version * `instance_dot_version_dot_service =`
   * instance `+ '.' +` version `+ '.' +` service If service is empty, then the
   * job will be sent to the service which is the default service when the job
   * is attempted. If version is empty, then the job will be sent to the version
   * which is the default version when the job is attempted. If instance is
   * empty, then the job will be sent to an instance which is available when the
   * job is attempted. If service, version, or instance is invalid, then the job
   * will be sent to the default version of the default service when the job is
   * attempted.
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
   * App instance. By default, the job is sent to an instance which is available
   * when the job is attempted. Requests can only be sent to a specific instance
   * if [manual scaling is used in App Engine
   * Standard](https://cloud.google.com/appengine/docs/python/an-overview-of-
   * app-engine?#scaling_types_and_instance_classes). App Engine Flex does not
   * support instances. For more information, see [App Engine Standard request
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
   * App service. By default, the job is sent to the service which is the
   * default service when the job is attempted.
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
   * App version. By default, the job is sent to the version which is the
   * default version when the job is attempted.
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
class_alias(AppEngineRouting::class, 'Google_Service_CloudScheduler_AppEngineRouting');
