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

namespace Google\Service\YouTubeAnalytics;

class ErrorProto extends \Google\Collection
{
  /**
   * location is an xpath-like path pointing to the request field that caused
   * the error.
   */
  public const LOCATION_TYPE_PATH = 'PATH';
  /**
   * other location type which can safely be shared externally.
   */
  public const LOCATION_TYPE_OTHER = 'OTHER';
  /**
   * Location is request parameter. This maps to the {@link PARAMETERS} in
   * {@link MessageLocation}.
   */
  public const LOCATION_TYPE_PARAMETER = 'PARAMETER';
  protected $collection_key = 'argument';
  /**
   * Error arguments, to be used when building user-friendly error messages
   * given the error domain and code. Different error codes require different
   * arguments.
   *
   * @var string[]
   */
  public $argument;
  /**
   * Error code in the error domain. This should correspond to a value of the
   * enum type whose name is in domain. See the core error domain in
   * error_domain.proto.
   *
   * @var string
   */
  public $code;
  /**
   * Debugging information, which should not be shared externally.
   *
   * @var string
   */
  public $debugInfo;
  /**
   * Error domain. RoSy services can define their own domain and error codes.
   * This should normally be the name of an enum type, such as:
   * gdata.CoreErrorDomain
   *
   * @var string
   */
  public $domain;
  /**
   * A short explanation for the error, which can be shared outside Google.
   * Please set domain, code and arguments whenever possible instead of this
   * error message so that external APIs can build safe error messages
   * themselves. External messages built in a RoSy interface will most likely
   * refer to information and concepts that are not available externally and
   * should not be exposed. It is safer if external APIs can understand the
   * errors and decide what the error message should look like.
   *
   * @var string
   */
  public $externalErrorMessage;
  /**
   * Location of the error, as specified by the location type. If location_type
   * is PATH, this should be a path to a field that's relative to the request,
   * using FieldPath notation (net/proto2/util/public/field_path.h). Examples:
   * authenticated_user.gaia_id resource.address[2].country
   *
   * @var string
   */
  public $location;
  /**
   * @var string
   */
  public $locationType;

  /**
   * Error arguments, to be used when building user-friendly error messages
   * given the error domain and code. Different error codes require different
   * arguments.
   *
   * @param string[] $argument
   */
  public function setArgument($argument)
  {
    $this->argument = $argument;
  }
  /**
   * @return string[]
   */
  public function getArgument()
  {
    return $this->argument;
  }
  /**
   * Error code in the error domain. This should correspond to a value of the
   * enum type whose name is in domain. See the core error domain in
   * error_domain.proto.
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Debugging information, which should not be shared externally.
   *
   * @param string $debugInfo
   */
  public function setDebugInfo($debugInfo)
  {
    $this->debugInfo = $debugInfo;
  }
  /**
   * @return string
   */
  public function getDebugInfo()
  {
    return $this->debugInfo;
  }
  /**
   * Error domain. RoSy services can define their own domain and error codes.
   * This should normally be the name of an enum type, such as:
   * gdata.CoreErrorDomain
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * A short explanation for the error, which can be shared outside Google.
   * Please set domain, code and arguments whenever possible instead of this
   * error message so that external APIs can build safe error messages
   * themselves. External messages built in a RoSy interface will most likely
   * refer to information and concepts that are not available externally and
   * should not be exposed. It is safer if external APIs can understand the
   * errors and decide what the error message should look like.
   *
   * @param string $externalErrorMessage
   */
  public function setExternalErrorMessage($externalErrorMessage)
  {
    $this->externalErrorMessage = $externalErrorMessage;
  }
  /**
   * @return string
   */
  public function getExternalErrorMessage()
  {
    return $this->externalErrorMessage;
  }
  /**
   * Location of the error, as specified by the location type. If location_type
   * is PATH, this should be a path to a field that's relative to the request,
   * using FieldPath notation (net/proto2/util/public/field_path.h). Examples:
   * authenticated_user.gaia_id resource.address[2].country
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * @param self::LOCATION_TYPE_* $locationType
   */
  public function setLocationType($locationType)
  {
    $this->locationType = $locationType;
  }
  /**
   * @return self::LOCATION_TYPE_*
   */
  public function getLocationType()
  {
    return $this->locationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ErrorProto::class, 'Google_Service_YouTubeAnalytics_ErrorProto');
