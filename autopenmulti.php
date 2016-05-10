<?php
namespace autopenmulticolor;

require_once __DIR__.'/autopen.php';
use autopen\AutoPen;

/**
 * Авторучка с 4-мя пастами. Необходимо включать каждый цвет отдельно.
 */
class AutoPenMulticolor extends AutoPen
{
	private $_colors       = [];
	private $_ink_residues = [];
	private $_active_color = 0; 
	
	/**
	 * конструктор класса
	 * @param file $output файловый указатель, полученный функцией fopen (или другими способами) с правами записи.
	 * @param array $colors цвета чернил
	 */
	function __construct($output,$colors=array('red','green','blue','black'))
	{
		// заполним начальные параметры
		$this->_colors = $colors;
		$this->_ink_residues=[];
		$this->_active_color = null;
		foreach($this->_colors as $id=>$color){
			if(is_null($this->_active_color))
				$this->_active_color = $id;
			$this->_ink_residues[$id]=self::FULL_INK_VALUE;
		}
		parent::__construct($output,$this->_colors[$this->_active_color]);
		$this->_ink_residue = $this->_ink_residues[$this->_active_color];
	}

	/**
	 * Открывает (включает) авторучку, после чего ею можно писать. Если чернила закончились - ручка не включается.
	 * @param  int $color_id номер пасты, которую нужно включить
	 * @return bool если удалось включить ручку - возвращает true, иначе false. 
	 */
	public function open($color_id=0)
	{
		if($color_id<0 || $color_id>=count($this->_colors))
			return false; // нельзя включить пасту, которой нет
		if($this->_opened && $this->_active_color==$color_id)
			return true; // нужная паста и так включена

		$this->close(); // предыдущая паста автоматически выключается

		// загрузим параметры новой пасты
		$this->_active_color = $color_id;
		$this->_ink_color = $this->_colors[$this->_active_color];
		$this->_ink_residue = $this->_ink_residues[$this->_active_color];

		// откроем
		return parent::open();
	}

	/**
	 * Закрывает (выключает) авторучку, после чего писать ею нельзя.
	 * @return bool если удалось выключить ручку - возвращает true, иначе false. 
	 */
	public function close()
	{
		if(!$this->_opened)
			return true; // ручка и так выключена

		// сохраним параметры чернила
		$this->_ink_residues[$this->_active_color]=$this->_ink_residue;

		// закроем ручку
		return parent::close();
	}


}