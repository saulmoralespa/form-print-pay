<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 29/08/17
 * Time: 09:06 AM
 */

class Form_Print_Pay_Admin
{
	public function __construct()
	{
		$this->name = fpp_form_print_pay()->name;
		$this->plugin_url = fpp_form_print_pay()->plugin_url;
		$this->version = fpp_form_print_pay()->version;
		add_action('admin_init', array($this, 'fpp_form_print_pay_metabox'));
		add_action('admin_menu', array($this, 'loadMenuFormPrintPay'));
		add_action('wp_ajax_fpp_form_print_pay',array($this,'ajax_fpp_form_print_pay'));
		add_action('wp_ajax_nopriv_fpp_form_print_pay',array($this,'ajax_fpp_form_print_pay'));
	}

	public function fpp_form_print_pay_metabox()
	{
		add_action('add_meta_boxes', array($this,'fpp_form_print_pay_custom_metabox'), 0);
	}

	public function fpp_form_print_pay_get_meta( $value )
	{
		global $post;
		$field = get_post_meta( $post->ID, $value, true );
		if ( ! empty( $field ) ) {
			return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
		} else {
			return false;
		}
	}

	public function fpp_form_print_pay_custom_metabox()
	{
		add_meta_box(
			'fpp_form_print_pay-form-print-pay',
			__('Form print pay', 'form-print-pay' ),
			array($this,'metabox_fpp_form_print_pay'),
			array('fpp_form_print_pay'),
			'normal',
			'high'
		);
	}

