<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];


// add custom fields
function art_woo_add_custom_fields() {
    $product_id = get_the_ID();
    $meta_values_public_date = get_post_meta($product_id, '_public_data', true);
	?>
	<div class="options_group">
		<h2><strong>Произвольная группа полей</strong></h2>
        <?php
        global $product, $post;
        echo '<div class="options_group">';
        woocommerce_wp_select(
            [
                'id'      => '_select',
                'label'   => 'Тип продукта',
                'options' => [
                    'rare'   => __( 'rare', 'woocommerce' ),
                    'frequent'   => __( 'frequent', 'woocommerce' ),
                    'unusual' => __( 'unusual', 'woocommerce' ),
                ],
            ]
        );
        echo '</div>';
        ?>
        <p class="form-field custom_field_type">
            <label for="custom_field_type">
                <?php echo 'дата публикации'; ?>
            </label>
        <div class="wrap-input-date">
            <input
                    class="input-date"
                    id="input-date"
                    type="date"
                    name="_public_data"
                    value="<?=$meta_values_public_date;?>"
            />
            <div class="main-container">
                <a href="#" class="button-save clear_cstm_flds">update custom fields</a>
                <a href="#" class="button-clear clear_cstm_flds">clear custom fields</a>
            </div>
        </div>
	</div>

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                   jQuery('#input-img').attr('src', e.target.result);
                    jQuery('#input-img').addClass('active')
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        jQuery("#imgInp").change(function() {
            readURL(this);
        });
        let imgDeleteBtn = document.querySelector(".icon-tabler-square-x")
        let imgInputField = document.getElementById("imgInp")
        console.log(imgDeleteBtn + '<br>' + imgInputField)
        imgDeleteBtn.addEventListener('click',()=>{
            imgInputField.value = ""
            jQuery('#input-img').removeClass('active')
            jQuery('#input-img').attr('src', '');
        })
    </script>
    <?php
}
add_action( 'woocommerce_product_options_general_product_data', 'art_woo_add_custom_fields' );
//save custom fields
function art_woo_custom_fields_save( $post_id ) {
    // Сохранение выпадающего списка.
    $woocommerce_select = $_POST['_select'];
    if ( ! empty( $woocommerce_select ) ) {
        update_post_meta( $post_id, '_select', esc_attr( $woocommerce_select ) );
    }
    $woocommerce_pack_length = $_POST['_public_data'];
    if ( ! empty( $woocommerce_pack_length ) ) {
        update_post_meta( $post_id, '_public_data', esc_attr( $woocommerce_pack_length ) );
    }
    $woocommerce_pack_length = $_POST['_img_prod'];
    if ( ! empty( $woocommerce_pack_length ) ) {
        update_post_meta( $post_id, '_img_prod', esc_attr( $woocommerce_pack_length ) );
    }
    else {
        // Иначе удаляем созданное поле из бд
        delete_post_meta( $post_id, '_product_field_type_ids' );
    }
}
add_action( 'woocommerce_process_product_meta', 'art_woo_custom_fields_save', 10 );

// Meta Box
class CustomFieldsMetaBox{

    private $screen = array(
        'post',
        'product',
    );

