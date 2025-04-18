

<?php
$user_id = get_current_user_id();

if(!$user_id) {
    //<!-- Sprawdź czy user jest zalogowany jeśli nie, zwróć pusty formularz -->
    // echo "Niezalogowany";
    $current_user = 0;
} else {
   // <!-- User jest zalogowany, odnieś się do jego sesji -->
    // echo "Zalogowany";
    $current_user = wp_get_current_user();
}
?>


<form method="POST">

    <input type="text" name="imie" placeholder="Imię" value="<?= $current_user->display_name ?>">
    
    <input type="text" name="email" placeholder="Adrese-mail" value="<?= $current_user->user_email ?>">

    <label class="term-label">
        <input type="checkbox" class="term-checkbox" value="1" required name="confirm" style="display: inline-block;width: auto;vertical-align: middle;">
        <p>Akceptuję <a href="/regulamin">Regulamin</a> serwisu oraz wyrażam zgodę na otrzymywanie drogą elektroniczną na wskazany przeze mnie adres email informacji handlowej w rozumieniu art. 10 ust. 1 ustawy z dnia 18 lipca 2002 roku o świadczeniu usług drogą elektroniczną od Mauricz.tv.</p>
    </label>
    <div class="g-recaptcha" data-sitekey="6LfInJcqAAAAADmqtMHbyrSkLMNBorJpQQ3Stq9a"></div>

    <div class="button-wrapper">
        <button type="submit" type="submit" name="submit_form" class="more">Uzyskaj bezpłatny dostęp</button>

    </div>
</form>

 