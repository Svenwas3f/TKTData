<?php
//Require general file
require_once(dirname(__FILE__, 3) . "/general.php");

 ?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>TKTDATA - FAQ</title>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="cache-control" content="no-cache">

    <meta name="author" content="Sven Waser">
    <meta name="publisher" content="Sven Waser">
    <meta name="copyright" content="Sven Waser">
    <meta name="reply-to" content="sven.waser@sven-waser.ch">

    <meta name="description" content="Wilkommen auf dem TKTData Store. Kaufen Sie sich hier ein Ticket für den nächsten Event">
    <meta name="keywords" content="TKTData, TKTData Store, Store">

    <meta name="content-language" content="de">
    <meta name="robots" content="noindex">

    <meta name="theme-color" content="#232b43">


    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $url; ?>medias/logo/favicon.ico">
    <link rel="shortcut icon" href="<?php echo $url; ?>medias/logo/favicon.ico">
    <link rel="icon" type="image/png" href="<?php echo $url; ?>medias/logo/favicon.png" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $url; ?>medias/logo/logo-512.png">
    <link rel="apple-touch-icon-precomposed" sizes="180x180" href="<?php echo $url; ?>medias/logo/logo-512.png">
    <meta name="msapplication-TileColor" content="#232b43">
    <meta name="msapplication-TileImage" content="<?php echo $url; ?>medias/logo/logo-512.png">

    <!-- Custom scripts -->
    <link rel="stylesheet" href="<?php echo $url; ?>store/style.css" />
    <link rel="stylesheet" href="<?php echo $url; ?>fonts/fonts.css" />

    <script src="<?php echo $url; ?>store/main.js"></script>
    <script>
    //Check hashtag
    window.onload = function() {
      if(window.location.hash) {
        var hash = window.location.hash.replace("#", "");
        var element = document.getElementById(hash);
        element.children[0].click();
      }
    }
    </script>
  </head>
  <body>
    <article>
      <header>
        <div class="banner" style="background-image: url('<?php echo $url; ?>medias/store/banner.jpg')">
          <a href="<?php echo $url; ?>store/"><img class="logo" src="<?php echo $url; ?>medias/store/logo-fitted.png"></a>
        </div>
      </header>


      <div class="accordion">
        <div class="element" id="payment-procedure">
          <div class="headline" onclick="accordion(0)">
            Wie kaufe ich ein Ticket
            <span class="toggler"></span>
          </div>
          <div class="content">
            <div class="text">
              Der Ticketkauf ist ganz einfach und kann von dir problemlos durchgeführ werden. Besuche dazu <a href="<?php echo $url; ?>store/" target="_blank"><?php echo $url; ?>store/</a> und wähle Dein Ticket aus. Du wirst auf eine neue Seite weitergeleitet wo du Deine Kontaktangaben machen must. Deine E-Mail wird zwingend benötigt, damit wir Dir dein Ticket zustellen können. Die restlichen Angaben können entweder zwingend oder freiwillig sein, dies kommt auf das Ticket an. Wenn du einen Coupon besitzt kannst du diesen einlösen, indem du auf &quot;Coupon einlösen&quot; klickst. Nach dem ausfüllen des Feldes wird Dir entsprechen der Ticketpreis angepasst. Bitte klicke dich aus dem Eingabefeld um die Änderung zu sehen. Danach kannst du auf &quot;Bezahlen&quot; klicken.<br />
              Nun die hälfte ist bereits erledigt. Warte einen Augenblick, bis das Zahlfenster geladen hat. Wähle nun Deine bevorzugte Zahlungsmethode aus und folge den Anweisungen des Zahlungsfenster. Nach der erfolgreichen Zahlung, warte kurz bis du auf eine neue Seite weitergeleitet wirts.<br />
              Du hast nun erfolgreich dein Ticket bestellt. Du solltest nun eine Zusammenfassung deiner Bestellung sehen. Das Ticket wird Dir unverzüglich per Mail zugestellt. Dies kann jedoch einige Minuten in anspruch nehmen.
            </div>
          </div>
        </div>

        <div class="element" id="ticket-lost">
          <div class="headline" onclick="accordion(1)">
            Ich habe mein Ticket verloren!
            <span class="toggler"></span>
          </div>
          <div class="content">
            <div class="text">
              Nur keine Panik. Das ist halb so wild, Dein Ticket ist bei uns gespeichert und wir können es Dir ganz einfach erneut zusenden. Besuche dafür <a href="<?php echo $url; ?>store/find-ticket" target="_blank"><?php echo $url; ?>store/find-ticket</a>. Gib deine E-Mailadresse ein und klicke auf das &quot;Such-Symbol&quot;. Es kommt nun eine Liste mit allen deinen Tickets. Um das Ticket erneut zu erhalten, kannst du einfach auf &quot;erneut senden&quot; klicken. Möchtest du den Veranstalter kontaktieren, kannst du auf &quot;Veranstalter&quot; klicken. Du wirst auf eine Kontaktseite weitergeleitet
            </div>
          </div>
        </div>

        <div class="element" id="payment-options">
          <div class="headline" onclick="accordion(2)">
            Zahlungsmöglichkeiten
            <span class="toggler"></span>
          </div>
          <div class="content">
            <div class="text">
              Jedes Ticket hat seine eigenen Zahlungsmethoden. Grundsätzlich sind jedoch alle gängigen Zahlungsmethoden verfügbar. Von Mastercard über Visa bis hin zur Rechnung oder Vorkasse ist praktisch alles enthalten.
            </div>
          </div>
        </div>

        <div class="element" id="personal-tickets">
          <div class="headline" onclick="accordion(3)">
            Personalisierte Tickets
            <span class="toggler"></span>
          </div>
          <div class="content">
            <div class="text">
              Bitte beachte, dass alle Tickets personalisiert sind und somit nicht an andere übertragen werden können. Möchtest du eine Ticketänderung vornehmen so melde Dich bitte im voraus bei der Eventleitung.
            </div>
          </div>
        </div>

        <div class="element" id="contact">
          <div class="headline" onclick="accordion(4)">
            Kontakt
            <span class="toggler"></span>
          </div>
          <div class="content">
            <div class="text">
              Das System läuft unter <span>TKTDATA</span>. Melde dich bei Problemen bei:<br />
              <br />
              Max Muster<br />
              <a href="mailto:max.muster@example.ch">max.muster@example.ch</a><br />
              Musterstrasse 1<br />
              1234 Musterort <br />
              079 123 45 67<br />
            </div>
          </div>
        </div>
      </div>


    </article>

    <footer>
      <div class="container">
        <div class="footer-element">
          <a href="<?php echo $url; ?>store/faq#contact">Kontakt</a>
          <a href="<?php echo $url; ?>store/find-ticket">Mein Ticket finden</a>
        </div>
        <div class="footer-element">
          <a href="<?php echo $url; ?>store/faq#payment-procedure">Wie kaufe ich ein Ticket?</a>
          <a href="<?php echo $url; ?>store/faq#payment-options">Welche Zahlungsmöglichkeiten gibt es?</a>
        </div>
        <div class="footer-element">
          <span class="powered">Powered by <span>TKTDATA</span></span>
        </div>
      </div>
    </footer>
  </body>
</html>
