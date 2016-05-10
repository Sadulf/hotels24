<?php
namespace autopencil;

require_once __DIR__.'/autopen.php';
use autopen\AutoPen;

/**
 * Авто-карандаш необходимо включать после каждой фразы, его можно "заправлять", пишет только серым цветом
 */
class AutoPencil extends AutoPen
{
	/**
	 * конструктор класса
	 * @param file $output файловый указатель, полученный функцией fopen (или другими способами) с правами записи.
	 */
	function __construct($output)
	{
		parent::__construct($output,'#CCCCCC');
	}

	/**
	 * написать что-то (уменьшает запас чернила)
	 * @param  string $text Текст, который необходимо написать
	 * @return int       число символов, которые были записаны, или false при ошибке
	 */
	public function write($text=null)
	{
		$r = parent::write($text);
		parent::close();
		return $r;
	}

	/**
	 * вставить новый стержень в авто-карандаш
	 * @param  real $size размер стержня (1 - целый, 0.5 - половина)
	 * @return bool true если стержень вставлен или false если не удалось его вставить
	 */
	public function reload($size=1)
	{
		if($size>1.0)
			return false; // слишком большой стержень
		if($size<=0.0)
			return false;// слишком маленький :)
		$this->_ink_residue = round(self::FULL_INK_VALUE*$size);
		return true;
	}
}