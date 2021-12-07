<?php

namespace Moodle\BehatExtension\ServiceContainer;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Testwork\ServiceContainer\ServiceProcessor;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Moodle\BehatExtension\Output\Formatter\MoodleProgressFormatterFactory;
use Behat\Behat\Tester\ServiceContainer\TesterExtension;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\Suite\ServiceContainer\SuiteExtension;
use Behat\Behat\Definition\ServiceContainer\DefinitionExtension;
use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Behat\Definition\Printer\ConsoleDefinitionListPrinter;
use Behat\Behat\Gherkin\ServiceContainer\GherkinExtension;
use Behat\Testwork\Output\ServiceContainer\OutputExtension;
use Behat\Testwork\Specification\ServiceContainer\SpecificationExtension;
use Moodle\BehatExtension\Driver\WebDriverFactory;

/**
 * Behat extension for moodle
 *
 * Provides multiple features directory loading (Gherkin\Loader\MoodleFeaturesSuiteLoader
 */
class BehatExtension implements ExtensionInterface {
    /**
     * Extension configuration ID.
     */
    const MOODLE_ID = 'moodle';

    const GHERKIN_ID = 'gherkin';

    /**
     * @var ServiceProcessor
     */
    private $processor;

    /**
     * Initializes compiler pass.
     *
     * @param null|ServiceProcessor $processor
     */
    public function __construct(ServiceProcessor $processor = null) {
        $this->processor = $processor ? : new ServiceProcessor();
    }

    /**
     * Loads moodle specific configuration.
     *
     * @param array            $config    Extension configuration hash (from behat.yml)
     * @param ContainerBuilder $container ContainerBuilder instance
     */
    public function load(ContainerBuilder $container, array $config) {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/services'));
        $loader->load('core.xml');

        // Getting the extension parameters.
        $container->setParameter('behat.moodle.parameters', $config);

        // Load moodle progress formatter.
        $moodleprogressformatter = new MoodleProgressFormatterFactory();
        $moodleprogressformatter->buildFormatter($container);

        // Load custom step tester event dispatcher.
        $this->loadEventDispatchingStepTester($container);

        // Load chained step tester.
        $this->loadChainedStepTester($container);

        // Load step count formatter.
        $this->loadMoodleListFormatter($container);

        // Load step count formatter.
        $this->loadMoodleStepcountFormatter($container);

        // Load screenshot formatter.
        $this->loadMoodleScreenshotFormatter($container);

        // Load namespace alias.
        $this->alias_old_namespaces();

        // Load skip passed controller and list locator.
        $this->loadSkipPassedController($container, $config['passed_cache']);
        $this->loadFilesystemSkipPassedScenariosListLocator($container);
    }

    /**
     * Loads moodle List formatter.
     *
     * @param ContainerBuilder $container
     */
    protected function loadMoodleListFormatter(ContainerBuilder $container) {
        $definition = new Definition('Moodle\BehatExtension\Output\Formatter\MoodleListFormatter', array(
            'moodle_list',
            'List all scenarios. Use with --dry-run',
            array('stepcount' => false),
            $this->createOutputPrinterDefinition()
        ));
        $definition->addTag(OutputExtension::FORMATTER_TAG, array('priority' => 101));
        $container->setDefinition(OutputExtension::FORMATTER_TAG . '.moodle_list', $definition);
    }

    /**
     * Loads moodle Step count formatter.
     *
     * @param ContainerBuilder $container
     */
    protected function loadMoodleStepcountFormatter(ContainerBuilder $container) {
        $definition = new Definition('Moodle\BehatExtension\Output\Formatter\MoodleStepcountFormatter', array(
            'moodle_stepcount',
            'Count steps in feature files. Use with --dry-run',
            array('stepcount' => false),
            $this->createOutputPrinterDefinition()
        ));
        $definition->addTag(OutputExtension::FORMATTER_TAG, array('priority' => 101));
        $container->setDefinition(OutputExtension::FORMATTER_TAG . '.moodle_stepcount', $definition);
    }

