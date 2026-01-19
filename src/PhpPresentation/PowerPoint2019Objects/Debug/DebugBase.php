<?php
declare(strict_types=1);

namespace PhpOffice\PhpPresentation\PowerPoint2019Objects\Debug;

use Exception;

/**
 * Base class for debugging with all kinds of usefull debugging methods
*/
class DebugBase
{
    protected $error_log_message_type = 0;
    protected $error_log_destination = 'error_log';
 
    /**
     * Constructor 
     *    Diverts the error_log output to a custom error log if $GLOBALS['error_log_destination'] is set.
     *    Use enablePersonalDebugLog() to divert the error log output.
    */
    public function __construct()
    {
        if (isset($GLOBALS['error_log_destination']))
        {
           $this->error_log_message_type = 3;
           $this->error_log_destination = $GLOBALS['error_log_destination'];
        }
    }

    /**
     * Enable a personal debug log for /var/www/adoperations/storage/logs/<USERNAME>_error_log
    */
    public function enablePersonalDebugLog()
    {
        $username = "BasVijfwinkel";
        $GLOBALS['error_log_destination'] = '/var/www/adoperations/storage/logs/'.str_replace(" ","",$username).'_error_log';
        // set the settings explicitly so that the calling class will also output data to the alternative log file
        $this->error_log_message_type = 3;
        $this->error_log_destination = $GLOBALS['error_log_destination'];
    }

    /**
     * Disable the personal debug log
    */
    public function disableAlternativeLogDestination()
    {
        $this->error_log_message_type = 0;
    }

    /**
     * Show the keys of the object in the log file
     * @param obj
     *     array obj
    */
    protected function ek($obj)
    {
       if (is_array($obj))
       {
           $this->e(array_keys($obj));
       }
       else
       {
           $this->e("*** error *** : Object is not an array");
       }
    }

    /**
     * Show the number of elements in the array object in the log file
     * @param obj
     *     array obj
    */
    protected function ec($obj)
    {
       if (is_array($obj))
       {
           $this->e(count($obj));
       }
       else
       {
           $this->e("*** error *** : Object is not an array");
           $this->e($obj);
       }
    }

    /**
     * Show the object in the log file
     * @param obj
     *     array obj
    */
    protected function e($obj)
    {
        if (func_num_args() > 1)
        {
            foreach(func_get_args() as $arg) { $this->e($arg); }
        }
        else
        {
          if (is_array($obj))
          {
           if (count($obj) == 0)
           {
               $this->_log('[] (empty array)');
           }
           else
           {
               if (count($obj) > 1000)
               {
                   $obj = array_slice($obj,0,500);
                   $obj[] = "(......... ONLY SHOWING FIRST 500 entries ..........)";
               }
               $this->error_log2(var_export($obj,true));
           }
          }
          elseif (is_bool($obj))
          {
            $this->_log((!$obj)?"false (bool)":"true (bool)");
          }
          elseif (is_object($obj))
          {
              $this->_log('Object of type : '.get_class($obj));
              if (method_exists($obj,'toString'))
              {
                  $this->error_log2($obj->toString());
              }
          }
          else
          {
            if (is_null($obj))
            {
                $this->_log('NULL');
            }
            else
            {
                if (is_string($obj))
                {
                    if ((trim(strtolower($obj)) == 'false') || (trim(strtolower($obj)) == 'true'))
                    {
                        // true/false as string
                        $this->_log($obj." (string)");
                    }
                    else
                    {
                       // some text
                       $this->error_log2($obj);
                    }
                }
                else
                {
                    // something else
                    $this->_log($obj);
                }
            }
          }
        }
    }

    /**
     * Helper method for error_log that chops up the string in 1KB blocks
     * @params output
     *     string output string to print
     * @params suppressLeadingInfo
     *     boolean suppress the leading date and filename
    */
    protected function error_log2($output, $suppresLeadingInfo = false)
    {
        if (strlen($output) > 1024)
        {
            $str = substr($output,0,1024);

            // try to split on space around 1024th character
            $pos = strrpos($str," ");
            if ($pos === false)
            {
                // try to split on a comma
                $pos = strrpos($str,","); 
            }

            if (($pos === false) || ($pos < 500))
            {
                $pos = 1024;
            }
            else
            {
                $str = substr($str,0,$pos);
            }

            $this->_log($str,true);
            $this->error_log2(substr($output,$pos), true);
        }
        else
        {
            $this->_log($output,$suppresLeadingInfo);
        }
    }

