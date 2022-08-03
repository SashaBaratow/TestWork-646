<?php
get_header(); ?>

    <div class="form-overlay" >
        <form id="add_product" action="<?=get_template_directory_uri()?>/assets/add-product-handler.php" method="post" enctype=”multipart/form-data”>
            <h1> CREATE PRODUCT </h1>
            <fieldset>
                <legend><span class="number">1</span> all fields are required </legend>
                <label for="name">Product Name*:</label>
                <input type="text" id="name" name="product_name" required>
                <label for="name">Created date*:</label>
                <input type="date" id="date" name="created_date" required>
                <label for="name">Price*:</label>
                <input type="number" id="date" name="product_price" required>
                <label for="type">Product type*:</label>
                <select id="type" name="product_type" required>
                    <optgroup label="type">
                        <option value="rare">rare</option>
                        <option value="frequent">frequent</option>
                        <option value="unusual">unusual</option>
                    </optgroup>
                </select>
<!--                <label for="name">Product Image:</label>-->
<!--                <input type="file" name="product_img" id="my_image_upload"   multiple="false" />-->

            </fieldset>
            <button type="submit">Create</button>
            <span id="result"></span>
        </form>

    </div>
<?php

?>

<?php
get_footer();
