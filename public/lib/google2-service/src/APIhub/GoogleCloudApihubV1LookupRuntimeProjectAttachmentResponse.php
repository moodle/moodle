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

class GoogleCloudApihubV1LookupRuntimeProjectAttachmentResponse extends \Google\Model
{
  protected $runtimeProjectAttachmentType = GoogleCloudApihubV1RuntimeProjectAttachment::class;
  protected $runtimeProjectAttachmentDataType = '';

  /**
   * Runtime project attachment for a project if exists, empty otherwise.
   *
   * @param GoogleCloudApihubV1RuntimeProjectAttachment $runtimeProjectAttachment
   */
  public function setRuntimeProjectAttachment(GoogleCloudApihubV1RuntimeProjectAttachment $runtimeProjectAttachment)
  {
    $this->runtimeProjectAttachment = $runtimeProjectAttachment;
  }
  /**
   * @return GoogleCloudApihubV1RuntimeProjectAttachment
   */
  public function getRuntimeProjectAttachment()
  {
    return $this->runtimeProjectAttachment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1LookupRuntimeProjectAttachmentResponse::class, 'Google_Service_APIhub_GoogleCloudApihubV1LookupRuntimeProjectAttachmentResponse');
