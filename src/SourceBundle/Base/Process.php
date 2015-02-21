<?php

namespace SourceBundle\Base;

use Symfony\Component\Process\Process as SymProcess;

class Process extends SymProcess {

	public function runOutput($output)
	{
		parent::run(function ($type, $buffer) use($output) {
			Process::ERR === $type
				? $output->writeln('ERROR:'.$buffer)
				: $output->writeln($buffer);
		});
	}
}

