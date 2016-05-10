<?php
namespace pen;

/**
 * Класс ручка
 */
class pen
{
	const FULL_INK_VALUE  = 10000;	// запас чернила в новом стержне (символов)

	protected $_ink_residue = self::FULL_INK_VALUE; // остаток чернила
	private $_ink_color   = '#0000CC';	// цвет чернила
	private $_output      = null;	// указатель на файл

	/**
	 * конструктор класса
	 * @param file $output файловый указатель, полученный функцией fopen (или другими способами) с правами записи.
	 * @param string $color цвет чернил
	 */
	function __construct($output,$color='#0000CC')
	{
		$this->_output = $output;
		$this->_ink_color = $color;
	}

	/**
	 * написать что-то (уменьшает запас чернила)
	 * @param  string $text Текст, который необходимо написать
	 * @return int       число символов, которые были записаны, или false при ошибке
	 */
	public function write($text=null)
	{
		if($this->_ink_residue == 0)
			return 0;
		$chars = strlen($text);
		if($chars == 0)
			return 0;

		if($chars > $this->_ink_residue){
			$text = substr($text,0,$this->_ink_residue);
			$chars = $this->_ink_residue;
		}

		if(!fwrite($this->_output,'<p style="color:'.$this->_ink_color.'">'.$text.'</p>'."\r\n"))
			return false;
		$this->_ink_residue-=$chars;
		return $chars;
	}

	/**
	 * получить остаток чернила
	 * @return int число символов, которые можно написать оставшимся чернилом
	 */
	public function getInkResidue() 
	{
		return $this->_ink_residue;
	}

	/**
	 * получить остаток чернила в процентах
	 * @return real остаток чернила в процентах
	 */
	public function getInkResiduePercent() 
	{
		return $this->_ink_residue / self::FULL_INK_VALUE;
	}
}