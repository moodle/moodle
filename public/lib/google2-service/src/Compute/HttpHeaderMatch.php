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

namespace Google\Service\Compute;

class HttpHeaderMatch extends \Google\Model
{
  /**
   * The value should exactly match contents of exactMatch.
   *
   * Only one of exactMatch, prefixMatch,suffixMatch, regexMatch,presentMatch or
   * rangeMatch must be set.
   *
   * @var string
   */
  public $exactMatch;
  /**
   * The name of the HTTP header to match.
   *
   * For matching against the HTTP request's authority, use a headerMatch with
   * the header name ":authority".
   *
   * For matching a request's method, use the headerName ":method".
   *
   * When the URL map is bound to a target gRPC proxy that has the
   * validateForProxyless field set to true, only non-binary user-specified
   * custom metadata and the `content-type` header are supported. The following
   * transport-level headers cannot be used in header matching rules:
   * `:authority`, `:method`, `:path`, `:scheme`, `user-agent`, `accept-
   * encoding`, `content-encoding`, `grpc-accept-encoding`, `grpc-encoding`,
   * `grpc-previous-rpc-attempts`, `grpc-tags-bin`, `grpc-timeout` and `grpc-
   * trace-bin`.
   *
   * @var string
   */
  public $headerName;
  /**
   * If set to false, the headerMatch is considered a match if the preceding
   * match criteria are met. If set to true, the headerMatch is considered a
   * match if the preceding match criteria are NOT met.
   *
   * The default setting is false.
   *
   * @var bool
   */
  public $invertMatch;
  /**
   * The value of the header must start with the contents ofprefixMatch.
   *
   * Only one of exactMatch, prefixMatch,suffixMatch, regexMatch,presentMatch or
   * rangeMatch must be set.
   *
   * @var string
   */
  public $prefixMatch;
  /**
   * A header with the contents of headerName must exist. The match takes place
   * whether or not the request's header has a value.
   *
   * Only one of exactMatch, prefixMatch,suffixMatch, regexMatch,presentMatch or
   * rangeMatch must be set.
   *
   * @var bool
   */
  public $presentMatch;
  protected $rangeMatchType = Int64RangeMatch::class;
  protected $rangeMatchDataType = '';
  /**
   * The value of the header must match the regular expression specified
   * inregexMatch. For more information about regular expression syntax, see
   * Syntax.
   *
   * For matching against a port specified in the HTTP request, use a
   * headerMatch with headerName set to PORT and a regular expression that
   * satisfies the RFC2616 Host header's port specifier.
   *
   * Only one of exactMatch, prefixMatch,suffixMatch, regexMatch,presentMatch or
   * rangeMatch must be set.
   *
   * Regular expressions can only be used when the loadBalancingScheme is set to
   * INTERNAL_SELF_MANAGED, EXTERNAL_MANAGED (regional scope) or
   * INTERNAL_MANAGED.
   *
   * @var string
   */
  public $regexMatch;
  /**
   * The value of the header must end with the contents ofsuffixMatch.
   *
   * Only one of exactMatch, prefixMatch,suffixMatch, regexMatch,presentMatch or
   * rangeMatch must be set.
   *
   * @var string
   */
  public $suffixMatch;

