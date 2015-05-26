<?php
namespace Fabsor;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;

class OrmCommand extends Command {
  protected function configure() {
    $this->setName('test-orm')
      ->setDescription('Test a recipe web service')
      ->addArgument('url', InputArgument::REQUIRED, 'The url to the web service');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $url = $input->getArgument('url');
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
      throw new Exception('Enter a valid base URL');
    }
    $client = new Client();
    $output->writeln('<info>Testing posting recipes...</info>');
    $data = $this->testPost($client, $url . '/recipes');
    $this->testGet($client, $url . '/recipes', $data['id']);
    $output->writeln('<info>I Found my recipe!</info>');
    $output->writeln('<info>Lets improve the recipe</info>');
    $this->testPut($client, $url . '/recipes', $data);
    $output->writeln('<info>Yay, you made it!</info>');
  }

  protected function testGet($client, $url, $id) {
    $res = $client->get($url, array('headers' => array('Accept' => 'application/json')));
    $this->assert(
      $res->getStatusCode() == 200,
      'The web service didn\'t return the right response code, should be 200. got ' . $res->getStatusCode());
    $this->assert(
      strpos($res->getHeader('content-type'), 'application/json') !== false,
      'The web service didn\'t tell me it returned json');
    $data = $res->json();

    $this->assert(is_array($data), 'The data returned is not an array');
    $this->assert(count($data) > 0, 'There should be at least one recipe here');
    $this->assert(!empty($this->findRecipe($data, $id)), 'The recipe you created does not exist');
    $res = $client->get($url . '/' . $id, array('headers' => array('Accept' => 'application/json')));
    $data = $res->json();
    $this->assert($data['id'] == $id, 'Could not retreive single recipe');
  }

  protected function findRecipe($recipes, $id) {
    return array_filter($recipes, function ($item) use ($id) {
        return $item['id'] == $id;
      }
    );
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
    $this->assert(!empty($data['id']), 'There should be an id present');
    return $data;
  }

  protected function testPut($client, $url, $data) {
    $body = array(
      'title' => 'Brownies',
      'description' => 'Bake them like I tell you to.'
    );
    $body['title'] .= rand(0, 5000);
    $res = $client->put($url . '/' . $data['id'], array('json' => $body));
    $this->assert(
      $res->getStatusCode() == 200,
      'The web service didn\'t return the right response code, when posting, should be 200');
    $this->assert(
      strpos($res->getHeader('content-type'), 'application/json') !== false,
      'The web service didn\'t tell me it returned json');
    $data = $res->json();
    $this->assert(is_array($data), 'The data returned is not an array');
    $this->assert($data['title'] == $body['title'], 'The title of the recipe is wrong');
    $this->assert($data['description'] == $body['description'], 'The description of the recipe is wrong.');
    $this->assert(!empty($data['id']), 'There should be an id present');
    return $data;
  }


  protected function assert($condition, $msg) {
    if (!$condition) {
      throw new \Exception($msg);
    }
  }
}