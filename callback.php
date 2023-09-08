<?php
  require __DIR__.'/vendor/autoload.php';

  require 'Garmin.php';
  require_once('config.php');

  try
  {
    session_start();

    $server = new Garmin([
        'identifier' => CLIENT_ID,
        'secret' => CLIENT_SECRET,
        'callback_uri' => REDIRECT_URI,
    ]);

    if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {
        // Retrieve the temporary credentials we saved before
        $temporaryCredentials = unserialize($_SESSION['temporary_credentials']);
    
        // We will now retrieve token credentials from the server
        $tokenCredentials = $server->getTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);
        $token = $tokenCredentials->getIdentifier();
        $tokenSecret = $tokenCredentials->getSecret();
        
        header('Location: flutter_callback.php'.'?'.'token='.$token.'&'.'tokenSecret='.$tokenSecret);
        exit;
    }
  }
  catch (Throwable $th)
  {
	//echo $th->getMessage();
  }

  header('Location: flutter_callback.php');
?>
