<?php

function checkForTickets(int $wantedTickets, array $foundTickets, array $seatChart, bool $inrow, bool $randomSeat): array
{

    $foundSeatCounter = 0;
    $foundTickets = [];

    for ($i = 0; $i < 5; $i++) {

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

/*function addBoughtSeats(array $foundTickets, array $seatChart)
{
    foreach ($foundTickets as $ticket) {
        $parts = explode(",", $ticket);
        $row = intval($parts[0]);
        $seat = intval($parts[1]);
        echo "row : " . $row . ", " . " seat: " . $seat . "<br>";
        $seatChart[$row - 1][$seat - 1] = "YS";
    }
} //function addBoughtSeats*/

function checkForSessionVariables()
{

    if (!isset($_SESSION['seatChart']))
        $_SESSION['seatChart'] = [];
    if (!isset($_SESSION['randomNumber']))
        $_SESSION['randomNumber'] = mt_rand(0, 40);
} //function checkForSessionVariables

function searchForTickets(array $seatChart, int $wantedTickets)
{

    $foundTickets = [];
    $seatsAreFound = false;
    $inrow = true;
    $randomSeat = false;
    $foundTickets = checkForTickets($wantedTickets, $foundTickets, $seatChart, $inrow, $randomSeat);

    if (count($foundTickets) == $wantedTickets) $seatsAreFound = true;

    else findSeatsOnDifferentRows($wantedTickets, $seatChart, $randomSeat, $foundTickets, $seatsAreFound);

    /*while ($seatsAreFound == false) {

    findSeatsOnDifferentRows($wantedTickets, $seatChart, $randomSeat);
    $inrow = false;
    $foundTickets = checkForTickets($wantedTickets, $foundTickets, $seatChart, $inrow, $randomSeat);
    if (count($foundTickets) == $wantedTickets) {
        $seatsAreFound = true;
        break;
    }

    $randomSeat = true;
    $foundTickets = checkForTickets($wantedTickets, $foundTickets, $seatChart, $inrow, $randomSeat);
    if (count($foundTickets) == $wantedTickets) $seatsAreFound = true;
    
}*/

    if ($seatsAreFound == true) {

        echo 'Your Tickets: ';
?>
        <br><br>
<?php

        foreach ($foundTickets as $ticket) {
            $parts = explode(",", $ticket);
            $row = intval($parts[0]);
            $seat = intval($parts[1]);
            echo "row : " . $row . ", " . " seat: " . $seat . "<br>";
            $seatChart[$row - 1][$seat - 1] = "YS";
        }
    } //if ($seatsAreFound == true)
} //function searchForTickets

function findSeatsOnDifferentRows(int $wantedTickets, array $seatChart, bool $randomSeat, array $foundTickets, bool $seatsAreFound)
{

    while ($seatsAreFound == false) {
        $inrow = false;
        $foundTickets = checkForTickets($wantedTickets, $foundTickets, $seatChart, $inrow, $randomSeat);

        if (count($foundTickets) == $wantedTickets) {
            $seatsAreFound = true;
            break;
        } //if (count($foundTickets) == $wantedTickets)

        $randomSeat = true;
        $foundTickets = checkForTickets($wantedTickets, $foundTickets, $seatChart, $inrow, $randomSeat);

        if (count($foundTickets) == $wantedTickets) $seatsAreFound = true;
    } //while ($seatsAreFound == false)

} //function findSeatsOnDifferentRows

/*function addBoughtTickets(array $foundTickets, array $seatChart)
{
    foreach ($foundTickets as $ticket) {
        $parts = explode(",", $ticket);
        $row = intval($parts[0]);
        $seat = intval($parts[1]);
        echo "row : " . $row . ", " . " seat: " . $seat . "<br>";
        $seatChart[$row - 1][$seat - 1] = "YS";
    }
}//function addBoughtTickets*/
