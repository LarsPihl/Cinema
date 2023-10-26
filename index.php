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
    <h4>The cost of each ticket is 10$.</h4>

    <form action="" method="post">
        <label for="numberOfSeats">Select the number of seats you want: </label><br>
        <input type="number" name="numberOfSeats" value="numberOfSeats"><br>
        <input type="submit" name="Submit" value="Submit">
    </form>

    <?php

    checkForSessionVariables(); //A random number (for number of seats already sold), and a seat chart is created 
    //if they don't already exist. These are put in variables, so that they can be used with mor eobvious names and lett clutter.

    $seatChart = $_SESSION['seatChart']; //
    $numberOfSeatsTaken = $_SESSION['randomNumber']; //
    $totalSales = $numberOfSeatsTaken * 10; //Total sum of sales, with each ticket costing 10$.
    $availableSeats = 40 - $numberOfSeatsTaken;

    foreach ($seatChart as $row) {
        foreach ($row as $seat) :
            if ($seat == "RFDB") $availableSeats--;
        endforeach;
    }

    if ($availableSeats == 0) echo 'Unfortunately, all seats are already sold.'; ?> <br> <?php

    //The number of available seats is calculated, by reducing the number of sold tickets, and unsold seats
    //reserved for handicapped people.

    if (isset($_POST['Submit'])) {

        /*If the user presses 'Submit', the input number if put in a variable in order to check whether
        that number of tickets if available. If the user is asking for tore tickets than are available, 
        an error message is printed.*/

        $wantedTickets = intval($_POST['numberOfSeats']);
        if ($wantedTickets > $availableSeats && $availableSeats != 0) {
            echo "Only " . $availableSeats . " seats are available. Please select fewer tickets."; ?><br><br> <?php
        } //if ($wantedTickets > $availableSeats)

        else {

        /*The number of tickets that the user asked for is available, and these are added to the
        number of sold tickets, and the total sales sum printed at the bottom of the page.*/

            $totalSales += $wantedTickets * 10;
            $numberOfSeatsTaken += $wantedTickets;

            /*An array is created that will store the bought tickets. A couple of bools are created in order to
            search until the tickets are found. '$inrow' starts as true, in order to first search for a pattern of 
            available seats in a single row. If such a pattern can't be found, the program will search for patterns in a row
            with added seats the row in fron and in the back, in order to have as many in a row as possible. This could mean
            for example that 2 persons sit in the last 2 seats in the first row, and 3 persons sit in the first 3 seats in 
            the second row. If such a pattern also can't be found, the third bool, '$randomSeat' is used in order to assign
            the user the first available seats, with no regards to whether these seats are interconnected.*/

            $foundTickets = [];
            $seatsAreFound = false;
            $inrow = true;
            $randomSeat = false;
            $foundTickets = checkForTickets($wantedTickets, $foundTickets, $seatChart, $inrow, $randomSeat);
            
            /*'checkForTickets' is first called with '$inrow = true' and '$randomSeat = false' in order to
            find conntected seats on one row that are available and match the requested number of tickets.
            If the returned array contains the same number of seats as the requested number from the user,
            the found tickets will be printed out, and the seats will be assigned "YS", meaning "your seat".
            Otherwise the program will attempt the aforementionen secondary and tertiary methods of finding
            seats before reassinging the seats and printing them. */

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

    /*If the seat chart is empty, the program will create 5 rows with 8 seats each. 3 seats in the first row 
    will be assigned 'RFDB', as in "reserved for disabled", and the rest will be assigned 'A' for available. */

    if (count($seatChart) == 0) {

        for ($i = 0; $i < 5; $i++) {
            $addSeats = [];

            for ($j = 0; $j < 8; $j++) {
                if ($i == 0 && ($j == 3 || $j == 4 || $j == 5)) array_push($addSeats, "RFDB");
                
                else array_push($addSeats, "A");
            }
            array_push($seatChart, $addSeats);
        }

        /*The randomly chosen number of sold seats is used, in order to create the same number of sold seats and assign
        them "UA", as in "unavailable". If a seat has already been assigned "UA", the counter is reduced one step
        in order to create the same unique number of unavailable seats in the chart as the value of the random number.*/

        for ($i = 0; $i < $numberOfSeatsTaken; $i++) {
            $row = mt_rand(0, 4);
            $number = mt_rand(0, 7);
            if ($seatChart[$row][$number] == "UA") $i--;
            $seatChart[$row][$number] = "UA";
        }

        $_SESSION['seatChart'] = $seatChart; //The changes are saved in the earlier used Session variable 
        //for the seating chart.
    } 

    /*The Seating chart is printed as a "<div> for each row, each containing 8 small "<div>"-elements. 
    A row counter is used to print the row number of each row. The assigned string on each seat is used to
    give the seats different background colours depending on if they are taken, available or sold to the user.*/

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

    <!--The number of sold tickets and the total sales sum is printed.-->

    <div> <?php echo "Seats sold: " . $numberOfSeatsTaken . "/40 = " . $numberOfSeatsTaken / 0.4 . "%"; ?></div>
    <div> <?php echo "Total sales: " . $totalSales . " $."; ?></div>

</body>

</html>