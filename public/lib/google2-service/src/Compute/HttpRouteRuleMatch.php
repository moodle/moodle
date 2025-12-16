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

class HttpRouteRuleMatch extends \Google\Collection
{
  protected $collection_key = 'queryParameterMatches';
  /**
   * For satisfying the matchRule condition, the path of the request must
   * exactly match the value specified infullPathMatch after removing any query
   * parameters and anchor that may be part of the original URL.
   *
   * fullPathMatch must be from 1 to 1024 characters.
   *
   * Only one of prefixMatch, fullPathMatch,regexMatch or path_template_match
   * must be specified.
   *
   * @var string
   */
  public $fullPathMatch;
  protected $headerMatchesType = HttpHeaderMatch::class;
  protected $headerMatchesDataType = 'array';
  /**
   * Specifies that prefixMatch and fullPathMatch matches are case sensitive.
   *
   * The default value is false.
   *
   * ignoreCase must not be used with regexMatch.
   *
   * Not supported when the URL map is bound to a target gRPC proxy.
   *
   * @var bool
   */
  public $ignoreCase;
  protected $metadataFiltersType = MetadataFilter::class;
  protected $metadataFiltersDataType = 'array';
  /**
   * If specified, this field defines a path template pattern that must match
   * the :path header after the query string is removed.
   *
   * A path template pattern can include variables and wildcards. Variables are
   * enclosed in curly braces, for example{variable_name}. Wildcards include *
   * that matches a single path segment, and ** that matches zero or more path
   * segments. The pattern must follow these rules:
   *
   *           - The value must be between 1 and 1024 characters.       - The
   * pattern must start with a leading slash ("/").       - No more than 5
   * operators (variables or wildcards) may appear in       the pattern.
   *
   * Precisely one ofprefixMatch, fullPathMatch,regexMatch, or pathTemplateMatch
   * must be set.
   *
   * @var string
   */
  public $pathTemplateMatch;
  /**
   * For satisfying the matchRule condition, the request's path must begin with
   * the specified prefixMatch.prefixMatch must begin with a /.
   *
   * The value must be from 1 to 1024 characters.
   *
   * The * character inside a prefix match is treated as a literal character,
   * not as a wildcard.
   *
   * Only one of prefixMatch, fullPathMatch,regexMatch or path_template_match
   * can be used within a matchRule.
   *
   * @var string
   */
  public $prefixMatch;
  protected $queryParameterMatchesType = HttpQueryParameterMatch::class;
  protected $queryParameterMatchesDataType = 'array';
  /**
   * For satisfying the matchRule condition, the path of the request must
   * satisfy the regular expression specified inregexMatch after removing any
   * query parameters and anchor supplied with the original URL. For more
   * information about regular expression syntax, see Syntax.
   *
   * Only one of prefixMatch, fullPathMatch,regexMatch or path_template_match
   * must be specified.
   *
   * Regular expressions can only be used when the loadBalancingScheme is set to
   * INTERNAL_SELF_MANAGED, EXTERNAL_MANAGED (regional scope) or
   * INTERNAL_MANAGED.
   *
   * @var string
   */
  public $regexMatch;

