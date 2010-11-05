<?php

namespace Bundle\AkismetBundle;

use Zend\Service\Akismet\Akismet as BaseAkismet;

use Symfony\Component\HttpFoundation\Request;

class Akismet extends BaseAkismet
{

  public function __construct(Request $request, $key)
  {
    $url = 'http' . ($request->isSecure()?'s':'') . '://' . $request->getHost() . '/' . $request->getBaseUrl();
    parent::__construct($key, $url);
  }
}