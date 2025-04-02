<?php
/**
 * Template Name: Szablon logowania
 */

 global $user_ID;

 global $wpdb;

 if($user_ID) { 
  // Zalogowany
  if(empty(get_option('mauricz_myaccount'))) {
    $getLinkAccount = get_permalink(57);// 57
  } else {
    $getLinkAccount = get_permalink((int)get_option('mauricz_myaccount'));// 57
  }
  
  header("Location:".$getLinkAccount);
  //echo "TEST".$getLinkAccount;
 }
 
get_header();
    if (isset($_GET['checkemail']) && $_GET['checkemail'] == 'registered') : ?>
        <div class="alert alert-success col-12 mt-10" role="alert">
        <strong>Rejestracja zakończona pomyślnie!</strong> Sprawdź swoją skrzynkę e-mail, aby aktywować konto.
        </div>
    <?php endif; ?>
    <?php
 if(!$user_ID) { 
    // Niezalogowany
        // Check if registration confirmation parameter is present
        
    ?>

    

    <div class="row login-section tml">

    <div class="col-md-6">
    <h1>Zaloguj się</h1>
 
    <?php 
    if(isset($_POST['wp-submit'])) {

        ?>
       <div class="tml-alerts" style="
    max-width: 75%;
    margin: auto;
">
        <?php
    //    print_r($user);
    //     print_r($_SESSION);
        if($_SESSION['inacvite_user'] == '1')  { 
            ?>
    <ul class="tml-errors"><li class="tml-message"><strong>Błąd:</strong> użytkownik jest nieaktywny.</li></ul></div>
            <?php
        }

        if($_SESSION['user_confirm'] == '1')  { 
            ?>
    <ul class="tml-errors"><li class="tml-success"><strong>Informacja:</strong> Na podany email przesłaliśmy link do aktywacji konta.</li></ul></div>
            <?php
        }
        
        if($_SESSION['authentication_failed'] == '1')  { 
?>
<ul class="tml-errors"><li class="tml-error"><strong>Błąd:</strong> proszę wpisać poprawne dane do logowania.</li></ul></div>
<?php
        }  
        ?>
 
<!-- <ul class="tml-errors"><li class="tml-error"><strong>Błąd:</strong> proszę wpisać poprawne dane do logowania.</li></ul></div> -->
        <?php
    }
    ?>
    <?php 
    wp_login_form( ['echo' =>true] );
    ?>
	
	<?php /*
    <p class="inner-login-left">Nie pamiętasz hasła? <a href="<?= bloginfo('url') ?>/wp-login.php?action=lostpassword">Kliknij tutaj</a></p>
	*/?>
	
	<p class="inner-login-left">Nie pamiętasz hasła? <a href="<?= bloginfo('url') ?>/lostpassword/">Kliknij tutaj</a></p>
	
    </div>

    <div class="col-md-6">
    <div>
    <h1>Zarejestruj się</h1>
    <h3>Otrzymasz liczne dodatkowe korzyści</h3>
    <ul>

    <li>
    dostęp do Twoich szkoleń
    </li>
    <li>
    podgląd historii zakupów
    </li>
    <li>
    brak konieczności wprowadzania swoich danych w kolejnych zakupach
    </li>
    <li>
    mozliwość otrzymania rabatów i kuponów promocyjnych
    </li>

    </ul>

	<?php /*
    <a href='<?= bloginfo('url') ?>/wp-login.php?action=register' class='more btn btn-secondary register'>Zarejestruj się</a>
	*/?>
	
	
	<a href='<?= bloginfo('url') ?>/register/' class='more btn btn-secondary register'>Zarejestruj się</a>

    </div>

    </div>
    </div>
    



    <?php
 } else { 
  // Zalogowany
 }

 get_footer();