  /**
   * The value should exactly match contents of exactMatch.
   *
   * Only one of exactMatch, prefixMatch,suffixMatch, regexMatch,presentMatch or
   * rangeMatch must be set.
   *
   * @param string $exactMatch
   */
  public function setExactMatch($exactMatch)
  {
    $this->exactMatch = $exactMatch;
  }
  /**
   * @return string
   */
  public function getExactMatch()
  {
    return $this->exactMatch;
  }
  /**
   * The name of the HTTP header to match.
   *
   * For matching against the HTTP request's authority, use a headerMatch with
   * the header name ":authority".
   *
   * For matching a request's method, use the headerName ":method".
   *
   * When the URL map is bound to a target gRPC proxy that has the
   * validateForProxyless field set to true, only non-binary user-specified
   * custom metadata and the `content-type` header are supported. The following
   * transport-level headers cannot be used in header matching rules:
   * `:authority`, `:method`, `:path`, `:scheme`, `user-agent`, `accept-
   * encoding`, `content-encoding`, `grpc-accept-encoding`, `grpc-encoding`,
   * `grpc-previous-rpc-attempts`, `grpc-tags-bin`, `grpc-timeout` and `grpc-
   * trace-bin`.
   *
   * @param string $headerName
   */
  public function setHeaderName($headerName)
  {
    $this->headerName = $headerName;
  }
  /**
   * @return string
   */
  public function getHeaderName()
  {
    return $this->headerName;
  }
  /**
   * If set to false, the headerMatch is considered a match if the preceding
   * match criteria are met. If set to true, the headerMatch is considered a
   * match if the preceding match criteria are NOT met.
   *
   * The default setting is false.
   *
   * @param bool $invertMatch
   */
  public function setInvertMatch($invertMatch)
  {
    $this->invertMatch = $invertMatch;
  }
  /**
   * @return bool
   */
  public function getInvertMatch()
  {
    return $this->invertMatch;
  }
  /**
   * The value of the header must start with the contents ofprefixMatch.
   *
   * Only one of exactMatch, prefixMatch,suffixMatch, regexMatch,presentMatch or
   * rangeMatch must be set.
   *
   * @param string $prefixMatch
   */
  public function setPrefixMatch($prefixMatch)
  {
    $this->prefixMatch = $prefixMatch;
  }
  /**
   * @return string
   */
  public function getPrefixMatch()
  {
    return $this->prefixMatch;
  }
  /**
   * A header with the contents of headerName must exist. The match takes place
   * whether or not the request's header has a value.
   *
   * Only one of exactMatch, prefixMatch,suffixMatch, regexMatch,presentMatch or
   * rangeMatch must be set.
   *
   * @param bool $presentMatch
   */
  public function setPresentMatch($presentMatch)
  {
    $this->presentMatch = $presentMatch;
  }
  /**
   * @return bool
   */
  public function getPresentMatch()
  {
    return $this->presentMatch;
  }
  /**
   * The header value must be an integer and its value must be in the range
   * specified in rangeMatch. If the header does not contain an integer, number
   * or is empty, the match fails.
   *
   * For example for a range [-5, 0]               - -3 will match.       - 0
   * will not match.       - 0.25 will not match.       - -3someString will not
   * match.
   *
   * Only one of exactMatch, prefixMatch,suffixMatch, regexMatch,presentMatch or
   * rangeMatch must be set.
   *
   * rangeMatch is not supported for load balancers that have
   * loadBalancingScheme set to EXTERNAL.
   *
   * @param Int64RangeMatch $rangeMatch
   */
  public function setRangeMatch(Int64RangeMatch $rangeMatch)
  {
    $this->rangeMatch = $rangeMatch;
  }
  /**
   * @return Int64RangeMatch
   */
  public function getRangeMatch()
  {
    return $this->rangeMatch;
  }
  /**
   * The value of the header must match the regular expression specified
   * inregexMatch. For more information about regular expression syntax, see
   * Syntax.
   *
   * For matching against a port specified in the HTTP request, use a
   * headerMatch with headerName set to PORT and a regular expression that
   * satisfies the RFC2616 Host header's port specifier.
   *
   * Only one of exactMatch, prefixMatch,suffixMatch, regexMatch,presentMatch or
   * rangeMatch must be set.
   *
   * Regular expressions can only be used when the loadBalancingScheme is set to
   * INTERNAL_SELF_MANAGED, EXTERNAL_MANAGED (regional scope) or
   * INTERNAL_MANAGED.
   *
   * @param string $regexMatch
   */
  public function setRegexMatch($regexMatch)
  {
    $this->regexMatch = $regexMatch;
  }
  /**
   * @return string
   */
  public function getRegexMatch()
  {
    return $this->regexMatch;
  }
  /**
   * The value of the header must end with the contents ofsuffixMatch.
   *
   * Only one of exactMatch, prefixMatch,suffixMatch, regexMatch,presentMatch or
   * rangeMatch must be set.
   *
   * @param string $suffixMatch
   */
  public function setSuffixMatch($suffixMatch)
  {
    $this->suffixMatch = $suffixMatch;
  }
  /**
   * @return string
   */
  public function getSuffixMatch()
  {
    return $this->suffixMatch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpHeaderMatch::class, 'Google_Service_Compute_HttpHeaderMatch');
