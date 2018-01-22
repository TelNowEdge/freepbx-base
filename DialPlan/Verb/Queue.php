<?php

namespace TelNowEdge\FreePBX\Base\DialPlan\Verb;

class Queue implements VerbInterface
{
  private $queuename ;
  private $options;
  private $rules;
  private $position;
  private $optionalurl;
  private $announceoverride;
  private $timeout;

  function ext_queue_plus($queuename, $options, $rules,$position,$optionalurl, $announceoverride, $timeout)
  {
    $this->queuename = $queuename;
    $this->options = $options;
    $this->rules = $rules;
    $this->position = $position;
    $this->optionalurl = $optionalurl;
    $this->announceoverride = $announceoverride;
    $this->timeout = $timeout;
  }

  public function output()
  {
    $cmdend="";
    if ($this->position != -1) {
      $cmdend=",".$this->position;
    }
    return sprintf(
		   'Queue(%s,%s,%s,%s,%s,,,,%s%s)',
		   $this->queuename,
		   $this->options,
		   $this->optionalurl,
		   $this->announceoverride,
		   $this->timeout,
		   $this->rules,
		   $cmdend
		   );
  }
}
