
<?php
/**
 * Template Name: Newsletter
 */

get_header();
$acf_fields = get_fields();
?>

    <?php $newsletter = $acf_fields['newsletter']; if($newsletter) : ?>
    
        <section class="newsletter">
            <div class="newsletter-container">
                <div class="newsletter-container__wrapper">
                    <div class="newsletter-container__top">
                        <div class="green-top">
                        <?= $newsletter['top_tekst'] ?>
                        </div>
                        <div class="title">
                            <?= $newsletter['tytul'] ?>
                        </div>
                    </div>
                    <div class="newsletter-container__form">
                    <div class="klaviyo-form-Smt65S"></div>
                    </div>
                </div>
                <div class="newsletter-container__image">
                    <div class="image">
                        <?= wp_get_attachment_image($newsletter['zdjecie']['id'], 'full') ?>

                    </div>
                </div>
            </div>
        </section>
    
    <?php endif; ?>

<?php
get_footer();
