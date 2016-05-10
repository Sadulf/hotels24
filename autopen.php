<?php
namespace autopen;

require_once __DIR__.'/pen.php';
use pen\Pen;

/**
 * Для того чтобы начать писать, авторучку необходимо сначала включить.
 */
class AutoPen extends Pen
{
	protected $_opened = false;

	/**
	 * Открывает (включает) авторучку, после чего ею можно писать. Если чернила закончились - ручка не включается.
	 * @return bool если удалось включить ручку - возвращает true, иначе false. 
	 */
	public function open()
	{
		if($this->_opened)
			return true;
		if($this->_ink_residue==0)
			return false;
		$this->_opened = true;
		return true;
	}

	/**
	 * Закрывает (выключает) авторучку, после чего писать ею нельзя.
	 * @return bool если удалось выключить ручку - возвращает true, иначе false. 
	 */
	public function close()
	{
		$this->_opened = false;
		return true;
	}

	/**
	 * написать что-то (уменьшает запас чернила)
	 * @param  string $text Текст, который необходимо написать
	 * @return int       число символов, которые были записаны, или false при ошибке
	 */
	public function write($text=null)
	{
		// проверим, включена ли ручка
		if(!$this->_opened)
			return false;
		return parent::write($text);
	}
} 