<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../autopencil.php';
use autopencil\AutoPencil;

class AutoPencilTest extends PHPUnit_Framework_TestCase {
	
	protected $fixture;
	private $fp;

    protected function setUp()
    {
		// инициализируем класс
		$this->fp = fopen(__DIR__.'/pen_output.html','w');
		fwrite($this->fp,'<html><body>'."\r\n");
		$this->fixture = new AutoPencil($this->fp);
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

		// включим ручку
		$this->fixture->open();

		// проверим что-нибудь
		$this->assertEquals($this->fixture->getInkResidue(),AutoPencil::FULL_INK_VALUE);
		$this->assertEquals($this->fixture->getInkResiduePercent(),1);

		// попробуем писать
		$text = 'SOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT';
		$this->assertEquals($this->fixture->write($text),strlen($text));
		$this->assertEquals($this->fixture->getInkResidue(),AutoPencil::FULL_INK_VALUE-strlen($text));

		// попробуем использовать всю пасту
		$written = strlen($text);
		$i = 0;
		do{
			$this->fixture->open();
			$res=$this->fixture->write($text);
			$written+=$res;

			// защита от бесконечного цикла
			$i++;
			if($i>500)
				break;
		}while($res>0 || $written<=AutoPencil::FULL_INK_VALUE);

		$this->assertEquals($this->fixture->getInkResidue(),0);
		$this->assertEquals($this->fixture->getInkResiduePercent(),0.0);

		// Выключим ручку
		$this->fixture->close();

		// Попытаемся включить (без чернил не должна включаться)
		$this->assertFalse($this->fixture->open());

		// вставим новый стержень
		$this->assertFalse($this->fixture->reload(2));
		$this->assertFalse($this->fixture->reload(0));
		$this->assertTrue($this->fixture->reload(0.9));
		$this->assertGreaterThan(0,$this->fixture->getInkResidue());

	}

}