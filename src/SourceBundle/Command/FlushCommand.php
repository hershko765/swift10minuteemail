<?php

namespace SourceBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use SourceBundle\Base\Process;

class FlushCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('entity:flush')
			->setDescription('update doctrine entity, update migrations, run migrations')
			->addOption('migrate', NULL, InputOption::VALUE_NONE, 'Run Migrations')
			->addArgument('entity', InputOption::VALUE_NONE, 'entity description in the format: Bundle:Entity')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$runMigrations = $input->getOption('migrate');
		list($bundle, $entity) = explode(':', $input->getArgument('entity'));

		$updateDoctrine = new Process("php app/console doctrine:generate:entities App$bundle:Model/$entity");
		$updateMigrations = new Process("php app/console doctrine:migrations:diff");

		$updateDoctrine->runOutput($output);

		$updateMigrations->runOutput($output);
		if ($runMigrations)
		{
			$runMigration = new Process("php app/console doctrine:migrations:migrate --no-interaction");
			$runMigration->runOutput($output);
		}
	}
}