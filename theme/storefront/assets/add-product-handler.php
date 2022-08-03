<?php
require_once('../../../../wp-config.php');



//$product_img = $_POST['product_img'];

// вставляем запись в базу данных
addNewProduct();
function addNewProduct(){
    $product_name = $_POST['product_name'];
    $created_date = $_POST['created_date'];
    $product_type = $_POST['product_type'];
    $product_price = $_POST['product_price'];
    if ($product_name != null && $created_date!=null && $product_type!=null && $product_price!=null){
        $user_ID = 1;
        $post_id = wp_insert_post(  wp_slash( array(
            'post_title'    => $product_name,
            'post_name'     => $product_name,
            'post_status'   => 'publish',
            'post_type'     => 'product',
            'post_author'   => $user_ID,
            'ping_status'   => get_option('default_ping_status'),
            'meta_input'    => [ '_public_data'=>$created_date, '_select'=>$product_type, '_price'=>$product_price,'_regular_price'=>$product_price],
        ) ) );
        echo '<p class="green"> product added successfully </p>';
    }else{
        echo '<p class="red"> oops something went wrong... </p>';
    }

}



//echo 'work'.$product_name.$created_date.$product_type.$product_price.$product_img;
