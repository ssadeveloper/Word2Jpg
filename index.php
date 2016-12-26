<?php
	ini_set('memory_limit','-1');									//Set memory status unlimited
	require_once './lib/MsWordToImageConvert.php'; //imports the file in Word2Jpg/lib/MsWordToImageConvert.php

	// =============={ Configuration Begin }==============
	$settings = array(

	  // Website title. Displayed of top of the page.
	  'title' => 'Convert Word to Image',

	  // Description for this website
	  'description' => 'This is website where you convert word to image.',

	  // Upload directory. Could be absolute or relative.
	  // Default: auto-detection
	  'base_path' => dirname(__FILE__) . DIRECTORY_SEPARATOR,

	  // Display list uploaded files
	  // Default: true
	  'listfiles' => true,


	  // Allow users to mark files as hidden
	  // Default: true
	  'allow_private' => true,

	  // Display file sizes
	  // Default: true
	  'listfiles_size' => true,

	  // Display file dates
	  // Default: true
	  'listfiles_date' => true,

	  // Display file dates format
	  // Default: 'F d Y H:i:s'
	  'listfiles_date_format' => 'F d Y H:i:s',

	  // Randomize file names. Number for file name lenght or false to disable.
	  // Default: 8
	  'random_name_len' => 1,

	  // Keep filetype (file extension) information (if random name is activated).
	  // Default: true
	  'random_name_keep_type' => true,

	  // Letters that are used for random file name generation (alphabet).
	  // Default: 'abcdefghijklmnopqrstuvwxyz0123456789'
	  'random_name_alphabet' => 'abcdefghijklmnopqrstuvwxyz0123456789',

	  // Display debugging information
	  // Default: false
	  'debug' => false,

	  // Complete URL to your directory with trailing slash (!)
	  // Default: autoDetectBaseUrl()
	  'url' => 'https://trypnauralmeditation.com/go/test/',
	  //'url' => 'http://localhost:8888/',

	  // Amount of seconds that each file should be stored for (0 for no limit)
	  // Default: 30 days (60 * 60 * 24 * 30)
	  'time_limit' => 60 * 60 * 24 * 30,

	  // Files that will be ignored
	  'ignores' => array(
	    '.',
	    '..',
	    'LICENSE',
	    'README.md',
	    basename($_SERVER['PHP_SELF']),
	    'config.php',
	  ),

	  // Language code
	  // Default: 'en'
	  'lang' => 'en',

	  // Language direction
	  // Default: 'ltr'
	  'lang_dir' => 'ltr',

	  // Privacy: Allow external references (the "fork me" ribbon)
	  // Default: true
	  'ribbon_enable' => true,
	);
	// =============={ Configuration End }==============
	// Load local config file if it exists.
	require_once './script/config.php';
	require_once './script/func.php';
	//Specific Setting
	// Files are being POSEed. Uploading them one by one.
	if (isset($_FILES['file'])) {
		//header('Content-type: text/plain');
		if (is_array($_FILES['file'])) {
			$file_array = diverseArray($_FILES['file']);
			ksort($file_array);
			foreach ($file_array as $key=>$file_data) {
					$targetFile = uploadFile($file_data,($key+1).'.png'); //upload doc files and convert to images
			} //END - foreach
		} else {

			$targetFile = uploadFile($_FILES['file'],'1.png');	//upload doc file and convert to images
		}
	}



	//clear $file_array if exists()
	if(isset($file_array) && count($file_array)!=0) unset($file_array);
	$file_array = createArrayFromPath($data['uploaddir']);	//Get all uploaded files
	ksort($file_array,true);																//Soort Files By Its Name

	//Delete files that has been created in privous session
	if (!isset($_FILES['file'])) {
		foreach($file_array as $file){
			deleteFile($file);
			removeOldFiles($file,$data['uploaddir']);
		}
		unset($file_array);
	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$settings['lang']?>" lang="<?=$settings['lang']?>" dir="<?=$settings['lang_dir']?>">
	<head>
		<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
		<meta http-equiv="content-style-type" content="text/css" />
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="language" content="<?=$settings['lang']?>" />

		<meta name="robots" content="noindex" />
		<meta name="referrer" content="origin-when-crossorigin" />
		<title><?=$settings['title']?></title>
		<style type="text/css" media="screen">
			body {
				background: #111;
				margin: 0;
				color: #ddd;
				font-family: sans-serif;
			}

			body > h1 {
				display: block;
				background: rgba(255, 255, 255, 0.05);
				padding: 8px 16px;
				text-align: center;
				margin: 0;
			}

			body > form {
				display: block;
				background: rgba(255, 255, 255, 0.075);
				padding: 16px 16px;
				margin: 0;
				text-align: center;
			}

			body > ul {
				display: block;
				padding: 0;
				max-width: 1000px;
				margin: 32px auto;
			}

			body > ul > li {
				display: block;
				margin: 0;
				padding: 0;
			}

			body > ul > li > a.uploaded_file {
				display: block;
				margin: 0 0 1px 0;
				list-style: none;
				background: rgba(255, 255, 255, 0.1);
				padding: 8px 16px;
				text-decoration: none;
				color: inherit;
				opacity: 0.5;
			}

			body > ul > li > a:hover {
				opacity: 1;
			}

			body > ul > li > a:active {
				opacity: 0.5;
			}

			body > ul > li > a > span {
				float: right;
				font-size: 90%;
			}

			body > ul > li > form {
				display: inline-block;
				padding: 0;
				margin: 0;
			}

			body > ul > li.owned {
				margin: 8px;
			}

			body > ul > li > form > button {
				opacity: 0.5;
				display: inline-block;
				padding: 4px 16px;
				margin: 0;
				border: 0;
				background: rgba(255, 255, 255, 0.1);
				color: inherit;
			}

			body > ul > li > form > button:hover {
				opacity: 1;
			}

			body > ul > li > form > button:active {
				opacity: 0.5;
			}

			body > ul > li.uploading {
				animation: upanim 2s linear 0s infinite alternate;
			}

			@keyframes upanim {
				from {
					opacity: 0.3;
				}
				to {
					opacity: 0.8;
				}
			}
		</style>
	</head>
	<body>
		<h1><?=$settings['title']?></h1>
		<form action="<?= $settings['url'] ?>" method="post" enctype="multipart/form-data" class="dropzone" id="simpleupload-form">
			<?=$data['description']?>
			Choose a file or Drag&amp;Drop. Maximum upload size is <?php echo $data['max_upload_size']; ?>.<br /><br />
			<input type="file" name="file[]" id="simpleupload-input" multiple />
		</form>
		<?php if ($settings['listfiles']) { ?>
			<ul id="simpleupload-ul">
				<?php
					foreach ($file_array as $mtime => $filename) {

						if(pathinfo($filename, PATHINFO_EXTENSION)!='png') continue;
						if($filename=='1.png') continue;
						$fqfn = $data['uploaddir'] . DIRECTORY_SEPARATOR . $filename;
						$file_info = array();
						$file_owner = false;
						$file_private = $filename[0] === '.';

						if ($settings['listfiles_size'])
							$file_info[] = formatSize(filesize($fqfn));

						if ($settings['listfiles_size'])
							$file_info[] = date($settings['listfiles_date_format'], $mtime);


						$file_info = implode(', ', $file_info);

						if (strlen($file_info) > 0)
							$file_info = ' (' . $file_info . ')';

						$class = '';
						if ($file_owner)
							$class = 'owned';

						if (!$file_private || $file_owner) {
							echo "<li class=\"' . $class . '\">";

							// Create full-qualified URL and clean it a bit
							$url = str_replace('/./', '/', sprintf('%s%s', $settings['url'] , $filename));

							echo "<a class=\"uploaded_file\" href=\"$url\" target=\"_blank\">$filename<span>$file_info</span></a>";

							if ($file_owner) {
								if ($settings['allow_deletion'])
									echo '<form action="' . $settings['url'] . '" method="post"><input type="hidden" name="target" value="' . $filename . '" /><input type="hidden" name="action" value="delete" /><button type="submit">delete</button></form>';

								if ($settings['allow_private'])
									if ($file_private)
										echo '<form action="' . $settings['url'] . '" method="post"><input type="hidden" name="target" value="' . $filename . '" /><input type="hidden" name="action" value="privatetoggle" /><button type="submit">make public</button></form>';
									else
										echo '<form action="' . $settings['url'] . '" method="post"><input type="hidden" name="target" value="' . $filename . '" /><input type="hidden" name="action" value="privatetoggle" /><button type="submit">make private</button></form>';
							}

							echo "</li>";
						}
					}
				?>
			</ul>
		<?php
		}

		if ($settings['ribbon_enable']) {
		?>

		<?php
		}
		?>

		<script type="text/javascript">
		<!--
			// Init some variables to shorten code

			var target_form        = document.getElementById('simpleupload-form');
			var target_ul          = document.getElementById('simpleupload-ul');
			var target_input       = document.getElementById('simpleupload-input');
			var settings_listfiles = <?=($settings['listfiles'] ? 'true' : 'false')?>;


			/**
			 * Initializes the upload form
			 */
			function init () {
				// Register drag-over event listener
				target_form.addEventListener('dragover', function (event) {
					event.preventDefault();
				}, false);

				// ... and the drop event listener
				target_form.addEventListener('drop', handleFiles, false);

				// Register onchange-event function
				target_input.onchange = function () {
					addFileLi('Uploading...', '');
					target_form.submit();
				};
			}

			/**
			 * Adds given file in a new li-tag to target_ul list
			 *
			 * @param name Name of the file
			 * @param info Some more informations
			 */
			function addFileLi (name, info) {
				if (settings_listfiles == false) {
					return;
				}

				target_form.style.display = 'none';

				var new_li = document.createElement('li');
				new_li.className = 'uploading';

				var new_a = document.createElement('a');
				new_a.innerHTML = name;
				new_li.appendChild(new_a);

				var new_span = document.createElement('span');
				new_span.innerHTML = info;
				new_a.appendChild(new_span);


				target_ul.insertBefore(new_li, target_ul.firstChild);
			}

			/**
			 * Handles given event for file upload
			 *
			 * @param event Event to handle file upload for
			 */
			function handleFiles (event) {
				event.preventDefault();

				var files = event.dataTransfer.files;

				var form = new FormData();

				for (var i = 0; i < files.length; i++) {
					form.append('file[]', files[i]);
					addFileLi(files[i].name, files[i].size + ' bytes');
				}

				var xhr = new XMLHttpRequest();
				xhr.onload = function() {
					window.location.reload();
				};

				xhr.open('post', '<?php echo $settings['url']; ?>', true);
				xhr.send(form);
			}

			// Initialize upload form
			init();

		//-->
		</script>
	</body>
</html>
