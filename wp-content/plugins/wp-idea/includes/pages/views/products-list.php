<?php 
use bpmj\wpidea\Course_Progress;
use bpmj\wpidea\Courses;
use bpmj\wpidea\courses\core\entities\Course_Structure;

use bpmj\wpidea\resources\Resource_Type;
use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\wolverine\user\User;

use bpmj\wpidea\wolverine\product\Product;
use bpmj\wpidea\admin\categories\Categories;

//require __DIR__.'/../../product-list-fnc.php';
//require __DIR__.'/../../product-list-fnc.php';
global $post;

$productTime = [];
$filterType='';

$getCategoryTag = getMauriczCategoryTagID(get_the_terms(get_the_ID(), 'download_category'), get_the_terms(get_the_ID(), 'download_tag'));
//echo "cat ID ".$getCategory;
if($getCategoryTag != null) { 
	if(is_tax('download_category')) {
		$filterType = 'category';
		$argsAll = array(
			'post_type'      => 'download',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_key' => 'sales_disabled',
			'meta_value' => 'off',
			'tax_query'      => array(
				array(
					'taxonomy' => 'download_category',
					'field'    => 'term_id',
					'terms'    => $getCategoryTag,
				),
			),
		);
	} else { 
		$filterType = 'tag';
		$argsAll = array(
			'post_type'      => 'download',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_key' => 'sales_disabled',
			'meta_value' => 'off',
			'tax_query'      => array(
				array(
					'taxonomy' => 'download_tag',
					'field'    => 'term_id',
					'terms'    => $getCategoryTag,
				),
			),
		);
	}
	
} else {
	$argsAll = array(
		'post_type'      => 'download',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_key' => 'sales_disabled',
		'meta_value' => 'off',
		'tax_query'      => array(
			array(
				'taxonomy' => 'download_category',
				'field'    => 'term_id',
				'terms'    => [21],
				'operator' => 'NOT IN'
			),
		),		
	);
}

	$getTimesProducts = get_posts( $argsAll );

	 foreach($getTimesProducts as $product_time) { 
			$productTime[] = get_field('czas_kursu', $product_time->ID);
	 }
?>
<?php
//wp_head();
//echo get_header();
?>

<!-- BEGIN: Szkolenia polecane przez uzytkownikow -->
<div class="bestsellers-block">
<div class="product-inner-block">
<h1 class="title-section text-center">Szkolenia 
	<span class="green-text">polecane</span>
	 przez użytkowników</h1>
</div>
<div class="product-list-upper row-full">
<div class="container">


<div class="row <?php /*flex-section */?>">
	<div class="col-lg-3 green-section">
		
	</div>
	<div class="col-lg-9">
		<div class="products-list bestseller row">
<?php
// Pobieranie kursów z kategorii Bestsellery
 
$args = array(
	'post_type'      => 'download',
	'posts_per_page' => 3,
	'meta_key' => 'sales_disabled',
    'meta_value' => 'off',
	'tax_query'      => array(
		array(
			'taxonomy' => 'download_category',
			'field'    => 'slug',
			'terms'    => 'bestsellery',
		),
	),
);

$bestsellers_product = get_posts( $args );

