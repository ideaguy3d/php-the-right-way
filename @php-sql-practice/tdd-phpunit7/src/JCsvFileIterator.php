<?php
/**
 * Created by PhpStorm.
 * User: julius
 * Date: 9/13/2018
 * Time: 12:18 PM
 */

declare(strict_types=1);

namespace TDD;


class JCsvFileIterator implements \Iterator
{
    protected $file;
    protected $key = 0;
    protected $current;
    
    public function __construct(string $file) {
        $this->file = fopen($file, 'r');
    }
    
    public function __destruct() {
        fclose($this->file);
    }
    
    public function rewind() {
        rewind($this->file);
        $this->current = fgetcsv($this->file);
        $this->key = 0;
    }
    
    public function valid() {
        return !feof($this->file);
    }
    
    public function key() {
        return $this->key;
    }
    
    public function current() {
        return $this->current;
    }
    
    public function next() {
        $this->current = fgetcsv($this->file);
        $this->key++;
    }
}