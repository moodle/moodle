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

namespace Google\Service\Bigquery;

class PythonOptions extends \Google\Collection
{
  protected $collection_key = 'packages';
  /**
   * Required. The name of the function defined in Python code as the entry
   * point when the Python UDF is invoked.
   *
   * @var string
   */
  public $entryPoint;
  /**
   * Optional. A list of Python package names along with versions to be
   * installed. Example: ["pandas>=2.1", "google-cloud-translate==3.11"]. For
   * more information, see [Use third-party
   * packages](https://cloud.google.com/bigquery/docs/user-defined-functions-
   * python#third-party-packages).
   *
   * @var string[]
   */
  public $packages;

  /**
   * Required. The name of the function defined in Python code as the entry
   * point when the Python UDF is invoked.
   *
   * @param string $entryPoint
   */
  public function setEntryPoint($entryPoint)
  {
    $this->entryPoint = $entryPoint;
  }
  /**
   * @return string
   */
  public function getEntryPoint()
  {
    return $this->entryPoint;
  }
  /**
   * Optional. A list of Python package names along with versions to be
   * installed. Example: ["pandas>=2.1", "google-cloud-translate==3.11"]. For
   * more information, see [Use third-party
   * packages](https://cloud.google.com/bigquery/docs/user-defined-functions-
   * python#third-party-packages).
   *
   * @param string[] $packages
   */
  public function setPackages($packages)
  {
    $this->packages = $packages;
  }
  /**
   * @return string[]
   */
  public function getPackages()
  {
    return $this->packages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PythonOptions::class, 'Google_Service_Bigquery_PythonOptions');
