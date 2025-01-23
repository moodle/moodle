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

namespace core_badges\local\backpack\ob\v2p0;

use core\url;
use core_badges\badge;
use core_badges\local\backpack\ob_factory;
use core_badges\local\backpack\ob\badge_exporter_interface;
use core_badges\local\backpack\ob\exporter_base;

/**
 * Class that represents badgeclass to be exported to a backpack.
 *
 * @package    core_badges
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badge_exporter extends exporter_base implements badge_exporter_interface {
    /** @var badge BadgeClass */
    protected $badge;

    /**
     * Constructs with badge identifier.
     *
     * @param int $id Badge identifier.
     */
    public function __construct(
        int $id,
    ) {
        $this->badge = new badge($id);
    }

    #[\Override]
    public function export(
        bool $nested = true,
    ): array {
        $data = [];

        $badgeid = $this->badge->id;
        $obversion = $this->get_version_from_namespace();
        $context = $this->badge->get_context();
        $classurl = $this->get_json_url();

        // Required.
        $data = [
            '@context' => OPEN_BADGES_V2_CONTEXT,
            'id' => $classurl->out(false),
            'type' => OPEN_BADGES_V2_TYPE_BADGE,
            'name' => $this->badge->name,
            'description' => $this->badge->description,
        ];

        if (!empty($this->badge->version)) {
            $data['version'] = $this->badge->version;
        }
        if (!empty($this->badge->language)) {
            $data['@language'] = $this->badge->language;
        }

        // Image.
        $urlimage = url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badgeid, '/', 'f3')->out(false);
        $data['image'] = [];
        if ($this->badge->imagecaption) {
            $data['image']['id'] = $urlimage;
            $data['image']['caption'] = $this->badge->imagecaption;
        } else {
            $data['image'] = $urlimage;
        }

        // Criteria.
        $data['criteria'] = $this->export_criteria();

        // Issuer.
        $issuer = ob_factory::create_issuer_exporter_from_id($badgeid, $obversion);
        if ($nested) {
            $data['issuer'] = $issuer->export();
        } else {
            // When no nested issuer information is needed, we just return the URL to the issuer JSON.
            $data['issuer'] = $issuer->get_json_url()->out(false);
        }

        // Tags.
        $tags = $this->badge->get_badge_tags();
        if (is_array($tags) && count($tags) > 0) {
            $data['tags'] = $tags;
        }

        // Related badges.
        $relateds = $this->export_related_badges();
        if (!empty($relateds)) {
            $data['related'] = $relateds;
        }

        // Alignments.
        $alignments = $this->export_alignments();
        if (!empty($alignments)) {
            $data['alignment'] = $alignments;
        }

        return $data;
    }

    #[\Override]
    public function export_related_badges(): array {
        $data = [];
        $relatedbadges = $this->badge->get_related_badges(true);
        if (!empty($relatedbadges)) {
            foreach ($relatedbadges as $related) {
                $relatedurl = new url('/badges/json/badge.php', ['id' => $related->id]);
                $data[] = [
                    'id' => $relatedurl->out(false),
                    'version' => $related->version,
                    '@language' => $related->language,
                ];
            }
        }
        return $data;
    }

    #[\Override]
    public function export_criteria(): array|string {
        $params = ['id' => $this->badge->id];
        $badgeurl = new url('/badges/badgeclass.php', $params);
        $narrative = $this->badge->markdown_badge_criteria();
        if (!empty($narrative)) {
            return [
                'id' => $badgeurl->out(false),
                'narrative' => $narrative,
            ];
        } else {
            return $badgeurl->out(false);
        }
    }

    #[\Override]
    public function export_alignments(): array {
        $alignments = $this->badge->get_alignments();
        if (empty($alignments)) {
            return [];
        }

        $data = [];
        foreach ($alignments as $item) {
            $alignment = [
                'targetName' => $item->targetname,
                'targetUrl' => $item->targeturl,
            ];
            if ($item->targetdescription) {
                $alignment['targetDescription'] = $item->targetdescription;
            }
            if ($item->targetframework) {
                $alignment['targetFramework'] = $item->targetframework;
            }
            if ($item->targetcode) {
                $alignment['targetCode'] = $item->targetcode;
            }
            $data[] = $alignment;
        }
        return $data;
    }

    #[\Override]
    public function get_json_url(): url {
        return new url(
            '/badges/json/badge.php',
            [
                'id' => $this->badge->id,
                'obversion' => $this->get_version_from_namespace(),
            ],
        );
    }
}
