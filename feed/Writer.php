<?php
  class Writer{

    private $filename;
    private $fileData;
    private $extension;

    public function __construct($filename, $extension){

      $this->filename = $filename;
      $this->extension = $extension;

      $this->fileData = $this->filename . $this->extension;

    }

    public function writeFile($content){

      file_put_contents($this->fileData, $content);

    }
  }
