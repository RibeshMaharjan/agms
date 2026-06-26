<?php

/**
 * Calculate cosine similarity between two products based on multiple parameters
 * @param array $current Current product details
 * @param array $product Product to compare with
 * @return float Cosine similarity between 0 and 1
 */
function calculateCosineSimilarity($current, $product) {
    // Convert attributes into binary vectors
    $vectorA = [
        'art_type' => !empty($current['ArtType']) && $current['ArtType'] == $product['ArtType'] ? 1 : 0,
        'art_medium' => !empty($current['ArtMedium']) && $current['ArtMedium'] == $product['ArtMedium'] ? 1 : 0,
        'artist' => !empty($current['Artist']) && $current['Artist'] == $product['Artist'] ? 1 : 0
    ];

    $vectorB = [
        'art_type' => 1,
        'art_medium' => 1,
        'artist' => 1
    ];

    // Calculate dot product and magnitudes
    $dotProduct = 0;
    $magnitudeA = 0;
    $magnitudeB = 0;

    foreach ($vectorA as $key => $value) {
        $dotProduct += $value * $vectorB[$key];
        $magnitudeA += $value * $value;
        $magnitudeB += $vectorB[$key] * $vectorB[$key];
    }

    $magnitudeA = sqrt($magnitudeA);
    $magnitudeB = sqrt($magnitudeB);

    // Avoid division by zero
    if ($magnitudeA == 0 || $magnitudeB == 0) {
        return 0;
    }

    // Calculate cosine similarity
    return $dotProduct / ($magnitudeA * $magnitudeB);
}

function weightedSort(&$recommendedArray) {
    foreach ($recommendedArray as &$product) {
        // Assign updated weights to attributes based on priority
        $weights = [
            'art_type' => 0.5,  // Highest priority
            'artist' => 0.25,
            'art_medium' => 0.15,
            'price' => 0.1      // Lowest priority
        ];

        // Calculate weighted score
        $product['weighted_score'] = 
            ($product['similarity_score'] * $weights['art_type']) +
            ($product['similarity_score'] * $weights['artist']) +
            ($product['similarity_score'] * $weights['art_medium']) +
            ($product['similarity_score'] * $weights['price']);
    }

    // Sort by weighted score
    usort($recommendedArray, function($a, $b) {
        return $b['weighted_score'] <=> $a['weighted_score'];
    });
}

/**
 * Get recommended products based on the current product
 * @param mysqli $con Database connection
 * @param int $current_product_id Current product ID
 * @param int $limit Number of recommendations to return
 * @return array Array of recommended products
 */
function getRecommendedProducts($con, $current_product_id, $limit = 4) {
    // Get current product details
    $current_product_query = mysqli_query($con, "SELECT p.*, t.ArtType as typename, m.ArtMedium as mediumname, a.Name as artistname 
        FROM tblartproduct p 
        JOIN tblarttype t ON t.ID = p.ArtType 
        JOIN tblartmedium m ON m.ID = p.ArtMedium 
        JOIN tblartist a ON a.ID = p.Artist 
        WHERE p.ID = '$current_product_id'");
    $current = mysqli_fetch_assoc($current_product_query);

    if (!$current) {
        return [];
    }

    // Get all other products with their details
    $all_products_query = mysqli_query($con, "SELECT p.*, t.ArtType as typename, m.ArtMedium as mediumname, a.Name as artistname 
        FROM tblartproduct p 
        JOIN tblarttype t ON t.ID = p.ArtType 
        JOIN tblartmedium m ON m.ID = p.ArtMedium 
        JOIN tblartist a ON a.ID = p.Artist 
        WHERE p.ID != '$current_product_id'");

    $recommendedArray = [];

    // Calculate cosine similarity for all products
    while ($product = mysqli_fetch_assoc($all_products_query)) {
        $similarity = calculateCosineSimilarity($current, $product);
        if ($similarity > 0) {
            $product['similarity_score'] = $similarity;
            $recommendedArray[] = $product;
        }
    }

    // Sort by weighted score
    weightedSort($recommendedArray);

    // Return top N recommendations
    return array_slice($recommendedArray, 0, $limit);
}
?>
