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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1PropertyDefinitionSchemaSource extends \Google\Model
{
  /**
   * The schema name in the source.
   *
   * @var string
   */
  public $name;
  /**
   * The Doc AI processor type name.
   *
   * @var string
   */
  public $processorType;

  /**
   * The schema name in the source.
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
   * The Doc AI processor type name.
   *
   * @param string $processorType
   */
  public function setProcessorType($processorType)
  {
    $this->processorType = $processorType;
  }
  /**
   * @return string
   */
  public function getProcessorType()
  {
    return $this->processorType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1PropertyDefinitionSchemaSource::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1PropertyDefinitionSchemaSource');
