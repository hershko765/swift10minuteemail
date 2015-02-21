<?php

namespace SourceBundle\Helpers;

use Symfony\Component\Config\Definition\Exception\Exception;

class Dir {

	public static function Src($path = NULL)
	{
		$src = is_dir(realpath('./src/')) ? realpath('./src/') : realpath('../src/');
		if ( ! is_dir($src))
			throw new Exception('src path not found');

		return $path ? $src.'/'.$path : $src.'/';
	}

	public static function Web()
	{
		$web = realpath('./');
		if ( ! is_dir($web))
			throw new Exception('web path not found');

		return $web;
	}

	public static function Resources($bundle)
	{
		$resources = is_dir(realpath('./src/App/'.ucwords($bundle).'Bundle/Resources'))
			? realpath('./src/App/'.ucwords($bundle).'Bundle/Resources')
			: realpath('../src/App/'.ucwords($bundle).'Bundle/Resources');
		if ( ! is_dir($resources))
			throw new Exception('resources path not found');

		return $resources.'/';
	}
}