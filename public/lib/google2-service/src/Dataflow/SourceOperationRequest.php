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

namespace Google\Service\Dataflow;

class SourceOperationRequest extends \Google\Model
{
  protected $getMetadataType = SourceGetMetadataRequest::class;
  protected $getMetadataDataType = '';
  /**
   * User-provided name of the Read instruction for this source.
   *
   * @var string
   */
  public $name;
  /**
   * System-defined name for the Read instruction for this source in the
   * original workflow graph.
   *
   * @var string
   */
  public $originalName;
  protected $splitType = SourceSplitRequest::class;
  protected $splitDataType = '';
  /**
   * System-defined name of the stage containing the source operation. Unique
   * across the workflow.
   *
   * @var string
   */
  public $stageName;
  /**
   * System-defined name of the Read instruction for this source. Unique across
   * the workflow.
   *
   * @var string
   */
  public $systemName;

  /**
   * Information about a request to get metadata about a source.
   *
   * @param SourceGetMetadataRequest $getMetadata
   */
  public function setGetMetadata(SourceGetMetadataRequest $getMetadata)
  {
    $this->getMetadata = $getMetadata;
  }
  /**
   * @return SourceGetMetadataRequest
   */
  public function getGetMetadata()
  {
    return $this->getMetadata;
  }
  /**
   * User-provided name of the Read instruction for this source.
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
   * System-defined name for the Read instruction for this source in the
   * original workflow graph.
   *
   * @param string $originalName
   */
  public function setOriginalName($originalName)
  {
    $this->originalName = $originalName;
  }
  /**
   * @return string
   */
  public function getOriginalName()
  {
    return $this->originalName;
  }
  /**
   * Information about a request to split a source.
   *
   * @param SourceSplitRequest $split
   */
  public function setSplit(SourceSplitRequest $split)
  {
    $this->split = $split;
  }
  /**
   * @return SourceSplitRequest
   */
  public function getSplit()
  {
    return $this->split;
  }
  /**
   * System-defined name of the stage containing the source operation. Unique
   * across the workflow.
   *
   * @param string $stageName
   */
  public function setStageName($stageName)
  {
    $this->stageName = $stageName;
  }
  /**
   * @return string
   */
  public function getStageName()
  {
    return $this->stageName;
  }
  /**
   * System-defined name of the Read instruction for this source. Unique across
   * the workflow.
   *
   * @param string $systemName
   */
  public function setSystemName($systemName)
  {
    $this->systemName = $systemName;
  }
  /**
   * @return string
   */
  public function getSystemName()
  {
    return $this->systemName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceOperationRequest::class, 'Google_Service_Dataflow_SourceOperationRequest');
