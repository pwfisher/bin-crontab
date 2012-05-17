<?php

include('lib.cron.php');
include('fct/api.auction.php');


/**
 * collect all auction data and save to redis
 * run every 3 - 12 hours
 * 
 * @return N/A
 * @access public
 */
 
$auctions = new Auctions;

//$auctions->afternic();
$auctions->bido();
$auctions->cax();
$auctions->flippa();
// HUGE
//$auctions->godaddy();	// 150Mb +
//$auctions->sedo();	// Largest (no easy way to get new

// Make redis list of latest
	
?>