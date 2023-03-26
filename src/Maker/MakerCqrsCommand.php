<?php

declare(strict_types=1);

namespace App\Maker;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Column;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

final class MakerCqrsCommand extends AbstractMaker
{
    public function __construct(public readonly ContainerBagInterface $containerBag)
    {
    }

    public static function getCommandName(): string
    {
        return 'make:cqrs-command';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new console command class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->addArgument(
                'CommandFolder',
                InputArgument::REQUIRED,
                'The directory of your command (e.g. <fg=yellow>Article/AddArticle</>)'
            )
            ->addArgument(
                'CommandClassName',
                InputArgument::REQUIRED,
                'The name of the command class without "Command" suffix (e.g. <fg=yellow>AddArticle</>)'
            )
            ->addArgument(
                'EventClassName',
                InputArgument::REQUIRED,
                'The name of the event class (e.g. <fg=yellow>ArticleAdded</>)'
            );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $applicationDirectoryNameSpace = $this->containerBag->get('kernel.project_dir') . '/src/Application/Command/' . $input->getArgument('CommandFolder');
        $eventDirectoryNameSpace = $this->containerBag->get('kernel.project_dir') . '/src/Domain/Event';

        if (false === is_dir($applicationDirectoryNameSpace) && false === is_dir($eventDirectoryNameSpace)) {
            mkdir($applicationDirectoryNameSpace, recursive: true);
            mkdir($eventDirectoryNameSpace, recursive: true);
        }

        $eventClassNameInput = $input->getArgument('EventClassName');
        $commandClassNameInput = $input->getArgument('CommandClassName');

        $commandFolderPath = str_replace('/', '\\', $input->getArgument('CommandFolder'));

        $commandClassName = $generator->createClassNameDetails(
            $commandClassNameInput,
            'Application\\Command\\'. $commandFolderPath . '\\',
            'Command'
        );

        $commandHandler = $generator->createClassNameDetails(
            $commandClassName->getShortName(),
            'Application\\Command\\'. $commandFolderPath . '\\',
            'Handler'
        );

        $event = $generator->createClassNameDetails(
            $eventClassNameInput,
            'Domain\\Event\\'
        );

        $generator->generateClass(
            $commandClassName->getFullName(),
            __DIR__ . '/Resources/skeleton/Command.tpl.php',
            [
                'attributes' => [
                    'Ulid' => 'test'
                ]
            ]
        );

        $generator->generateClass(
            $commandHandler->getFullName(),
            __DIR__ . '/Resources/skeleton/CommandHandler.tpl.php',
            [
                'commandClassName' => $commandClassName->getShortName(),
                'commandPath' => $commandClassName->getFullName(),
            ]
        );

       $generator->generateClass(
            $event->getFullName(),
            __DIR__ . '/Resources/skeleton/Command.tpl.php',
           [
               'attributes' => [
                   'Ulid' => 'test'
               ]
           ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: Open your new ' . $commandHandler->getShortName(). ' class and add your logic.',
        ]);
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }
}