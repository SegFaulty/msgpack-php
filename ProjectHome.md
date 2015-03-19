# msgpack-php #
## Summary ##
This project provides a pure PHP-implementation of the [MessagePack-format](http://msgpack.sourceforge.net/spec).

_"MessagePack is an extremely efficient object serialization library. It's like JSON, but very fast and small"_ (http://msgpack.sourceforge.net)

## Common ##
The goal of this project is to be a first, simple, and easy to use solution, but it is not highly performing (because it's php not c).

## Usage ##
```
// encode
$phpVariable = array( 1, 'bla', 'foo');
$binaryString = MsgPack_Coder::encode($phpVariable);

// decode
$phpVariable = MsgPack_Coder::decode($binaryString);
```

## Download ##
"`svn checkout http://msgpack-php.googlecode.com/svn/trunk/ msgpack-php-read-only`"

or copy the file from [here](https://code.google.com/p/msgpack-php/source/browse/trunk/lib/MsgPack/Coder.php)

## History ##
This project was initially developed for use with a MessageQueue System for [Schottenland.de](http://www.schottenland.de) on top of the advanced key-value-store [redis](http://code.google.com/p/redis/).
