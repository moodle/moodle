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

namespace Google\Service\AndroidManagement;

class SignupUrl extends \Google\Model
{
  /**
   * The name of the resource. Use this value in the signupUrl field when
   * calling enterprises.create to complete the enterprise signup flow.
   *
   * @var string
   */
  public $name;
  /**
   * A URL where an enterprise admin can register their enterprise. The page
   * can't be rendered in an iframe.
   *
   * @var string
   */
  public $url;

  /**
   * The name of the resource. Use this value in the signupUrl field when
   * calling enterprises.create to complete the enterprise signup flow.
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
   * A URL where an enterprise admin can register their enterprise. The page
   * can't be rendered in an iframe.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SignupUrl::class, 'Google_Service_AndroidManagement_SignupUrl');
