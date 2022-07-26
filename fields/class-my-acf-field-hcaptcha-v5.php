<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('my_acf_field_hcaptcha') ) :


class my_acf_field_hcaptcha extends acf_field {
	
	
	/*
	*  __construct
	*
	*  Cette fonction configurera les données de type de champ
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct( $settings ) 
	{
		
		/*
		*  name (string) Un seul mot, pas d'espaces. Les traits de soulignement sont autorisés
		*/
		
		$this->name = 'hcaptcha';
		
		
		/*
		*  label (string) Plusieurs mots, peuvent inclure des espaces, visibles lors de la sélection d'un type de champ
		*/
		
		$this->label = __('Hcaptcha', 'acf-hcaptcha');
		
		
		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		
		$this->category = 'jquery';
		
		/*
		*  settings (array) Stocker les paramètres du plug-in (url, path, version) comme référence pour une utilisation ultérieure avec des actifs
		*/
		
		$this->settings = $settings;		
		
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Créez des paramètres supplémentaires pour votre champ. Ceux-ci sont visibles lors de l'édition d'un champ
	*
	*/
	
	function render_field_settings( $field ) 
	{
		
		/*
		*  acf_render_field_setting
		*
		*  Cette fonction créera un paramètre pour votre champ. Passez simplement le paramètre $field et un tableau de paramètres de champ.
		*  Le tableau de paramètres ne nécessite pas de « valeur » ou de « préfixe » ; Ces paramètres se trouvent dans le tableau $field.
		*/
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Site key','acf-hcaptcha'),
			'instructions'	=> __('Enter the site key.','acf-hcaptcha'),
			'type'			=> 'text',
			'name'			=> 'site_key',
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Secret key','acf-hcaptcha'),
			'instructions'	=> __('Enter the Secret key.','acf-hcaptcha'),
			'type'			=> 'text',
			'name'			=> 'secret_key',
		));
	}

	/*
	*  render_field()
	*
	*  Créez l'interface HTML de votre champ
	*
	*/
	
	function render_field( $field ) 
	{
		/*
		*  Créez l'interface HTML du champ Hcaptcha dans le back office
		*/
        ?>
		    <input type="hidden" name="<?php echo esc_attr($field['name']) ?>" value="1" />
		<?php

        	if (!empty($field['site_key'])) 
		{
			echo('<div class="h-captcha" data-sitekey="'. $field['site_key'] .'"></div>');
		}
	}
	
		
	/*
	*  input_admin_enqueue_scripts()
	*
	*  Cette action est appelée dans l'action admin_enqueue_scripts sur l'écran d'édition où votre champ est créé
	*  Utilisez cette action pour ajouter CSS + JavaScript pour assister votre action render_field().
	*
	*/
	function input_admin_enqueue_scripts() 
	{
		
		// vars
		$version = $this->settings['version'];
		
		// register & include JS
		wp_register_script('acf-hcaptcha', "https://www.hCaptcha.com/1/api.js", array(), $version);
		wp_enqueue_script('acf-hcaptcha');
		
	}


	/*
	*  validate_value()
	*
	*  Ce filtre permet d'effectuer une validation sur la valeur avant l'enregistrement.
	*  Toutes les valeurs sont validées quel que soit le paramètre requis du champ. Cela vous permet de valider et de retourner
	*  messages à l'utilisateur si la valeur n'est pas correcte
	*
	*  @param	$valid (boolean) statut de validation basé sur la valeur et le paramètre requis du champ
	*  @param	$value (mixed) la valeur $_POST
	*  @param	$field (array) le tableau de champs contenant toutes les options de champ
	*  @param	$input (string) le nom d'entrée correspondant pour la valeur $_POST
	*  @return	$valid
	*/
	
	function validate_value( $valid, $value, $field, $input )
	{
		$data = array(
		            'secret' => $field['secret_key'],
		            'response' => $_POST['h-captcha-response']
		        );

		$verify = curl_init();
		curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
		curl_setopt($verify, CURLOPT_POST, true);
		curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($verify);
		// var_dump($response);
		$responseData = json_decode($response);

		//	if(/* $value != $responseData ||*/ $responseData->succes )
		// {
		// 	$valid = __('Protection antispam non validée.','acf-hcaptcha');
		// }
		
		
		// return
		return $valid;
		
	}

}


// initialize
new my_acf_field_hcaptcha( $this->settings );


// class_exists check
endif;

?>