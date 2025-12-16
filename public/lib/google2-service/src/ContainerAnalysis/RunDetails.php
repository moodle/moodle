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

namespace Google\Service\ContainerAnalysis;

class RunDetails extends \Google\Collection
{
  protected $collection_key = 'byproducts';
  protected $builderType = ProvenanceBuilder::class;
  protected $builderDataType = '';
  protected $byproductsType = ResourceDescriptor::class;
  protected $byproductsDataType = 'array';
  protected $metadataType = BuildMetadata::class;
  protected $metadataDataType = '';

  /**
   * @param ProvenanceBuilder $builder
   */
  public function setBuilder(ProvenanceBuilder $builder)
  {
    $this->builder = $builder;
  }
  /**
   * @return ProvenanceBuilder
   */
  public function getBuilder()
  {
    return $this->builder;
  }
  /**
   * @param ResourceDescriptor[] $byproducts
   */
  public function setByproducts($byproducts)
  {
    $this->byproducts = $byproducts;
  }
  /**
   * @return ResourceDescriptor[]
   */
  public function getByproducts()
  {
    return $this->byproducts;
  }
  /**
   * @param BuildMetadata $metadata
   */
  public function setMetadata(BuildMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return BuildMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RunDetails::class, 'Google_Service_ContainerAnalysis_RunDetails');