	public function metabox_fpp_form_print_pay($post)
	{
		wp_nonce_field('_fpp_form_print_pay_nonce','fpp_form_print_pay_nonce');
		$meta_custom = get_post_meta($post->ID,'fpp_form_print_pay_meta', true);  ?>
		<div class="container-fluid">
			<div class="row">
						<table class="table table-striped">
							<thead>
							<tr>
								<th>Texto para el usuario</th>
								<th>Nombre del campo</th>
								<th>Tipo</th>
								<th>Valor x defecto</th>
								<th>Quitar</th>
							</tr>
							</thead>
							<tbody id="template">
                            <?php

                            if (isset($meta_custom['text_user']))
                            {
                                $user = 'text_user';
                                $type = 'type_user';
                                $field = 'field_name';
                                $value = 'default_value';

	                            $valselect = 'valselect';
	                            $select_user = 'select_user';
	                            $select_name = 'select_name';

                                for ($i=0; $i < count($meta_custom[$user]); $i++) {
                                    ?>
							<tr>
								<td><input type="text" class="form-control" name="text_x_user[]" data-fom-print="data" value="<?php  echo $meta_custom[$user][$i]; ?>"></td>
								<td><input type="text" class="form-control" name="field_name[]" data-fom-print="data" value="<?php  $name = explode( '_', $meta_custom[$field][$i] ); echo $name[0] . "\" "; if($name[0] == 'email' || $name[0] == 'nameproduct' || $name[0] == 'price') echo "readonly"; ?>></td>
								<td>

									<?php if ($meta_custom[$type][$i] == 'hidden'){
									    echo "<select class=\"form-control\" name=\"type[]\" data-fom-print=\"data\">   <option value='{$meta_custom[$type][$i]}' selected>Oculto</option></select>";

									}else{
									    ?>
                                    <select class="form-control" name="type[]" data-fom-print="data">
                                    <option value="text" <?php if ($meta_custom[$type][$i] == 'text'){echo 'selected';} ?>>Texto</option>
                                    <option value="email" <?php if ($meta_custom[$type][$i] == 'email'){echo 'selected';} ?>>Email</option>
                                    <option value="number" <?php if ($meta_custom[$type][$i] == 'number'){echo 'selected';} ?>>Numérico</option>
                                    <option value="tel" <?php if ($meta_custom[$type][$i] == 'tel'){echo 'selected';} ?>>Teléfono</option>
                                    <option value="hidden" <?php if ($meta_custom[$type][$i] == 'hidden'){echo 'selected';} ?>>Oculto</option>
                                    <option value="select" <?php if ($meta_custom[$type][$i] == 'select'){echo 'selected';} ?>>Select</option>
                                    <option value="textarea" <?php if ($meta_custom[$type][$i] == 'textarea'){echo 'selected';} ?>>Textarea</option>
                                    </select>
                                <?php   } ?>
								</td>
								<td><input type="text" class="form-control" name="default_value[]" data-fom-print="data" value="<?php echo $meta_custom[$value][$i]; ?>"></td>
								<td><button class="btn btn-danger" title="Eliminar" data-action="remove" data-toggle="tooltip"><span class="fa fa-times"></span>X</button> </td>
							</tr>
	                                <?php
                                }
                                ?>
                                </tbody>
                                </table>
                                <button type="button" class="button btn btn-success" id="new-field">Nuevo Campo</button>
                                <input type="hidden" name="idpost" data-fom-print="data" value="<?php echo $post->ID; ?>">
                                <button type="button" class="button" id="submit_form_print"><?php echo __('Guardar formulario','form-print-pay');?></button>
                                <table class="form-table">
                                    <div id="text_pdf_form_print">
                                        <tr>
                                            <th>Texto documento impreso</th>
                                        </tr>
                                        <tr>
                                            <td><textarea name="text_form_print_pdf" id="text_form_print_pdf" style="width: 100%;" rows="5"><?php if (isset($meta_custom['text_pdf'])) echo $meta_custom['text_pdf']; ?></textarea></td>
                                        </tr>
                                        <tr>
                                            <td><button type="button" class="button" id="save_text_form_pdf">Guardar Cambios</button></td>
                                        </tr>
                                    </div>
                                </table>
                                <div class="overlay-form-print-pay" style="display: none;">
                                    <div class="overlay-content-form-print-pay">
                                        <img src="<?php echo fpp_form_print_pay()->plugin_url . 'assets/img/loading.gif';?>" alt="">
                                    </div>
                                </div>
                                </div>
                                </div>
                                <?php
                            }else {
	                            ?>
                                <tr>
                                    <td><input type="text" class="form-control" name="text_x_user[]" data-fom-print="data" value="" readonly></td>
                                    <td><input type="text" class="form-control" name="field_name[]" data-fom-print="data" value="price" readonly></td>
                                    <td>
                                        <select class="form-control" name="type[]" data-fom-print="data">
                                            <option value="hidden" selected>Oculto</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="default_value[]" id="price_form_print" data-fom-print="data" value=""></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="text_x_user[]" data-fom-print="data" value="" readonly></td>
                                    <td><input type="text" class="form-control" name="field_name[]" data-fom-print="data" value="nameproduct" readonly></td>
                                    <td>
                                        <select class="form-control" name="type[]" data-fom-print="data">
                                            <option value="hidden" selected>Oculto</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="default_value[]" id="description_form_print" data-fom-print="data" value=""></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="text_x_user[]" data-fom-print="data" value="" ></td>
                                    <td><input type="text" class="form-control" name="field_name[]" data-fom-print="data" value="email" readonly></td>
                                    <td>
                                        <select class="form-control" name="type[]" data-fom-print="data">
                                            <option value="email" selected>Email</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="default_value[]" id="email_form_print" data-fom-print="data" value="name@domain.com"></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="text_x_user[]" data-fom-print="data" value=""></td>
                                    <td><input type="text" class="form-control" name="field_name[]" data-fom-print="data" value=""></td>
                                    <td>
                                        <select class="form-control" name="type[]" data-fom-print="data">
                                            <option value="text">Texto</option>
                                            <option value="email">Email</option>
                                            <option value="number">Numérico</option>
                                            <option value="tel">Teléfono</option>
                                            <option value="hidden">Oculto</option>
                                            <option value="select">Select</option>
                                            <option value="textarea">Textarea</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="default_value[]" data-fom-print="data" value=""></td>
                                    <td><button class="btn btn-danger" title="Eliminar" data-action="remove" data-toggle="tooltip"><span class="fa fa-times"></span>X</button> </td>
                                </tr>
                                </tbody>
                                </table>
                                <button type="button" class="button btn btn-success" id="new-field">Nuevo Campo</button>
                                <input type="hidden" name="idpost" data-fom-print="data" value="<?php echo $post->ID; ?>">
                                <button type="button" class="button" id="submit_form_print"><?php echo __('Guardar formulario','form-print-pay');?></button>
                                <div id="text_pdf_form_print" style="display: none;">
                                <table class="form-table">
                                        <tr>
                                            <th>Texto documento impreso</th>
                                        </tr>
                                        <tr>
                                            <td><textarea name="text_form_print_pdf" id="text_form_print_pdf" style="width: 100%;" rows="5"></textarea></td>
                                        </tr>
                                        <tr>
                                            <td><button type="button" class="button" id="save_text_form_pdf">Guardar Cambios</button></td>
                                        </tr>
                                </table>
                                </div>
                                <div class="overlay-form-print-pay" style="display: none;">
                                    <div class="overlay-content-form-print-pay">
                                        <img src="<?php echo fpp_form_print_pay()->plugin_url . 'assets/img/loading.gif';?>" alt="">
                                    </div>
                                </div>
                                </div>
                                </div>
	                            <?php
                            }
	}