  /**
   * For satisfying the matchRule condition, the path of the request must
   * exactly match the value specified infullPathMatch after removing any query
   * parameters and anchor that may be part of the original URL.
   *
   * fullPathMatch must be from 1 to 1024 characters.
   *
   * Only one of prefixMatch, fullPathMatch,regexMatch or path_template_match
   * must be specified.
   *
   * @param string $fullPathMatch
   */
  public function setFullPathMatch($fullPathMatch)
  {
    $this->fullPathMatch = $fullPathMatch;
  }
  /**
   * @return string
   */
  public function getFullPathMatch()
  {
    return $this->fullPathMatch;
  }
  /**
   * Specifies a list of header match criteria, all of which must match
   * corresponding headers in the request.
   *
   * @param HttpHeaderMatch[] $headerMatches
   */
  public function setHeaderMatches($headerMatches)
  {
    $this->headerMatches = $headerMatches;
  }
  /**
   * @return HttpHeaderMatch[]
   */
  public function getHeaderMatches()
  {
    return $this->headerMatches;
  }
  /**
   * Specifies that prefixMatch and fullPathMatch matches are case sensitive.
   *
   * The default value is false.
   *
   * ignoreCase must not be used with regexMatch.
   *
   * Not supported when the URL map is bound to a target gRPC proxy.
   *
   * @param bool $ignoreCase
   */
  public function setIgnoreCase($ignoreCase)
  {
    $this->ignoreCase = $ignoreCase;
  }
  /**
   * @return bool
   */
  public function getIgnoreCase()
  {
    return $this->ignoreCase;
  }
  /**
   * Opaque filter criteria used by the load balancer to restrict routing
   * configuration to a limited set of xDS compliant clients. In their xDS
   * requests to the load balancer, xDS clients present node metadata. When
   * there is a match, the relevant routing configuration is made available to
   * those proxies.
   *
   * For each metadataFilter in this list, if itsfilterMatchCriteria is set to
   * MATCH_ANY, at least one of thefilterLabels must match the corresponding
   * label provided in the metadata. If its filterMatchCriteria is set to
   * MATCH_ALL, then all of its filterLabels must match with corresponding
   * labels provided in the metadata. If multiple metadata filters are
   * specified, all of them need to be satisfied in order to be considered a
   * match.
   *
   * metadataFilters specified here is applied after those specified in
   * ForwardingRule that refers to theUrlMap this HttpRouteRuleMatch belongs to.
   *
   * metadataFilters only applies to load balancers that haveloadBalancingScheme
   * set toINTERNAL_SELF_MANAGED.
   *
   * Not supported when the URL map is bound to a target gRPC proxy that has
   * validateForProxyless field set to true.
   *
   * @param MetadataFilter[] $metadataFilters
   */
  public function setMetadataFilters($metadataFilters)
  {
    $this->metadataFilters = $metadataFilters;
  }
  /**
   * @return MetadataFilter[]
   */
  public function getMetadataFilters()
  {
    return $this->metadataFilters;
  }
  /**
   * If specified, this field defines a path template pattern that must match
   * the :path header after the query string is removed.
   *
   * A path template pattern can include variables and wildcards. Variables are
   * enclosed in curly braces, for example{variable_name}. Wildcards include *
   * that matches a single path segment, and ** that matches zero or more path
   * segments. The pattern must follow these rules:
   *
   *           - The value must be between 1 and 1024 characters.       - The
   * pattern must start with a leading slash ("/").       - No more than 5
   * operators (variables or wildcards) may appear in       the pattern.
   *
   * Precisely one ofprefixMatch, fullPathMatch,regexMatch, or pathTemplateMatch
   * must be set.
   *
   * @param string $pathTemplateMatch
   */
  public function setPathTemplateMatch($pathTemplateMatch)
  {
    $this->pathTemplateMatch = $pathTemplateMatch;
  }
  /**
   * @return string
   */
  public function getPathTemplateMatch()
  {
    return $this->pathTemplateMatch;
  }
  /**
   * For satisfying the matchRule condition, the request's path must begin with
   * the specified prefixMatch.prefixMatch must begin with a /.
   *
   * The value must be from 1 to 1024 characters.
   *
   * The * character inside a prefix match is treated as a literal character,
   * not as a wildcard.
   *
   * Only one of prefixMatch, fullPathMatch,regexMatch or path_template_match
   * can be used within a matchRule.
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
   * Specifies a list of query parameter match criteria, all of which must match
   * corresponding query parameters in the request.
   *
   * Not supported when the URL map is bound to a target gRPC proxy.
   *
   * @param HttpQueryParameterMatch[] $queryParameterMatches
   */
  public function setQueryParameterMatches($queryParameterMatches)
  {
    $this->queryParameterMatches = $queryParameterMatches;
  }
  /**
   * @return HttpQueryParameterMatch[]
   */
  public function getQueryParameterMatches()
  {
    return $this->queryParameterMatches;
  }
  /**
   * For satisfying the matchRule condition, the path of the request must
   * satisfy the regular expression specified inregexMatch after removing any
   * query parameters and anchor supplied with the original URL. For more
   * information about regular expression syntax, see Syntax.
   *
   * Only one of prefixMatch, fullPathMatch,regexMatch or path_template_match
   * must be specified.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRouteRuleMatch::class, 'Google_Service_Compute_HttpRouteRuleMatch');
