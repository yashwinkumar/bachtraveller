<?php 
/**
* @subpackage Traveler
* @since 1.0.9
**/

if(!class_exists('STAdminUploadIcon')){

	class STAdminUploadIcon extends STAdmin{

		private $upload_font_folder = "st_uploadfont"; // Upload folder name
		private $path = "/fonts/packages"; // Path to upload sub folder name
		private $path_folder = ''; // Path to package folder
		private $path_file_css = ''; // Path to css file in package folder
		private $foldername_font = ''; // Name of package folder
		private $message = ''; // Messages

		public function __construct(){
			ob_start();
			$this->add_enqueue_script();
			$this->createUploadFolder();
			$this->setFolderUpload();
			$this->create_submenu();
		}

		/** 
		* @since 1.0.9
		**/
		public function setFolderUpload(){
			add_filter('upload_dir', array($this,'_awesome_fonticon_dir'));
		}

		/** 
		* @since 1.0.9
		* @return custom upload folder
		**/
		public function _awesome_fonticon_dir($param){
			$param['path'] = $param['basedir'].'/'.$this->upload_font_folder;
		    $param['url'] = $param['baseurl'].'/'.$this->upload_font_folder;

		    return $param;
		}

		/** 
		* @since 1.0.9
		* Create upload folder
		**/
		public function createUploadFolder(){

			$upload_dir = wp_upload_dir();
			$upload_folder =$upload_dir['basedir'];

			$path = $upload_folder.'/'.$this->upload_font_folder;

			if(!is_dir($path))
				mkdir($path, 0755, true);
		}

		/** 
		* @since 1.0.9
		**/
		public function create_submenu(){
			add_action('admin_menu', array($this,'_register_uploadfont_submenu_page'), 50);

		}

		/** 
		* @since 1.0.9
		**/
		public function _register_uploadfont_submenu_page() {
			    add_submenu_page(apply_filters('ot_theme_options_menu_slug','st_traveler_options'), 'Importer Fonticon', 'Importer Fonticon', 'manage_options', 'st-upload-custom-fonticon', array($this,'_st_upload_icon_content' ));
		}

		/** 
		* @since 1.0.9
		* Callback 
		**/
		public function _st_upload_icon_content(){
			/* Upload font */
			if(isset($_POST['upload-font'])){

				$font_file = $_FILES['font-file'];

				if(strchr($font_file['name'], '.zip')){

					$movefile = $this->saveFontfile($font_file); 

					if ($movefile && !isset( $movefile['error'] )) {

						  $unzipfile = $this->unzipFontfile($movefile['file'], $font_file['name']);

					   	if($unzipfile){

					   		unlink( $movefile['file'] );

					   		/* save fonticon data */

					   		$new_item = $this->_getContentFont();

					   		$newMetaFont = $this->newMetaFont($new_item);

					   		if($this->updateFont($newMetaFont)){

					      		$this->message = 'Successful';  

					      		$this->show_uploadfont_messsage('updated');
					   		}
					      	else{

					      		$this->message = 'Error upload new fonts';  

					      		$this->show_uploadfont_messsage('error'); 
					      	}  
					   	}else{
					      	$this->message = 'Error';   

					      	$this->show_uploadfont_messsage('error');    
					   	}
					}else{
						$this->message = $movefile['error'];

					    $this->show_uploadfont_messsage('error');
					}
				}else{

					$this->message = 'Not a zip file';
					$this->show_uploadfont_messsage('error');
				}
			}

			/* Delete font */
			if(isset($_GET['deletefont'])){

				$fontname = $_GET['deletefont'];

				$listfont = get_option('st_list_fonticon_', array());

				if(is_array($listfont) && count($listfont[$fontname])){

					$path_folder = $listfont[$fontname]['path_folder'];

					array_map('unlink', glob($path_folder.'/*'));

					$rmdir = rmdir($path_folder);

					unset($listfont[$fontname]);

					update_option('st_list_fonticon_', $listfont);

					if($rmdir){

						wp_redirect(admin_url('/admin.php?page=st-upload-custom-fonticon'));

					}else{

						$this->message = 'Remove error';

						$this->show_uploadfont_messsage('error');
					}
				}else{
					$this->message = 'This font do not exits.';

					$this->show_uploadfont_messsage('error');
				}
			}
			?>
			
			<!-- List fonts -->
			<?php if(isset($_GET['listfont'])) : 

				$data = array(
					'fontname' => $_GET['listfont'],
					'listfont' => get_option('st_list_fonticon_', array())
				);

				echo balanceTags($this->load_view('upload_fonticon/view_font', false, $data)); 
				
			?>
			<?php else:

				$data = array(
					'list_fonts' => get_option('st_list_fonticon_', array()),
					);

				echo balanceTags($this->load_view('upload_fonticon/index', false, $data)); 

			endif;
	     
		}

		/** 
		* @since 1.0.9
		**/
		public function saveFontfile($font_file = ''){

			$upload_overrides = array( 'test_form' => false );

			$movefile = wp_handle_upload( $font_file, $upload_overrides );

			return $movefile;
		}

		/** 
		* @since 1.0.9
		**/
		public function unzipFontfile($file = '', $fontname = ''){

			$this->foldername_font = $this->create_folder($fontname);

			$unzipfile = unzip_file( $file, $this->path_folder);

			return $unzipfile;
		}

		/** 
		* @since 1.0.9
		**/
		public function create_folder($fontname){

			WP_Filesystem();

			$destination = wp_upload_dir();

			$destination_path = $destination['path'];

			$name = sanitize_title(str_replace('.zip', '', $fontname));

			$last_number = '';

			preg_match_all('/[\d]+/', $name , $last_number);

            $end_item=end($last_number[0]);
			if(!empty($end_item)){

				$name = substr($name, 0, strrpos($name, end($last_number[0])));

				$last_number = intval(end($last_number[0]));

			}else{
				$last_number = '';
			}

			$i = 1;

			$new_name = '';

			do{

				$new_name = $name.$last_number;

				$last_number = $i;

				$i++;
			}
			while(is_dir($destination_path.'/'.$new_name));

			$name = empty($new_name)? $name : $new_name;

			mkdir($destination_path.'/'.$name, 0755);

			$this->path_folder = $destination_path.'/'.$name;

			$this->path_file_css = $destination['url'].'/'.$name;

			return $name;
		}

		/** 
		* @since 1.0.9
		**/
		public function _getContentFont(){
			$contents = file_get_contents($this->path_file_css.'/flaticon.css');

			$content_rewite = str_replace('flaticon-', 'flaticon-'.$this->foldername_font.'-', $contents);
			$content_rewite = str_replace('Flaticon', 'Flaticon-'.$this->foldername_font, $content_rewite);
			$content_rewite = str_replace('font-size: 20px', 'font-size: 18px'.$this->foldername_font, $content_rewite);
			$content_rewite = str_replace('margin-left: 20px;', 'margin-left: 0px;'.$this->foldername_font, $content_rewite);
			/* Read and rewite file css */
			$handle = fopen($this->path_folder.'/flaticon.css', 'w') or die('Cannot open file:  '.$this->path_folder.'/flaticon.css');
			fwrite($handle, $content_rewite);

			$items = array();
			preg_match_all("/flaticon\D[a-z0-9]*\D[a-z0-9]*:/", $content_rewite, $items);
			foreach($items[0] as $key => $val){
				$items[0][$key] = str_replace(':', '', $val);
			}
			$item = array(
				$this->foldername_font => array(
					'icon_list' => $items[0],
					'path_folder' => $this->path_folder,
					'link_file_css' => $this->path_file_css.'/flaticon.css'
					)
				);
			return $item;
		}

		/** 
		* @since 1.0.9
		**/
		public function newMetaFont($new_item){
			$old_item = get_option('st_list_fonticon_', array());
			if(!is_array($old_item))
				$old_item = array();
			$old_item = array_merge($new_item, $old_item);
			return $old_item;
		}

		/** 
		* @since 1.0.9
		**/
		public function updateFont($arr){
			return update_option('st_list_fonticon_', $arr );
		}

		/** 
		* @since 1.0.9
		**/
		public function add_enqueue_script(){
			add_action('admin_enqueue_scripts',array(& $this,'add_script'));
		}

		/** 
		* @since 1.0.9
		**/
		public function add_script(){

			$listfont = get_option('st_list_fonticon_', array());

			if(is_array($listfont) && count($listfont))

				foreach($listfont as $key => $val){

					wp_enqueue_style($key, $val['link_file_css']);
				}

			wp_enqueue_script('update-fonticon', get_template_directory_uri() . '/js/admin/upload-fonticon.js', array('jquery'), null, true);
		}

		/** 
		* @since 1.0.9
		**/
		public function show_uploadfont_messsage($type = 'updated '){
			echo '
				<div class="'.$type.'">
					<p>'.$this->message.'</p>
				</div>
			';
		}

	}
	$st_upload_font = new STAdminUploadIcon();
}
?>