foreach($bestsellers_product as $product) { 
	echo "<div class='col-sm-6 col-lg-4'>";
	echo "<div class='product'>";
	
	 //Miniatura
	 echo "<div class='product-thumbnail'>";
	 echo getProductLabel($product->ID);
	 echo "<a href='".get_permalink($product->ID)."'>";
		if(empty(get_the_post_thumbnail_url( $product->ID, 'thumbnail'))) {
			echo "<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAYAAAB5fY51AAAAAXNSR0IArs4c6QAAFcNJREFUeF7tnYeSpcYSRFl577333uv//0Dee++999IqkhAKpqaA5t5hY3PqEDGh93aAqT5ZJN3VDZx46qmnTnZsEIAABAwInMCwDFQiRAhAoCeAYZEIEICADQEMy0YqAoUABDAscgACELAhgGHZSEWgEIAAhkUOQAACNgQwLBupCBQCEMCwyAEIQMCGAIZlIxWBQgACGBY5AAEI2BDAsGykIlAIQADDIgcgAAEbAhiWjVQECgEIYFjkAAQgYEMAw7KRikAhAAEMixyAAARsCGBYNlIRKAQggGGRAxCAgA0BDMtGKgKFAAQwLHIAAhCwIYBh2UhFoBCAAIZFDkAAAjYEMCwbqQgUAhDAsMgBCEDAhgCGZSMVgUIAAhgWOQABCNgQwLBspCJQCEAAwyIHIAABGwIYlo1UBAoBCGBY5AAEIGBDAMOykYpAIQABDIscgAAEbAhgWDZSESgEIIBhkQMQgIANAQzLRioChQAEMCxyAAIQsCGAYdlIRaAQgACGRQ5AAAI2BDAsG6kIFAIQwLDIAQhAwIYAhmUjFYFCAAIYFjkAAQjYEMCwbKQiUAhAAMMiByAAARsCGJaNVAQKAQhgWOQABCBgQwDDspGKQCEAAQyLHIAABGwIYFg2UhEoBCCAYZEDEICADQEMy0YqAoUABDAscgACELAhgGHZSEWgEIAAhkUOQAACNgQwLBupCBQCEMCwyAEIQMCGAIZlIxWBQgACGBY5AAEI2BDAsGykIlAIQADDIgcgAAEbAhiWjVQECgEIYFjkAAQgYEMAw7KRikAhAAEMixyAAARsCGBYNlIRKAQggGGRAxCAgA0BDMtGKgKFAAQwLHIAAhCwIYBh2UhFoBCAAIZFDkAAAjYEMCwbqQgUAhDAsMgBCEDAhgCGZSMVgUIAAhgWOQABCNgQwLBspCJQCEAAwyIHIAABGwIYlo1UBAoBCGBY5AAEIGBDAMOykYpAIQABDIscgAAEbAhgWDZSESgEIIBhkQMQgIANAQzLRioChQAEMCxyAAIQsCGAYdlIRaAQgACGRQ5AAAI2BDAsG6kIFAIQwLDIAQhAwIYAhmUjFYFCAAIYFjkAAQjYEMCwbKQiUAhAAMMiByAAARsCGJaNVAQKAQhgWOQABCBgQwDDspGKQCEAAQyLHIAABGwIYFg2UhEoBCCAYZEDEICADQEMy0YqAoUABDAscgACELAhgGHZSEWgEIAAhkUOQAACNgQwLBupCBQCEMCwyAEIQMCGAIZlIxWBQgACGBY5AAEI2BDAsGykIlAIQADDIgcgAAEbAhiWjVQECgEIYFjkAAQgYEMAw7KRikAhAAEMixyAAARsCGBYNlIRKAQggGGRAxCAgA0BDMtGKgKFAAQwLHIAAhCwIYBh2UhFoBCAAIZFDkAAAjYEMCwbqQgUAhDAsMgBCEDAhgCGZSMVgUIAAhgWOQABCNgQwLBspCJQCEAAwyIHIAABGwIYlo1UBAoBCGBY5AAEIGBDAMOykYpAIQABDIscgAAEbAhgWDZSESgEIIBhkQMQgIANAQzLRioChQAEMCxyAAIQsCGAYdlIRaAQgACGleTAOeec01122WXdueee25199tnd33//3f3666/9z88//9z9888/O2eOznfRRRd1F1xwQXfmmWd2f/31V3/eH3/8sf/fu24614UXXtj/6G8oxt9++60/7++//77raW2OU7svvvjiTtqdddZZ3Z9//tlz/eWXX/r/njx5cue2nHfeeT3X888/vzvjjDN6nmL7ww8/7HXerXJh54YaHIhhjUS64ooruptuuqm/4Oc2Jet7773Xm1frJoO6/fbbOyX/1KaL68MPP+x++umn1tP2F+dtt93WXXrppZPH6OL99NNPuy+//LL5vC47Sq+rrrqqN/+57fvvv+/ef//93shatyuvvLLPBzHONpmgbgjKhTXn3SoXWtvlvB+G9Z96d955Z9+rWrMpWd94443FQ2644Ybu+uuvX9xv2OGTTz7pDWZpU4/innvuWdrt/9+rR/DWW2/t1Sto/mMb7yiDuv/++/te8JpNXMV3abvvvvv6XlXLJuMSV/Fd2rbKhaW/e1x+j2F1XXfzzTd311xzzU6afvHFF32vaGrTeXX+tdvHH3/cffbZZ5OH6S6ti+rEiROrTq3e2+uvv77qmNNx5wcffHC2tzoX85tvvjlrLnfffXd3ySWXrG72q6++2g9BT3UurA7U+IDyhqW6xAMPPHBIwqFupWGfaksyCPXAMoNQL0u9rbjp7v/QQw8d+nfVl3Remce4nhV3fPnll/taSbY99thjh4ZButMPtRXVcnTubHj7wQcfWA8P1VtV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'/>";
		} else {
			echo "<img src='".get_the_post_thumbnail_url( $product->ID, 'thumbnail')."'/>";
		}
		echo "</a>";
		echo "</div>";

		// Tytuł produktu
		echo "<h3 class='product-title'>";
			echo "<a href='".get_permalink($product->ID)."'>";
				echo $product->post_title;
			echo "</a>";
		echo "</h3>";

		//  Czas trwania / liczba lekcji
		echo "<table class='product-feature'>";
		echo "<tr>";
			echo "<td>";
				echo "Czas trwania";
			echo "</td>";
			echo "<td>";
				echo get_field('czas_kursu', $product->ID);
			echo "min</td>";
		echo "</tr>";

		echo "<tr>";
			echo "<td>";
				echo "Liczba modułów";
			echo "</td>";
			echo "<td>";
				echo get_field('liczba_lekcji', $product->ID);
			echo "</td>";
		echo "</tr>";
	echo "</table>";
?>

<?php
		// Cena produktu
		$sale_price_from_date = get_post_meta($product->ID,  'sale_price_from_date', true);
		$sale_price_to_date = get_post_meta($product->ID,  'sale_price_to_date', true);
?>


<div class="price-container">
<?php
		
		if((date('Y-m-d') >= $sale_price_from_date) && (date('Y-m-d') < $sale_price_to_date)) { 
			?>
			<?php 
echo "<h4 class='product-price sale'>";
	echo number_format(get_post_meta($product->ID,  'sale_price', true),2,'.','');
echo " PLN</h4>";
?>

<h4 class="crossed"><?php echo number_format(get_post_meta($product->ID,  'edd_price', true),2,'.',''); ?> PLN</h4>



<?php
} else { 
echo "<h4 class='product-price'>";
	echo number_format(get_post_meta($product->ID,  'edd_price', true),2,'.','');
echo " PLN</h4>";
 
}
?>
</div> 

<small class="omniprice">
	<!-- Najniższa cena z 30 dni:  -->
	<?= bpmj_render_lowest_price_information($product->ID); ?>
	<!-- PLN -->
</small>

<?php
// Dodaj do koszyka
echo '<a href="'.get_permalink($product->ID).'" class="more-green">
<i class="fa fa-shopping-bag"></i> 
Sprawdź szkolenie</a>';

	echo "</div>";
	echo "</div>";
}

