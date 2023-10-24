<?php

session_start();
require __DIR__ . "/functions.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <h1>Cinema</h1>

    <h3>Welcome to the Cinema. Please order your tickets</h3>

    <form action="" method="post">
        <label for="numberOfSeats">Select the number of seats you want: </label><br>
        <input type="number" name="numberOfSeats" value="numberOfSeats"><br>
        <input type="submit" name="Submit" value="Submit">
    </form>

    <?php



    if (!isset($_SESSION['seatChart']))
        $_SESSION['seatChart'] = [];
    if (!isset($_SESSION['randomNumber']))
        $_SESSION['randomNumber'] = mt_rand(0, 40);

    $seatChart = $_SESSION['seatChart'];
    $numberOfSeatsTaken = $_SESSION['randomNumber'];
    $availableSeats = 40 - $numberOfSeatsTaken;

    if (isset($_POST['Submit'])) {

        $wantedTickets = intval($_POST['numberOfSeats']);
        if ($wantedTickets > $availableSeats)
            echo "Only " . $availableSeats . " seats are available. Please select fewer tickets.<br><br>";

        else {

            $foundTickets = [];
            $seatsAreFound = false;
            $inrow = true;
            $randomSeat = false;
            $foundTickets = checkForTickets($wantedTickets, $foundTickets, $seatChart, $inrow, $randomSeat);

            if (count($foundTickets) == $wantedTickets) $seatsAreFound = true;

            while ($seatsAreFound == false) {
                $inrow = false;
                $foundTickets = checkForTickets($wantedTickets, $foundTickets, $seatChart, $inrow, $randomSeat);
                if (count($foundTickets) == $wantedTickets) {
                    $seatsAreFound = true;
                    break;
                }

                $randomSeat = true;
                $foundTickets = checkForTickets($wantedTickets, $foundTickets, $seatChart, $inrow, $randomSeat);
                if (count($foundTickets) == $wantedTickets) $seatsAreFound = true;
            }

            if ($seatsAreFound == true) {

                echo 'Your Tickets: <br>';

                foreach ($foundTickets as $ticket) {
                    $parts = explode(",", $ticket);
                    $row = intval($parts[0]);
                    $seat = intval($parts[1]);
                    echo "row : " . $row . ", " . " seat: " . $seat . "<br>";
                    $seatChart[$row - 1][$seat - 1] = "YS";
                }
            }
        } //else
    } // if submit
    if (count($seatChart) == 0) {

        for ($i = 0; $i < 5; $i++) {
            $addSeats = [];
            for ($j = 0; $j < 8; $j++) {
                if ($i == 0 && ($j == 3 || $j == 4 || $j == 5))
                    array_push($addSeats, "RFDB");
                else array_push($addSeats, "A");
            }
            array_push($seatChart, $addSeats);
        }

        for ($i = 0; $i < $numberOfSeatsTaken; $i++) {
            $row = mt_rand(0, 4);
            $number = mt_rand(0, 7);
            if ($seatChart[$row][$number] == "UA") $i--;
            $seatChart[$row][$number] = "UA";
        }

        $_SESSION['seatChart'] = $seatChart;
    } //sold tickets before user.

    $rowCounter = 1;
    foreach ($seatChart as $key) {

        echo "Row " . $rowCounter . ": ";
        $rowCounter++;
    ?> <div class=seatChart><?php
                            foreach ($key as $value) {
                                if ($value == "A") $color = "green";
                                else if ($value == "YS") $color = "yellow";
                                else $color = "red";
                            ?> <div class="seat" style="background-color: <?php echo $color; ?>;"> <?php echo $value . " "; ?> </div> <?php
                                                                                                                                    }
                                                                                                                                        ?>

        </div>
        <br>
    <?php

    }

    ?>

    <div> <?php echo "Seats sold: " . $numberOfSeatsTaken . "/40 = " . $numberOfSeatsTaken / 0.4 . "%" ?></div>

</body>

</html>