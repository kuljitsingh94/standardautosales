<?php
/**
* Compute the start and end date of some fixed o relative quarter in a specific year.
* @param mixed $quarter  Integer from 1 to 4 or relative string value:
*                        'this', 'current', 'previous', 'first' or 'last'.
*                        'this' is equivalent to 'current'. Any other value
*                        will be ignored and instead current quarter will be used.
*                        Default value 'current'. Particulary, 'previous' value
*                        only make sense with current year so if you use it with
*                        other year like: get_dates_of_quarter('previous', 1990)
*                        the year will be ignored and instead the current year
*                        will be used.
* @param int $year       Year of the quarter. Any wrong value will be ignored and
*                        instead the current year will be used.
*                        Default value null (current year).
* @param string $format  String to format returned dates
* @return array          Array with two elements (keys): start and end date.
*/
function get_dates_of_quarter($quarter = 'current', $year = null, $format = null)
{
    if ( !is_int($year) ) {        
       $year = (new DateTime)->format('Y');
    }
    $current_quarter = ceil((new DateTime)->format('n') / 3);
    switch (  strtolower($quarter) ) {
    case 'this':
    case 'current':
       $quarter = ceil((new DateTime)->format('n') / 3);
       break;

    case 'previous':
       $year = (new DateTime)->format('Y');
       if ($current_quarter == 1) {
          $quarter = 4;
          $year--;
        } else {
          $quarter =  $current_quarter - 1;
        }
        break;

    case 'first':
        $quarter = 1;
        break;

    case 'last':
        $quarter = 4;
        break;

    default:
        $quarter = (!is_int($quarter) || $quarter < 1 || $quarter > 4) ? $current_quarter : $quarter;
        break;
    }
    if ( $quarter === 'this' ) {
        $quarter = ceil((new DateTime)->format('n') / 3);
    }
    $start = new DateTime($year.'-'.(3*$quarter-2).'-1 00:00:00');
    $end = new DateTime($year.'-'.(3*$quarter).'-'.($quarter == 1 || $quarter == 4 ? 31 : 30) .' 23:59:59');

    return array(
        'start' => $format ? $start->format($format) : $start,
        'end' => $format ? $end->format($format) : $end,
    );
}
?>