?>
</div>
</div>
</div>


</div>
</div>
</div>
<!-- END: Szkolenia polecane przez uzytkownikow -->



<div class="product-list-lower row-full">
<div class="container">

<div class="row">
<div class="col-sm-3 left-sidebar">
<h5>Kategorie</h5>

<!-- BEGIN: Kategorie -->
<ul class="product-list-categories">
	<li>
		<a href="<?= get_permalink(56); ?>">Wszystkie</a>
	</li>
<?php
// Zwrócenie wszystkich kategorii

	$categories = get_terms( array(
		'taxonomy' => 'download_category',
		'hide_empty' => true
		) );

	
foreach ($categories as $key => $category) {
    echo "<li>";
	echo "<a href='".get_term_link($category->term_id)."'>";
    echo $category->name;
	echo "</a>";
    echo "</li>";
}
?>
</ul>
<!--  END: Kategorie -->


<h5>Poziom</h5>

<!-- BEGIN: Poziom -->
<ul class="levels">
<?php
 $tags = get_terms( array(
	 'taxonomy' => 'download_tag', 
	 'hide_empty' => true, 
	 ) );



foreach ($tags as $key => $tag) {
	//print_r($tag);
    echo "<li>";
	echo "<a href='".get_tag_link($tag)."'>";
    echo $tag->name;
	echo "</a>";
    echo "</li>";
}

?>
</ul>
<!-- END: Poziom -->

