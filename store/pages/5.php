<script>
function loadMessageByHash() {
  if(window.location.hash) {
    var hash = window.location.hash.replace("#", "");
    var element = document.getElementById(hash);
    element.children[0].click();
  }
}

window.onload = loadMessageByHash;
window.onhashchange = loadMessageByHash;
</script>

<header>
  <div class="banner" style="background-image: url('<?php echo $url; ?>medias/store/banner.jpg')">
    <a href="<?php echo $url . "store/" . $type; ?>"><img class="logo" src="<?php echo $url; ?>medias/store/logo-fitted.png"></a>
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
