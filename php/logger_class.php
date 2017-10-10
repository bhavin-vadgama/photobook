<?php


class Logger {

    private $file,$prefix;

    public function __construct($filename) {
        $this->file = $filename;
    }

    public function setTimestamp($format) {
        $this->prefix = date($format);
    }

    public function putLog($insert) {
        if (isset($this->prefix)) {
            file_put_contents($this->file, $this->prefix.$insert, FILE_APPEND);
        } else {
            file_put_contents($this->file, date("D M d 'y h.i A").$insert, FILE_APPEND);
        }
    }

    public function getLog() {
        $content = @file_get_contents($this->file);
        return $content;
    }

}