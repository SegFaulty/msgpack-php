<?php

require '../../lib/MsgPack/Coder.php';

class MsgPack_CoderTest extends PHPUnit_Framework_TestCase {

	function testPackUnpack() {
		$pack = pack('d', (double) 1.3);
		#echo 'o:'.$this->debugMessagePack($pack)."\n";
		#echo 'o1:'.$this->debugMessagePack("\xCD\xCC\xCC\xCC\xCC\xCC\xF4\x3F")."\n";
		self::assertSame("\xCD\xCC\xCC\xCC\xCC\xCC\xF4\x3F", $pack);
		$pack = pack('f', (float) 1.3);
		#echo 'p:'.$this->debugMessagePack($pack)."\n";
		self::assertSame("\x66\x66\xa6\x3f", $pack);
	}

	function testEncode() {

		$testMessage = true;
		$expected = chr(MsgPack_Coder::VALUE_SCALAR_TRUE);
		self::assertSame($expected, MsgPack_Coder::encode($testMessage));
		$testMessage = 'HalloBallo';
		$expected = chr(MsgPack_Coder::VALUE_RAW_FIX+10).'HalloBallo';
		self::assertSame($expected, MsgPack_Coder::encode($testMessage));
		$testMessage = str_repeat('1', pow(2,16)-1);
		$expected = chr(MsgPack_Coder::VALUE_RAW_16).chr(255).chr(255).$testMessage;
		self::assertSame($expected, MsgPack_Coder::encode($testMessage));
		$testMessage = str_repeat('1', pow(2,16)); // first 32 bit
		$expected = chr(MsgPack_Coder::VALUE_RAW_32).chr(0).chr(1).chr(0).chr(0).$testMessage;
		self::assertSame(substr($expected,0,6), substr(MsgPack_Coder::encode($testMessage),0,6),$this->debugMessagePack(MsgPack_Coder::encode($testMessage)));
		self::assertSame($expected, MsgPack_Coder::encode($testMessage));
		$testMessage = '12345678901234567890123456789012'; // 32
		$expected = chr(MsgPack_Coder::VALUE_RAW_16).chr(0).chr(32).$testMessage;
		self::assertSame($expected, MsgPack_Coder::encode($testMessage), $this->debugMessagePack(MsgPack_Coder::encode($testMessage)));
		$testMessage = -1;
		$expected = chr(255);
		self::assertSame($expected, MsgPack_Coder::encode($testMessage));

	}


	function testEncodeArray() {

		$testMessage = array();
		$expected = chr( MsgPack_Coder::VALUE_LIST_FIX);
		self::assertSame($expected, MsgPack_Coder::encode($testMessage), $this->debugMessagePack(MsgPack_Coder::encode($testMessage)));
		$testMessage = array(1,2,3);
		$expected = chr( MsgPack_Coder::VALUE_LIST_FIX+3).chr(1).chr(2).chr(3);
		self::assertSame($expected, MsgPack_Coder::encode($testMessage), $this->debugMessagePack(MsgPack_Coder::encode($testMessage)));
		$testMessage = array(12=>1);
		$expected = chr( MsgPack_Coder::VALUE_MAP_FIX+1).chr(12).chr(1);
		self::assertSame($this->debugMessagePack($expected), $this->debugMessagePack(MsgPack_Coder::encode($testMessage)));
		$testMessage = array(12=>1,8=>2,100=>3);
		$expected = chr( MsgPack_Coder::VALUE_MAP_FIX+3).chr(12).chr(1).chr(8).chr(2).chr(100).chr(3);
		self::assertSame($expected, MsgPack_Coder::encode($testMessage), $this->debugMessagePack(MsgPack_Coder::encode($testMessage)));
		$testMessage = array('abc','de','yz');
		$expected =  chr( MsgPack_Coder::VALUE_LIST_FIX+3)
					.chr(MsgPack_Coder::VALUE_RAW_FIX+3).'abc'
					.chr(MsgPack_Coder::VALUE_RAW_FIX+2).'de'
					.chr(MsgPack_Coder::VALUE_RAW_FIX+2).'yz';
		self::assertSame($this->debugMessagePack($expected), $this->debugMessagePack(MsgPack_Coder::encode($testMessage)));
	}

	function testDecode() {
		$binary = "\xff";
		self::assertSame(-1, MsgPack_Coder::decode($binary));

	}

