<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 9/5/2018
 * Time: 2:53 AM
 *
 * solving a HackerRank problem
 */

//-- sample input 1:
queensAttack(5, 3, 4, 3, [[5, 5], [4, 2], [2, 3]]);

function queensAttack (int $n, int $k, $r_q, $c_q, array $obstacles): void {

}

/**
 * @param $n
 * @param $k
 * @param $r_q
 * @param $c_q
 * @param array $obstacles
 */
function queensAttackV1(int $n, int $k, $r_q, $c_q, array $obstacles): void {
    var_dump($obstacles);
    $board = [];
    // construct the board
    for($i=0; $i<$n; $i++) {
        $board[$i] = [];
        for($j=0; $j<$n; $j++) {
            $board[$i] []= 'O';
        }
    }

    // place queen on board
    $board[($r_q-1)][($c_q-1)] = 'Q';
    $queenRow = $board[($r_q-1)];

    // place obstacles on board
    for ($i = 0; $i < count($obstacles); $i++) {
        $rec = $obstacles[$i]; 
        $board[($rec[0]-1)][($rec[1]-1)] = 'X';
    }

    // sum horizontal moves


    // sum vertical moves

    // sum diagonal moves

    var_dump($board);
    echo "breakpoint";
}