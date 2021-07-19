<?php
//Create new user
$user = new User();
$user->user = $current_user;

//////////////////////////////////////
//Update user
//////////////////////////////////////
if( isset( $_POST["update"])) {
  //Update Password if neccessary
  if(! empty($_POST["pw1"]) &&! empty($_POST["pw2"]) ) {
    if( $user->updatePassword($_POST["pw1"], $_POST["pw2"]) ) {
      Action::success( Language::string(0) );
    }else{
      Action::fail( Language::string(1) );
    }
  }

  //Update infos
  if($user->updateInfos($_POST["name"], $_POST["mail"], $_POST["language"])) {
    Action::success( Language::string(2) );
  }else{
    Action::fail( Language::string(3) );
  }
}

//////////////////////////////////////
// Start form
//////////////////////////////////////
$form = new HTML('form', array(
  'action' => $url_page,
  'method' => 'post',
  'additional' => 'style="max-width: 500px;"',
));

$form->customHTML('<h4>' . Language::string(10) . '</h4>');

// ID
$form->addElement(
  array(
    'type' => 'text',
    'name' => 'userID',
    'value' => $user->values()["id"],
    'placeholder' => Language::string(11),
    'disabled' => true,
  ),
);

// Name
$form->addElement(
  array(
    'type' => 'text',
    'name' => 'name',
    'value' => $user->values()["name"],
    'placeholder' => Language::string(12),
  ),
);

// Email
$form->addElement(
  array(
    'type' => 'text',
    'name' => 'mail',
    'value' => $user->values()["email"],
    'placeholder' => Language::string(13),
  ),
);

// Languages
$options = array();
foreach( Language::all() as $language ) {
  $options[$language["code"]] = $language["loc"] . ' (' . $language["int"] . ')';
}

$form->addElement(
  array(
    'type' => 'select',
    'name' => 'language',
    'value' => ($user->values()["language"] ?? null),
    'headline' => (isset($user->values()["language"]) ? $options[$user->values()["language"]] : Language::string(14)),
    'options' => $options
  ),
);
//////////////////////////////////////
// Update password
//////////////////////////////////////
$form->customHTML('<h4 style="Margin-top: 20px;">' . Language::string(15) . '</h4>');

$form->addElement(
  array(
    'type' => 'password',
    'name' => 'pw1',
    'placeholder' => Language::string(16),
  ),
);

$form->addElement(
  array(
    'type' => 'password',
    'name' => 'pw2',
    'placeholder' => Language::string(17),
  ),
);

$form->addElement(
  array(
    'type' => 'button',
    'name' => 'update',
    'value' => Language::string(18),
  ),
);

// Show form
$form->prompt();
 ?>
