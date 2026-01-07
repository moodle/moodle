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

namespace Google\Service\Storagetransfer;

class ObjectConditions extends \Google\Collection
{
  protected $collection_key = 'includePrefixes';
  /**
   * If you specify `exclude_prefixes`, Storage Transfer Service uses the items
   * in the `exclude_prefixes` array to determine which objects to exclude from
   * a transfer. Objects must not start with one of the matching
   * `exclude_prefixes` for inclusion in a transfer. The following are
   * requirements of `exclude_prefixes`: * Each exclude-prefix can contain any
   * sequence of Unicode characters, to a max length of 1024 bytes when
   * UTF8-encoded, and must not contain Carriage Return or Line Feed characters.
   * Wildcard matching and regular expression matching are not supported. * Each
   * exclude-prefix must omit the leading slash. For example, to exclude the
   * object `s3://my-aws-bucket/logs/y=2015/requests.gz`, specify the exclude-
   * prefix as `logs/y=2015/requests.gz`. * None of the exclude-prefix values
   * can be empty, if specified. * Each exclude-prefix must exclude a distinct
   * portion of the object namespace. No exclude-prefix may be a prefix of
   * another exclude-prefix. * If include_prefixes is specified, then each
   * exclude-prefix must start with the value of a path explicitly included by
   * `include_prefixes`. The max size of `exclude_prefixes` is 1000. For more
   * information, see [Filtering objects from transfers](/storage-
   * transfer/docs/filtering-objects-from-transfers).
   *
   * @var string[]
   */
  public $excludePrefixes;
  /**
   * If you specify `include_prefixes`, Storage Transfer Service uses the items
   * in the `include_prefixes` array to determine which objects to include in a
   * transfer. Objects must start with one of the matching `include_prefixes`
   * for inclusion in the transfer. If exclude_prefixes is specified, objects
   * must not start with any of the `exclude_prefixes` specified for inclusion
   * in the transfer. The following are requirements of `include_prefixes`: *
   * Each include-prefix can contain any sequence of Unicode characters, to a
   * max length of 1024 bytes when UTF8-encoded, and must not contain Carriage
   * Return or Line Feed characters. Wildcard matching and regular expression
   * matching are not supported. * Each include-prefix must omit the leading
   * slash. For example, to include the object `s3://my-aws-
   * bucket/logs/y=2015/requests.gz`, specify the include-prefix as
   * `logs/y=2015/requests.gz`. * None of the include-prefix values can be
   * empty, if specified. * Each include-prefix must include a distinct portion
   * of the object namespace. No include-prefix may be a prefix of another
   * include-prefix. The max size of `include_prefixes` is 1000. For more
   * information, see [Filtering objects from transfers](/storage-
   * transfer/docs/filtering-objects-from-transfers).
   *
   * @var string[]
   */
  public $includePrefixes;
  /**
   * If specified, only objects with a "last modification time" before this
   * timestamp and objects that don't have a "last modification time" are
   * transferred.
   *
   * @var string
   */
  public $lastModifiedBefore;
  /**
   * If specified, only objects with a "last modification time" on or after this
   * timestamp and objects that don't have a "last modification time" are
   * transferred. The `last_modified_since` and `last_modified_before` fields
   * can be used together for chunked data processing. For example, consider a
   * script that processes each day's worth of data at a time. For that you'd
   * set each of the fields as follows: * `last_modified_since` to the start of
   * the day * `last_modified_before` to the end of the day
   *
   * @var string
   */
  public $lastModifiedSince;
  /**
   * Optional. If specified, only objects matching this glob are transferred.
   *
   * @var string
   */
  public $matchGlob;
  /**
   * Ensures that objects are not transferred if a specific maximum time has
   * elapsed since the "last modification time". When a TransferOperation
   * begins, objects with a "last modification time" are transferred only if the
   * elapsed time between the start_time of the `TransferOperation`and the "last
   * modification time" of the object is less than the value of
   * max_time_elapsed_since_last_modification`. Objects that do not have a "last
   * modification time" are also transferred.
   *
   * @var string
   */
  public $maxTimeElapsedSinceLastModification;
  /**
   * Ensures that objects are not transferred until a specific minimum time has
   * elapsed after the "last modification time". When a TransferOperation
   * begins, objects with a "last modification time" are transferred only if the
   * elapsed time between the start_time of the `TransferOperation` and the
   * "last modification time" of the object is equal to or greater than the
   * value of min_time_elapsed_since_last_modification`. Objects that do not
   * have a "last modification time" are also transferred.
   *
   * @var string
   */
  public $minTimeElapsedSinceLastModification;

