<?php

/*

Laravel HashSlug: Package providing a trait to use Hashids on a model
Copyright (C) 2017-2020  Balázs Dura-Kovács

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

namespace Balping\HashSlug;

trait HasHashSlug {
	/**
	 * Cached hashslug
	 * @var null|string
	 */
	private $hashslug = null;

	/**
	 * Cached HashIds instance
	 * @var null|\Hashids\Hashids
	 */
	private static $hashIds = null;

	/**
	 * Returns a chached Hashids instance
	 * or initialises it with salt
	 * 
	 * @return \Hashids\Hashids
	 */
	private static function getHashids(){
		if (is_null(self::$hashIds)){

			$minSlugLength = config('hashslug.minSlugLength', 5);
			if(isset(self::$minSlugLength)) {
				$minSlugLength = self::$minSlugLength;
			}

			if(isset(self::$modelSalt)) {
				$modelSalt = self::$modelSalt;
			}else{
				$modelSalt = get_called_class();
			}

			if(isset(self::$alphabet)) {
				$alphabet = self::$alphabet;
			}else{
				$alphabet = config('hashslug.alphabet', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
			}

			$salt = config('hashslug.appsalt', config('app.key')) . $modelSalt;
			
			// This is impotant!
			// Don't use a weak hash, otherwise
			// your app key can be exposed
			// http://carnage.github.io/2015/08/cryptanalysis-of-hashids
			$salt = hash('sha256', $salt);

			self::$hashIds = new \Hashids\Hashids($salt, $minSlugLength, $alphabet);
		}

		return self::$hashIds;
	}

	/**
	 * Hashslug calculated from id
	 * @return string
	 */
	public function slug(){
		if (is_null($this->hashslug)){
			$hashids = $this->getHashids();

			$this->hashslug = $hashids->encode($this->{$this->getKeyName()});
		}

		return self::getHashSlugPrefix() . $this->hashslug;
	}

	public function getRouteKeyName(){
		return 'hashslug';
	}

	public function getRouteKey() {
		return $this->slug();
	}

	/**
	 * Used in implicit model binding AND
	 * used in explicit model binding if no callback
	 * is specified, eg: Route::model('post', Post::class)
	 * 
	 * @param  string $slug
	 * @param  string|null  $field
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function resolveRouteBinding($slug, $field = null){
		$id = self::decodeSlug($slug);
		return parent::where($field ?? $this->getKeyName(), $id)->first();
	}

	/**
	 * Prefix to applied in front of hashslug
	 * @return string
	 */
	public static function getHashSlugPrefix(){
		$prefix = '';
		if(isset(self::$hashSlugPrefix)) {
			$prefix = self::$hashSlugPrefix;
		}

		return $prefix;
	}

	/**
	 * Decodes slug to id
	 * @param  string $slug
	 * @return int|null
	 */
	public static function decodeSlug($slug){
		$hashids = self::getHashids();

		// remove prefix
		$prefix = self::getHashSlugPrefix();
		if($prefix != '' && !\Str::startsWith($slug, $prefix)){
			// slug is not correctly prefixed
			return null;
		}
		$slug = substr($slug, strlen($prefix));

		$decoded = $hashids->decode($slug);

		if(! isset($decoded[0])){
			return null;
		}

		return (int) $decoded[0];
	}

	/**
	 * Wrapper around Model::findOrFail
	 * 
	 * @param  string $slug
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public static function findBySlugOrFail($slug){
		$id = self::decodeSlug($slug);

		return self::findOrFail($id);
	}

	/**
	 * Wrapper around Model::find
	 * 
	 * @param  string $slug
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	public static function findBySlug($slug){
		$id = self::decodeSlug($slug);

		return self::find($id);
	}
}
