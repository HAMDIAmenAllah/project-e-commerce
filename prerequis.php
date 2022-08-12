<?php

function increment (int &$a){
    $a++;
}

$x=4;

increment($x);

echo $x;