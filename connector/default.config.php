<?
/**
 * Do not modify the default.config.php file, instead, override the settings in the config.php file
 */

$config = [
    'datetimeFormat' => 'm/d/Y g:i A',
    'quality' => 90,
    'defaultPermission' => 0775,

    'sources' => [
        'default' => [],
        'images' => [
            'root' => 'http://tibiamate.local/images',
            'baseurl' => 'http://tibiamate.local/images',
            'maxFileSize' => '8mb',
            'createThumb' => true,
            'extensions' => ['jpg', 'png', 'gif', 'jpeg'],
        ]
    ],

    'createThumb' => true,
    'thumbFolderName' => '_thumbs',
    'excludeDirectoryNames' => ['.tmb', '.quarantine'],
    'maxFileSize' => '8mb',

    'allowCrossOrigin' => false,
    'allowReplaceSourceFile' => true,

    'baseurl' => ((isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/',
    'root' => __DIR__,
    'extensions' => ['jpg', 'png', 'gif', 'jpeg'],
    'imageExtensions' => ['jpg', 'png', 'gif', 'jpeg'],

	'debug' => JODIT_DEBUG,
	'accessControl' => []
];


$config['roleSessionVar'] = 'JoditUserRole';

$config['accessControl'][] = array(
	'role'                => '*',

	'extensions'          => '*',
	'path'                => '/',

	'FILES'               => true,
	'FILE_MOVE'           => true,
	'FILE_UPLOAD'         => true,
	'FILE_UPLOAD_REMOTE'  => true,
	'FILE_REMOVE'         => true,
	'FILE_RENAME'         => true,

	'FOLDERS'             => true,
	'FOLDER_MOVE'         => true,
	'FOLDER_REMOVE'       => true,
	'FOLDER_RENAME'       => true,

	'IMAGE_RESIZE'        => true,
	'IMAGE_CROP'          => true,
);

$config['accessControl'][] = array(
	'role'                => '*',

	'extensions'          => 'exe,bat,com,sh,swf',

	'FILE_MOVE'           => false,
	'FILE_UPLOAD'         => false,
	'FILE_UPLOAD_REMOTE'  => false,
	'FILE_RENAME'         => false,
);

return $config;