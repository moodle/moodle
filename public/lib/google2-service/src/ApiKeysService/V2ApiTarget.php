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

namespace Google\Service\ApiKeysService;

class V2ApiTarget extends \Google\Collection
{
  protected $collection_key = 'methods';
  /**
   * Optional. List of one or more methods that can be called. If empty, all
   * methods for the service are allowed. A wildcard (*) can be used as the last
   * symbol. Valid examples:
   * `google.cloud.translate.v2.TranslateService.GetSupportedLanguage`
   * `TranslateText` `Get*` `translate.googleapis.com.Get*`
   *
   * @var string[]
   */
  public $methods;
  /**
   * The service for this restriction. It should be the canonical service name,
   * for example: `translate.googleapis.com`. You can use [`gcloud services
   * list`](https://cloud.google.com/sdk/gcloud/reference/services/list) to get
   * a list of services that are enabled in the project.
   *
   * @var string
   */
  public $service;

  /**
   * Optional. List of one or more methods that can be called. If empty, all
   * methods for the service are allowed. A wildcard (*) can be used as the last
   * symbol. Valid examples:
   * `google.cloud.translate.v2.TranslateService.GetSupportedLanguage`
   * `TranslateText` `Get*` `translate.googleapis.com.Get*`
   *
   * @param string[] $methods
   */
  public function setMethods($methods)
  {
    $this->methods = $methods;
  }
  /**
   * @return string[]
   */
  public function getMethods()
  {
    return $this->methods;
  }
  /**
   * The service for this restriction. It should be the canonical service name,
   * for example: `translate.googleapis.com`. You can use [`gcloud services
   * list`](https://cloud.google.com/sdk/gcloud/reference/services/list) to get
   * a list of services that are enabled in the project.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(V2ApiTarget::class, 'Google_Service_ApiKeysService_V2ApiTarget');
