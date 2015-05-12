<?php
require('vendor/autoload.php');
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\AcceptHeader;

function main() {
  $request = Request::createFromGlobals();
  $info = array();
  $accept = AcceptHeader::fromString($request->headers->get('Accept'));
  if ($accept->has('text/html')) {
    $response = new Response(
      '<dl><dt>URL</dt><dd>' . $request->getPathInfo() . '</dd></dl>',
      Response::HTTP_OK,
      array('content-type' => 'text/html')
    );
  }
  else if ($accept->has('application/json')) {
    $response = new Response(
      json_encode(array('url' => $request->getPathInfo())),
      Response::HTTP_OK,
      array('content-type' => 'application/json'));
  }
  else {
    $response = new Response('Not allowed', 405);
  }
  $response->prepare($request);
  $response->send();

}
main();
