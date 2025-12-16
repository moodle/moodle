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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1RuntimeProjectAttachment extends \Google\Model
{
  /**
   * Output only. Create time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Identifier. The resource name of a runtime project attachment. Format: "pro
   * jects/{project}/locations/{location}/runtimeProjectAttachments/{runtime_pro
   * ject_attachment}".
   *
   * @var string
   */
  public $name;
  /**
   * Required. Immutable. Google cloud project name in the format:
   * "projects/abc" or "projects/123". As input, project name with either
   * project id or number are accepted. As output, this field will contain
   * project number.
   *
   * @var string
   */
  public $runtimeProject;

  /**
   * Output only. Create time.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Identifier. The resource name of a runtime project attachment. Format: "pro
   * jects/{project}/locations/{location}/runtimeProjectAttachments/{runtime_pro
   * ject_attachment}".
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
   * Required. Immutable. Google cloud project name in the format:
   * "projects/abc" or "projects/123". As input, project name with either
   * project id or number are accepted. As output, this field will contain
   * project number.
   *
   * @param string $runtimeProject
   */
  public function setRuntimeProject($runtimeProject)
  {
    $this->runtimeProject = $runtimeProject;
  }
  /**
   * @return string
   */
  public function getRuntimeProject()
  {
    return $this->runtimeProject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1RuntimeProjectAttachment::class, 'Google_Service_APIhub_GoogleCloudApihubV1RuntimeProjectAttachment');