	public function loadMenuFormPrintPay()
	{
		$configuracion = fpp_form_print_pay()->Config;
		$paypal = fpp_form_print_pay()->configPaypal;
		$pdf =  fpp_form_print_pay()->configPdf;
		$email =  fpp_form_print_pay()->configEmail;
		add_menu_page($this->name, ucfirst($this->name), 'manage_options', 'menus'. $this->name, array($this,'menu'. $this->name), $this->plugin_url .'icon.jpg');
		add_submenu_page('menus' . $this->name, 'Ordenes pendientes', 'Ordenes pendientes', 'manage_options', 'config-' . $this->name,array($configuracion,'configInit'));
		add_submenu_page('menus' . $this->name, 'paypal', 'paypal', 'manage_options', 'configpaypal-' . $this->name,array($paypal,'configInit'));
		add_submenu_page('menus' . $this->name, 'PDF', 'PDF', 'manage_options', 'configpdf-' . $this->name,array($pdf,'configInit'));
		add_submenu_page('menus' . $this->name, 'Email', 'Email', 'manage_options', 'configemail-' . $this->name,array($email,'configInit'));
		remove_submenu_page('menus'. $this->name, 'menus'.$this->name);
		add_action('admin_head', array($this, 'head_menu'));
		add_action('admin_footer', array($this,'footer_menu'));
	}

	public function head_menu()
    {
        wp_enqueue_style('css-ui-form-print','//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', null);
	    wp_enqueue_style('css-form-print-pay-admin',$this->plugin_url."assets/css/form-print-pay.css", array(), $this->version, null);
    }