    /**
     * Loads moodle screenshot formatter.
     *
     * @param ContainerBuilder $container
     */
    protected function loadMoodleScreenshotFormatter(ContainerBuilder $container) {
        $definition = new Definition('Moodle\BehatExtension\Output\Formatter\MoodleScreenshotFormatter', array(
            'moodle_screenshot',
            'Take screenshot of all steps. Use --format-settings \'{"formats": "html,image"}\' to get specific o/p type',
            array('formats' => 'html,image'),
            $this->createOutputPrinterDefinition()
        ));
        $definition->addTag(OutputExtension::FORMATTER_TAG, array('priority' => 102));
        $container->setDefinition(OutputExtension::FORMATTER_TAG . '.moodle_screenshot', $definition);
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
     * Loads skip passed controller.
     *
     * @param ContainerBuilder $container
     * @param null|string      $cachePath
     */
    protected function loadSkipPassedController(ContainerBuilder $container, $cachePath) {
        $definition = new Definition('Moodle\BehatExtension\Tester\Cli\SkipPassedController', array(
            new Reference(EventDispatcherExtension::DISPATCHER_ID),
            $cachePath,
            $container->getParameter('paths.base')
        ));
        $definition->addTag(CliExtension::CONTROLLER_TAG, array('priority' => 200));
        $container->setDefinition(CliExtension::CONTROLLER_TAG . '.passed', $definition);
    }

    /**
     * Loads filesystem passed scenarios list locator.
     *
     * @param ContainerBuilder $container
     */
    private function loadFilesystemSkipPassedScenariosListLocator(ContainerBuilder $container) {
        $definition = new Definition('Moodle\BehatExtension\Locator\FilesystemSkipPassedListLocator', array(
            new Reference(self::GHERKIN_ID)
        ));
        $definition->addTag(SpecificationExtension::LOCATOR_TAG, array('priority' => 50));
        $container->setDefinition(SpecificationExtension::LOCATOR_TAG . '.filesystem_skip_passed_scenarios_list', $definition);
    }

    /**
     * Loads definition printers.
     *
     * @param ContainerBuilder $container
     */
    private function loadDefinitionPrinters(ContainerBuilder $container) {
        $definition = new Definition('Moodle\BehatExtension\Definition\Printer\ConsoleDefinitionInformationPrinter', array(
            new Reference(CliExtension::OUTPUT_ID),
            new Reference(DefinitionExtension::PATTERN_TRANSFORMER_ID),
            new Reference(DefinitionExtension::DEFINITION_TRANSLATOR_ID),
            new Reference(GherkinExtension::KEYWORDS_ID)
        ));
        $container->removeDefinition('definition.information_printer');
        $container->setDefinition('definition.information_printer', $definition);

    }

    /**
     * Loads definition controller.
     *
     * @param ContainerBuilder $container
     */
    private function loadController(ContainerBuilder $container) {
        $definition = new Definition('Moodle\BehatExtension\Definition\Cli\AvailableDefinitionsController', array(
                new Reference(SuiteExtension::REGISTRY_ID),
                new Reference(DefinitionExtension::WRITER_ID),
                new Reference('definition.list_printer'),
            new Reference('definition.information_printer'))
        );
        $container->removeDefinition(CliExtension::CONTROLLER_TAG . '.available_definitions');
        $container->setDefinition(CliExtension::CONTROLLER_TAG . '.available_definitions', $definition);
    }

    /**
     * Loads chained step tester.
     *
     * @param ContainerBuilder $container
     */
    protected function loadChainedStepTester(ContainerBuilder $container) {
        // Chained steps.
        $definition = new Definition('Moodle\BehatExtension\EventDispatcher\Tester\ChainedStepTester', array(
            new Reference(TesterExtension::STEP_TESTER_ID),
        ));
        $definition->addTag(TesterExtension::STEP_TESTER_WRAPPER_TAG, array('priority' => 100));
        $container->setDefinition(TesterExtension::STEP_TESTER_WRAPPER_TAG . '.substep', $definition);
    }

    /**
     * Loads event-dispatching step tester.
     *
     * @param ContainerBuilder $container
     */
    protected function loadEventDispatchingStepTester(ContainerBuilder $container) {
        $definition = new Definition('Moodle\BehatExtension\EventDispatcher\Tester\MoodleEventDispatchingStepTester', array(
            new Reference(TesterExtension::STEP_TESTER_ID),
            new Reference(EventDispatcherExtension::DISPATCHER_ID)
        ));
        $definition->addTag(TesterExtension::STEP_TESTER_WRAPPER_TAG, array('priority' => -9999));
        $container->setDefinition(TesterExtension::STEP_TESTER_WRAPPER_TAG . '.event_dispatching', $definition);
    }

    /**
     * Setups configuration for current extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder) {
        $builder->
                children()->
                    arrayNode('capabilities')->
                    useAttributeAsKey('key')->
                    prototype('variable')->end()->
                end()->
                arrayNode('steps_definitions')->
                    useAttributeAsKey('key')->
                    prototype('variable')->end()->
                end()->
                scalarNode('moodledirroot')->
                    defaultNull()->
                    end()->
                scalarNode('passed_cache')->
                    info('Sets the passed cache path')->
                    defaultValue(
                        is_writable(sys_get_temp_dir())
                            ? sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'behat_passed_cache'
                            : null)->
                    end()->
                end()->
            end();
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigKey() {
        return self::MOODLE_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager) {
        if (null !== $minkExtension = $extensionManager->getExtension('mink')) {
            $minkExtension->registerDriverFactory(new WebDriverFactory());
        }
    }

    public function process(ContainerBuilder $container) {
        // Load controller for definition printing.
        $this->loadDefinitionPrinters($container);
        $this->loadController($container);
    }

    /**
     * Alias old namespace of given. when and then for BC.
     */
    private function alias_old_namespaces() {
        class_alias('Moodle\\BehatExtension\\Context\\Step\\Given', 'Behat\\Behat\\Context\\Step\\Given', true);
        class_alias('Moodle\\BehatExtension\\Context\\Step\\When', 'Behat\\Behat\\Context\\Step\\When', true);
        class_alias('Moodle\\BehatExtension\\Context\\Step\\Then', 'Behat\\Behat\\Context\\Step\\Then', true);
    }
}
