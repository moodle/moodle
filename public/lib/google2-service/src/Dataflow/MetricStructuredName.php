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

class MetricStructuredName extends \Google\Model
{
  /**
   * Zero or more labeled fields which identify the part of the job this metric
   * is associated with, such as the name of a step or collection. For example,
   * built-in counters associated with steps will have context['step'] = .
   * Counters associated with PCollections in the SDK will have
   * context['pcollection'] = .
   *
   * @var string[]
   */
  public $context;
  /**
   * Worker-defined metric name.
   *
   * @var string
   */
  public $name;
  /**
   * Origin (namespace) of metric name. May be blank for user-define metrics;
   * will be "dataflow" for metrics defined by the Dataflow service or SDK.
   *
   * @var string
   */
  public $origin;

  /**
   * Zero or more labeled fields which identify the part of the job this metric
   * is associated with, such as the name of a step or collection. For example,
   * built-in counters associated with steps will have context['step'] = .
   * Counters associated with PCollections in the SDK will have
   * context['pcollection'] = .
   *
   * @param string[] $context
   */
  public function setContext($context)
  {
    $this->context = $context;
  }
  /**
   * @return string[]
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * Worker-defined metric name.
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
   * Origin (namespace) of metric name. May be blank for user-define metrics;
   * will be "dataflow" for metrics defined by the Dataflow service or SDK.
   *
   * @param string $origin
   */
  public function setOrigin($origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return string
   */
  public function getOrigin()
  {
    return $this->origin;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetricStructuredName::class, 'Google_Service_Dataflow_MetricStructuredName');
