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

namespace Google\Service\CloudNaturalLanguage;

class XPSExampleSet extends \Google\Model
{
  protected $fileSpecType = XPSFileSpec::class;
  protected $fileSpecDataType = '';
  /**
   * Fingerprint of the example set.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Number of examples.
   *
   * @var string
   */
  public $numExamples;
  /**
   * Number of input sources.
   *
   * @var string
   */
  public $numInputSources;

  /**
   * File spec of the examples or input sources.
   *
   * @param XPSFileSpec $fileSpec
   */
  public function setFileSpec(XPSFileSpec $fileSpec)
  {
    $this->fileSpec = $fileSpec;
  }
  /**
   * @return XPSFileSpec
   */
  public function getFileSpec()
  {
    return $this->fileSpec;
  }
  /**
   * Fingerprint of the example set.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Number of examples.
   *
   * @param string $numExamples
   */
  public function setNumExamples($numExamples)
  {
    $this->numExamples = $numExamples;
  }
  /**
   * @return string
   */
  public function getNumExamples()
  {
    return $this->numExamples;
  }
  /**
   * Number of input sources.
   *
   * @param string $numInputSources
   */
  public function setNumInputSources($numInputSources)
  {
    $this->numInputSources = $numInputSources;
  }
  /**
   * @return string
   */
  public function getNumInputSources()
  {
    return $this->numInputSources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSExampleSet::class, 'Google_Service_CloudNaturalLanguage_XPSExampleSet');
