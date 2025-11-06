<?php

//namespace Inn\App;
namespace src;

class Test
{
public $string_data = 'string';
protected $prot_data = 'string protected';
private $id;
public static $count = 0;

public function __construct()
{
    $this->id = '2003';
    self::$count++;
    echo "Object {$this->id} created. Exist: " . self::$count . '.<br>';
}

public function __destruct()
{
    self::$count--;
    echo 'Destroyed. Left: ' . self::$count . '.<br>';
}

    public function hello(){
    echo 'hello!';
}
}