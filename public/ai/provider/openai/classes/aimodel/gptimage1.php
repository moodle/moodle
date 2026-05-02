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

namespace aiprovider_openai\aimodel;

use core_ai\aimodel\base;

/**
 * GPT Image 1.5 AI model.
 *
 * @package    aiprovider_openai
 * @copyright  2026 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gptimage1 extends base implements openai_base, openai_image_base {
    #[\Override]
    public function get_model_display_name(): string {
        return 'GPT Image 1.5';
    }

    #[\Override]
    public function get_model_name(): string {
        return 'gpt-image-1.5';
    }

    #[\Override]
    public function model_type(): array {
        return [self::MODEL_TYPE_IMAGE];
    }

    #[\Override]
    public function response_format(): ?string {
        return null;
    }

    #[\Override]
    public function get_output_format(): ?string {
        return 'png';
    }

    #[\Override]
    public function calculate_size(string $ratio): string {
        if ($ratio === 'square') {
            $size = '1024x1024';
        } else if ($ratio === 'landscape') {
            $size = '1536x1024';
        } else if ($ratio === 'portrait') {
            $size = '1024x1536';
        } else {
            throw new \coding_exception('Invalid aspect ratio: ' . $ratio);
        }
        return $size;
    }

    #[\Override]
    public function calculate_quality(string $quality): string {
        if ($quality === 'standard') {
            $processedquality = 'medium';
        } else if ($quality === 'hd') {
            $processedquality = 'high';
        } else {
            throw new \coding_exception('Invalid quality: ' . $quality);
        }

        return $processedquality;
    }
}
