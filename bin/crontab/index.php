<?php

require_once('../../fct/class.db.php');
require_once('class.cron.php');


// delete option
if (isset($_REQUEST['delete'])) {
	if (isset($_REQUEST['cron_ID'])) {
		
	} else {
		$crontab->deleteCronFile($crontab->filename);
		$crontab->createCronFile($crontab->filename);
		$crontab->addToCrontab();
	}
	header("Location: index.php");
} else if (isset($_REQUEST['clean'])) {	// clean / rebuild crontab from db
	$crontab->saveCrontab();
	header("Location: index.php");
} else if (isset($_REQUEST['save'])) {	// save cron
	$cron_ID = $_REQUEST['cron_ID'];
	$cron_name = addslashes($_REQUEST['cron_name']);
	$cron_details = addslashes($_REQUEST['cron_details']);
	$cron_interval = $_REQUEST['cron_interval'];
	$cron_command = addslashes($_REQUEST['cron_command']);
	$cron_email = $_REQUEST['cron_email'];
	
	$crontab->saveCron($cron_interval, $cron_command, $cron_ID, $cron_name, $cron_details, $cron_email);
	$crontab->saveCrontab();
	$crontab->addToCrontab();
	header("Location: index.php");
}

?>

<a href="/bin/index.php" >&lt; Back</a><br />

<table><tr><td valign="top" width="50%">

    <div class="box">
      <h1>Crontab</h1>
       
      <div class="content" id="crontab"><? $crontab->printCrontab(); ?></div>
    </div>

    <div class="box">
      <h2>File Output [<a href="index.php?clean">Clean</a>] [<a href="index.php?delete">Delete</a>]</h2>
      
      <div class="content" id="source"><? $crontab->readCrontabDir(); ?></div>
    </div>

</td><td valign="top">

  	<div class="box">
      <h2>New Cron</h2>
      <form method="post" action="index.php?save">
      	<table cellpadding="0" cellspacing="0" id="new">
          <tr><td colspan="3">
            <input type="hidden" name="cron_ID" value="" />
            <input class="biFieldOne" type="text" name="cron_name" value="" />
          </td><td colspan="3">
            <input class="biFieldTwo" type="text" name="cron_interval" value="* * * * *" />
          </td></tr>
          <tr><td colspan="3">
            <span class="label">Name</span>
          </td><td colspan="3">
            <span class="label">Interval</span>
            <span class="caption">(see below)</span>
          </td></tr>
          <tr>
          <td colspan="6">
            <textarea class="longField" name="cron_command" rows="3" onFocus="this.rows=6" onBlur="this.rows=3"></textarea>
          </td></tr>
          <tr><td colspan="6">
            <span class="label">Command</span>
          </td></tr>
          <tr><td colspan="6">
            <input class="longField" type="text" name="cron_email" value="" />
          </td></tr>
          <tr><td colspan="6">
            <span class="label">Email</span>
            <span class="caption">Leave blank for default</span>
          </td></tr>
          <tr><td colspan="6">
            <textarea class="longField" name="cron_details" rows="3" onFocus="this.rows=6" onBlur="this.rows=3"></textarea>
          </td></tr>
          <tr><td colspan="6">
            <span class="label">Details</span>
          </td></tr>
        </table>
        <input type="submit" value="Save" />
  		</form>
    </div>
    <div class="box">
      <h2>Notes</h2>
      <div class="content">
      	<pre>* * * * * command to be executed
        - - - - -
        | | | | |
        | | | | ----- Day of week (0 - 7) (Sunday=0 or 7)
        | | | ------- Month (1 - 12)
        | | --------- Day of month (1 - 31)
        | ----------- Hour (0 - 23)
        ------------- Minute (0 - 59)</pre>
        
        <br />
        <p>Instead of the first five fields, you can use any one of eight special strings. It will not just save your time but it will improve readability.</p>
        <table border=1>
        <tr>
        
        <td> Special string</td>
        <td>Meaning</td>
        </tr>
        <tr>
        <td>@reboot</td>
        <td>Run once, at startup.</td>
        </tr>
        <tr>
        <td>@yearly / @annually</td>
        <td>Run once a year, "0 0 1 1 *".</td>
        </tr>
        <tr>
        <td>@monthly</td>
        <td>Run once a month, "0 0 1 * *".</td>
        </tr>
        <tr>
        <td>@weekly</td>
        <td>Run once a week, "0 0 * * 0".</td>
        </tr>
        <tr>
        <td>@daily / @midnight</td>
        <td>Run once a day, "0 0 * * *".</td>
        </tr>
        <tr>
        <td>@hourly</td>
        <td> Run once an hour, "0 * * * *".</td>
        </tr>
        <tr>
        <td>@now</td>
        <td> Run shortly after save time. "i+1 G j n *"</td>
        </tr>
        </table>
			</div>
    </div>
    <div class="box">
      <div class="title">Samples</div>
      <div class="content">
      	<p>To stop receiving email output from crontab you need to append <b>&gt;/dev/null 2&gt;&amp;1</b>.<br />
        <b>|</b> is used to "pipe" output from one program and turn it into input for the next program.<br />
        <b>;</b> seperates multiple commands.<br />
		<b>&amp;&amp;</b> seperates multiple commands, but require the previous to complete first.<br />
        <br />
        <a href="http://www.cyberciti.biz/faq/how-do-i-add-jobs-to-cron-under-linux-or-unix-oses/" target="_blank">More Details</a><br /><br />
        <b>Samples</b><br />
        */20 * * * * /usr/bin/lynx -source http://rfqs.ca/fct/cron/cron.tender_seo<br />
        0 5,20 * * * mysqldump -urfqs_user -prfqs12345 --all-databases | gzip -9 > backups/database/`date +\%F_\%H\%M`.sql.gz<br />
        php admin/plugins/run.AB.php<br />
        wget http://domain.com/<br />
        cd ../ &amp;&amp; php index.php<br />
        </p>
      </div>
    </div>
</td></tr></table>

<?php
$crontab->destroyFilePoint(); // OPTIONAL
?>