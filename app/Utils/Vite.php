<?php
/**
 * Functionality related to Vite.
 */

namespace App\Utils;

class Vite {
	public static $VITE_DEV;
	public static $VITE_PORT;
	public static $VITE_HOST;

	public static $manifest;

	public static function init () {
		self::$VITE_DEV  = getenv('VITE_DEV');
		self::$VITE_PORT = getenv('VITE_PORT') ?: '5173';
		self::$VITE_HOST = 'http://127.0.0.1:'.self::$VITE_PORT; // Note: no trailing slash.
		self::$manifest  = self::getAssetManifest();
	}

	/**
	 * Output Vite's client script tag if we're in Vite dev.
	 */
	public static function clientScriptHTML () {
		if (!self::$VITE_DEV) {
			return;
		}

		return '<script type="module" crossorigin src="'. self::$VITE_HOST .'/@vite/client"></script>';
	}

	/**
	 * Read Vite's asset manifest and load it into an assoc. array.
	 * 
	 * Vite includes a short hash in asset filenames after processing.
	 * We can use the manifest to lookup an asset and get its complete
	 * path, including the filename+hash.
	 * 
	 * @throws \Error Generic error if the manifest doesn't exist,
	 *     or if decoding it into an assoc. array fails.
	 * @return array Assoc. array of processed assets and their
	 *     
	 */
	public static function getAssetManifest ()/* : array */ {
		global $ROOT;

		if (self::$manifest) {
			return self::$manifest;
		}
	
		$manifest = $ROOT . '/public/build/manifest.json';
	
		if (!file_exists($manifest)) {
			//throw new \Error('manifest.json not found! Maybe you need to run `yarn run build`?');
			return false; // TODO
		}
	
		$manifest = file_get_contents($manifest);
	
		if (!json_decode($manifest)) {
			//throw new \Error('json_decode on manifest.json failed!');
			return false; // TODO
		}
	
		$manifest = self::$manifest = json_decode($manifest, true);
	
		return $manifest;
	}

	/**
	 * Takes an asset filename, which should correspond to an entry in 
	 * the asset manifest, and returns a URI.
	 * 
	 * @param string $entry Asset filename. For example, for 
	 *     `resources/scripts/main.js`, we would pass in `main.js`.
	 * @throws \Error Generic error if an asset can't be found.
	 * @return string Asset URI.
	 */
	public static function getAssetURL (string $entry): string {
		if (self::$VITE_DEV) {
			return self::$VITE_HOST.'/'.$entry;
		}
	
		if (!isset(self::$manifest[$entry])) {
			//throw new \Error("Requested asset '{$entry}' not found!");
			return '';
		}
	
		return '/build/'.self::$manifest[$entry]['file'];
	}

	public static function assetHTML (string $asset) {
		// In vite dev, asset will be located at the root of vite's dev server.
		if (self::$VITE_DEV) {
			//return self::$VITE_HOST . '/' . $asset;
			return '<script type="module" crossorigin src="' . self::$VITE_HOST . '/' . $asset . '"></script>';
		}

		if (!isset(self::$manifest[$asset])) {
			return ''; // TODO: Throw error?
		}

		$entry = self::$manifest[$asset];

		$output = '<script type="module" crossorigin src="/' . $entry['file'] . '"></script>';

		if (!empty($entry['imports'])) {
			foreach($entry['imports'] as $importURI) {
				$output .= "\n";
				$output .= '<link rel="modulepreload" rel="/' . $importURI . '">';
			}
		}

		if (!empty($entry['css'])) {
			foreach($entry['css'] as $cssURI) {
				$output .= "\n";
				$output .= '<link rel="stylesheet" href="/' . $cssURI . '"/>';
			}
		}

		return $output;
	}
}

Vite::init(); // initialize class props.
