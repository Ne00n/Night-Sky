<?php

//Stolen from http://stackoverflow.com/questions/45953/php-execute-a-background-process

class BackgroundProcess {

  //This launches the command $cmd, redirects the command output to $outputfile, and writes the process id to $pidfile.
  public function startProcess($cmd)
  {
    exec(sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));
    return $pidfile;
  }

  //Checks if the Process with the PID is still running
  public function isRunning($pid)
  {
      try{
          $result = shell_exec(sprintf("ps %d", $pid));
          if( count(preg_split("/\n/", $result)) > 2){
              return true;
          }
      }catch(Exception $e){}

      return false;
  }

}

?>
