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
 * Moodle behat context class resolver.
 *
 * @package    behat
 * @copyright  2016 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moodle\BehatExtension\Output\Formatter;

use Behat\Testwork\Exception\ServiceContainer\ExceptionExtension;
use Behat\Testwork\Output\ServiceContainer\OutputExtension;
use Behat\Testwork\ServiceContainer\ServiceProcessor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Behat\Behat\Output\ServiceContainer\Formatter\ProgressFormatterFactory;
use Behat\Behat\EventDispatcher\Event\OutlineTested;
use Behat\Testwork\Output\ServiceContainer\Formatter\FormatterFactory;
use Behat\Testwork\Translator\ServiceContainer\TranslatorExtension;

class MoodleProgressFormatterFactory implements FormatterFactory {
    /**
     * @var ServiceProcessor
     */
    private $processor;

    /*
     * Available services
     */
    const ROOT_LISTENER_ID_MOODLE = 'output.node.listener.moodleprogress';
    const RESULT_TO_STRING_CONVERTER_ID_MOODLE = 'output.node.printer.result_to_string';

    /*
     * Available extension points
     */
    const ROOT_LISTENER_WRAPPER_TAG_MOODLE = 'output.node.listener.moodleprogress.wrapper';

    /**
     * Initializes extension.
     *
     * @param null|ServiceProcessor $processor
     */
    public function __construct(ServiceProcessor $processor = null) {
        $this->processor = $processor ? : new ServiceProcessor();
    }

    /**
     * {@inheritdoc}
     */
    public function buildFormatter(ContainerBuilder $container) {
        $this->loadRootNodeListener($container);
        $this->loadCorePrinters($container);
        $this->loadPrinterHelpers($container);
        $this->loadFormatter($container);
    }

    /**
     * {@inheritdoc}
     */
    public function processFormatter(ContainerBuilder $container) {
        $this->processListenerWrappers($container);
    }

    /**
     * Loads progress formatter node event listener.
     *
     * @param ContainerBuilder $container
     */
    protected function loadRootNodeListener(ContainerBuilder $container) {
        $definition = new Definition('Behat\Behat\Output\Node\EventListener\AST\StepListener', array(
            new Reference('output.node.printer.moodleprogress.step')
        ));
        $container->setDefinition(self::ROOT_LISTENER_ID_MOODLE, $definition);
    }

    /**
     * Loads formatter itself.
     *
     * @param ContainerBuilder $container
     */
    protected function loadFormatter(ContainerBuilder $container) {

        $definition = new Definition('Behat\Behat\Output\Statistics\TotalStatistics');
        $container->setDefinition('output.moodleprogress.statistics', $definition);

        $moodleconfig = $container->getParameter('behat.moodle.parameters');

        $definition = new Definition('Moodle\BehatExtension\Output\Printer\MoodleProgressPrinter',
            array($moodleconfig['moodledirroot']));
        $container->setDefinition('moodle.output.node.printer.moodleprogress.printer', $definition);

        $definition = new Definition('Behat\Testwork\Output\NodeEventListeningFormatter', array(
            'moodle_progress',
            'Prints information about then run followed by one character per step.',
            array(
                'timer' => true
            ),
            $this->createOutputPrinterDefinition(),
            new Definition('Behat\Testwork\Output\Node\EventListener\ChainEventListener', array(
                    array(
                        new Reference(self::ROOT_LISTENER_ID_MOODLE),
                        new Definition('Behat\Behat\Output\Node\EventListener\Statistics\StatisticsListener', array(
                            new Reference('output.moodleprogress.statistics'),
                            new Reference('output.node.printer.moodleprogress.statistics')
                        )),
                        new Definition('Behat\Behat\Output\Node\EventListener\Statistics\ScenarioStatsListener', array(
                            new Reference('output.moodleprogress.statistics')
                        )),
                        new Definition('Behat\Behat\Output\Node\EventListener\Statistics\StepStatsListener', array(
                            new Reference('output.moodleprogress.statistics'),
                            new Reference(ExceptionExtension::PRESENTER_ID)
                        )),
                        new Definition('Behat\Behat\Output\Node\EventListener\Statistics\HookStatsListener', array(
                            new Reference('output.moodleprogress.statistics'),
                            new Reference(ExceptionExtension::PRESENTER_ID)
                        )),
                        new Definition('Behat\Behat\Output\Node\EventListener\AST\SuiteListener', array(
                            new Reference('moodle.output.node.printer.moodleprogress.printer')
                        ))
                    )
                )
            )
        ));
        $definition->addTag(OutputExtension::FORMATTER_TAG, array('priority' => 1));
        $container->setDefinition(OutputExtension::FORMATTER_TAG . '.moodleprogress', $definition);
    }

    /**
     * Loads printer helpers.
     *
     * @param ContainerBuilder $container
     */
    protected function loadPrinterHelpers(ContainerBuilder $container) {
        $definition = new Definition('Behat\Behat\Output\Node\Printer\Helper\ResultToStringConverter');
        $container->setDefinition(self::RESULT_TO_STRING_CONVERTER_ID_MOODLE, $definition);
    }

    /**
     * Loads feature, scenario and step printers.
     *
     * @param ContainerBuilder $container
     */
    protected function loadCorePrinters(ContainerBuilder $container) {
        $definition = new Definition('Behat\Behat\Output\Node\Printer\CounterPrinter', array(
            new Reference(self::RESULT_TO_STRING_CONVERTER_ID_MOODLE),
            new Reference(TranslatorExtension::TRANSLATOR_ID),
        ));
        $container->setDefinition('output.node.moodle.printer.counter', $definition);

        $definition = new Definition('Behat\Behat\Output\Node\Printer\ListPrinter', array(
            new Reference(self::RESULT_TO_STRING_CONVERTER_ID_MOODLE),
            new Reference(ExceptionExtension::PRESENTER_ID),
            new Reference(TranslatorExtension::TRANSLATOR_ID),
            '%paths.base%'
        ));
        $container->setDefinition('output.node.moodle.printer.list', $definition);

        $definition = new Definition('Behat\Behat\Output\Node\Printer\Progress\ProgressStepPrinter', array(
            new Reference(self::RESULT_TO_STRING_CONVERTER_ID_MOODLE)
        ));
        $container->setDefinition('output.node.printer.moodleprogress.step', $definition);

        $definition = new Definition('Behat\Behat\Output\Node\Printer\Progress\ProgressStatisticsPrinter', array(
            new Reference('output.node.moodle.printer.counter'),
            new Reference('output.node.moodle.printer.list')
        ));
        $container->setDefinition('output.node.printer.moodleprogress.statistics', $definition);
    }

    /**
     * Creates output printer definition.
     *
     * @return Definition
     */
    protected function createOutputPrinterDefinition() {
        return new Definition('Behat\Testwork\Output\Printer\StreamOutputPrinter', array(
            new Definition('Behat\Behat\Output\Printer\ConsoleOutputFactory'),
        ));
    }

    /**
     * Processes all registered pretty formatter node listener wrappers.
     *
     * @param ContainerBuilder $container
     */
    protected function processListenerWrappers(ContainerBuilder $container) {
        $this->processor->processWrapperServices($container, self::ROOT_LISTENER_ID_MOODLE, self::ROOT_LISTENER_WRAPPER_TAG_MOODLE);
    }
}
