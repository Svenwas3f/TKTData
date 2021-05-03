<?php
//Create new user
$user = new User();
$user->user = $current_user;

//Update user
if( isset( $_POST["update"])) {
  //Update Password if neccessary
  if(! empty($_POST["pw1"]) &&! empty($_POST["pw2"]) ) {
    if( $user->updatePassword($_POST["pw1"], $_POST["pw2"]) ) {
      Action::success("Das Passwort wurde <strong>erfolgreich</strong> geändert.");
    }else{
      Action::fail("Das Passwort konnte <strong>nicht</strong> geändert werden");
    }
  }

  //Update infos
  if($user->updateInfos($_POST["name"], $_POST["mail"])) {
    Action::success("Ihre Änderung wurde <strong>erfolgreich</strong> durchgeführt.");
  }else{
    Action::fail("Ihre Änderung konnte <strong>nicht</strong> durchgeführt werden");
  }
}

//Start form to edit, show user
echo '<form action="" method="post" style="max-width: 500px;">';
/**
 * Read user info
 */
echo '<h4>Benutzerdaten</h4>';
//ID
echo '<label class="txt-input">';
  echo '<input type="text" value="' . $user->values()["id"] . '" name="userID" disabled/>'; //Disable username 
  echo '<span class="placeholder">Benutzername</span>';
echo '</label>';

//Name
echo '<label class="txt-input">';
  echo '<input type="text" value="' . $user->values()["name"] . '" name="name"/>';
  echo '<span class="placeholder">Name</span>';
echo '</label>';

//E-Mail
echo '<label class="txt-input">';
  echo '<input type="email" value="' . $user->values()["email"] . '" name="mail" required/>';
  echo '<span class="placeholder">E-Mail</span>';
echo '</label>';

/**
 * Reset password
 */
 echo '<h4 style="Margin-top: 20px;">Passwort ändern</h4>';
 echo '<label class="txt-input">';
   echo '<input type="password" name="pw1"/>';
   echo '<span class="placeholder">Neues Passwort</span>';
 echo '</label>';

 echo '<label class="txt-input">';
   echo '<input type="password" name="pw2"/>';
   echo '<span class="placeholder">Passwort bestätigen</span>';
 echo '</label>';

//Add submit button
echo '<input type="submit" name="update" value="UPDATE" title="Benutzer aktualisieren"/>';

//Close form
echo '</form>';
 ?>
