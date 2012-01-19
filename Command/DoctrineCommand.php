<?php


use Symfony\Component\Console\Helper\HelperSet,
    Symfony\Component\Console\Helper\DialogHelper,
    Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper,
    Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;

/**
 * DoctrineCommand.
 *
 * This command adds the Doctrine 2 CLI to the list of options in the yiic tool. You must
 * configure protected/config/console.php with the same information as set out in your
 * main.php including the component and KodeFoundry alias.
 *
 * This command file should then live in protected/commands
 *
 * @package  KodeFoundry\Doctrine\Command
 * @author   Kevin Bradwick <kevin@kodefoundry.com>
 * @license  New BSD http://www.opensource.org/licenses/bsd-license.php
 * @version  Release: ##VERSION##
 */

class DoctrineCommand extends CConsoleCommand
{
    /**
     * The command runner
     *
     * @param array $args
     * @return null
     */
    public function run($args)
    {
        unset($_SERVER['argv'][1]);
        $cli = $this->getCli();

        $cli->add(new \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand());
        $cli->add(new \Doctrine\DBAL\Tools\Console\Command\ImportCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\RunDqlCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\InfoCommand());
        $cli->add(new \Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand());

        $cli->run();
    }

    /**
     * Get the Symfony command interface
     *
     * @return Symfony\component\Console\Application
     */
    protected function getCli()
    {
        $em = Yii::app()->doctrineEntityManager->getEntityManager();

        $helperSet = new HelperSet(
            array(
                'db'     => new ConnectionHelper($em->getConnection()),
                'em'     => new EntityManagerHelper($em),
                'dialog' => new DialogHelper(),
            )
        );

        $cli = new Symfony\Component\Console\Application(
            'Doctrine Command Line Interface',
            Doctrine\Common\Version::VERSION
        );

        $cli->setHelperSet($helperSet);
        $cli->setCatchExceptions(true);

        return $cli;
    }
}
