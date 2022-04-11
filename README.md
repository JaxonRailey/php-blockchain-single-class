# PHP BlockChain

An example of BlockChain created with PHP. The data is stored in a JSON file.

```php
include 'blockchain.class.php';

$blockchain = new BlockChain();


/**
  * Set file path of blockchain
  *
  * @param string $file
  * @param int $difficulty (optional)
  *
  * @return bool
  */

$blockchain->open($file, $difficulty);


/**
  * Set or get difficulty of blockchain
  * If $difficulty param is passed, set the difficulty, else return current difficulty
  *
  * @param int $difficulty (optional, default 4)
  *
  * @return int
  */

$blockchain->difficulty();


/**
  * Return total blocks
  *
  * @return int
  */

$blockchain->count();


/**
  * Return a single block by hash
  *
  * @param string $hash
  *
  * @return object
  */

$blockchain->hash($hash);


/**
  * Return all blockchain or only a block by index
  * If $index param is passed, get a single block, else return all blocks
  *
  * @param int $index (optional, default null)
  *
  * @return array|object
  */

$blockchain->get();


/**
  * Return last block of blockchain
  * If $onlyHash param is passed, return only hash of last block, else return the entire last block
  *
  * @param bool $onlyHash (optional, default null)
  *
  * @return object|string
  */

$blockchain->last();


/**
  * Generate new block
  *
  * @param string|number|array $data
  *
  * @return string
  */

$blockchain->block($data);


/**
  * Validate the blockchain's integrity
  * If return false, the blockchain has been tampered with
  *
  * @return bool
  */

$blockchain->valid();

```

This project is only for educational purpose, to show in a simple way how a blockchain works.

:star: **If you liked what I did, if it was useful to you or if it served as a starting point for something more magical let me know with a star** :green_heart:
