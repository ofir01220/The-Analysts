<?php
  require __DIR__.'/vendor/autoload.php';

  require 'Garmin.php';
  require_once('config.php');
  
  try
  {   
    $server = new Garmin([
        'identifier' => CLIENT_ID,
        'secret' => CLIENT_SECRET,
        'callback_uri' => REDIRECT_URI,
    ]);
    
    // Retrieve temporary credentials
    $temporaryCredentials = $server->getTemporaryCredentials();
    
    // Store credentials in the session, we'll need them later
    session_start();
    $_SESSION['temporary_credentials'] = serialize($temporaryCredentials);
    //$_SESSION['temporary_credentials'] = $temporaryCredentials;
    session_write_close();

    // Second part of OAuth 1.0 authentication is to redirect the
    // resource owner to the login screen on the server.
    $server->authorize($temporaryCredentials);
  }
  catch (Throwable $th)
  {
    echo $th;
  }
  finally
  {
  }
?>