	function testEncodeDecodeInt() {

		$testMessage = 0;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = 1;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = 127;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = -1;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = -31;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = 128;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = 255;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = 256;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = 65535;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = 65536;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = PHP_INT_MAX;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = -1;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = -32;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = -33;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = -128;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = -129;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = -255;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = -256;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = -65535;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = -65536;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = -2000000000; // 2milliarden
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
	}
	function testEncodeDecode() {
		
		$testMessage = null;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = true;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = false;
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = array(1,2,3);
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = array(12=>1,8=>2,100=>3);
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = 'abc';
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = array('abc','de','yz');
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = str_repeat('a',32); // first 16bit
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = str_repeat('b',pow(2,16)-1); // last 16bit
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = str_repeat('c',pow(2,16)); // first 32bit
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = array(1,2,3,4,5,6,7,8,9,0,1,2,3,4,5,6); // first array 16
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		/* disabled.. timeconsuming
		$testMessage = ''; // first 32bit
		for($i=0;$i<65536;$i++) {
			$testMessage[] = $i;
		}
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		*/
		$testMessage = (double) 1.3; // float
		#echo $this->debugMessagePack(MsgPack_Coder::encode($testMessage));
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
		$testMessage = (float) 1.25; // float
		#echo $this->debugMessagePack(MsgPack_Coder::encode($testMessage));
		self::assertSame($testMessage, MsgPack_Coder::decode(MsgPack_Coder::encode($testMessage)));
	}

	function testGetIntFromMessagePack() {
		$messagePack = chr(0).chr(16);
		self::assertSame(16, TestCoder::getIntFromMessagePack_public($messagePack,2));
		self::assertSame(0, strlen($messagePack));
		$messagePack = chr(0).chr(0).chr(0).chr(16);
		self::assertSame(16, TestCoder::getIntFromMessagePack_public($messagePack,4));
		self::assertSame(0, strlen($messagePack));
	}

	function testPythonTestCases() {
/*
		    cases = [
        (None, '\xc0'),
        (False, '\xc2'),
        (True, '\xc3'),
        (0, '\x00'),
        (127, '\x7f'),
        (128, '\xcc\x80'),
        (256, '\xcd\x01\x00'),
        (-1, '\xff'),
        (-33, '\xd0\xdf'),
        (-129, '\xd1\xff\x7f'),
        ]
*/
		self::assertEquals($this->debugMessagePack("\xc0"), $this->debugMessagePack( MsgPack_Coder::encode(null)));
		self::assertEquals($this->debugMessagePack("\xc2"), $this->debugMessagePack( MsgPack_Coder::encode(false)));
		self::assertEquals($this->debugMessagePack("\xc3"), $this->debugMessagePack( MsgPack_Coder::encode(true)));
		$testCases = array(
			0 => "\x00",
			127=> "\x7f",
			128=> "\xcc\x80",
			256=> "\xcd\x01\x00",
			-1=> "\xff",
			-33=> "\xd0\xdf",
			-129=> "\xd1\xff\x7f",
		);
		foreach( $testCases as $value=>$expected ) {
			$binary = MsgPack_Coder::encode($value);
			self::assertEquals($this->debugMessagePack($expected), $this->debugMessagePack($binary));
		}
/*
        ({1:1}, '\x81\x01\x01'),
        (1.0, "\xcb\x3f\xf0\x00\x00\x00\x00\x00\x00"),
        ([], '\x90'),
        (range(15),"\x9f\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e"),
        (range(16),"\xdc\x00\x10\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f"),
        ({}, '\x80'),
        (dict([(x,x) for x in range(15)]), '\x8f\x00\x00\x01\x01\x02\x02\x03\x03\x04\x04\x05\x05\x06\x06\x07\x07\x08\x08\t\t\n\n\x0b\x0b\x0c\x0c\r\r\x0e\x0e'),
        (dict([(x,x) for x in range(16)]), '\xde\x00\x10\x00\x00\x01\x01\x02\x02\x03\x03\x04\x04\x05\x05\x06\x06\x07\x07\x08\x08\t\t\n\n\x0b\x0b\x0c\x0c\r\r\x0e\x0e\x0f\x0f'),
*/
		$testCases = array(
			array(	'input' => array(1=>1),
					'expected' => "\x81\x01\x01",),
			array(	'input' => 1.0,
					'expected' => "\xcb\x3f\xf0\x00\x00\x00\x00\x00\x00",),
			array(	'input' => array(),
					'expected' => "\x90",),
			array(	'input' => array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15),
					'expected' => "\x9f\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e",),
			array(	'input' => array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16),
					'expected' => "\xdc\x00\x10\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f",),
			// others not possible, because all are lists (not associated arrays) not maps
		);
	}

	protected function debugMessagePack($messagePack) {
		$out = '';
		for($i=0; $i<strlen($messagePack); $i++) {
			$out.= ' '.dechex(ord($messagePack[$i]));
		}
		return $out;
	}
}

class TestCoder extends MsgPack_Coder {
	/**
	 * @static
	 * @param string $messagePack
	 * @param int $len
	 * @return int
	 */
	static public function getIntFromMessagePack_public(&$messagePack, $len) {
		return self::getIntFromMessagePack($messagePack, $len);
	}
}