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

namespace Google\Service\CCAIPlatform;

class Component extends \Google\Collection
{
  protected $collection_key = 'serviceAttachmentNames';
  /**
   * Name of the component.
   *
   * @var string
   */
  public $name;
  /**
   * Associated service attachments. The service attachment names that will be
   * used for sending private traffic to the CCAIP tenant project. Example
   * service attachment name: "projects/${TENANT_PROJECT_ID}/regions/${REGION}/s
   * erviceAttachments/ingress-default".
   *
   * @var string[]
   */
  public $serviceAttachmentNames;

  /**
   * Name of the component.
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
   * Associated service attachments. The service attachment names that will be
   * used for sending private traffic to the CCAIP tenant project. Example
   * service attachment name: "projects/${TENANT_PROJECT_ID}/regions/${REGION}/s
   * erviceAttachments/ingress-default".
   *
   * @param string[] $serviceAttachmentNames
   */
  public function setServiceAttachmentNames($serviceAttachmentNames)
  {
    $this->serviceAttachmentNames = $serviceAttachmentNames;
  }
  /**
   * @return string[]
   */
  public function getServiceAttachmentNames()
  {
    return $this->serviceAttachmentNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Component::class, 'Google_Service_CCAIPlatform_Component');
