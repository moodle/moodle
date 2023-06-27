<?php

namespace Gettext\Languages\Exporter;

class Ruby extends Exporter
{
    /**
     * {@inheritdoc}
     *
     * @see \Gettext\Languages\Exporter\Exporter::getDescription()
     */
    public static function getDescription()
    {
        return 'Build a Ruby hash';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Gettext\Languages\Exporter\Exporter::toStringDo()
     */
    protected static function toStringDo($languages)
    {
        $lines = array();
        $lines[] = 'PLURAL_RULES = {';
        foreach ($languages as $lc) {
            $lines[] = '  \'' . $lc->id . '\' => {';
            $lines[] = '    \'name\' => \'' . addslashes($lc->name) . '\',';
            if (isset($lc->supersededBy)) {
                $lines[] = '    \'supersededBy\' => \'' . $lc->supersededBy . '\',';
            }
            if (isset($lc->script)) {
                $lines[] = '    \'script\' => \'' . addslashes($lc->script) . '\',';
            }
            if (isset($lc->territory)) {
                $lines[] = '    \'territory\' => \'' . addslashes($lc->territory) . '\',';
            }
            if (isset($lc->baseLanguage)) {
                $lines[] = '    \'baseLanguage\' => \'' . addslashes($lc->baseLanguage) . '\',';
            }
            $lines[] = '    \'formula\' => \'' . $lc->formula . '\',';
            $lines[] = '    \'plurals\' => ' . count($lc->categories) . ',';
            $catNames = array();
            foreach ($lc->categories as $c) {
                $catNames[] = "'{$c->id}'";
            }
            $lines[] = '    \'cases\' => [' . implode(', ', $catNames) . '],';
            $lines[] = '    \'examples\' => {';
            foreach ($lc->categories as $c) {
                $lines[] = '      \'' . $c->id . '\' => \'' . $c->examples . '\',';
            }
            $lines[] = '    },';
            $lines[] = '  },';
        }
        $lines[] = '}';
        $lines[] = '';

        return implode("\n", $lines);
    }
}
