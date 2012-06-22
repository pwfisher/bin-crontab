<?
/**
 *	This class is here to help you create cron jobs with ease.
 *
 *	Version 0.1
 *	-------------------------
 *	Creating cron jobs functions
 *
 *	To do in next version: make cron job manipulations
 *
 *	@author	Richard Sumilang	<richard@richard-sumilang.com>
 *  @author will Farrell
 *	@package
 */

class crontab{
	
	var $interval=NULL;
	var $command=NULL;
	var $directory="";
	var $filename="root.crontab";
	var $email="admin@domain.tld";	//
	var $crontabPath=NULL;
	var $handle=NULL;
	
	/**
	 *	Constructor. Attempts to create directory for
	 *	holding cron jobs
	 *
	 *	@author	Richard Sumilang	 <richard@richard-sumilang.com>
	 *	@param	string	$dir		 Directory to hold cron job files
	 *	@param	string	$filename	 Filename to write to
	 *	@param	string	$crontabPath Path to cron program
	 *	@access	public
	 */
	function __construct($filename=NULL, $dir=NULL, $crontabPath=NULL){
		global $database;
		
		//$this->directory = ($dir) ? $dir : dirname(__FILE__).DIRECTORY_SEPARATOR;
		$this->filename = dirname(__FILE__).DIRECTORY_SEPARATOR.(($filename)?$filename:$this->filename);
		
		// create table if not existant
		$this->db = $database;
		$q = "CREATE TABLE IF NOT EXISTS `crontab` (
			  `cron_ID` int(11) NOT NULL AUTO_INCREMENT,
			  `cron_name` text COLLATE utf8_unicode_ci NOT NULL,
			  `cron_details` text COLLATE utf8_unicode_ci NOT NULL,
			  `cron_interval` text COLLATE utf8_unicode_ci NOT NULL,
			  `cron_command` text COLLATE utf8_unicode_ci NOT NULL,
			  `cron_email` text COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (`cron_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
		$this->db->query($q);
		
		//$result=$this->setDirectory($this->directory);
		//if(!$result)
		//	exit('Directory error');
		$result= $this->createCronFile($this->filename);
		if(!$result)
			exit('File error');
		$this->pathToCrontab=($crontabPath) ? NULL : $crontabPath;
	}
	
	function __destruct() {
		$this->addToCrontab();
	}

	/**
	 *	Set date parameters
	 *
	 *	If any parameters are left NULL then they default to *
	 *
	 *	A hyphen (-) between integers specifies a range of integers. For
	 *	example, 1-4 means the integers 1, 2, 3, and 4.
	 *
	 *	A list of values separated by commas (,) specifies a list. For
	 *	example, 3, 4, 6, 8 indicates those four specific integers.
	 *
	 *	The forward slash (/) can be used to specify step values. The value
	 *	of an integer can be skipped within a range by following the range
	 *	with /<integer>. For example, 0-59/2 can be used to define every other
	 *	minute in the minute field. Step values can also be used with an asterisk.
	 *	For instance, the value * /3 (no space) can be used in the month field to run the
	 *	task every third month...
	 *
	 *	@author	Richard Sumilang	<richard@richard-sumilang.com>
	 *	@param	mixed	$min		Minute(s)... 0 to 59
	 *	@param	mixed	$hour		Hour(s)... 0 to 23
	 *	@param	mixed	$day		Day(s)... 1 to 31
	 *	@param	mixed	$month		Month(s)... 1 to 12 or short name
	 *	@param	mixed	$dayofweek	Day(s) of week... 0 to 7 or short name. 0 and 7 = sunday
	 *	$access	public
	 */
	function setInterval($interval=NULL){
		
		if($interval) $this->interval=$interval;
		else $this->interval="@reboot";
		
	}
	
	/**
	 *	Set the directory path. Will check it if it exists then
	 *	try to open it. Also if it doesn't exist then it will try to
	 *	create it, makes it with mode 0700
	 *
	 *	@author	Richard Sumilang	<richard@richard-sumilang.com>
	 *	@param	string	$directory	Directory, relative or full path
	 *	@access	public
	 *	@return	boolean
	 */
	function setDirectory($directory){
		if(!$directory) return false;
		
		if(is_dir($directory)){
			if($dh=opendir($directory)){
				$this->directory=$directory;
				return true;
			}else
				return false;
		}else{
			echo $directory;
			if(mkdir($directory, 0700)){
				$this->directory=$directory;
				return true;
			}
		}
		return false;
	}
	
	
	/**
	 *	Create cron file
	 *
	 *	This will create a cron job file for you and set the filename
	 *	of this class to use it. Make sure you have already set the directory
	 *	path variable with the consructor. If the file exists and we can write
	 *	it then return true esle false. Also sets $handle with the resource handle
	 *	to the file
	 *
	 *	@author	Richard Sumilang	<richard@richard-sumilang.com>
	 *	@param	string	$filename	Name of file you want to create
	 *	@access	public
	 *	@return	boolean
	 */
	function createCronFile($filename=NULL){
		if(!$filename)
			return false;
		
		if(file_exists($this->directory.$filename)){
			if ($handle=fopen($this->directory.$filename, 'a')){
				$this->handle=&$handle;
				$this->filename=$filename;
				return true;
			} else
				return false;
		} else {
			$fh = fopen($this->directory.$filename, 'w') or print("can't open file");
			fwrite($fh, '');
			fclose($fh);
		}
		
		if(!$handle=fopen($this->directory.$filename, 'a'))
			return false;
		else{
			$this->handle=&$handle;
			$this->filename=$filename;
			return true;
		}
	}
	
	function deleteCronFile($filename=NULL){
		if(!$filename)
			return false;
		
		if(file_exists($this->directory.$filename))
			unlink($this->directory.$filename);
		return true;
	}
	
	/**
	 *	Set command to execute
	 *
	 *	@author	Richard Sumilang	<richard@richard-sumilang.com>
	 *	@param	string	$command	Comand to set
	 *	@access	public
	 *	@return	string	$command
	 */
	function setCommand($command){
		if($command){
			$this->command=$command;
			return false;
		}else
			return false;
	}
	
	
	
	/**
	 *	Write cron command to file. Make sure you used createCronFile
	 *	before using this function of it will return false
	 *
	 *	@author	Richard Sumilang	<richard@richard-sumilang.com>
	 *	@access	public
	 *	@return	void
	 */
	function saveCronFile(){
		$command=$this->interval." ".$this->command."\n";
		if(!fwrite($this->handle, $command))
			return true;
		else
			return false;
	}
	
	/**
	 *	Write cron command to file. Make sure you used createCronFile
	 *	before using this function of it will return false
	 *
	 *	@author	Richard Sumilang	<richard@richard-sumilang.com>
	 *	@access	public
	 *	@return	void
	 */
	function saveCronHeader($email = NULL){
		//$command .= "SHELL=/bin/bash\n";
		//$command .= "PATH=/sbin:/bin:/usr/sbin:/usr/bin\n";
		if($email) $command = "MAILTO=".$email."\n";
		else if($this->email) $command = "MAILTO=".$this->email."\n";
		else $command = "MAILTO=root\n";
		//$command .= "HOME=/\n";

		if(!fwrite($this->handle, $command))
			return true;
		else
			return false;
	}
	
	
	/**
	 *	Save cron in system
	 *
	 *	@author	Richard Sumilang	<richard@richard-sumilang.com>
	 *	@access	public
	 *	@return boolean				true if successful else false
	 */
	function addToCrontab($crontabPath = ''){
		
		if(!$this->filename)
			exit('No name specified for cron file');
					
		if(exec($crontabPath."crontab ".$this->directory.$this->filename))
			return true;
		else
			return false;
	}
	
	
	/**
	 *	Destroy file pointer
	 *
	 *	@author	Richard Sumilang	<richard@richard-sumilang.com>
	 *	@access	public
	 *	@return void
	 */
	function destroyFilePoint(){
		fclose($this->handle);
		return true;
	}
	
	/**
	 *	Read cron file
	 *
	 *	@author	will Farrell	<will.farrell@gmail.com>
	 *	@access	public
	 *	@return file lines
	 */
	function readCrontab(){
		$lines = file($this->directory.$this->filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		//$data = file_get_contents($this->directory.$this->filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		//var_dump($lines);
		//$lines = explode("\n", $data);
		if ($lines) {
			// Loop through our array, show HTML source as HTML source; and line numbers too.
			$return = '';
			foreach ($lines as $line_num => $line) {
					//echo "Line #<b>{$line_num}</b> : " . htmlspecialchars($line) . "<br />\n";
					$return .= htmlspecialchars($line) . "<br />\n";
			}
			echo $return;
		} else {
			return false;
		}
	}
	
	
	/**
	 *	Get crontab from DB
	 *
	 *	@author	will Farrell	<will.farrell@gmail.com>
	 *	@access	public
	 *	@return file lines
	 */
	function printCrontab(){
		global $database;
		
		$q = "SELECT cron_ID FROM crontab ORDER BY cron_name";
		$r = $database->query($q);
		
		if(!$r || (mysql_num_rows($r) < 1)){
			echo "No crons found.";
		} else {
			while($p = mysql_fetch_assoc($r)) {
				$this->printCron($p['cron_ID']);
			}
		}
		
	}
	
	/**
	 *	Get cront from DB
	 *
	 *	@author	will Farrell	<will.farrell@gmail.com>
	 *	@access	public
	 *	@return file lines
	 */
	function printCron($id = 0){
		global $database;
		
		$q = "SELECT * FROM crontab WHERE cron_ID = '{{cron_ID}}'";
		$r = $database->query($q, array('cron_ID' => $id));
		
		$p = mysql_fetch_assoc($r);
		?>
		<div id="show<? echo $p['cron_ID']; ?>" style="display:block" onclick="this.style.display = 'none'; getElementById('edit<? echo $p['cron_ID']; ?>').style.display = 'block';">
		<? echo "<b>".stripslashes($p['cron_name']).":</b> ".stripslashes($p['cron_interval'])." ".urldecode($p['cron_command']); //".$p['cron_ID'].")  ?></div>
		<div id="edit<? echo $p['cron_ID']; ?>" style="display:none;">
        <form method="post" action="index.php?save">
		<table cellpadding="0" cellspacing="0">
			<tr><td colspan="3">
				<input type="hidden" name="cron_ID" value="<? echo $p['cron_ID']; ?>" />
				<input class="biFieldOne" type="text" name="cron_name" value="<? echo stripslashes($p['cron_name']); ?>" />
			</td><td colspan="3">
				<input class="biFieldTwo" type="text" name="cron_interval" value="<? echo stripslashes($p['cron_interval']); ?>" />
			</td></tr>
			<tr><td colspan="3">
				<span class="label">Name</span>
			</td><td colspan="3">
				<span class="label">Interval</span>
				<span class="caption">See right column</span>
			</td></tr>
			<tr>
			<td colspan="6">
				<textarea class="longField" name="cron_command" rows="3" onFocus="this.rows=6" onBlur="this.rows=3"><? echo urldecode($p['cron_command']); ?></textarea>
			</td></tr>
			<tr><td colspan="6">
				<span class="label">Command</span>
			</td></tr>
			<tr><td colspan="6">
				<input class="longField" type="text" name="cron_email" value="<?php echo stripslashes($p['cron_email']); ?>" />
			</td></tr>
			<tr><td colspan="6">
				<span class="label">Email</span>
				<span class="caption">Leave blank for default</span>
			</td></tr>
			<tr><td colspan="6">
				<textarea class="longField" name="cron_details" rows="3" onFocus="this.rows=6" onBlur="this.rows=3"><? echo stripslashes($p['cron_details']); ?></textarea>
			</td></tr>
			<tr><td colspan="6">
				<span class="label">Details</span>
			</td></tr>
		</table>
        <input type="submit" value="Save" />
  		</form>
		</div><?
		
		
	}
	
	/**
	 *	Save cron to DB
	 *
	 *	@author	will Farrell	<will.farrell@gmail.com>
	 *	@access	public
	 *	@return void
	 */
	function saveCron($cron_interval, $cron_command, $cron_ID = '', $cron_name = '', $cron_details = '', $cron_email = ''){
		global $database;
		
		$s = array('cron_ID' => $cron_ID,
							'cron_name' => $cron_name, 
							'cron_details' => $cron_details,
							'cron_interval' => $cron_interval,
							'cron_command' => $cron_command, 
							'cron_email' => $cron_email);
		$database->insert('crontab', $s);
	}
	
	/**
	 *	Save crontab from DB
	 *
	 *	@author	will Farrell	<will.farrell@gmail.com>
	 *	@access	public
	 *	@return void
	 */
	function saveCrontab(){
		global $database;
		
		$q = "SELECT cron_interval, cron_command, cron_email FROM crontab";
		$r = $database->query($q);
		
		if(file_exists($this->directory.$this->filename)) unlink($this->directory.$this->filename);
		$this->createCronFile($this->filename);
		
		//$this->saveCronHeader();
		
		while($c = mysql_fetch_assoc($r)) {
			$this->addCron($c['cron_command'], $c['cron_interval'], $c['cron_email']);
		}
		
		$this->addToCrontab();
	}
	
	/**
	 *	Save new command to crontab from DB
	 *
	 *	@author	will Farrell	<will.farrell@gmail.com>
	 *	@access	public
	 *	@return void
	 */
	function addCron($command = '', $interval = '@now', $header = ''){
		if($interval == '@now') {
			$interval = (date("i")+1)." ".date("G")." ".date("j")." ".date("n")." *";
		}
		$this->saveCronHeader($header);
		$this->setInterval($interval);
		$this->setCommand(urldecode($command));
		$this->saveCronFile();
	}
	
}

$crontab = new crontab();
	
?>