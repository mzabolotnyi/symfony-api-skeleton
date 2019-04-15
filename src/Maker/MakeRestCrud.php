<?php

namespace App\Maker;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Common\Inflector\Inflector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Validator\Validation;

/**
 * Class MakeRestCrud
 * @package App\Maker
 */
class MakeRestCrud extends AbstractMaker
{
    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;
    /**
     * @var ParameterBag
     */
    private $bag;

    /**
     * MakeRestCrud constructor.
     * @param DoctrineHelper $doctrineHelper
     * @param ParameterBag $bag
     */
    public function __construct(DoctrineHelper $doctrineHelper, ParameterBagInterface $bag)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->bag = $bag;
    }


    /**
     * @return string
     */
    public static function getCommandName(): string
    {
        return 'make:rest-crud';
    }


    /**
     * @param Command $command
     * @param InputConfiguration $inputConfig
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates RESTFULL CRUD for Doctrine entity class')
            ->addArgument(
                'entity-class',
                InputArgument::OPTIONAL,
                sprintf(
                    'The class name of the entity to create CRUD (e.g. <fg=yellow>%s</>)',
                    Str::asClassName(Str::getRandomTerm())
                )
            )
            ->setHelp(
                file_get_contents(
                    $this->bag->get('kernel.project_dir') . '/vendor/symfony/maker-bundle/src/Resources/help/MakeCrud.txt'
                )
            );
        $inputConfig->setArgumentAsNonInteractive('entity-class');
    }

    /**
     * @param InputInterface $input
     * @param ConsoleStyle $io
     * @param Command $command
     */
    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if (null === $input->getArgument('entity-class')) {
            $argument = $command->getDefinition()->getArgument('entity-class');
            $entities = $this->doctrineHelper->getEntitiesForAutocomplete();
            $question = new Question($argument->getDescription());
            $question->setAutocompleterValues($entities);
            $value = $io->askQuestion($question);
            $input->setArgument('entity-class', $value);
        }
    }

    /**
     * @param DependencyBuilder $dependencies
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {

        $dependencies->addClassDependency(
            Route::class,
            'router'
        );
        $dependencies->addClassDependency(
            AbstractType::class,
            'form'
        );
        $dependencies->addClassDependency(
            Validation::class,
            'validator'
        );
        $dependencies->addClassDependency(
            TwigBundle::class,
            'twig-bundle'
        );
        $dependencies->addClassDependency(
            DoctrineBundle::class,
            'orm-pack'
        );
        $dependencies->addClassDependency(
            CsrfTokenManager::class,
            'security-csrf'
        );
        $dependencies->addClassDependency(
            ParamConverter::class,
            'annotations'
        );
    }


    /**
     * @param InputInterface $input
     * @param ConsoleStyle $io
     * @param Generator $generator
     * @throws \Exception
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists(
                $input->getArgument('entity-class'),
                $this->doctrineHelper->getEntitiesForAutocomplete()
            ),
            'Entity\\'
        );

        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassDetails->getFullName());
        $repositoryVars = [];

        if (null !== $entityDoctrineDetails->getRepositoryClass()) {
            $repositoryClassDetails = $generator->createClassNameDetails(
                '\\' . $entityDoctrineDetails->getRepositoryClass(),
                'Repository\\',
                'Repository'
            );
            $repositoryVars = [
                'repository_full_class_name' => $repositoryClassDetails->getFullName(),
                'repository_class_name' => $repositoryClassDetails->getShortName(),
            ]
            ;
        }
        $controllerClassDetails = $generator->createClassNameDetails(
            $entityClassDetails->getRelativeNameWithoutSuffix(),
            'Controller\\',
            'Controller'
        );

        $iter = 0;
        do {
            $formClassDetails = $generator->createClassNameDetails(
                $entityClassDetails->getRelativeNameWithoutSuffix() . ($iter ?: ''),
                'Form\\',
                'Type'
            );
            ++$iter;
        } while (class_exists($formClassDetails->getFullName()));

        $entityVarPlural = lcfirst(Inflector::pluralize($entityClassDetails->getShortName()));
        $entityVarSingular = lcfirst(Inflector::singularize($entityClassDetails->getShortName()));
        $nameConverter = new CamelCaseToSnakeCaseNameConverter();
        $routePath = str_replace(
            '_',
            '-',
            $nameConverter->normalize($entityClassDetails->getShortName())
        );

        $generator->generateController(
            $controllerClassDetails->getFullName(),
            $this->bag->get('kernel.project_dir') . '/src/Maker/Tpl/Controller.tpl.php',
            array_merge(
                [
                    'entity_full_class_name' => $entityClassDetails->getFullName(),
                    'entity_class_name' => $entityClassDetails->getShortName(),
                    'form_full_class_name' => $formClassDetails->getFullName(),
                    'form_class_name' => $formClassDetails->getShortName(),
                    'route_path' => $routePath,
                    'entity_var_plural' => $entityVarPlural,
                    'entity_var_singular' => $entityVarSingular,
                ],
                $repositoryVars
            )
        );

        $generator->generateClass(
            $formClassDetails->getFullName(),
            $this->bag->get('kernel.project_dir') . '/src/Maker/Tpl/Type.tpl.php',
            [
                'bounded_full_class_name' => $entityClassDetails->getFullName(),
                'bounded_class_name' => $entityClassDetails->getShortName(),
                'form_fields' => $entityDoctrineDetails->getFormFields(),
            ]
        );

        $generator->writeChanges();
        $this->writeSuccessMessage($io);
        $io->text(
            sprintf(
                'Next: Check your new CRUD by going to <fg=yellow>%s/</>',
                $routePath
            )
        );
    }
}