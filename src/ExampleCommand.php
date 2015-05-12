<?php
namespace Fabsor;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;


class ExampleCommand extends Command {
  protected function configure() {
    $this->setName('ls')
      ->setDescription('List files in the directory')
      ->addArgument('dir', InputArgument::OPTIONAL, 'The directory');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $dir = $input->getArgument('dir');
    $dir = $dir ? $dir : getcwd();
    $finder = new Finder();
    $iterator = $finder
              ->files()
              ->depth(0)
              ->in($dir);
    foreach ($iterator as $file) {
      print basename($file->getRealPath()) . "\n";
    }
  }
}