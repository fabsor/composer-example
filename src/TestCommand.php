<?php
namespace Fabsor;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;

class TestCommand extends Command {
  protected function configure() {
    $this->setName('test-service')
      ->setDescription('Test a recipe web service')
      ->addArgument('url', InputArgument::REQUIRED, 'The url to the web service');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $url = $input->getArgument('url');
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
      throw new Exception('Enter a valid base URL');
    }
    $client = new Client();
    $output->writeln('<info>Testing getting recipes...</info>');
    $this->testGet($client, $url . '/recipes');
    $output->writeln('<info>Those were some nice recipes! Lets add one...</info>');
    $this->testPost($client, $url . '/recipes');

    $output->writeln('<info>Yay, you made it!</info>');
  }

  protected function testGet($client, $url) {
    $res = $client->get($url);
    $this->assert(
      $res->getStatusCode() == 200,
      'The web service didn\'t return the right response code, should be 200. got ' . $res->getStatusCode());
    $this->assert(
      strpos($res->getHeader('content-type'), 'application/json') !== false,
      'The web service didn\'t tell me it returned json');
    $data = $res->json();

    $this->assert(is_array($data), 'The data returned is not an array');
    $this->assert(count($data) == 3, 'There should be three recipes here.');
    $this->assert($data[0]['title'] == 'Delicious pancackes', 'The first recipe is not delicious pancakes :(');
    $this->assert($data[1]['title'] == 'Epic strawberry cake', 'The second recipe is not an epic strawberry cake =/');
    $this->assert($data[2]['title'] == 'Majestic cinnamon buns', 'The third recipe doesnt seem to include cinnamon.');
  }

  protected function testPost($client, $url) {
    $body = array(
      'title' => 'Brownies',
      'description' => 'Bake them however you like'
    );
    $body['title'] .= rand(0, 5000);
    $res = $client->post($url, array('json' => $body));
    $this->assert(
      $res->getStatusCode() == 201,
      'The web service didn\'t return the right response code, when posting, should be 201');
    $this->assert(
      strpos($res->getHeader('content-type'), 'application/json') !== false,
      'The web service didn\'t tell me it returned json');
    $data = $res->json();
    $this->assert(is_array($data), 'The data returned is not an array');
    $this->assert($data['title'] == $body['title'], 'The title of the recipe is wrong');
    $this->assert($data['description'] == $body['description'], 'The description of the recipe is wrong.');
  }

  protected function assert($condition, $msg) {
    if (!$condition) {
      throw new \Exception($msg);
    }
  }
}