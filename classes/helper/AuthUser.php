<?php

namespace helper;

use core\DB;

class AuthUser {

	private static $userObj = [];
	private static $groups = [];
	private static $permissions = [];
	private static $authenticated = false;

	public static function init() {
		if(isset($_COOKIE['np_login']) && !empty($_COOKIE['np_login'])) {
			self::$userObj = DB::selectFirst("SELECT *
											  FROM accounts WHERE
											  login_hash = ?", [$_COOKIE['np_login']]);
			if(!empty(self::$userObj)){
				self::$authenticated = true;
				self::$groups = explode(",", self::$userObj['groups']);

				// Set permissions
				$dbPerms = DB::select("SELECT * FROM accounts_permissions WHERE
											  account_id = ?", [self::$userObj['id']]);

				foreach($dbPerms AS $perms) {
					// Fix all permissions
					self::$permissions[$perms['parent_id']][$perms['sub_id']] = 1;
				}

				$rurl = $_SERVER['REQUEST_URI'];
				$rurl = str_replace(strstr($rurl,"?"),"",$rurl);
				$rurlAs = [];
				$rurlArray = explode("/",$rurl);
				if(!empty($rurlArray)) {
					foreach($rurlArray as $rurlA) {
						if(!empty($rurlA)) {
							$rurlAs[] = $rurlA;
						}
					}
				}
				// CHECK FOR FACEBOOK USERNAME CREATION BEFORE LOGIN
				if(empty(self::$userObj['username']) && $rurlAs[0] != "verification") {
					header("Location: /verification/username");
					exit;
				}

                // Check if we want to upload a profile image
                if(isset($_GET['delete_cosmetic_image'])){
                    self::delete_cosmetic_image();
                }

                // Check if we want to upload a profile image
                if(isset($_FILES['upload_cosmetic_image'])){
                    self::upload_cosmetic_image();
                }

                // Check if we want to upload a profile image
                if(isset($_FILES['upload_profile_image'])){
                    self::upload_profile_image();
                }

                // Check if we want to edit the profile text
                if(isset($_POST['member_profile_text_new'])){
                    $text = strip_tags($_POST['member_profile_text_new']);
                    self::update_profile_text($text);
                }

                // Check if we want to delete the profile image
                if(isset($_GET['delete_profile_image'])){
                    self::delete_profile_image();
                }

				// UPDATE USER ACTIVITY
				self::updateLastActivity();
			}
		}
	}

    private static function delete_cosmetic_image() {
        unlink(INTERNAL_PATH . "/public/images/cosmetics/default/" . $_GET['delete_cosmetic_image']);
    }

    private static function upload_cosmetic_image() {
        $allowedExts = array("jpg", "jpeg", "gif", "png");

        @$extension = end(explode(".", $_FILES["upload_cosmetic_image"]["name"]));

        if(in_array($extension, $allowedExts)){
            $profileImage = "cosmetic_" . time() . "." . $extension;

            if(move_uploaded_file($_FILES["upload_cosmetic_image"]["tmp_name"], INTERNAL_PATH . "/public/images/cosmetics/default/" . $profileImage)){
                //self::resize(INTERNAL_PATH . "/public/images/members/" . $profileImage, INTERNAL_PATH . "/public/images/members/" . $profileImage, 75, 75);
                return true;
            }
            else {
                echo "Couldn't upload new image.";
            }
        }
    }

    private static function update_profile_text($profileText) {
        DB::execute("UPDATE accounts SET profile_text = ? WHERE id = ? ", [$profileText, AuthUser::getId()]);
    }

    private static function delete_profile_image() {
        if(file_exists(INTERNAL_PATH . "/public/images/members/" . AuthUser::getProfileImage())){
            // the image already exists, remove it
            @unlink(INTERNAL_PATH . "/public/images/members/" . AuthUser::getProfileImage());
        }
        DB::execute("UPDATE accounts SET profile_image = '' WHERE id = ?",[AuthUser::getId() ]);
    }

    private static function upload_profile_image() {

        $allowedExts = array("jpg", "jpeg", "gif", "png");

        @$extension = end(explode(".", $_FILES["upload_profile_image"]["name"]));

        if(in_array($extension, $allowedExts)){
            $profileImage = "member_" . AuthUser::getId() . "." . $extension;

            if(move_uploaded_file($_FILES["upload_profile_image"]["tmp_name"], INTERNAL_PATH . "/public/images/members/" . $profileImage)){
                self::resize(INTERNAL_PATH . "/public/images/members/" . $profileImage, INTERNAL_PATH . "/public/images/members/" . $profileImage, 75, 75);
                DB::execute("UPDATE accounts SET profile_image = '$profileImage' WHERE id = '" . AuthUser::getId() . "'");
                // ob_flush();
            }
        }
    }

    private static function resize($old_path, $new_path, $width, $height, $real_ratio = false) {
        list($source_image_width, $source_image_height, $source_image_type) = getimagesize($old_path);
        $upload_image = false;
        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                $upload_image = imagecreatefromgif($old_path);
                break;
            case IMAGETYPE_JPEG:
                $upload_image = imagecreatefromjpeg($old_path);
                break;
            case IMAGETYPE_PNG:
                $upload_image = imagecreatefrompng($old_path);
                break;
        }

        if ($upload_image === false) {
            return false;
        }

        if(!$real_ratio) {
            $source_aspect_ratio = $source_image_width / $source_image_height;
            $thumbnail_aspect_ratio = $width / $height;
            if ($source_image_width <= $width && $source_image_height <= $height) {
                $thumbnail_image_width = $source_image_width;
                $thumbnail_image_height = $source_image_height;
            } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
                $thumbnail_image_width = (int) ($height * $source_aspect_ratio);
                $thumbnail_image_height = $height;
            } else {
                $thumbnail_image_width = $width;
                $thumbnail_image_height = (int) ($width / $source_aspect_ratio);
            }
        }
        else {
            $thumbnail_image_width = $width;
            $thumbnail_image_height = $height;
            $source_image_width = $width;
            $source_image_height = $height;
        }

        $new_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
        imagecopyresampled($new_image, $upload_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
        imagejpeg($new_image, $new_path, 100);
        imagedestroy($upload_image);
        imagedestroy($new_image);
        return true;
    }


	public static function updateLastActivity() {
		DB::execute("UPDATE accounts SET last_activity = ? WHERE id = ?",[time(), AuthUser::getId()]);
	}
	public static function getIsoLanguage(){
		if(self::$authenticated){
			return self::$userObj['language_iso'];
		}
		return LANGUAGE_DEFAULT_ISO_CODE;
	}
	public static function getInfo($get = 'id') {
		if(self::$authenticated && isset(self::$userObj[$get])){
			return self::$userObj[$get];
		}
		return 0;
	}

	public static function isAuthenticated() {
		return self::$authenticated;
	}

	public static function inGroup($id) {
		return in_array($id, self::$groups);
	}

	public static function getSubPermissions($id = 0){
		return DB::select("SELECT b.* FROM accounts_permissions AS a
		 						INNER JOIN permissions_sub AS b
		 							ON a.parent_id = b.parent_id
		 							AND a.sub_id = b.sub_id
		 							OR  a.parent_id = b.parent_id
		 							AND a.sub_id = '*'
		 						WHERE a.account_id = ?
									AND a.parent_id = ?
									AND a.sub_id IS NOT NULL
								", [self::$userObj['id'], $id]);
	}

	public static function hasSubPermissions($parent_id) {
		foreach(self::$permissions[$parent_id] AS $sub) {
			return true;
		}
	return false;
	}

	public static function hasPermission($id){
		$subPermission = false;
		$id = (string)$id;
		$perm[0] = NULL;
		$perm[1] = NULL;

		// Fix the permission id in case its a sub permission
		/*
		if(is_int($id) && isset(self::$permissions[$id]) && !self::hasPermission(self::$permissions[$id])){
			return true;
		}
		*/

		if(stristr($id, '.')) {
			$subPermission = true;
			$perm = explode(".", $id);
			$perm[0] = intval($perm[0]);
			$perm[1] = intval($perm[1]);
		}
		else {
			$perm[0] = intval($id);
			$perm[1] = 0;
		}

		// Full access to all parent permissions
		if(isset(self::$permissions["*"])) {
			return true;
		}
		// Parent permission
		if(!$subPermission && isset(self::$permissions[$perm[0]][$perm[1]]) && !empty(self::$permissions[$perm[0]][$perm[1]])) {
			return true;
		}
		// Full parent permission
		if($subPermission && isset(self::$permissions[$perm[0]][0]) && !empty(self::$permissions[$perm[0]][0])) {
			return true;
		}
		// Sub permission
		if($subPermission && isset(self::$permissions[$perm[0]][$perm[1]]) && !empty(self::$permissions[$perm[0]][$perm[1]])) {
			return true;
		}
		// Full sub permission
		if($subPermission && isset(self::$permissions[$perm[0]]["*"])) {
			return true;
		}

		return false;
	}

	public static function getId() {
		if(self::$authenticated){
			return self::$userObj['id'];
		}
		return 0;
	}

	public static function getUserName() {
		if(self::$authenticated){
			return self::$userObj['username'];
		}
		return '';
	}

	public static function getProfileImage() {
		if(self::$authenticated){
			return self::$userObj['profile_image'];
		}
		return '';
	}

	public static function getNovaPoints() {
		if(self::$authenticated){
			return self::$userObj['points_nova'];
		}
		return 0;
	}

	public static function logout() {
		setcookie('np_login', '', time() - 3600, "/");
	}

	public static function login($username, $password, $rememberMe = false) {
		$username = strtolower($username);
		$password = md5("NP2015-07-08" . $password);

		$row = DB::selectFirst("SELECT * FROM accounts WHERE
													lower(username) = ?
													AND password = ?
													AND status = ?
										", [$username, $password, 'active']);

		if(!empty($row) && $row['id'] > 0){

			//register ip used
			DB::execute("INSERT IGNORE INTO accounts_ip_used(account_id, ipv4, ipv4_proxy) VALUES (?,?,?)", [
				$row['id'],
				$_SERVER['REMOTE_ADDR'],
				@$_SERVER['HTTP_X_FORWARDED_FOR']
			]);
			$hash = self::setCookie($rememberMe);
			//update last logged in details
			DB::execute('UPDATE accounts SET last_ip = ?, login_hash = ?, last_login = ?, times_loggedin = times_loggedin+1 WHERE id = ?', [
				$_SERVER['REMOTE_ADDR'],
				$hash,
				time(),
				$row['id']
			]);
			return true;
		}
		return false;
	}

	public static function loginFromFacebook($fbUser) {

		$row = DB::selectFirst("SELECT * FROM accounts WHERE
													facebook_id = ?
													AND status = ?
										", [$fbUser['id'], 'active']);

		if(!empty($row) && $row['id'] > 0) {
			//register ip used
			/*
            DB::execute("INSERT IGNORE INTO accounts_ip_used(account_id, ipv4, ipv4_proxy) VALUES (?,?,?)", [
				$row['id'],
				$_SERVER['REMOTE_ADDR'],
				@$_SERVER['HTTP_X_FORWARDED_FOR']
			]);
			*/
			$hash = self::setCookie(true);
			//update last logged in details
			DB::execute('UPDATE accounts SET last_ip = ?, login_hash = ?, last_login = ?, times_loggedin = times_loggedin+1, facebook_dump = ? WHERE id = ?', [
				$_SERVER['REMOTE_ADDR'],
				$hash,
				time(),
				json_encode($fbUser),
				$row['id']
			]);
			return true;
		}
		else {
			// We didnt find a match for the facebook ID, let's check if the email is already registered normally, and if so update the facebook ID
			$check = DB::selectFirst("SELECT * FROM accounts WHERE
													email = ?
													AND status = ?
										", [$fbUser['email'], 'active']);

			if($check['id'] > 0 && $check['facebook_id'] <= 0) {
				// Yea as I thought, let's update the facebook ID for the user
				DB::execute("UPDATE accounts SET facebook_id = ?, facebook_dump = ? WHERE id = ?",[$fbUser['id'], json_encode($fbUser), $check['id']]);

				//register ip used
                /*
				DB::execute("INSERT IGNORE INTO accounts_ip_used(account_id, ipv4, ipv4_proxy) VALUES (?,?,?)", [
					$row['id'],
					$_SERVER['REMOTE_ADDR'],
					@$_SERVER['HTTP_X_FORWARDED_FOR']
				]);
                */
				// Now log the user in again
				$hash = self::setCookie(true);
				//update last logged in details
				DB::execute('UPDATE accounts SET last_ip = ?, login_hash = ?, last_login = ?, times_loggedin = times_loggedin+1 WHERE id = ?', [
					$_SERVER['REMOTE_ADDR'],
					$hash,
					time(),
                    $check['id']
				]);
			return true;
			}
			else {
			    // The account already existed and had a Facebook ID, log the person in
                $hash = self::setCookie(true);
                //update last logged in details
                DB::execute('UPDATE accounts SET last_ip = ?, login_hash = ?, last_login = ?, times_loggedin = times_loggedin+1 WHERE id = ?', [
                    $_SERVER['REMOTE_ADDR'],
                    $hash,
                    time(),
                    $check['id']
                ]);
            return true;
            }
		}
	}

	private static function setCookie($rememberMe = false) {
		$unique = false;
		$hash = '';
		while(!$unique) {
			$hash = bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
			$check = DB::selectFirst("SELECT COUNT(id) AS matched FROM accounts WHERE
													login_hash = ?
												", [$hash]);
			if($check['matched'] <= 0){
				$unique = true;
			}
		}
		$time = ($rememberMe) ? LOGIN_EXPIRATION_ALWAYS : LOGIN_EXPIRATION;
		setcookie('np_login', $hash, time() + $time, "/");
		self::init();
		return $hash;
	}
}