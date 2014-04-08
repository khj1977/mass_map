<?php

// This class is used not to use memcache.
// If cache mechanism is appropriately implemened,
// non-cache of (key, val) pair should not be problem
class NullCache {

  public function __construct() {
    // Do nothing.
  }
  
  public function get($key) {
    return null;
  }
  
  public function set($key, $val) {
    // Do nothing.
  }

}

?>