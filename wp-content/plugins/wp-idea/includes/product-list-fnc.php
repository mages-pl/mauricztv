<?php 
/**
 * @param int $id_product
 * @return string
 */
function getProductLabel($id_product) { 
	// Cena produktu
	$sale_price_from_date = get_post_meta($id_product,  'sale_price_from_date', true);
	$sale_price_to_date = get_post_meta($id_product,  'sale_price_to_date', true);

	// Kategorie
	$category = get_the_terms($id_product, 'download_category');

	$getCategoryId = [];
	if(is_array($category)) { 
	foreach($category as $cat) { 
		$getCategoryId[] = $cat->term_id;
	}
}

	$output = '';
	if((date('Y-m-d') >= $sale_price_from_date) && (date('Y-m-d') < $sale_price_to_date)) { 
		$output .= "<div class='label-product sale'>";
		$output .= "Promocja";
		$output .= "</div>";
	} else if(in_array(13, $getCategoryId)) { 
		$output .= "<div class='label-product'>";
		$output .= "Bestsellers";
		$output .= "</div>";
	} else if(in_array(29, $getCategoryId)) { 
		$output .= "<div class='label-product all-in'>";
		$output .= "Pełny pakiet";
		$output .= "</div>";
	} else { 
		if((@get_post_meta($id_product,  'edd_sale_price', true)  > 0) && (get_post_meta($id_product,  'edd_sale_price', true) != @get_post_meta($id_product,  'edd_price', true))) {
			$output .= "<div class='label-product sale'>";
			$output .= "Promocja";
			$output .= "</div>";
		}
	}
	return $output;
}

/**
 * @param array $category
 * @param array $tag
 * @return int 
 */
function getMauriczCategoryTagID($category = null, $tag = null){ 
	if(is_tax('download_category')) { 
		if(is_array($category)) { 
			foreach ($category as $c) {
				return $c->term_id;
			}
		}
	}

	if(is_tax('download_tag')) {
		if(is_array($tag)) { 
			foreach ($tag as $t) {
				return $t->term_id;
			}
		} 
	}

	return null;
}
/**
 * @param array $category
 * @param array $tag
 * @return string
 */
function getMauriczSectionName($category = null, $tag = null) {
	
    if(is_tax('download_category')) { 
        if($category instanceof WP_Term) {
            return $category->name;
        } elseif(is_array($category)) { 
            foreach ($category as $c) {
                return $c->name;
            }
        }
    }
	if(is_tax('download_tag')) {
		if(is_array($tag)) { 
			foreach ($tag as $t) {
				return $t->name;
			}
		}
	}

	return "Sprawdź pozostałe szkolenia";
	
}