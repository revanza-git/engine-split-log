<?php

Class Script
{

    function shrinkFile($filePath, $bakFilePath, $maxFileSize, $shrinkedFileSize){
        $fileSize = filesize($filePath);

        if($fileSize > $maxFileSize){
            $handle = fopen($filePath, 'a+b');
            $bakHandle = fopen($bakFilePath, 'a+b');

            // position in main file requested new size before end of file
            if(fseek($handle, $fileSize - $shrinkedFileSize) === -1)
                return false;
        
            // clear the backup file
            if(!ftruncate($bakHandle, 0))
                return false;
            
            // copy the required new size portion to the temporary backup file
            while (!feof($handle)) {
                $buffer = fread($handle, 8192); 
                fwrite($bakHandle, $buffer);
            }

            // clear the main file
            if(!ftruncate($handle, 0))
                return false;
            
            // position at start of backup file
            if(!rewind($bakHandle))
                return false;
            
            // copy backup file to main file
            while (!feof($bakHandle)) {
                $buffer = fread($bakHandle, 8192); 
                fwrite($handle, $buffer);
            }

            // close streams
            fclose($handle);
            fclose($bakHandle);
        }
        
        // optionally delete the temporary backup file here
        //if(file_exists($bakFilePath))
            //unlink($bakFilePath);
        
        return true;

}