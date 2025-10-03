<?php declare(strict_types=1);

const DEAD = '.';
const ALIVE = 'X';

function readInput($string) {
    $matrix = [];
    $lines = explode("\n", $string);
    foreach ($lines as $line) {
        $matrix[] = str_split($line);
    }
    return $matrix;
}

function writeOutput($matrix) {
    return implode("\n", array_map(function ($line) {
        return implode('', $line);
    }, $matrix));
}


function gameStep($matrix) {
    $newMatrix = []; 
    foreach ($matrix as $x => $line){
        foreach ($line as $y => $cell){
            $neighbours = getNeighbours($matrix, $x, $y);
            $aliveCells = array_filter($neighbours, function($tmp) {
                return $tmp == 'X';
            });
            $aliveCells = count($aliveCells);
            $deadCells = count($neighbours) - $aliveCells;
            if ($aliveCells == 3 && $matrix[$x][$y] == '.'){
                $newMatrix[$x][$y] = 'X';
            } else if ( $matrix[$x][$y] == 'X' && ($aliveCells > 3 || $aliveCells < 2)){
                $newMatrix[$x][$y] = '.';
            } else {
                $newMatrix[$x][$y] = $matrix[$x][$y];
            }
        }
    }
    return $newMatrix;
}

function getNeighbours($matrix, $x, $y) {
    $neighbours = [];
    for ($dx = -1; $dx <= 1; $dx++){
        for ($dy = -1; $dy <= 1; $dy++){
            if ($dx == 0 && $dy == 0){
                continue;
            }
            $newX = $x + $dx;
            $newY = $y + $dy;
            if (isset($matrix[$newX][$newY])){
                $neighbours[] = $matrix[$newX][$newY];
            }
        }
    }
    return $neighbours;
}
