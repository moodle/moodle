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

class RunTaskRequest extends \Google\Model
{
  /**
   * Unspecified. Defaults to BASIC.
   */
  public const RESPONSE_VIEW_VIEW_UNSPECIFIED = 'VIEW_UNSPECIFIED';
  /**
   * The basic view omits fields which can be large or can contain sensitive
   * data. This view does not include the body in AppEngineHttpRequest. Bodies
   * are desirable to return only when needed, because they can be large and
   * because of the sensitivity of the data that you choose to store in it.
   */
  public const RESPONSE_VIEW_BASIC = 'BASIC';
  /**
   * All information is returned. Authorization for FULL requires
   * `cloudtasks.tasks.fullView` [Google IAM](https://cloud.google.com/iam/)
   * permission on the Queue resource.
   */
  public const RESPONSE_VIEW_FULL = 'FULL';
  /**
   * The response_view specifies which subset of the Task will be returned. By
   * default response_view is BASIC; not all information is retrieved by default
   * because some data, such as payloads, might be desirable to return only when
   * needed because of its large size or because of the sensitivity of data that
   * it contains. Authorization for FULL requires `cloudtasks.tasks.fullView`
   * [Google IAM](https://cloud.google.com/iam/) permission on the Task
   * resource.
   *
   * @var string
   */
  public $responseView;

  /**
   * The response_view specifies which subset of the Task will be returned. By
   * default response_view is BASIC; not all information is retrieved by default
   * because some data, such as payloads, might be desirable to return only when
   * needed because of its large size or because of the sensitivity of data that
   * it contains. Authorization for FULL requires `cloudtasks.tasks.fullView`
   * [Google IAM](https://cloud.google.com/iam/) permission on the Task
   * resource.
   *
   * Accepted values: VIEW_UNSPECIFIED, BASIC, FULL
   *
   * @param self::RESPONSE_VIEW_* $responseView
   */
  public function setResponseView($responseView)
  {
    $this->responseView = $responseView;
  }
  /**
   * @return self::RESPONSE_VIEW_*
   */
  public function getResponseView()
  {
    return $this->responseView;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RunTaskRequest::class, 'Google_Service_CloudTasks_RunTaskRequest');
