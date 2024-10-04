<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @copyright 2019 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');

$categorysortorder = [
    'Smileys & Emotion',
    'People & Body',
    'Animals & Nature',
    'Food & Drink',
    'Travel & Places',
    'Activities',
    'Objects',
    'Symbols',
    'Flags'
];

// Source: https://github.com/iamcal/emoji-data
$rawdata = file_get_contents('./emoji_pretty.json');
$jsondata = json_decode($rawdata, true);
$emojibycategory = [];
$obsoletes = [];
// Emoji categories used in the emoji-data library.
$categories = [];

foreach ($jsondata as $data) {
    $category = $data['category'];
    $unified = $data['unified'];

    if ($category === 'Component') {
        continue;
    }

    if (!in_array($category, $categories)) {
        $categories[] = $category;
    }

    if (!empty($data['obsoleted_by'])) {
        // Skip any obsolete emojis. We'll merge these short names into the
        // newer emoji later on.
        $obsoletes[] = [
            'shortname' => $data['short_name'],
            'by' => $data['obsoleted_by']
        ];
        continue;
    }

    if (!isset($emojibycategory[$category])) {
        $emojibycategory[$category] = [
            'name' => $category,
            'emojis' => []
        ];
    }

    $emojibycategory[$category]['emojis'][] = [
        'sortorder' => (int) $data['sort_order'],
        'unified' => $unified,
        'shortnames' => [$data['short_name']]
    ];
}
// Detect any category changes.
// Some emoji categories from the emoji-data library are missing.
if ($missingcategories = array_diff($categories, $categorysortorder)) {
    die("The following categories are missing: " . implode(', ', $missingcategories) .
        ". For more details on how to properly fix this issue, please see /lib/emoji-data/readme_moodle.txt");
}
// Some emoji categories are not being used anymore in the emoji-data library.
if ($unusedcategories = array_diff($categorysortorder, $categories)) {
    die("The following categories are no longer used: " . implode(', ', $unusedcategories) .
        ". For more details on how to properly fix this issue, please see /lib/emoji-data/readme_moodle.txt");
}

$emojibycategory = array_values($emojibycategory);
// Sort the emojis within each category into the order specified in the raw data.
$emojibycategory = array_map(function($category) {
    usort($category['emojis'], function($a, $b) {
        return $a['sortorder'] <=> $b['sortorder'];
    });
    return $category;
}, $emojibycategory);

// Add the short names for the obsoleted emojis into the list of short names
// of the newer emoji.
foreach ($obsoletes as $obsolete) {
    $emojibycategory = array_map(function($category) use ($obsolete) {
        $category['emojis'] = array_map(function($emoji) use ($obsolete) {
            if ($obsolete['by'] == $emoji['unified']) {
                $emoji['shortnames'] = array_merge($emoji['shortnames'], [$obsolete['shortname']]);
            }
            unset($emoji['sortorder']);
            return $emoji;
        }, $category['emojis']);
        return $category;
    }, $emojibycategory);
}
// Sort the emoji categories into the correct order.
usort($emojibycategory, function($a, $b) use ($categorysortorder) {
    $aindex = array_search($a['name'], $categorysortorder);
    $bindex = array_search($b['name'], $categorysortorder);
    return $aindex <=> $bindex;
});

$emojibyshortname = array_reduce($jsondata, function($carry, $data) {
    $unified = null;
    $shortname = $data['short_name'];
    if (!empty($data['obsoleted_by'])) {
        $unified = $data['obsoleted_by'];
    } else {
        $unified = $data['unified'];
    }
    $carry[$shortname] = $unified;
    return $carry;
}, []);

$loader = new \Mustache_Loader_ArrayLoader([
    'data.js' => file_get_contents('./data.js.mustache')
]);
$mustache = new \core\output\mustache_engine(['loader' => $loader]);

echo $mustache->render('data.js', [
    'byCategory' => json_encode($emojibycategory, JSON_PRETTY_PRINT),
    'byShortName' => json_encode($emojibyshortname, JSON_PRETTY_PRINT)
]);
