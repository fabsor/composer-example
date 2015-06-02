<?php
require 'vendor/autoload.php';
use Philo\Blade\Blade;

$articles = [
  [
    'title' => 'Title',
    'description' => 'A description'
  ],
  [
    'description' => 'only a description'
  ],
  [
    'title' => 'a title'
  ]
];

function blade_conditionals($articles) {

  $views = __DIR__ . '/views';
  $cache = __DIR__ . '/cache';

  $blade = new Blade($views, $cache);

  echo $blade->view()->make('logic')
    ->with(['articles' => $articles])
    ->render();
}



function twig_blocks($articles) {
  $loader = new Twig_Loader_Filesystem(__DIR__ . '/views');
  $twig = new Twig_Environment($loader);
  $template = $twig->loadTemplate('article.html.twig');
  echo $template->render(['article' => $articles[0]]);
}



function mustache_partials($articles) {
  $m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/views'),
  ));
  echo $m->render('articles', array('articles' => $articles));
}


switch ($_GET['engine']) {
  case 'twig':
    twig_blocks($articles);
  case 'blade':
    blade_conditionals($articles);
    break;
 case 'mustache':
    mustache_partials($articles);
    break;
}
