<?php
/**
 * Utility class for handling metadata
 */
abstract class H5PMetadata {

  private static $fields = array(
    'title' => array(
      'type' => 'text',
      'maxLength' => 255
    ),
    'authors' => array(
      'type' => 'json'
    ),
    'changes' => array(
      'type' => 'json'
    ),
    'source' => array(
      'type' => 'text',
      'maxLength' => 255
    ),
    'license' => array(
      'type' => 'text',
      'maxLength' => 32
    ),
    'licenseVersion' => array(
      'type' => 'text',
      'maxLength' => 10
    ),
    'licenseExtras' => array(
      'type' => 'text',
      'maxLength' => 5000
    ),
    'authorComments' => array(
      'type' => 'text',
      'maxLength' => 5000
    ),
    'yearFrom' => array(
      'type' => 'int'
    ),
    'yearTo' => array(
      'type' => 'int'
    ),
    'defaultLanguage' => array(
      'type' => 'text',
      'maxLength' => 32,
    )
  );

  /**
   * JSON encode metadata
   *
   * @param object $content
   * @return string
   */
  public static function toJSON($content) {
    // Note: deliberatly creating JSON string "manually" to improve performance
    return
      '{"title":' . (isset($content->title) ? json_encode($content->title) : 'null') .
      ',"authors":' . (isset($content->authors) ? $content->authors : 'null') .
      ',"source":' . (isset($content->source) ? '"' . $content->source . '"' : 'null') .
      ',"license":' . (isset($content->license) ? '"' . $content->license . '"' : 'null') .
      ',"licenseVersion":' . (isset($content->license_version) ? '"' . $content->license_version . '"' : 'null') .
      ',"licenseExtras":' . (isset($content->license_extras) ? json_encode($content->license_extras) : 'null') .
      ',"yearFrom":' . (isset($content->year_from) ? $content->year_from : 'null') .
      ',"yearTo":' .  (isset($content->year_to) ? $content->year_to : 'null') .
      ',"changes":' . (isset($content->changes) ? $content->changes : 'null') .
      ',"defaultLanguage":' . (isset($content->default_language) ? '"' . $content->default_language . '"' : 'null') .
      ',"authorComments":' . (isset($content->author_comments) ? json_encode($content->author_comments) : 'null') . '}';
  }

  /**
   * Make the metadata into an associative array keyed by the property names
   * @param mixed $metadata Array or object containing metadata
   * @param bool $include_title
   * @param bool $include_missing For metadata fields not being set, skip 'em.
   *                             Relevant for content upgrade
   * @param array $types
   * @return array
   */
  public static function toDBArray($metadata, $include_title = true, $include_missing = true, &$types = array()) {
    $fields = array();

    if (!is_array($metadata)) {
      $metadata = (array) $metadata;
    }

    foreach (self::$fields as $key => $config) {

      // Ignore title?
      if ($key === 'title' && !$include_title) {
        continue;
      }

      $exists = array_key_exists($key, $metadata);

      // Don't include missing fields
      if (!$include_missing && !$exists) {
        continue;
      }

      $value = $exists ? $metadata[$key] : null;

      // lowerCamelCase to snake_case
      $db_field_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));

      switch ($config['type']) {
        case 'text':
          if ($value !== null && strlen($value) > $config['maxLength']) {
            $value = mb_substr($value, 0, $config['maxLength']);
          }
          $types[] = '%s';
          break;

        case 'int':
          $value = ($value !== null) ? intval($value) : null;
          $types[] = '%d';
          break;

        case 'json':
          $value = ($value !== null) ? json_encode($value) : null;
          $types[] = '%s';
          break;
      }

      $fields[$db_field_name] = $value;
    }

    return $fields;
  }

  /**
   * The metadataSettings field in libraryJson uses 1 for true and 0 for false.
   * Here we are converting these to booleans, and also doing JSON encoding.
   * This is invoked before the library data is beeing inserted/updated to DB.
   *
   * @param array $metadataSettings
   * @return string
   */
  public static function boolifyAndEncodeSettings($metadataSettings) {
    // Convert metadataSettings values to boolean
    if (isset($metadataSettings['disable'])) {
      $metadataSettings['disable'] = $metadataSettings['disable'] === 1;
    }
    if (isset($metadataSettings['disableExtraTitleField'])) {
      $metadataSettings['disableExtraTitleField'] = $metadataSettings['disableExtraTitleField'] === 1;
    }

    return json_encode($metadataSettings);
  }
}