<h5>Czas trwania</h5>
<!-- BEGIN: Czas trwania -->
<div>
	<form method="POST" class="mjfilter">
		<div class="range-container">
			<label class="left-align">
			<?= getMinMaxRange($productTime,'min'); ?>min
			</label>
			<label class="right-align">
			<?= getMinMaxRange($productTime,'max'); ?>min
			</label>
		</div>
  <input type="range" id="czas" name="czas" 
         min="<?= getMinMaxRange($productTime,'min'); ?>" max="<?= getMinMaxRange($productTime,'max'); ?>" value="<?= getMinMaxRange($productTime,'max'); ?>" step="0.5" onchange="setRangeTime(this)">
		 <label class="czas" data-range="czas">do <?= getMinMaxRange($productTime,'max'); ?>min</label>
		 
		 <input type="hidden" name="filter_type" id="filter_type" value="<?= $filterType; ?>"/>
		 <input type="hidden" name="id_category_tag" id="id_category_tag" value="<?= $getCategoryTag; ?>"/>

		 <input type="hidden" name="id_post" id="id_post" value="<?= get_the_ID(); ?>"/>
		 <input type="hidden" name="url_mjfilter" id="url_mjfilter" value="<?= bloginfo('url'); ?>/wp-content/plugins/wp-idea/includes/pages/views/ajax-filter-result.php"/>
</form>
		 <script type="text/javascript">
			 function setRangeTime(obj) { 
				 document.querySelector("."+obj.getAttribute('name')).innerHTML = "do "+obj.value+"min";

				
				$.ajax({
					method:'POST',
					url:$("#url_mjfilter").val(),
					data: $(".mjfilter").serialize(),
					beforeSend: function() {
						$(".ajax-product-list").css('opacity', '0.5');
						$(".ajax-product-list").html('Ładowanie...');
					},
					success: function(data) {
						$(".ajax-product-list").css('opacity', '1');
						$(".ajax-product-list").html(data);
					},
					error: function(xhr) {
						$(".ajax-product-list").css('opacity', '1');
						$(".ajax-product-list").html("error"+data);
					}
				});
			 }

		 </script>
</div>
<!-- END: Czas trwania -->
</div>

<div class="col-sm-9 products-list">
<!-- BEGIN: Lista kursów -->


<h1 class="title-section"><?php echo getMauriczSectionName(get_the_terms(get_the_ID(), 'download_category'), get_the_terms(get_the_ID(), 'download_tag')); ?></h1>
<div class="row ajax-product-list">
<?php
// Zwrócenie wszystkich kursów



