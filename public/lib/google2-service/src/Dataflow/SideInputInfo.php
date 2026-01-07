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

class SideInputInfo extends \Google\Collection
{
  protected $collection_key = 'sources';
  /**
   * How to interpret the source element(s) as a side input value.
   *
   * @var array[]
   */
  public $kind;
  protected $sourcesType = Source::class;
  protected $sourcesDataType = 'array';
  /**
   * The id of the tag the user code will access this side input by; this should
   * correspond to the tag of some MultiOutputInfo.
   *
   * @var string
   */
  public $tag;

  /**
   * How to interpret the source element(s) as a side input value.
   *
   * @param array[] $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return array[]
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The source(s) to read element(s) from to get the value of this side input.
   * If more than one source, then the elements are taken from the sources, in
   * the specified order if order matters. At least one source is required.
   *
   * @param Source[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return Source[]
   */
  public function getSources()
  {
    return $this->sources;
  }
  /**
   * The id of the tag the user code will access this side input by; this should
   * correspond to the tag of some MultiOutputInfo.
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SideInputInfo::class, 'Google_Service_Dataflow_SideInputInfo');
