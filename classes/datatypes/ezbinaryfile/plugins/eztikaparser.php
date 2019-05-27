<?php

class eZTikaParser
{
    // if true debug messages will be write to eztika.log
    protected $debugIsEnabled = false;

    // if true the tmp cache file for extracted text is not unlinked
    protected $debugKeepTempFiles = false;

    function __construct()
    {
        $eztikaINI = eZINI::instance('eztika.ini');
        $debugEnabled = $eztikaINI->variable('DebugSettings', 'Debug');
        if ($debugEnabled == 'enabled') {
            $this->debugIsEnabled = true;
            $keepTempFiles = $eztikaINI->variable('DebugSettings', 'KeepTempFiles');
            if ($keepTempFiles == 'enabled') {
                $this->debugKeepTempFiles = true;
            }

        }
    }

    function parseFile($fileName)
    {
        $originalFileSize = filesize($fileName);
        $this->writeEzTikaLog('[START] eZMultiParser for File: ' . round($originalFileSize / 1024, 2) . ' KByte ' . $fileName);

        $binaryINI = eZINI::instance('binaryfile.ini');
        $textExtractionTool = $binaryINI->variable('TikaHandlerSettings', 'TextExtractionTool');

        $startTime = time();
        $tmpDirectory = eZSys::cacheDirectory() . eZSys::fileSeparator() . 'eztika';
        eZDir::mkdir($tmpDirectory, false, true);
        $tmpName = $tmpDirectory . eZSys::fileSeparator() . 'eztika_' . md5($startTime) . '.txt';
        $handle = fopen($tmpName, "w");
        fclose($handle);
        chmod($tmpName, 0777);

        $cmd = "$textExtractionTool $fileName > $tmpName";

        $this->writeEzTikaLog('exec: ' . $cmd);

        // perform eztika command
        exec($cmd, $returnArray, $returnCode);

        $this->writeEzTikaLog("exec returnCode: $returnCode");

        $metaData = '';
        if (file_exists($tmpName)) {
            $fp = fopen($tmpName, 'r');
            $fileSize = filesize($tmpName);
            $metaData = fread($fp, $fileSize);
            fclose($fp);

            // keep tempfile in debugmode
            if ($this->debugKeepTempFiles === true) {
                $this->writeEzTikaLog('keep tempfile for debugging extracted metadata: ' . $tmpName);
            } else {
                $this->writeEzTikaLog('unlink tempfile: ' . $tmpName);
                unlink($tmpName);
            }

            if ($fileSize === false || $fileSize === 0) {
                $this->writeEzTikaLog("[ERROR] no metadata was extracted! Check if eztika is working correctly");
            } else {
                $this->writeEzTikaLog('metadata read from tempfile ' . round($fileSize / 1024, 2) . ' KByte');
            }
        } else {
            $this->writeEzTikaLog("[ERROR] no tempfile '$tmpName' for eztika output exists,
                check if eztika command is working,
                check if eztika is executeable");
        }

        // write an error message to error.log if no data could be extracted
        if ($metaData == '' && $originalFileSize > 0) {
            eZDebug::writeError("eztika can not extract content from binaryfile for searchindex \nexec( $cmd )", __METHOD__);
        }

        $endTime = time();
        $seconds = $endTime - $startTime;
        $this->writeEzTikaLog("[END] after $seconds s");

        return $metaData;
    }

    /**
     *
     * write log message to eztika.log
     * @param string $message
     */
    private function writeEzTikaLog($message)
    {
        if ($this->debugIsEnabled) {
            eZLog::write($message, 'eztika.log');
        }
    }

}

