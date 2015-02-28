<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

class CleanCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('clean:visitors')
			->setDescription('remove visitors that past there time');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

        $this->getContainer()->enterScope('request');
        $this->getContainer()->set('request', new Request(), 'request');
        $gateway = $this->getContainer()->get('handler_gateway');

        $gateway->getHandler('Visitor', 'Clean', 'App')->execute();
    }
}