<?php

function read_function ($filename) {
    try
    {
        //Check if filetype is valid
        if(end(explode('.', $filename)) !== 'txt')
        {
            throw new Exception('Not valid file type');
        }

        //Make sure that file is read
        $file = fopen($filename, "r");
        if(!$file)
        {
            throw new Exception('Can not read file');
        }

        /* Read files and if duplicate take the latest rule and overwrite previous value, like CSS.
        *  Also split by first "=" to allow "=" signs in value string
        */

        $lines = array();
        while(!feof($file))
        {
            $line = fgets($file);
            $sections = explode('=', $line, 2);
            $key = trim(array_shift($sections));
            $value = trim(implode('.', $sections));
            $lines[$key] = $value;
        }

        //Define recursion function to add values to the config as multidimensional array
        function recurseConfig (&$config, $domains, $value)
        {
            $domain = array_shift($domains);
            if(!$config[$domain]) 
            {
                $config[$domain] = array();
            }
            if(count($domains) === 0)
            {
                $config[$domain] = $value;
                return;
            }
            recurseConfig($config[$domain], $domains, $value);
        }

        $config = array();
        foreach($lines as $key => $value)
        {
            //Invoke key recusion to allow infinite keys/domain-keys in configuration
            $domains = explode('.', $key);
            recurseConfig($config, $domains, $value);
        }

        return $config;
    } 
    catch (Exception $e) 
    {
        echo $e;
    }
}

$res = read_function("config.txt");
echo '<pre>';
var_dump($res);
echo '</pre>';