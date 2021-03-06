<?php
  namespace elpho\io;

  use elpho\lang\Text;

  class IcmpPackage{
    private $data = "";
    private $type = "\x00";
    private $code = "\x00";
    private $checksum = "\x00\x00";
    private $identifier = "\x00\x00";
    private $sequence = "\x00\x00";

    public function __construct($type,$data=null){
      if(is_a($type,Socket)){
        $type = $type->read(255);

        $this->setCode($type->substr(1,1));
        $this->setIdentifier($type->substr(4,2));
        $this->setSequence($type->substr(6,2));

        $data = $type->substr(8);
        $type = $type->substr(0,1);
      }

      if(ord($type) > 16) throw new IoException("Invalid ICMP type. Check RFC 792 for more info.");

      $this->setType($type);
      $this->setData($data);
    }

    public function setData($data){
      $this->data = $data;
    }
    public function setType($type){
      $this->type = $type;
    }
    public function setCode($code){
      $this->code = $code;
    }
    public function setIdentifier($identifier){
      $this->identifier = $identifier;
    }
    public function setSequence($sequence){
      $this->sequence = $sequence;
    }

    public function getData(){
      return new Text($this->data);
    }
    public function getType(){
      return new Text($this->type);
    }
    public function getCode(){
      return new Text($this->code);
    }
    public function getChecksum(){
      $data = $this->getData();
      if($data->length()%2) $data = $data->concat("\x00");

      $bit = unpack('n*', $data);
      $sum = array_sum($bit);

      while ($sum >> 16) $sum = ($sum >> 16) + ($sum & 0xffff);

      return new Text(pack('n*', ~$sum));
    }
    public function getIdentifier(){
      return new Text($this->identifier);
    }
    public function getSequence(){
      return new Text($this->sequence);
    }

    public function toString(){
      return new Text($this->getType().$this->getCode().$this->getChecksum().$this->getIdentifier().$this->getSequence().$this->getData());
    }
    public function __toString(){
      return $this->toString()->toString();
    }
  }
