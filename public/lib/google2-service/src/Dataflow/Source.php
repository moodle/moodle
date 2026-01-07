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

class Source extends \Google\Collection
{
  protected $collection_key = 'baseSpecs';
  /**
   * While splitting, sources may specify the produced bundles as differences
   * against another source, in order to save backend-side memory and allow
   * bigger jobs. For details, see SourceSplitRequest. To support this use case,
   * the full set of parameters of the source is logically obtained by taking
   * the latest explicitly specified value of each parameter in the order:
   * base_specs (later items win), spec (overrides anything in base_specs).
   *
   * @var array[]
   */
  public $baseSpecs;
  /**
   * The codec to use to decode data read from the source.
   *
   * @var array[]
   */
  public $codec;
  /**
   * Setting this value to true hints to the framework that the source doesn't
   * need splitting, and using SourceSplitRequest on it would yield
   * SOURCE_SPLIT_OUTCOME_USE_CURRENT. E.g. a file splitter may set this to true
   * when splitting a single file into a set of byte ranges of appropriate size,
   * and set this to false when splitting a filepattern into individual files.
   * However, for efficiency, a file splitter may decide to produce file
   * subranges directly from the filepattern to avoid a splitting round-trip.
   * See SourceSplitRequest for an overview of the splitting process. This field
   * is meaningful only in the Source objects populated by the user (e.g. when
   * filling in a DerivedSource). Source objects supplied by the framework to
   * the user don't have this field populated.
   *
   * @var bool
   */
  public $doesNotNeedSplitting;
  protected $metadataType = SourceMetadata::class;
  protected $metadataDataType = '';
  /**
   * The source to read from, plus its parameters.
   *
   * @var array[]
   */
  public $spec;

  /**
   * While splitting, sources may specify the produced bundles as differences
   * against another source, in order to save backend-side memory and allow
   * bigger jobs. For details, see SourceSplitRequest. To support this use case,
   * the full set of parameters of the source is logically obtained by taking
   * the latest explicitly specified value of each parameter in the order:
   * base_specs (later items win), spec (overrides anything in base_specs).
   *
   * @param array[] $baseSpecs
   */
  public function setBaseSpecs($baseSpecs)
  {
    $this->baseSpecs = $baseSpecs;
  }
  /**
   * @return array[]
   */
  public function getBaseSpecs()
  {
    return $this->baseSpecs;
  }
  /**
   * The codec to use to decode data read from the source.
   *
   * @param array[] $codec
   */
  public function setCodec($codec)
  {
    $this->codec = $codec;
  }
  /**
   * @return array[]
   */
  public function getCodec()
  {
    return $this->codec;
  }
  /**
   * Setting this value to true hints to the framework that the source doesn't
   * need splitting, and using SourceSplitRequest on it would yield
   * SOURCE_SPLIT_OUTCOME_USE_CURRENT. E.g. a file splitter may set this to true
   * when splitting a single file into a set of byte ranges of appropriate size,
   * and set this to false when splitting a filepattern into individual files.
   * However, for efficiency, a file splitter may decide to produce file
   * subranges directly from the filepattern to avoid a splitting round-trip.
   * See SourceSplitRequest for an overview of the splitting process. This field
   * is meaningful only in the Source objects populated by the user (e.g. when
   * filling in a DerivedSource). Source objects supplied by the framework to
   * the user don't have this field populated.
   *
   * @param bool $doesNotNeedSplitting
   */
  public function setDoesNotNeedSplitting($doesNotNeedSplitting)
  {
    $this->doesNotNeedSplitting = $doesNotNeedSplitting;
  }
  /**
   * @return bool
   */
  public function getDoesNotNeedSplitting()
  {
    return $this->doesNotNeedSplitting;
  }
  /**
   * Optionally, metadata for this source can be supplied right away, avoiding a
   * SourceGetMetadataOperation roundtrip (see SourceOperationRequest). This
   * field is meaningful only in the Source objects populated by the user (e.g.
   * when filling in a DerivedSource). Source objects supplied by the framework
   * to the user don't have this field populated.
   *
   * @param SourceMetadata $metadata
   */
  public function setMetadata(SourceMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return SourceMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The source to read from, plus its parameters.
   *
   * @param array[] $spec
   */
  public function setSpec($spec)
  {
    $this->spec = $spec;
  }
  /**
   * @return array[]
   */
  public function getSpec()
  {
    return $this->spec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Source::class, 'Google_Service_Dataflow_Source');
