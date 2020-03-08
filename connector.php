<?php
define('JODIT_DEBUG', false);
require_once __DIR__.'/connector/vendor/autoload.php';
require_once __DIR__.'/connector/Application.php';

$images_rel_path = 'public/images/';

$config = [
    'datetimeFormat' => 'd/m/Y g:i A',
    'quality' => 90,
    'defaultPermission' => 0775,
    'sources' => [
        'TibiaMate Images' => [
            'root' => $images_rel_path,
            'baseurl' => $images_rel_path,
            'maxFileSize' => '4000kb',
            'createThumb' => false,
            'extensions' => ['jpg', 'png', 'gif', 'jpeg', 'bmp', 'svg', 'ico'],
        ]
    ],
    'createThumb' => false,
    'thumbFolderName' => '_thumbs',
    'excludeDirectoryNames' => ['.tmb', '.quarantine'],
    'maxFileSize' => '8mb',
    'allowCrossOrigin' => false,
    'allowReplaceSourceFile' => true,
  //'baseurl' => ((isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/',
    'baseurl' => $images_rel_path,
  //'root' => '/',
    'root' => $images_rel_path,
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
    'FILE_UPLOAD_REMOTE'  => false,
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
    'extensions'          => 'exe,bat,com,sh,swf,php,js',
    'FILE_MOVE'           => false,
    'FILE_UPLOAD'         => false,
    'FILE_UPLOAD_REMOTE'  => false,
    'FILE_RENAME'         => false,
);

$action = '';
if (isset($_POST['action'])) { $action = trim($_POST['action']); }
if ($action == '') {
  if (isset($_GET['action'])) { $action = trim($_GET['action']); }
}

$fileBrowser = new \JoditRestApplication($config);

try {
  if ($action != '') { $fileBrowser->action = $action; }
  $fileBrowser->checkAuthentication();
  $fileBrowser->execute();
} catch(\ErrorException $e) {
  $fileBrowser->exceptionHandler($e);
}

exit;