    /**
     * Print a line in the log file. If any parameters are provided as input, the parameters will be printed and another line will be added at the end.
     * @params parameters
     *     object parameters to print
    */
    protected function line()
    {
        $this->_log('------------------------------------------------------------------------');
        if (func_num_args() > 0)
        {
            foreach(func_get_args() as $arg)
            {
                $this->e($arg);
                //if ($arg == 'AP not found') { $this->stacktrace(5); }
            }
            $this->line();
        }
    }

    /**
     * Print a stack trace. Alias for the method callstack.
     * @params limit
     *     integer depth of the stack trace to print. Default 5
    */
    protected function stacktrace($limit = 5) { $this->callstack($limit); }

    /**
     * Print a stack trace
     * @params limit
     *     integer depth of the stack trace to print. Default 5
    */
    protected function callstack($limit = 5)
    {
        $calls = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit);
        array_shift($calls);//remove this method from the callstack
        $result = [];
        foreach($calls as $call)
        {
            $line = isset($call['line'])?$call['line']:'--';
            $function = isset($call['function'])?$call['function']:'--';
            $file = isset($call['file'])?$call['file']:'--';
            $this->_log($line." ".$function." ".$file);
        }
    }

   /**
     * Tick stack
     * @params label
     *     string text for this tick event. Default '<<no label>>'
    */
   protected function tick($label = "<<no label>>")
   {
       if (!property_exists($this, 'tickobject'))
       {
           // initialize
           $this->{"tickobject"} = [];
           $this->tickobject['ticktime']      = microtime(true);
           $this->tickobject['label']         = $label;
           $this->tickobject['tickstack']     = [];
       }
       else
       {
           if ($this->tickobject['ticktime'] != 0) { $this->tickobject['tickstack'][] = ['label' => $this->tickobject['label'], 'ticktime' => $this->tickobject['ticktime']]; }
           $this->tickobject['ticktime'] = microtime(true);
           $this->tickobject['label']    = $label;
       }
   }
   
    /**
     * Print the tick trace list
     *
    */
    protected function printticktrace()
    {
        $this->tickobject['tickstack'][] = ['label' => $this->tickobject['label'], 'ticktime' => $this->tickobject['ticktime']];
        $this->tickobject['ticktime'] = 0;
        $starttick = 0;
        $prevtickindex = 0;
        $tickcount = 0;
        $ticks = count($this->tickobject['tickstack']) - 1;
        foreach($this->tickobject['tickstack'] as $tickindex => $tick)
        {
            if ($starttick == 0)
            {
                $starttick = $tick['ticktime'];
                $label = 'start';
                $prevtickindex = 0;
                $time = 0;
            }
            else
            {
                    $label = $tick['label'];
                    $time = $tick['ticktime'];
                    $this->e($tickcount." ".$this->tickobject['tickstack'][$prevtickindex]['label']." ".round(($tick['ticktime']-$this->tickobject['tickstack'][$prevtickindex]['ticktime']),3));
                    $tickcount++;
                    $prevtickindex = $tickindex;
            }
        }
    }

    /**
     * Internal log method called by other methods
     * @params msg
     *     string message to log
     * @params suppressLeadingInfo
     *     boolean if true the date and caller information will not be added at the beginning of the message
    */
    protected function _log($msg,$suppressLeadingInfo = false)
    {
        if ($this->error_log_message_type != 0)
        {
            $string = "";
            if (!$suppressLeadingInfo)
            {
                $dbt=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,10);
                $caller = isset($dbt[3]['function']) ? $dbt[3]['function'] : null;
                $string .= date('Y-m-d H:i:s')." ".$caller." ";
            }
            $string .= $msg."\n";
            error_log($string,$this->error_log_message_type,$this->error_log_destination);
        }
        else
        {
           error_log($msg);
        }
    }

    /**
     * output the obj to the user's personal debug log
     * Same as $this->enablePersonalDebugLog() followed by $thi->e($obj)
    */
    protected function p($obj)
    {
        $this->enablePersonalDebugLog();
        $this->e($obj);
    }

    /**
     * showStartLog
     *
     * @return void
     */
    protected function showStartLog()
    {
        $this->enablePersonalDebugLog();
        $debug = debug_backtrace();
        $this->e('@@@ '.'Start:'.$debug[1]['class'].'\\'.$debug[1]['function']);
    }

}

