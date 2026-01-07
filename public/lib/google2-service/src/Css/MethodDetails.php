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

namespace Google\Service\Css;

class MethodDetails extends \Google\Model
{
  /**
   * Output only. The name of the method for example
   * `cssproductsservice.listcssproducts`.
   *
   * @var string
   */
  public $method;
  /**
   * Output only. The path for the method such as
   * `v1/cssproductsservice.listcssproducts`.
   *
   * @var string
   */
  public $path;
  /**
   * Output only. The sub-API that the method belongs to. In the CSS API, this
   * is always `css`.
   *
   * @var string
   */
  public $subapi;
  /**
   * Output only. The API version that the method belongs to.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. The name of the method for example
   * `cssproductsservice.listcssproducts`.
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * Output only. The path for the method such as
   * `v1/cssproductsservice.listcssproducts`.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Output only. The sub-API that the method belongs to. In the CSS API, this
   * is always `css`.
   *
   * @param string $subapi
   */
  public function setSubapi($subapi)
  {
    $this->subapi = $subapi;
  }
  /**
   * @return string
   */
  public function getSubapi()
  {
    return $this->subapi;
  }
  /**
   * Output only. The API version that the method belongs to.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MethodDetails::class, 'Google_Service_Css_MethodDetails');
