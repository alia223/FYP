@extends('layouts.app')
@section('content')
<?php
// Set timezone
date_default_timezone_set('Europe/London');

// Get prev & next month
if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    // This year and month
    $ym = date('Y-m');
}
//get the date from the url parameter
if (isset($_GET['ymd'])) {
    $ymd = $_GET['ymd'];
} else {
    //today
    $ymd = date('Y-m-d');
}

// Check format
$timestamp = strtotime($ym . '-01');  // the first day of the month
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

// Today (Format:2020-12-30)
$today = date('Y-m-d');
// Title (Format: December, 2020)
$title = date('F, Y', $timestamp);

// Create prev & next month link
$prev = date('Y-m', strtotime('-1 month', $timestamp));
$next = date('Y-m', strtotime('+1 month', $timestamp));

// Number of days in the month
$day_count = date('t', $timestamp);

// 1:Mon 2:Tue 3: Wed ... 7:Sun
$str = date('N', $timestamp);

// Array for calendar
$weeks = [];
$week = '';

// Add empty cell(s)
$week .= str_repeat('<td></td>', $str - 1);
//loop through calendar cells which contain dates
for ($day = 1; $day <= $day_count; $day++, $str++) {

    $date = $ym . '-' . $day;
    $currentDate = preg_split("[-]", $date);
    $currentD = $currentDate[2];
    $currentM = $currentDate[1];
    $currentY = $currentDate[0];
    $isToday = $currentD == date('d')+$rules->booking_interval && $currentM == date('m') && $currentY == date('Y');
    $dayIsGreater_MonthAndYearSame = $currentD > date('d')+$rules->booking_interval && $currentM == date('m') && $currentY == date('Y');
    $monthIsGreater_YearIsSame = $currentM > date('m') && $currentY == date('Y');
    $yearIsGreater = $currentY > date('Y');
    $todayOrLater = $isToday || $dayIsGreater_MonthAndYearSame || $monthIsGreater_YearIsSame || $yearIsGreater;
    //if the cell contains the current date or later i.e. today or later it is fine to display buttons in each of these cells
    //don't show buttons on cells of past as this is unecessary
    if($todayOrLater) {
        //if cell contains today's date and today isnt a saturday or sunday as schools dont have childcare clubs on weekends typically
        if ((strtotime($today) == strtotime($date)) && !(($str + 1) % 7 == 0 ) && !($str % 7 == 0)) {
            //if there is atleast 1 booking that exists on this day
            if(sizeof($bookings->where('booking_date', date('Y-m-d', strtotime($date)))) > 0) {
                //style button using admin rule colours and allow button to take user to a page that shows the bookings that have been made for that day
                $week .= '<td style="border-width: 3px; border-color:'.$rules->brand_colour.'"><a class="btn"';
                $week .= 'href="';
                $week .= url('bookings', date('Y-m-d', strtotime($date)));
            } 
            //if there are no bookings on the date contained within this current cell
            else{
                //if the user is an admin
                if(!Gate::denies('admin')) {
                    //apply admin rule colours and direct admin to a page showing all bookings
                    $week .= '<td style="border-width: 3px; border-color:'.$rules->brand_colour.'"><a class="btn" style="border-style: solid; border-color:rgb(0,0,0); 
                    background-color: white; color: black;"';
                    $week .= 'href="';
                    $week .= url('bookings', date('Y-m-d', strtotime($date)));
                }
                else {
                    //apply default black and white styling as there are no bookings on date contained within this cell
                    //Usually if there are no bookings the user is sent to the creat a booking page
                    //However, this is the admin so they wouldn't be booking sessions using the admin account
                    //Therefore, direct admin page that displays all bookings (i.e. 0 bookings)
                    $week .= '<td style="border-width: 3px; border-color:'.$rules->brand_colour.'"><a class="btn" style="background-color: white; border-style: solid; 
                    border-color:rgb(0,0,0); color: black"';
                    $week .= 'href="';
                    $week .= action('App\Http\Controllers\BookingController@create', ['date' => date('Y-m-d', strtotime($date))]);
                }
            }
            $week .= '">'.$day.'</a></td>';
        }
        //if it is saturday or sunday, style cell with red text as bookings are not possible on the weekend
        else if((($str + 1) % 7 == 0 ) || ($str % 7 == 0)) {
            $week .= '<td style="color: red;">'.$day.'</a></td>';
        } 
        //if the date contained within the cell isn't today and it isn't a weekend
        else {
            //if a booking exists on this day
            if(sizeof($bookings->where('booking_date', date('Y-m-d', strtotime($date)))) > 0) {
                //apply admin rule colours and direct user to bookings page displaying all bookings on the date contained within this cell
                $week .= '<td><a class="btn" style="border-style: solid; border-color:rgb(0,0,0);"';
                $week .= 'href="';
                $week .= url('bookings', date('Y-m-d', strtotime($date)));
            } 
            //otherwise, if there are no bookings
            else{
                //if it is an admin account
                if(!Gate::denies('admin')) {
                    //apply default black and white style and direct admin to booking page displaying 0 bookings
                    $week .= '<td><a class="btn" style="background-color: white; border-style: solid; border-color:rgb(0,0,0); color: black"';
                    $week .= 'href="';
                    $week .= url('bookings', date('Y-m-d', strtotime($date)));
                }
                //if it is a normal user i.e. a parent and there is no booking on this day then they will want to create a booking so direct them to this page
                else {
                    $week .= '<td><a class="btn" style="background-color: white; border-style: solid; border-color:rgb(0,0,0); color: black"';
                    $week .= 'href="';
                    $week .= action('App\Http\Controllers\BookingController@create', ['date' => date('Y-m-d', strtotime($date))]);
                }
            }
            //close all remaining tags
            $week .= '">'.$day.'</a></td>';
        }
    }
    //otherwise, if the date contained within this cell isn't today or later, i.e. it is a past date
    else {
        //if it is the weekend i.e. saturday or sunday create a cell with no styling or buttons directing user to anywhere as date is in the past
        if((($str + 1) % 7 == 0 ) || ($str % 7 == 0)) {
            $week .= '<td>'.$day.'</td>';
        } 
        //if it is the a weekday, again, create a cell with no styling or buttons directing user to anywhere as date is in the past
        else {
            //if a booking exists on this day
            if(sizeof($bookings->where('booking_date', date('Y-m-d', strtotime($date)))) > 0) {
                //apply admin rule colours and direct user to bookings page displaying all bookings on the date contained within this cell
                $week .= '<td><a class="btn" style="border-style: solid; border-color:rgb(0,0,0);"';
                $week .= 'href="';
                $week .= url('bookings', date('Y-m-d', strtotime($date)));
                //close all remaining tags
                $week .= '"';
                $week .= '>';
                $week .= $day . '</a></td>';
            } else {
                $week .= '<td>'.$day.'</td>';
            }
        }
    }

    // Sunday OR last day of the month
    if ($str % 7 == 0 || $day == $day_count) {

        // last day of the month
        if ($day == $day_count && $str % 7 != 0) {
            // Add empty cell(s)
            $week .= str_repeat('<td></td>', 7 - $str % 7);
        }

        $weeks[] = '<tr>'.$week.'</tr>';

        $week = '';
    }
}
?>
<div class="container" style="margin: 0; padding: 0;">
    <div class="row">
        <div class="col-md-2">
            <div class="sidebar" style="height:screen-height;">
                @include('sidebar')            
            </div>
        </div>
        <div class="col-md-2"></div>
            <div class="col-md-8">
                    <table style="margin: 0 auto;">
                        <tr>
                            <td>
                                <a href="?ym=<?= $prev; ?>" class="btn btn-link" style="color: <?php echo $rules->text_colour;?>; height: 25px; width:25px; padding:0; margin: 0;">
                                    <i class="material-icons">arrow_back</i>
                                </a>
                            </td>
                            <td>
                                <span class="text-center" style="color: <?php echo $rules->brand_colour; ?>"><?= $title; ?></span>
                            </td>
                            <td>
                                <a href="?ym=<?= $next; ?>" class="btn btn-link" style="color: <?php echo $rules->text_colour;?>; height: 25px; width:25px; padding:0; margin: 0;">
                                    <i class="material-icons">arrow_forward</i>
                                </a>
                            </td>
                        </tr>
                    </table>
                <p><a class="btn" href="{{ url('bookings') }}?ym=<?php echo date('Y-m');?>">Today</a></p>
                <table id="calendar" class="table table-bordered">
                    <thead>
                        <tr><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th><th>S</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($weeks as $week) { echo $week;} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#bookings').addClass('active');
    $('.btn').on("click", function() {
        getDate($(this).attr("value"))
    });
</script>
@endsection