    private $meta_fields = array(
        array(
            'label' => 'choose image',
            'id' => 'my_img_product',
            'type' => 'media',
            'returnvalue' => 'id'
        )

    );

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'admin_footer', array( $this, 'media_fields' ) );
        add_action( 'save_post', array( $this, 'save_fields' ) );
    }


    public function add_meta_boxes() {
        foreach ( $this->screen as $single_screen ) {
            add_meta_box(
                'CustomFields',
                __( 'CustomFields', '' ),
                array( $this, 'meta_box_callback' ),
                $single_screen,
                'normal',
                'default'
            );
        }
    }

    public function meta_box_callback( $post ) {
        wp_nonce_field( 'CustomFields_data', 'CustomFields_nonce' );
        $this->field_generator( $post );
    }
    public function media_fields() {
        ?><script>
            jQuery(document).ready(function($){
                if ( typeof wp.media !== 'undefined' ) {
                    var _custom_media = true,
                        _orig_send_attachment = wp.media.editor.send.attachment;
                    $('.new-media').click(function(e) {
                        var send_attachment_bkp = wp.media.editor.send.attachment;
                        var button = $(this);
                        var id = button.attr('id').replace('_button', '');
                        _custom_media = true;
                        wp.media.editor.send.attachment = function(props, attachment){
                            if ( _custom_media ) {
                                if ($('input#' + id).data('return') == 'url') {
                                    $('input#' + id).val(attachment.url);
                                } else {
                                    $('input#' + id).val(attachment.id);
                                }
                                $('div#preview'+id).css('background-image', 'url('+attachment.url+')');
                            } else {
                                return _orig_send_attachment.apply( this, [props, attachment] );
                            };
                        }
                        wp.media.editor.open(button);
                        return false;
                    });
                    $('.add_media').on('click', function(){
                        _custom_media = false;
                    });
                    $('.remove-media').on('click', function(){
                        var parent = $(this).parents('td');
                        parent.find('input[type="text"]').val('');
                        parent.find('div').css('background-image', 'url()');
                    });
                }
            });
        </script><?php
    }

    public function field_generator( $post ) {
        $output = '';
        foreach ( $this->meta_fields as $meta_field ) {
            $label = '<label for="' . $meta_field['id'] . '">' . $meta_field['label'] . '</label>';
            $meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
            if ( empty( $meta_value ) ) {
                if ( isset( $meta_field['default'] ) ) {
                    $meta_value = $meta_field['default'];
                }
            }
            switch ( $meta_field['type'] ) {
                case 'media':
                    $meta_url = '';
                    if ($meta_value) {
                        if ($meta_field['returnvalue'] == 'url') {
                            $meta_url = $meta_value;
                        } else {
                            $meta_url = wp_get_attachment_url($meta_value);
                        }
                    }
                    $input = sprintf(
                        '<input style="display:none;" id="%s" name="%s" type="text" value="%s"  data-return="%s"><div id="preview%s" style="margin-right:10px;border:1px solid #e2e4e7;background-color:#fafafa;display:inline-block;width: 100px;height:100px;background-image:url(%s);background-size:cover;background-repeat:no-repeat;background-position:center;"></div><input style="width: 19%%;margin-right:5px;" class="button new-media" id="%s_button" name="%s_button" type="button" value="Select" /><input style="width: 19%%;" class="button remove-media" id="%s_buttonremove" name="%s_buttonremove" type="button" value="Clear" />',
                        $meta_field['id'],
                        $meta_field['id'],
                        $meta_value,
                        $meta_field['returnvalue'],
                        $meta_field['id'],
                        $meta_url,
                        $meta_field['id'],
                        $meta_field['id'],
                        $meta_field['id'],
                        $meta_field['id']
                    );
                    break;


                default:
                    $input = sprintf(
                        '<input %s id="%s" name="%s" type="%s" value="%s">',
                        $meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
                        $meta_field['id'],
                        $meta_field['id'],
                        $meta_field['type'],
                        $meta_value
                    );
            }
            $output .= $this->format_rows( $label, $input );
        }
        echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
    }

    public function format_rows( $label, $input ) {
        return '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';
    }

    public function save_fields( $post_id ) {
        if ( ! isset( $_POST['CustomFields_nonce'] ) )
            return $post_id;
        $nonce = $_POST['CustomFields_nonce'];
        if ( !wp_verify_nonce( $nonce, 'CustomFields_data' ) )
            return $post_id;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;
        foreach ( $this->meta_fields as $meta_field ) {
            if ( isset( $_POST[ $meta_field['id'] ] ) ) {
                switch ( $meta_field['type'] ) {
                    case 'email':
                        $_POST[ $meta_field['id'] ] = sanitize_email( $_POST[ $meta_field['id'] ] );
                        break;
                    case 'text':
                        $_POST[ $meta_field['id'] ] = sanitize_text_field( $_POST[ $meta_field['id'] ] );
                        break;
                }
                update_post_meta( $post_id, $meta_field['id'], $_POST[ $meta_field['id'] ] );
            } else if ( $meta_field['type'] === 'checkbox' ) {
                update_post_meta( $post_id, $meta_field['id'], '0' );
            }
        }
    }
}

if (class_exists('CustomFieldsMetabox')) {
    new CustomFieldsMetabox;
};
add_action( 'admin_footer','setDataFromDb' );
add_action( 'save_post', 'setDataFromDb' );
 function setDataFromDb()
 {
     $post_id = get_the_ID();
     $meta_values = get_post_meta($post_id, 'my_img_product', true);
     set_post_thumbnail($post_id, $meta_values);
 }

//clear custom fields
function my_scripts(){
    wp_enqueue_script( 'my-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '01', false );
}
function my_scripts_frontend(){
    wp_enqueue_script( 'my-ajx-js', get_template_directory_uri() . '/assets/js/ajax.js', array('jquery'), '01', false );
}
add_action('admin_head', 'my_scripts');
add_action('wp_enqueue_scripts', 'my_scripts_frontend');


/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}



/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */
