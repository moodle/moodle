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

namespace Google\Service\Dataproc;

class InstantiateWorkflowTemplateRequest extends \Google\Model
{
  /**
   * Optional. Map from parameter names to values that should be used for those
   * parameters. Values may not exceed 1000 characters.
   *
   * @var string[]
   */
  public $parameters;
  /**
   * Optional. A tag that prevents multiple concurrent workflow instances with
   * the same tag from running. This mitigates risk of concurrent instances
   * started due to retries.It is recommended to always set this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The tag must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and
   * hyphens (-). The maximum length is 40 characters.
   *
   * @var string
   */
  public $requestId;
  /**
   * Optional. The version of workflow template to instantiate. If specified,
   * the workflow will be instantiated only if the current version of the
   * workflow template has the supplied version.This option cannot be used to
   * instantiate a previous version of workflow template.
   *
   * @var int
   */
  public $version;

  /**
   * Optional. Map from parameter names to values that should be used for those
   * parameters. Values may not exceed 1000 characters.
   *
   * @param string[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return string[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Optional. A tag that prevents multiple concurrent workflow instances with
   * the same tag from running. This mitigates risk of concurrent instances
   * started due to retries.It is recommended to always set this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The tag must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and
   * hyphens (-). The maximum length is 40 characters.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * Optional. The version of workflow template to instantiate. If specified,
   * the workflow will be instantiated only if the current version of the
   * workflow template has the supplied version.This option cannot be used to
   * instantiate a previous version of workflow template.
   *
   * @param int $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return int
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstantiateWorkflowTemplateRequest::class, 'Google_Service_Dataproc_InstantiateWorkflowTemplateRequest');
