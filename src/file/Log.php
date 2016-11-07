<?php
  namespace elpho\io\file;

  use elpho\lang\Text;

  class Log{

    private $file;
    private $format;

    public function __construct(File $file, $timeFormat="Y-m-d H:i:s "){
      $this->file = $file;
      $this->format = $timeFormat;
    }

    public function setFormat($format){
      $this->format = $format;
    }
    public function getFormat(){
      return $this->format;
    }

    public function write($text){
      $this->file->refresh();
      $this->file = null;

      if(is_string($text))
        $text = new Text($text);

      if($this->file == null)
        throw new NullPointerException();

      $sliced = $text->split("\n");

      if($sliced->length() > 1){
        $sliced->map(array($this,"write"));
        return;
      }

      $time = new Text(date($this->format));

      $this->file->writeLine($time->concat($sliced[0]));
      $this->file->save();
    }
    public function close(){
      $this->file = null;
    }
    public function clear(){
      $this->file->clear();
    }
  }