<?php

function checkForSessionVariables()
{

    /*Session variables for the seating chart and a random number between 0 and 40 (for number of seats already sold)
    are created if they don't already exist.*/

    if (!isset($_SESSION['seatChart']))
        $_SESSION['seatChart'] = [];
    if (!isset($_SESSION['randomNumber']))
        $_SESSION['randomNumber'] = mt_rand(0, 40);
} //function checkForSessionVariables


function checkForTickets(int $wantedTickets, array $foundTickets, array $seatChart, bool $inrow, bool $randomSeat): array
{

    $foundSeatCounter = 0;
    $foundTickets = [];

    for ($i = 0; $i < 5; $i++) {

        /*In the attempt to find connected seats in one row, the variables are redeclared on every new row
        so that the program doesn't count connected seats on different rows as a match. This instruction
        is only used the first time the function is called, when '$inrow' is true.*/

        if ($inrow == true) {
            $foundSeatCounter = 0;
            $foundTickets = [];
        }

        for ($j = 0; $j < 8; $j++) {
            if ($seatChart[$i][$j] == "A") {
                array_push($foundTickets, $i + 1 . "," . $j + 1);
                $foundSeatCounter++;
            } //if ($seatChart[$i][$j] == "A") 
            else {
                if ($randomSeat == false) {
                    $foundTickets = [];
                    $foundSeatCounter = 0;
                }
            } //else

            if ($foundSeatCounter == $wantedTickets) {
                return $foundTickets;
            } //if
        } //inner loop

    } //outer loop

    $foundTickets = [];

    return $foundTickets;
} //function checkForTickets