$all_product = get_posts( $argsAll );

	 foreach($all_product as $product) {		 
		 echo "<div class='col-sm-6 col-lg-4'>";
		 echo "<div class='product'>";
		 //Miniatura
		 echo "<div class='product-thumbnail'>";

		// Sprawdz czy jest promocja lub bestseller

		echo getProductLabel($product->ID);

		// echo "<div class='label-product'>";
		// echo "Bestseller";
		// echo "</div>";
		// 

		 echo "<a href='".get_permalink($product->ID)."'>";
			if(empty(get_the_post_thumbnail_url( $product->ID, 'thumbnail'))) {
				echo "<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAYAAAB5fY51AAAAAXNSR0IArs4c6QAAFcNJREFUeF7tnYeSpcYSRFl577333uv//0Dee++999IqkhAKpqaA5t5hY3PqEDGh93aAqT5ZJN3VDZx46qmnTnZsEIAABAwInMCwDFQiRAhAoCeAYZEIEICADQEMy0YqAoUABDAscgACELAhgGHZSEWgEIAAhkUOQAACNgQwLBupCBQCEMCwyAEIQMCGAIZlIxWBQgACGBY5AAEI2BDAsGykIlAIQADDIgcgAAEbAhiWjVQECgEIYFjkAAQgYEMAw7KRikAhAAEMixyAAARsCGBYNlIRKAQggGGRAxCAgA0BDMtGKgKFAAQwLHIAAhCwIYBh2UhFoBCAAIZFDkAAAjYEMCwbqQgUAhDAsMgBCEDAhgCGZSMVgUIAAhgWOQABCNgQwLBspCJQCEAAwyIHIAABGwIYlo1UBAoBCGBY5AAEIGBDAMOykYpAIQABDIscgAAEbAhgWDZSESgEIIBhkQMQgIANAQzLRioChQAEMCxyAAIQsCGAYdlIRaAQgACGRQ5AAAI2BDAsG6kIFAIQwLDIAQhAwIYAhmUjFYFCAAIYFjkAAQjYEMCwbKQiUAhAAMMiByAAARsCGJaNVAQKAQhgWOQABCBgQwDDspGKQCEAAQyLHIAABGwIYFg2UhEoBCCAYZEDEICADQEMy0YqAoUABDAscgACELAhgGHZSEWgEIAAhkUOQAACNgQwLBupCBQCEMCwyAEIQMCGAIZlIxWBQgACGBY5AAEI2BDAsGykIlAIQADDIgcgAAEbAhiWjVQECgEIYFjkAAQgYEMAw7KRikAhAAEMixyAAARsCGBYNlIRKAQggGGRAxCAgA0BDMtGKgKFAAQwLHIAAhCwIYBh2UhFoBCAAIZFDkAAAjYEMCwbqQgUAhDAsMgBCEDAhgCGZSMVgUIAAhgWOQABCNgQwLBspCJQCEAAwyIHIAABGwIYlo1UBAoBCGBY5AAEIGBDAMOykYpAIQABDIscgAAEbAhgWDZSESgEIIBhkQMQgIANAQzLRioChQAEMCxyAAIQsCGAYdlIRaAQgACGRQ5AAAI2BDAsG6kIFAIQwLDIAQhAwIYAhmUjFYFCAAIYFjkAAQjYEMCwbKQiUAhAAMMiByAAARsCGJaNVAQKAQhgWOQABCBgQwDDspGKQCEAAQyLHIAABGwIYFg2UhEoBCCAYZEDEICADQEMy0YqAoUABDAscgACELAhgGHZSEWgEIAAhkUOQAACNgQwLBupCBQCEMCwyAEIQMCGAIZlIxWBQgACGBY5AAEI2BDAsGykIlAIQADDIgcgAAEbAhiWjVQECgEIYFjkAAQgYEMAw7KRikAhAAEMixyAAARsCGBYNlIRKAQggGGRAxCAgA0BDMtGKgKFAAQwLHIAAhCwIYBh2UhFoBCAAIZFDkAAAjYEMCwbqQgUAhDAsMgBCEDAhgCGZSMVgUIAAhgWOQABCNgQwLBspCJQCEAAwyIHIAABGwIYlo1UBAoBCGBY5AAEIGBDAMOykYpAIQABDIscgAAEbAhgWDZSESgEIIBhkQMQgIANAQzLRioChQAEMCxyAAIQsCGAYdlIRaAQgACGleTAOeec01122WXdueee25199tnd33//3f3666/9z88//9z9888/O2eOznfRRRd1F1xwQXfmmWd2f/31V3/eH3/8sf/fu24614UXXtj/6G8oxt9++60/7++//77raW2OU7svvvjiTtqdddZZ3Z9//tlz/eWXX/r/njx5cue2nHfeeT3X888/vzvjjDN6nmL7ww8/7HXerXJh54YaHIhhjUS64ooruptuuqm/4Oc2Jet7773Xm1frJoO6/fbbOyX/1KaL68MPP+x++umn1tP2F+dtt93WXXrppZPH6OL99NNPuy+//LL5vC47Sq+rrrqqN/+57fvvv+/ef//93shatyuvvLLPBzHONpmgbgjKhTXn3SoXWtvlvB+G9Z96d955Z9+rWrMpWd94443FQ2644Ybu+uuvX9xv2OGTTz7pDWZpU4/innvuWdrt/9+rR/DWW2/t1Sto/mMb7yiDuv/++/te8JpNXMV3abvvvvv6XlXLJuMSV/Fd2rbKhaW/e1x+j2F1XXfzzTd311xzzU6afvHFF32vaGrTeXX+tdvHH3/cffbZZ5OH6S6ti+rEiROrTq3e2+uvv77qmNNx5wcffHC2tzoX85tvvjlrLnfffXd3ySWXrG72q6++2g9BT3UurA7U+IDyhqW6xAMPPHBIwqFupWGfaksyCPXAMoNQL0u9rbjp7v/QQw8d+nfVl3Remce4nhV3fPnll/taSbY99thjh4ZButMPtRXVcnTubHj7wQcfWA8P1VtV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'/>";
			} else {
				echo "<img src='".get_the_post_thumbnail_url( $product->ID, 'thumbnail')."'/>";
			}
			echo "</a>";
			echo "</div>";

			// Tytuł produktu
			echo "<h3 class='product-title'>";
				echo "<a href='".get_permalink($product->ID)."'>";
					echo $product->post_title;
				echo "</a>";
			echo "</h3>";



			//  Czas trwania / liczba lekcji
			echo "<table class='product-feature'>";
			echo "<tr>";
				echo "<td>";
					echo "Czas trwania";
				echo "</td>";
				echo "<td>";
					echo get_field('czas_kursu', $product->ID);
				echo "min</td>";
			echo "</tr>";

			echo "<tr>";
				echo "<td>";
					echo "Liczba modułów";
				echo "</td>";
				echo "<td>";
					echo get_field('liczba_lekcji', $product->ID);
				echo "</td>";
			echo "</tr>";
		echo "</table>";
?>

<?php
			// Cena produktu
			$sale_price_from_date = get_post_meta($product->ID,  'sale_price_from_date', true);
			$sale_price_to_date = get_post_meta($product->ID,  'sale_price_to_date', true);
?>


<div class="price-container">
<?php
			
			if((date('Y-m-d') >= $sale_price_from_date) && (date('Y-m-d') < $sale_price_to_date)) { 
				?>
				<?php 
	echo "<h4 class='product-price sale'>";
		echo number_format(get_post_meta($product->ID,  'sale_price', true),2,'.','');
	echo " PLN</h4>";
	?>

	<h4 class="crossed"><?php echo number_format(get_post_meta($product->ID,  'edd_price', true),2,'.',''); ?> PLN</h4>

	
	
<?php
} else { 
	echo "<h4 class='product-price'>";
		echo number_format(get_post_meta($product->ID,  'edd_price', true),2,'.','');
	echo " PLN</h4>";
	 
}
?>
	</div> 

	<small class="omniprice">
		<!-- Najniższa cena z 30 dni:  -->
		<?= bpmj_render_lowest_price_information($product->ID); ?>
		<!-- PLN -->
	</small>
 <?php
	
	/*
		echo  get_post_meta($product->ID,  'edd_price', true);
		echo "<br/>";
		echo get_post_meta( $product->ID,  'sale_price', true);	
		echo "</h4>";
		
		echo "<br/>";
		echo  get_post_meta( $product->ID,  'sale_price_from_date', true);
		echo "<br/>";
		echo  get_post_meta( $product->ID,  'sale_price_to_date', true);
		echo "<br/>";
		echo  get_post_meta($product->ID,  'edd_featured_image', true);
		echo "<br/>";
		echo  get_post_meta($product->ID,  'purchase_limit_items_left', true);
		echo "<br/>";
		echo  get_post_meta($product->ID,  'name', true);
		echo "<br/>";
		echo  get_post_meta($product->ID,  'categories', true);
		echo "<br/>";
		echo  get_post_meta($product->ID,  'logo', true);
		echo "<br/>";
		echo  get_post_meta($product->ID,  'banner', true);
		

		echo  get_post_meta($product->ID,  'featured_image', true);
		

		echo  get_post_meta($product->ID,  'guid', true);

		*/

		  
	// Dodaj do koszyka
			echo '<a href="'.get_permalink($product->ID).'" class="more-green">
			<i class="fa fa-shopping-bag"></i> 
			Sprawdź szkolenie</a>';
			echo "</div>";
			echo "</div>";
			
	 }
?>
</div>
<!-- END: Lista kursów -->
</div>

</div>


</div>
</div>


<div class="row">
    <!-- BEGIN: Rekomendacje -->
	<div class="hp-opinion row-full" style="margin-bottom:0px;">
	<div class="product-inner-block">
<h1 class="title-section text-center">Sprawdź rekomendacje naszych kursantów</h1>
	</div>
<div class="row">
	<?php
echo do_shortcode("[ic_add_posts template='template-opinion.php' category='opinie' showposts='3']");
?>
</div>
</div>
    <!-- END: Rekomendacje -->
</div>
<?php
function getMinMaxRange($productTime, $type = 'max') {
		
	if(count($productTime) == 0) {
		return 0;
	} else {
		if($type == 'min') { 
			if(count($productTime) < 2) {
				return 0;
			}  else {
				return min($productTime);
			}
		} elseif($type == 'avg') {
			$sortDate = sort($productTime);
			$countDate = count($productTime);
			$avg = (int)$countDate/2;
			return $productTime[$avg];
		} else { 
			return max($productTime);
		}
	}
}
?>