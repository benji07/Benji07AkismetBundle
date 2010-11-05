<?php

namespace Bundle\AkismetBundle;

use Symfony\Component\HttpFoundation\Request;

class Akismet
{

  protected $username;
  protected $key;
  private $host = 'rest.akismet.com';
  private $verify_key_url = '/1.1/verify-key';
  private $comment_check_url = '/1.1/comment-check';
  private $submit_spam_url = '/1.1/submit-spam';
  private $submit_ham_url = '/1.1/submit-ham';
  private $base_host = '';
  private $user_agent = 'Symfony/2 | Akismet/1.1';

  public function __construct(Request $request, $username, $key)
  {
    $this->username = $username;
    $this->key = $key;
    $this->base_host = 'http' . ($request->isSecure()?'s':'') . '://' . $request->getHost() . '/' . $request->getBaseUrl();
  }

  /**
   * Submit the comment as a spam
   * @param $comment
   * @see Akismet::isSpam
   */
  public function reportAsSpam(array $comment = array())
  {
    if (!$this->verifyKey())
    {
      throw new \Exception('Invalid Api key');
    }

    $params = array('key' => $this->key, 'blog' => $this->base_host) + $comment;

    $response = $this->post($params, $this->key . '.' . $this->host, $this->submit_spam_url, 80);
  }

  /**
   * Report the comment as a ham
   * @param $comment
   * @see Akismet::isSpam
   */
  public function notASpam(array $comment = array())
  {
    if (!$this->verifyKey())
    {
      throw new \Exception('Invalid Api key');
    }

    $params = array('key' => $this->key, 'blog' => $this->base_host) + $comment;

    $response = $this->post($params, $this->key . '.' . $this->host, $this->submit_ham_url, 80);
  }

  /**
   * Check if the comment is a spam or not
   *
   * @param $comment array the comment to check
   * <ul>
   *  <li>blog: (required) The front page or home URL of the instance making the request.
   *  For a blog or wiki this would be the front page. Note: Must be a full URI, including http://.</li>
   *  <li>user_ip: (required) IP address of the comment submitter.</li>
   *  <li>referrer: (note spelling) The content of the HTTP_REFERER header should be sent here.</li>
   *  <li>permalink: The permanent location of the entry the comment was submitted to.</li>
   *  <li>comment_type: May be blank, comment, trackback, pingback, or a made up value like "registration".</li>
   *  <li>comment_author: Name submitted with the comment</li>
   *  <li>comment_author_email: Email address submitted with the comment</li>
   *  <li>comment_author_url: URL submitted with comment</li>
   *  <li>comment_content: The content that was submitted.</li>
   * </ul>
   * @return boolean
   */
  public function isSpam(array $comment = array())
  {
    if (!$this->verifyKey())
    {
      throw new \Exception('Invalid Api key');
    }

    $params = array('key' => $this->key, 'blog' => $this->base_host) + $comment;

    $response = $this->post($params, $this->key . '.' . $this->host, $this->comment_check_url, 80);

    if ($response == 'false')
    {
      return false;
    }
    else if ($response == 'true')
    {
      return true;
    }
    else
    {
      throw new \Exception($response);
    }
  }

  /**
   * Check if the api key is valid
   * @return boolean
   */
  public function verifyKey()
  {
    $params = array(
        'key' => $this->key,
        'blog' => $this->base_host,
    );

    $response = $this->post($params, $this->host, $this->verify_key_url, 80);

    return $response == 'valid';
  }

  /**
   * Send a request to akismet rest api
   * @param array|string $request
   * @param string $host
   * @param string $path
   * @param string $port
   * @return string response send by akismet without header
   */
  public function post($request, $host, $path, $port = 80)
  {
    $rq = $request;
    if (is_array($request))
    {
      $rq = \http_build_query($request);
    }

    $http = 'POST ' . $path . ' HTTP/1.0' . "\r\n";
    $http .= 'Host: ' . $host . "\r\n";
    $http .= 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8' . "\r\n";
    $http .= 'Content-Length: ' . strlen($rq) . "\r\n";
    $http .= 'User-Agent: ' . $this->user_agent . "\r\n";
    $http .= "\r\n";
    $http .= $rq;

    $fs = \fsockopen($host, $port, $errno, $errstr, 3);

    $response = '';
    if ($fs !== false)
    {
      fwrite($fs, $http);

      while (!feof($fs))
      {
        $response .= fgets($fs, 1160); // One TCP-IP packet
      }
      fclose($fs);

      $response = explode("\r\n\r\n", $response, 2);
    }
    return $response[1];
  }

}