<?php

namespace local_formattertest;

global $CFG;
require_once ($CFG->libdir . '/moodlelib.php');

class string_manager_messageformatter extends \core_string_manager_standard {

	public function get_string($identifier, $component = '', $a = null, $lang = null) {
		global $CFG;

		$this->countgetstring ++;
		// There are very many uses of these time formatting strings without the 'langconfig' component,
		// it would not be reasonable to expect that all of them would be converted during 2.0 migration.
		static $langconfigstrs = array (
				'strftimedate' => 1,
				'strftimedatefullshort' => 1,
				'strftimedateshort' => 1,
				'strftimedatetime' => 1,
				'strftimedatetimeshort' => 1,
				'strftimedaydate' => 1,
				'strftimedaydatetime' => 1,
				'strftimedayshort' => 1,
				'strftimedaytime' => 1,
				'strftimemonthyear' => 1,
				'strftimerecent' => 1,
				'strftimerecentfull' => 1,
				'strftimetime' => 1
		);

		if (empty ( $component )) {
			if (isset ( $langconfigstrs [$identifier] )) {
				$component = 'langconfig';
			} else {
				$component = 'moodle';
			}
		}

		if ($lang === null) {
			$lang = current_language ();
		}

		$string = $this->load_component_strings ( $component, $lang );

		if (! isset ( $string [$identifier] )) {
			if ($component === 'pix' or $component === 'core_pix') {
				// This component contains only alt tags for emoticons, not all of them are supposed to be defined.
				return '';
			}
			if ($identifier === 'parentlanguage' and ($component === 'langconfig' or $component === 'core_langconfig')) {
				// Identifier parentlanguage is a special string, undefined means use English if not defined.
				return 'en';
			}
			// Do not rebuild caches here!
			// Devs need to learn to purge all caches after any change or disable $CFG->langstringcache.
			if (! isset ( $string [$identifier] )) {
				// The string is still missing - should be fixed by developer.
				if ($CFG->debugdeveloper) {
					list ( $plugintype, $pluginname ) = \core_component::normalize_component ( $component );
					if ($plugintype === 'core') {
						$file = "lang/en/{$component}.php";
					} else if ($plugintype == 'mod') {
						$file = "mod/{$pluginname}/lang/en/{$pluginname}.php";
					} else {
						$path = core_component::get_plugin_directory ( $plugintype, $pluginname );
						$file = "{$path}/lang/en/{$plugintype}_{$pluginname}.php";
					}
					debugging ( "Invalid get_string() identifier: '{$identifier}' or component '{$component}'. " . "Perhaps you are missing \$string['{$identifier}'] = ''; in {$file}?", DEBUG_DEVELOPER );
				}
				return "[[$identifier]]";
			}
		}

		$string = $string [$identifier];

		if ($a !== null) {
			// Process array's and objects (except lang_strings).
			if (is_array ( $a ) or (is_object ( $a ) && ! ($a instanceof lang_string))) {
				$a = ( array ) $a;

				if (strpos($string, '{$a') === false) {
					$string = \MessageFormatter::formatMessage($lang, $string, $a);
				} else {
					$search = array ();
					$replace = array ();
					foreach ( $a as $key => $value ) {
						if (is_int ( $key )) {
							// We do not support numeric keys - sorry!
							continue;
						}
						if (is_array ( $value ) or (is_object ( $value ) && ! ($value instanceof lang_string))) {
							// We support just string or lang_string as value.
							continue;
						}
						$search [] = '{$a->' . $key . '}';
						$replace [] = ( string ) $value;
					}
					if ($search) {
						$string = str_replace ( $search, $replace, $string );
					}
				}
			} else {
				$string = str_replace ( '{$a}', ( string ) $a, $string );
			}
		}

		if ($CFG->debugdeveloper) {
			// Display a debugging message if sting exists but was deprecated.
			if ($this->string_deprecated ( $identifier, $component )) {
				list ( $plugintype, $pluginname ) = core_component::normalize_component ( $component );
				debugging ( "String [{$identifier},{$plugintype}_{$pluginname}] is deprecated. " . 'Either you should no longer be using that string, or the string has been incorrectly deprecated, in which case you should report this as a bug. ' . 'Please refer to https://docs.moodle.org/dev/String_deprecation', DEBUG_DEVELOPER );
			}
		}

		return $string;
	}
}