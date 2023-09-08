<?php

use GuzzleHttp\Exception\BadResponseException;
use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Credentials\CredentialsInterface;
use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Credentials\TemporaryCredentials;
use League\OAuth1\Client\Credentials\CredentialsException;
use League\OAuth1\Client\Server\User;

class Garmin extends Server
{

  protected $test = false;

  public function test($bool = true) {
    $this->test = $bool;
  }

  /**
   * {@inheritDoc}
   */
  public function urlTemporaryCredentials()
  {
    $test = $this->test ? 'test' : '';
    return "https://connectapi{$test}.garmin.com/oauth-service-1.0/oauth/request_token";
  }

  /**
   * {@inheritDoc}
   */
  public function urlAuthorization()
  {
    $test = $this->test ? 'test' : '';
    return "https://connect{$test}.garmin.com/oauthConfirm";
  }

  /**
   * {@inheritDoc}
   */
  public function urlTokenCredentials()
  {
    $test = $this->test ? 'test' : '';
    return "https://connectapi{$test}.garmin.com/oauth-service-1.0/oauth/access_token";
  }

  /**
   * {@inheritDoc}
   */
  public function urlUserDetails()
  {
    throw new \Exception("Garmin does not have a userDetails endpoint currently. Sorry...");
  }

  /**
   * {@inheritDoc}
   */
  public function userDetails($data, TokenCredentials $tokenCredentials)
  {
    $user = new User;
    return $user;
  }

  /**
   * {@inheritDoc}
   */
  public function userUid($data, TokenCredentials $tokenCredentials)
  {
    return;
  }

  /**
   * {@inheritDoc}
   */
  public function userEmail($data, TokenCredentials $tokenCredentials)
  {
    return;
  }

  /**
   * {@inheritDoc}
   */
  public function userScreenName($data, TokenCredentials $tokenCredentials)
  {
    return;
  }

  /**
   * Garmin doesn't have any public endpoints, so let's make sure we don't try this at home, kids
   */
  protected function fetchUserDetails(TokenCredentials $tokenCredentials, $force = true) {
    return [];
  }

  /**
   * Generate the OAuth protocol header for requests other than temporary
   * credentials, based on the URI, method, given credentials & body query
   * string.
   *
   * @param  string  $method
   * @param  string  $uri
   * @param  CredentialsInterface  $credentials
   * @param  array  $bodyCredentials
   * @return string
   */
  protected function protocolHeader($method, $uri, CredentialsInterface $credentials, array $bodyParameters = array())
  {
    $parameters = array_merge($this->baseProtocolParameters(), array(
      'oauth_token' => $credentials->getIdentifier(),
    ));

    $this->signature->setCredentials($credentials);

    $parameters['oauth_signature'] = $this->signature->sign($uri, array_merge($parameters, $bodyParameters), $method);
    if ( isset($bodyParameters['oauth_verifier']) ) {
     $parameters['oauth_verifier'] =  $bodyParameters['oauth_verifier'];
    }

    return $this->normalizeProtocolParameters($parameters);
  }

  /**
   * Handle a bad response coming back when getting token credentials.
   *
   * @param  BadResponseException
   * @return void
   * @throws CredentialsException
   */
  protected function handleTokenCredentialsBadResponse(BadResponseException $e)
  {
      $response = $e->getResponse();
      $body = $response->getBody();
      $statusCode = $response->getStatusCode();

      $body->uncompress();

      throw new CredentialsException("Received HTTP status code [$statusCode] with message \"$body\" when getting token credentials.");
  }

  /**
   * Get the authorization URL by passing in the temporary credentials
   * identifier or an object instance.
   *
   * @param  TemporaryCredentials|string  $temporaryIdentifier
   * @return string
   */
  public function getAuthorizationUrl($temporaryIdentifier, array $options = [])
  {
    // Somebody can pass through an instance of temporary
    // credentials and we'll extract the identifier from there.
    if ($temporaryIdentifier instanceof TemporaryCredentials) {
      $temporaryIdentifier = $temporaryIdentifier->getIdentifier();
    }

    $parameters = array(
      'oauth_token' => $temporaryIdentifier,
      'oauth_callback' => $this->clientCredentials->getCallbackUri(),
    );

    return $this->urlAuthorization().'?'.http_build_query($parameters);
  }
}