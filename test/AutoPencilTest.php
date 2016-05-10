<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../autopencil.php';
use autopencil\AutoPencil;

class TestModelTest extends PHPUnit_Framework_TestCase {
	function testPen() {

		// инициализируем класс
		$fp = fopen(__DIR__.'/pen_output.html','w');
		fwrite($fp,'<html><body>'."\r\n");
		$pen = new AutoPencil($fp);

		// попытаемся писать выключенной ручкой
		$this->assertFalse($pen->write('Я пишу выключенной ручкой'));

		// включим ручку
		$pen->open();

		// проверим что-нибудь
		$this->assertEquals($pen->getInkResidue(),AutoPencil::FULL_INK_VALUE);
		$this->assertEquals($pen->getInkResiduePercent(),1);

		// попробуем писать
		$text = 'SOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT';
		$this->assertEquals($pen->write($text),strlen($text));
		$this->assertEquals($pen->getInkResidue(),AutoPencil::FULL_INK_VALUE-strlen($text));

		// попробуем использовать всю пасту
		$written = strlen($text);
		$i = 0;
		do{
			$pen->open();
			$res=$pen->write($text);
			$written+=$res;

			// защита от бесконечного цикла
			$i++;
			if($i>500)
				break;
		}while($res>0 || $written<=AutoPencil::FULL_INK_VALUE);

		$this->assertEquals($pen->getInkResidue(),0);
		$this->assertEquals($pen->getInkResiduePercent(),0.0);

		// Выключим ручку
		$pen->close();

		// Попытаемся включить (без чернил не должна включаться)
		$this->assertFalse($pen->open());

		// вставим новый стержень
		$this->assertFalse($pen->reload(2));
		$this->assertFalse($pen->reload(0));
		$this->assertTrue($pen->reload(0.9));
		$this->assertGreaterThan(0,$pen->getInkResidue());

		// завершение
		fwrite($fp,'</body></html>');
		fclose($fp);
	}

}