	public function footer_menu()
	{

	    wp_enqueue_script('admin-config-form-print-pay', $this->plugin_url."assets/js/config.js", array('jquery'), $this->version, true);
		wp_enqueue_script('jquery-ui-form-print','https://code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery'), $this->version, true);
		if ( 'fpp_form_print_pay' == get_post_type() ){
		    $meta_custom = get_post_meta(get_the_ID(),'fpp_form_print_pay_meta',true);
		    if (count($meta_custom) == 5){
			    $array = array('field_name' => $meta_custom['field_name']);
			    wp_localize_script( 'admin-config-form-print-pay', 'fpp_form_print_pay', $array );
            }
        }
	}


	public function ajax_fpp_form_print_pay()
	{
	    if($_POST['field_name']){
		    $names_fpp_form_print_pay = array();
			foreach ($_POST['field_name'] as $key => $value){
				$names_fpp_form_print_pay[$key] = $value . '_print';
			}
		}
		if (isset($_POST['text_x_user'])){
			$text_user = array();
			$text_user = $_POST['text_x_user'];
		}

		if (isset($_POST['type'])){
			$type_user = array();
			$type_user = $_POST['type'];
		}
		if (isset($_POST['default_value'])){
			$default_value = array();
			$default_value = $_POST['default_value'];
		}


		if (isset($names_fpp_form_print_pay)){
			$array = array('field_name' => $names_fpp_form_print_pay, 'text_user' => $text_user, 'type_user' => $type_user, 'default_value' => $default_value);
			update_post_meta($_POST['idpost'], 'fpp_form_print_pay_meta', $array);
			$meta_custom = get_post_meta($_POST['idpost'], 'fpp_form_print_pay_meta', true);
			$meta_custom = $meta_custom['field_name'];
			$array_merge = array_merge( array( 'fielname' => $meta_custom), array( 'id' => $_POST['idpost']));
			echo json_encode($array_merge);
		}

		if (isset($_POST['text_form_print_pdf'])){

			$meta_custom = get_post_meta($_POST['idpost'], 'fpp_form_print_pay_meta', true);
			$array_merge = array_merge($meta_custom, array('text_pdf' => $_POST['text_form_print_pdf']));
			update_post_meta($_POST['idpost'], 'fpp_form_print_pay_meta',$array_merge);
		}
		if (isset($_POST['usersuject'])){
		    unset($_POST['action']);
		    update_option('form-print-email', $_POST);
        }
        if (isset($_POST['font-form-print'])){
		    unset($_POST['action']);
		    update_option('config-pdf-form-print', $_POST);
        }
        if (isset($_FILES['logo-pdf-form-print'])){

	        if(!empty($_FILES['logo-pdf-form-print']['name'])) {

		         // Setup the array of supported file types. In this case, it's just PDF.
		        $supported_jpg = array('image/jpeg');

		        // Get the file type of the upload
		        $arr_file_type = wp_check_filetype(basename($_FILES['logo-pdf-form-print']['name']));
		        $uploaded_type = $arr_file_type['type'];

		        // Check if the type is supported. If not, throw an error.
		        if(in_array($uploaded_type, $supported_jpg)) {

			        if (!is_dir(fpp_form_print_pay()->uploads_dir))
			            fpp_form_print_pay()->createDirUploads(fpp_form_print_pay()->uploads_dir);
		                $this->uploadImg('jpg');

		        }else {
			        wp_die(__("The file type that you've uploaded is not a html.","form-print-pay"));
		        } // end if/else

	        }

        }
		if (isset($_POST['test-form-print'])){
            $array = array('test' => $_POST['test-form-print'], 'api_user' => $_POST['form-api-user'], 'api_password' => $_POST['form-api-password'], 'api_signature' => $_POST['form-api-signature'], 'currency' => $_POST['form-currency']);
			update_option('form-print_pay_paypal',$array);
		}
		if (isset($_POST['form_print'])){
		    unset($_POST['form_print']);
		    unset($_POST['action']);
		    $price = $_POST['price_print'];
			$description = $_POST['description_print'];
			$email = $_POST['email_print'];
		    $urlform = $_POST['url_form'];
		    unset($_POST['url_form']);

		    $uniquid = $_POST['uniquid_form_print'];
		    unset($_POST['uniquid_form_print']);
		    $idpost = explode('_',$uniquid);
		    $idpost = $idpost[0];

		    $meta_custom = get_post_meta($idpost,'fpp_form_print_pay_meta', true);
		    $array_merge = array_merge($meta_custom, array($uniquid => $_POST));
            update_post_meta($idpost,'fpp_form_print_pay_meta',$array_merge);

			$products = [];

			$products[0]['ItemName'] = $description;
			$products[0]['ItemPrice'] = $price; //Item Price
			$products[0]['ItemDesc'] = $description; //Item Number
			$products[0]['ItemQty']	= "1";
			$products[0]['url_form'] = $urlform;
			$products[0]['email'] = $email;
			$products[0]['uniquid'] = $uniquid;

            $paypal = fpp_form_print_pay()->Paypal;
			$paypal->SetExpressCheckOut($products);

        }

        if(isset($_POST['tokenpaypal'])){
	        $paypal = fpp_form_print_pay()->Paypal;
	        $paypal->DoExpressCheckoutPayment($_POST['tokenpaypal'],$_POST['PayerIDpaypal'],$_POST['pdata']);
        }

        if (isset($_POST['statusorder'])){
		    $paypal = fpp_form_print_pay()->Paypal;
		    $id = (int)$_POST['id'];
		    $paypal->action_status_paypal($id,$_POST['uniquid'],$_POST['email'],$_POST['transactionid']);
        }

		die();
	}

	function uploadImg($type)
    {
	    if (is_writable(fpp_form_print_pay()->plugin_path . "icon.jpg")){
		    // Use the WordPress API to upload the file
		    $upload = wp_upload_bits($_FILES['logo-pdf-form-print']['name'], null, file_get_contents($_FILES['logo-pdf-form-print']['tmp_name']));
		    $logo = fpp_form_print_pay()->uploads_dir . "img/logopdf.$type";
		    if(isset($upload['error']) && $upload['error'] != 0) {
			    $array = array('status' => false, 'message' => __('There was an error uploading your file. The error is: ','form-print-pay') . $upload['error']);
			    die(json_encode($array));
		    } else {
			    chmod($upload['file'],0777);
			    if(rename($upload['file'],$logo) && file_exists($logo)){
				    $array = array('status' => true);
				    die(json_encode($array));
			    }else{
				    $array = array('status' => true, 'message' => 'No se ha podido subir la imágen intente de nuevo');
				    die(json_encode($array));
                }
		    }
	    }else{
		    $array = array('status' => false, 'message' => 'Se necesitan permisos de escritura');
		    die(json_encode($array));
        }
    }
}