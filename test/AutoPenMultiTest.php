<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../autopenmulti.php';
use autopenmulticolor\AutoPenMulticolor;

class AutoPenMulticolorTest extends PHPUnit_Framework_TestCase {
	
	protected $fixture;
	private $fp;

    protected function setUp()
    {
		// инициализируем класс
		$this->fp = fopen(__DIR__.'/pen_output.html','w');
		fwrite($this->fp,'<html><body>'."\r\n");
		$this->fixture = new AutoPenMulticolor($this->fp);
    }

    protected function tearDown()
    {
		// завершение
		fwrite($this->fp,'</body></html>');
		fclose($this->fp);
        $this->fixture = NULL;
    }

	function testPen() {

		// попытаемся писать выключенной ручкой
		$this->assertFalse($this->fixture->write('Я пишу выключенной ручкой'));

		// включим ручку (пасту 1)
		$this->fixture->open(0);

		// проверим что-нибудь
		$this->assertEquals($this->fixture->getInkResidue(),AutoPenMulticolor::FULL_INK_VALUE);
		$this->assertEquals($this->fixture->getInkResiduePercent(),1);

		// попробуем писать
		$text = 'SOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT';
		$this->assertEquals($this->fixture->write($text),strlen($text));
		$this->assertEquals($this->fixture->getInkResidue(),AutoPenMulticolor::FULL_INK_VALUE-strlen($text));

		// попробуем использовать всю пасту
		$written = strlen($text);
		$i = 0;
		do{
			$res=$this->fixture->write($text);
			$written+=$res;

			// защита от бесконечного цикла
			$i++;
			if($i>500)
				break;
		}while($res>0 || $written<=AutoPenMulticolor::FULL_INK_VALUE);

		$this->assertEquals($this->fixture->getInkResidue(),0);
		$this->assertEquals($this->fixture->getInkResiduePercent(),0.0);

		// Выключим ручку
		$this->fixture->close();

		// Попытаемся включить (без чернил не должна включаться)
		$this->assertFalse($this->fixture->open());

		$this->fixture->open(1); // включим другую пасту и попробуем писать
		$this->assertEquals($this->fixture->write($text),strlen($text));
		$this->assertEquals($this->fixture->getInkResidue(),AutoPenMulticolor::FULL_INK_VALUE-strlen($text));
	}

}