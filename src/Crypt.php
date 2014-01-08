<?php
namespace rmochi\PHPUtil;

abstract class Crypt
{
	protected $td;

	/**
	 * 暗号/複合化の元となるkey文字列を返却
	 */
	abstract protected function getKey();

	public static function __callStatic($name, $args)
	{
		switch ($name) {
			case 'encrypt':
				$self = new self();
				return $self->encrypt($args[0]);
				break;

			case 'decrypt':
				$self = new self();
				return $self->decrypt($args[0]);
				break;

			default:
				return false;
		}
	}

	protected function __construct()
	{
		$td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_CFB, '');

		if ($td === false) {
			throw new Exception();
		}
		$this->td = $td;
	}

	protected function __destruct()
	{
		if (isset($this->td)) {
			mcrypt_module_close($this->td);
		}
	}

	// protected function encrypt($str) {{{
	/**
	 * 暗号化
	 *
	 * @param  string $str
	 * @return string|false
	 */
	protected function encrypt($str)
	{
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->td), MCRYPT_DEV_RANDOM);

		/* キーを作成します */
		mcrypt_generic_init($this->td, $this->getCryptKey(), $iv);

		$encrypted_str = mcrypt_generic($this->td, $str);
		mcrypt_generic_deinit($this->td);

		return base64_encode(
			base64_encode($iv) . "\n" . base64_encode($encrypted_str)
		);
	}
	// }}}

	// protected function decrypt($str) {{{
	/**
	 * 複合化
	 *
	 * @param  string $str
	 * @return string|false
	 */
	protected function decrypt($str)
	{
		$item = explode("\n", base64_decode($str));

		if (count($item) != 2) {
			return false;
		}
		$iv  = base64_decode($item[0]);
		$enc = base64_decode($item[1]);

		$res = mcrypt_generic_init($this->td, $this->getCryptKey(), $iv);

		if ($res === false || $res < 0) {
			return false;
		}
		$decrypted_str = mdecrypt_generic($this->td, $enc);
		mcrypt_generic_deinit($this->td);

		return $decrypted_str;
	}
	// }}}

	protected function getCryptKey()
	{
		$ks  = mcrypt_enc_get_key_size($this->td);
		$key = substr(hash('sha1', $this->getKey()), 0, $ks);

		return $key;
	}
}
