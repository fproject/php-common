<?php
///////////////////////////////////////////////////////////////////////////////
//
// © Copyright f-project.net 2010-present.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
///////////////////////////////////////////////////////////////////////////////

namespace fproject\common\utils;

use Exception;

/**
 * Utility class to walk through a data stream byte by byte with conventional names
 *
 */
class BinaryStream
{
    /**
     * @var string Byte stream
     */
    protected $_stream;

    /**
     * @var int Length of stream
     */
    protected $_streamLength;

    /**
     * @var bool BigEndian encoding?
     */
    protected static $_bigEndian;

    /**
     * Check if system is using BigEndian encoding
     * @return boolean
     */
    public function isBigEndian()
    {
        return self::$_bigEndian;
    }

    /**
     * @var int Current position in stream
     */
    protected $_needle;

    /**
     * @var bool str* functions overloaded using mbstring.func_overload?
     */
    protected $_mbStringFunctionsOverloaded;

    /**
     * Constructor
     *
     * Create a reference to a byte stream that is going to be parsed or created
     * by the methods in the class. Detect if the class should use big or
     * little Endian encoding.
     *
     * @param  string $stream use '' if creating a new stream or pass a string if reading.
     * @throws Exception
     */
    public function __construct($stream)
    {
        if (!is_string($stream)) {
            throw new Exception('Inputdata is not of type String');
        }

        $this->_stream       = $stream;
        $this->_needle       = 0;
        $this->_mbStringFunctionsOverloaded = function_exists('mb_strlen') && (ini_get('mbstring.func_overload') !== '') && ((int)ini_get('mbstring.func_overload') & 2);
        $this->_streamLength = $this->_mbStringFunctionsOverloaded ? mb_strlen($stream, '8bit') : strlen($stream);
        self::checkSystemBigEndian();
    }

    /**
     * Looks if the system is Big Endian or not
     */
    static private function checkSystemBigEndian()
    {
        if(!isset(self::$_bigEndian))
        {
            self::$_bigEndian  = (pack('l', 1) === "\x00\x00\x00\x01");
        }
    }

    /**
     * Returns the current stream
     *
     * @return string
     */
    public function getStream()
    {
        return $this->_stream;
    }

    /**
     * Read the number of bytes in a row for the length supplied.
     *
     * @todo   Should check that there are enough bytes left in the stream we are about to read.
     * @param  int $length
     * @return string
     * @throws Exception for buffer under-run
     */
    public function readBytes($length)
    {
        if (($length + $this->_needle) > $this->_streamLength) {
            throw new Exception('Buffer underrun at needle position: ' . $this->_needle . ' while requesting length: ' . $length);
        }
        $bytes = $this->_mbStringFunctionsOverloaded ? mb_substr($this->_stream, $this->_needle, $length, '8bit') : substr($this->_stream, $this->_needle, $length);
        $this->_needle+= $length;
        return $bytes;
    }

    /**
     * Write any length of bytes to the stream
     *
     * Usually a string.
     *
     * @param  string $bytes
     * @return BinaryStream
     */
    public function writeBytes($bytes)
    {
        $this->_stream.= $bytes;
        return $this;
    }

    /**
     * Reads a signed byte
     *
     * @return int Value is in the range of -128 to 127.
     * @throws Exception
     */
    public function readByte()
    {
        if (($this->_needle + 1) > $this->_streamLength) {
            throw new Exception(
                'Buffer underrun at needle position: '
                . $this->_needle
                . ' while requesting length: '
                . $this->_streamLength
            );
        }

        return ord($this->_stream{$this->_needle++});
    }

    /**
     * Writes the passed string into a signed byte on the stream.
     *
     * @param  string $stream
     * @return BinaryStream
     */
    public function writeByte($stream)
    {
        $this->_stream.= pack('c', $stream);
        return $this;
    }

    /**
     * Reads a signed 32-bit integer from the data stream.
     *
     * @return int Value is in the range of -2147483648 to 2147483647
     */
    public function readInt()
    {
        return ($this->readByte() << 8) + $this->readByte();
    }

    /**
     * Write an the integer to the output stream as a 32 bit signed integer
     *
     * @param  int $stream
     * @return BinaryStream
     */
    public function writeInt($stream)
    {
        $this->_stream.= pack('n', $stream);
        return $this;
    }

    /**
     * Reads a UTF-8 string from the data stream
     *
     * @return string A UTF-8 string produced by the byte representation of characters
     */
    public function readUTF()
    {
        $length = $this->readInt();
        return $this->readBytes($length);
    }

    /**
     * Wite a UTF-8 string to the outputstream
     *
     * @param  string $stream
     * @return BinaryStream
     */
    public function writeUTF($stream)
    {
        $this->writeInt($this->_mbStringFunctionsOverloaded ? mb_strlen($stream, '8bit') : strlen($stream));
        $this->_stream.= $stream;
        return $this;
    }


    /**
     * Read a long UTF string
     *
     * @return string
     */
    public function readLongUTF()
    {
        $length = $this->readLong();
        return $this->readBytes($length);
    }

    /**
     * Write a long UTF string to the buffer
     *
     * @param  string $stream
     * @return BinaryStream
     */
    public function writeLongUTF($stream)
    {
        $this->writeLong($this->_mbStringFunctionsOverloaded ? mb_strlen($stream, '8bit') : strlen($stream));
        $this->_stream.= $stream;
    }

    /**
     * Read a long numeric value
     *
     * @return double
     */
    public function readLong()
    {
        return ($this->readByte() << 24) + ($this->readByte() << 16) + ($this->readByte() << 8) + $this->readByte();
    }

    /**
     * Write long numeric value to output stream
     *
     * @param  int|string $stream
     * @return BinaryStream
     */
    public function writeLong($stream)
    {
        $this->_stream.= pack('N', $stream);
        return $this;
    }

    /**
     * Read a 16 bit unsigned short.
     *
     * @todo   This could use the unpack() w/ S,n, or v
     * @return double
     */
    public function readUnsignedShort()
    {
        $byte1 = $this->readByte();
        $byte2 = $this->readByte();
        return (($byte1 << 8) | $byte2);
    }

    /**
     * Reads an IEEE 754 double-precision floating point number from the data stream.
     *
     * @return double|null Floating point number
     */
    public function readDouble()
    {
        $bytes = $this->_mbStringFunctionsOverloaded ? mb_substr($this->_stream, $this->_needle, 8, '8bit') : substr($this->_stream, $this->_needle, 8);
        $this->_needle+= 8;

        if (!self::$_bigEndian) {
            $bytes = strrev($bytes);
        }

        $double = unpack('dflt', $bytes);
        $flt = $double['flt'];
        if(is_nan($flt))
            return null;
        else
            return $flt;
    }

    /**
     * Writes an IEEE 754 double-precision floating point number from the data stream.
     *
     * @param  string|double $stream
     * @return BinaryStream
     */
    public function writeDouble($stream)
    {
        $stream = pack('d', $stream);
        if (!self::$_bigEndian) {
            $stream = strrev($stream);
        }
        $this->_stream.= $stream;
        return $this;
    }

}
