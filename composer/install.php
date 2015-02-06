<?php
var_dump($package->getName());die();

parent::install($repo, $package);

if($package->getType() !== 'asgard-bundle')
	return;

if(file_exists('installed.txt')) {
	$installed = explode("\n", file_get_contents(__DIR__.'/installed.txt'));
	if(in_array($package->getName(), $installed))
		return;
}
else
	$installed = [];

$root = dirname(__DIR__);

if(!file_exists($root.'/vendor/autoload.php'))
	return;

#Add Bundle to Kernel.php
require_once $root.'/autoload.php';

$path = $this->getInstallPath($package);

$kernelPath = $root.'/app/Kernel.php';
$kernelCode = file_get_contents($kernelPath);

$class = '';
foreach(explode('/', $package->getPrettyName()) as $piece)
	$class .= '\\'.ucfirst($piece);
$class .= '\\Bundle';

if(file_exists($path.'\\Bundle.php') && $class = \Asgard\Common\Tools::loadClassFile($path.'\\Bundle.php'))
	$line = "class_exists('$class') ? new $class:null,";
else
	$line = "'$path',";

$kernelCode = preg_replace('/(#Composer Bundles.*?)/', $line."\n\t\t\t\t".'\1', $kernelCode, -1, $count);

if($count && file_put_contents($kernelPath, $kernelCode))
	echo 'Bundle added to app/Kernel.php'."\n";
else
	echo 'Bundle could not be added to app/Kernel.php'."\n";

#Publish files
$cmd = 'php console publish "'.$path.'" --config --web --migrations --migrate';
exec($cmd, $output, $returnVar);
if($returnVar !== 0)
	echo 'Could not publish the bundle. Please try manually using: '.$cmd."\n";

$installed[] = $package->getName();
file_put_contents(__DIR__.'/installed.txt', implode("\n", $installed));