  /**
   * If you specify `exclude_prefixes`, Storage Transfer Service uses the items
   * in the `exclude_prefixes` array to determine which objects to exclude from
   * a transfer. Objects must not start with one of the matching
   * `exclude_prefixes` for inclusion in a transfer. The following are
   * requirements of `exclude_prefixes`: * Each exclude-prefix can contain any
   * sequence of Unicode characters, to a max length of 1024 bytes when
   * UTF8-encoded, and must not contain Carriage Return or Line Feed characters.
   * Wildcard matching and regular expression matching are not supported. * Each
   * exclude-prefix must omit the leading slash. For example, to exclude the
   * object `s3://my-aws-bucket/logs/y=2015/requests.gz`, specify the exclude-
   * prefix as `logs/y=2015/requests.gz`. * None of the exclude-prefix values
   * can be empty, if specified. * Each exclude-prefix must exclude a distinct
   * portion of the object namespace. No exclude-prefix may be a prefix of
   * another exclude-prefix. * If include_prefixes is specified, then each
   * exclude-prefix must start with the value of a path explicitly included by
   * `include_prefixes`. The max size of `exclude_prefixes` is 1000. For more
   * information, see [Filtering objects from transfers](/storage-
   * transfer/docs/filtering-objects-from-transfers).
   *
   * @param string[] $excludePrefixes
   */
  public function setExcludePrefixes($excludePrefixes)
  {
    $this->excludePrefixes = $excludePrefixes;
  }
  /**
   * @return string[]
   */
  public function getExcludePrefixes()
  {
    return $this->excludePrefixes;
  }
  /**
   * If you specify `include_prefixes`, Storage Transfer Service uses the items
   * in the `include_prefixes` array to determine which objects to include in a
   * transfer. Objects must start with one of the matching `include_prefixes`
   * for inclusion in the transfer. If exclude_prefixes is specified, objects
   * must not start with any of the `exclude_prefixes` specified for inclusion
   * in the transfer. The following are requirements of `include_prefixes`: *
   * Each include-prefix can contain any sequence of Unicode characters, to a
   * max length of 1024 bytes when UTF8-encoded, and must not contain Carriage
   * Return or Line Feed characters. Wildcard matching and regular expression
   * matching are not supported. * Each include-prefix must omit the leading
   * slash. For example, to include the object `s3://my-aws-
   * bucket/logs/y=2015/requests.gz`, specify the include-prefix as
   * `logs/y=2015/requests.gz`. * None of the include-prefix values can be
   * empty, if specified. * Each include-prefix must include a distinct portion
   * of the object namespace. No include-prefix may be a prefix of another
   * include-prefix. The max size of `include_prefixes` is 1000. For more
   * information, see [Filtering objects from transfers](/storage-
   * transfer/docs/filtering-objects-from-transfers).
   *
   * @param string[] $includePrefixes
   */
  public function setIncludePrefixes($includePrefixes)
  {
    $this->includePrefixes = $includePrefixes;
  }
  /**
   * @return string[]
   */
  public function getIncludePrefixes()
  {
    return $this->includePrefixes;
  }
  /**
   * If specified, only objects with a "last modification time" before this
   * timestamp and objects that don't have a "last modification time" are
   * transferred.
   *
   * @param string $lastModifiedBefore
   */
  public function setLastModifiedBefore($lastModifiedBefore)
  {
    $this->lastModifiedBefore = $lastModifiedBefore;
  }
  /**
   * @return string
   */
  public function getLastModifiedBefore()
  {
    return $this->lastModifiedBefore;
  }
  /**
   * If specified, only objects with a "last modification time" on or after this
   * timestamp and objects that don't have a "last modification time" are
   * transferred. The `last_modified_since` and `last_modified_before` fields
   * can be used together for chunked data processing. For example, consider a
   * script that processes each day's worth of data at a time. For that you'd
   * set each of the fields as follows: * `last_modified_since` to the start of
   * the day * `last_modified_before` to the end of the day
   *
   * @param string $lastModifiedSince
   */
  public function setLastModifiedSince($lastModifiedSince)
  {
    $this->lastModifiedSince = $lastModifiedSince;
  }
  /**
   * @return string
   */
  public function getLastModifiedSince()
  {
    return $this->lastModifiedSince;
  }
  /**
   * Optional. If specified, only objects matching this glob are transferred.
   *
   * @param string $matchGlob
   */
  public function setMatchGlob($matchGlob)
  {
    $this->matchGlob = $matchGlob;
  }
  /**
   * @return string
   */
  public function getMatchGlob()
  {
    return $this->matchGlob;
  }
  /**
   * Ensures that objects are not transferred if a specific maximum time has
   * elapsed since the "last modification time". When a TransferOperation
   * begins, objects with a "last modification time" are transferred only if the
   * elapsed time between the start_time of the `TransferOperation`and the "last
   * modification time" of the object is less than the value of
   * max_time_elapsed_since_last_modification`. Objects that do not have a "last
   * modification time" are also transferred.
   *
   * @param string $maxTimeElapsedSinceLastModification
   */
  public function setMaxTimeElapsedSinceLastModification($maxTimeElapsedSinceLastModification)
  {
    $this->maxTimeElapsedSinceLastModification = $maxTimeElapsedSinceLastModification;
  }
  /**
   * @return string
   */
  public function getMaxTimeElapsedSinceLastModification()
  {
    return $this->maxTimeElapsedSinceLastModification;
  }
  /**
   * Ensures that objects are not transferred until a specific minimum time has
   * elapsed after the "last modification time". When a TransferOperation
   * begins, objects with a "last modification time" are transferred only if the
   * elapsed time between the start_time of the `TransferOperation` and the
   * "last modification time" of the object is equal to or greater than the
   * value of min_time_elapsed_since_last_modification`. Objects that do not
   * have a "last modification time" are also transferred.
   *
   * @param string $minTimeElapsedSinceLastModification
   */
  public function setMinTimeElapsedSinceLastModification($minTimeElapsedSinceLastModification)
  {
    $this->minTimeElapsedSinceLastModification = $minTimeElapsedSinceLastModification;
  }
  /**
   * @return string
   */
  public function getMinTimeElapsedSinceLastModification()
  {
    return $this->minTimeElapsedSinceLastModification;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ObjectConditions::class, 'Google_Service_Storagetransfer_ObjectConditions');
