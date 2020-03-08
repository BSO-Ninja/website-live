<?php
namespace core;

use helper\AuthUser;

class Languages {
	public static $cachedTranslations;

	public static function getTranslationFields() {
		return DB::select("SELECT * FROM languages_fields");
	}

	public static function getHtmlAreaTranslation($parentID = 0, $lang = NULL, $fallbackLang = "EN") {
		$lang = !empty($lang) ? $lang : AuthUser::getInfo('language_iso');
		$htmlArea = DB::selectFirst("SELECT * FROM html_areas_translations WHERE parent_id = ? AND language_iso = ?", [$parentID, $lang]);
		if(empty($htmlArea['html'])) {
			$htmlArea = DB::selectFirst("SELECT * FROM html_areas_translations WHERE parent_id = ? AND language_iso = ?", [$parentID, $fallbackLang]);
		}
		return !empty($htmlArea['html']) ? $htmlArea['html'] : "EDIT HERE";
	}

	public static function getRules($iso_code = "EN") {
		return DB::selectFirst("SELECT * FROM rules WHERE iso_code = ?", [$iso_code]);
	}

	public static function getTranslation($lang = "EN", $fieldID = 0) {
		$langID = DB::selectFirst("SELECT id FROM languages WHERE iso_code = ?", [$lang]);
		return DB::selectFirst("SELECT * FROM languages_translations WHERE lang_id = ? AND field_id = ?", [$langID['id'], $fieldID]);
	}

	public static function getLanguage($isoCode = "EN") {
		return DB::selectFirst("SELECT * FROM languages WHERE iso_code = ?",[$isoCode]);
	}

	public static function getLanguages() {
		return DB::select("SELECT * FROM languages ORDER BY id ASC");
	}

	public static function translate($text, $exampleText = "") {
		// Check what language is set
		$language = AuthUser::getIsoLanguage();
		if(!isset(self::$cachedTranslations[$language])){
			$lang = DB::selectFirst("SELECT id FROM languages WHERE iso_code LIKE ?", [$language]);
			self::$cachedTranslations[$language] = ['language_id' => $lang['id']];
		}
		if(!isset(self::$cachedTranslations[$language][$text])){
			$id = 0;
			$translatedText = "";

			$trans = DB::selectFirst('
						SELECT field.id, trans.translation
						FROM languages_translations trans
						JOIN languages_fields field 
						ON field.id = trans.field_id
						WHERE lang_id = ? 
						AND default_text 
						LIKE ?
						', [self::$cachedTranslations[$language]['language_id'], $text]);

			if(!empty($trans)){
				$translatedText = $trans['translation'];
				$id = $trans['id'];
			} else {
				$field = DB::selectFirst('SELECT * FROM languages_fields WHERE default_text LIKE ?', [$text]);
				if(empty($field)){
					$fieldId = DB::execute("INSERT INTO languages_fields SET default_text = ?, example_text = ?, goto_url = ?", [$text, $exampleText, $_SERVER['REQUEST_URI']]);
				} else {
					$fieldId = $field['id'];
				}
				$trans = DB::selectFirst('SELECT * FROM languages_translations WHERE field_id = ? AND lang_id = ?', [
					$fieldId,
					self::$cachedTranslations[$language]['language_id']
				]);
				if(empty($trans)){
					DB::execute('INSERT INTO languages_translations(lang_id, field_id, translation) VALUES (?,?,?)', [
						self::$cachedTranslations[$language]['language_id'],
						$fieldId,
						$text
					]);
				}
				$id = $fieldId;
				$translatedText = $text;
			}

			self::$cachedTranslations[$language][$text] = ['id' => $id, 'text' => $translatedText];
		}
		if(AuthUser::hasPermission(4)){
			return sprintf('<span class="translatableText" id="translation_%d">%s</span>', self::$cachedTranslations[$language][$text]['id'], self::$cachedTranslations[$language][$text]['text']);
		}
		return self::$cachedTranslations[$language][$text]['text'];
	}

	public static function saveTranslation($fieldId, $newText, $language) {
		if(!AuthUser::isAuthenticated()){
			return false;
		}
		$lang = DB::selectFirst('SELECT id FROM languages WHERE iso_code = ?', [$language]);
		if(empty($lang)){
			return false;
		}
		$check = DB::selectFirst("SELECT id, translation FROM languages_translations WHERE lang_id = ? AND field_id = ?", [
			$lang['id'],
			$fieldId
		]);
		if(!empty($check) && $check['translation'] != $newText) {//exists + different
			DB::execute('UPDATE languages_translations SET translation = ? WHERE lang_id = ? AND field_id = ?', [
				$newText,
				$lang['id'],
				$fieldId
			]);
		}
		else if(empty($check)) {
			DB::execute('INSERT INTO languages_translations SET translation = ?, lang_id = ?, field_id = ?', [
				$newText,
				$lang['id'],
				$fieldId
			]);
		}
		else{//not saved, no change
			return false;
		}
		DB::execute('INSERT INTO translation_log(account_id, field_id, language_id, timestamp, translation) VALUES(?,?,?,?,?)',
				[AuthUser::getId(),$fieldId, $lang['id'],time(),$newText]);

		return true;
	}

}
