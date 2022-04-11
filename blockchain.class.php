<?php

    class BlockChain {

        protected $chain      = [];
        protected $difficulty = 4;
        protected $file       = null;


        /**
         * Set file path of blockchain
         *
         * @param string $file
         * @param int $difficulty (optional)
         *
         * @return bool
         */

        public function open(string $file, int $difficulty = null) {

            if (is_file($file)) {
                $chain = json_decode(file_get_contents($file));
                $this->chain = is_array($chain) ? $chain : [];
            }

            if ($difficulty) {
                $this->difficulty = $difficulty;
            }

            $this->file = $file;

            return $this->valid();
        }


        /**
         * Set or get difficulty of blockchain
         * If $difficulty param is passed, set the difficulty, else return current difficulty
         *
         * @param int $difficulty (optional, default 4)
         *
         * @return int
         */

        public function difficulty(int $difficulty = null) {

            if ($difficulty) {
                $this->difficulty = $difficulty;
            }

            return $this->difficulty;
        }


        /**
         * Return total blocks
         *
         * @return int
         */

        public function count() {

            return count($this->chain);
        }


        /**
         * Return a single block by hash
         *
         * @param string $hash
         *
         * @return object
         */

        public function hash(string $hash) {

            foreach ($this->chain as $chain) {
                if ($chain->hash == $hash) {
                    return $this->normalize($chain);
                }
            }

            return false;
        }


        /**
         * Return all blockchain or only a block by index
         * If $index param is passed, get a single block, else return all blocks
         *
         * @param int $index (optional, default null)
         *
         * @return array|object
         */

        public function get(int $index = null) {

            if ($index) {
                return isset($this->chain[$index]) ? $this->normalize($this->chain[$index]) : false;
            }

            foreach ($this->chain as $chain) {
                $chain = $this->normalize($chain);
            }

            return $this->chain;
        }


        /**
         * Return last block of blockchain
         * If $onlyHash param is passed, return only hash of last block, else return the entire last block
         *
         * @param bool $onlyHash (optional, default null)
         *
         * @return object|string
         */

        public function last(bool $onlyHash = false) {

            if (is_array($this->chain) && count($this->chain)) {
                $block = $this->chain[count($this->chain) - 1];
                if ($onlyHash) {
                    return $block->hash;
                }
                return $this->normalize($block);
            }

            return false;
        }


        /**
         * Generate new block
         *
         * @param string|number|array $data
         *
         * @return string
         */

        public function block($data) {

            if ($this->last(true)) {
                $block = $this->generate($data, $this->last(true));
            } else {
                $block = $this->generate($data);
            }

            $block = $this->mine($block);
            $this->chain[] = $block;
            $data = json_encode($this->chain, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            $dir = dirname($this->file);
            if (!is_dir($dir)) {
                mkdir($dir, 0644, true);
            }

            file_put_contents($this->file, $data);

            return $block->hash;
        }


        /**
         * Validate the blockchain's integrity
         * If return false, the blockchain has been tampered with
         *
         * @return bool
         */

        public function valid() {

            for ($i = 0; $i < count($this->chain); $i++) {
                $current = $this->chain[$i];

                if ($current->hash != $this->calculate($current)) {
                    return false;
                }

                if ($i) {
                    $previous = $this->chain[$i - 1];
                    if ($current->previous != $previous->hash) {
                        return false;
                    }
                }
            }

            return true;
        }


        /**
          * Generate new block
          *
          * @param mixed $data
          * @param string $previous (optional)
          *
          * @return array
          */

          protected function generate($data, string $previous = null) {

            $block = (Object)[
                'index'     => isset($this->chain) ? count($this->chain): 0,
                'timestamp' => strtotime('now'),
                'data'      => $data,
                'proof'     => 0,
                'previous'  => $previous
            ];

            $block->hash = $this->calculate($block);

            if (!$block->index && !$previous) {
                unset($block->previous);
            }

            return $block;
        }


        /**
          * Mine a block
          *
          * @param object $block
          *
          * @return object
          */

          protected function mine(object $block) {

            while (substr($block->hash, 0, $this->difficulty) !== str_repeat('0', $this->difficulty)) {
                $block->proof++;
                $block->hash = $this->calculate($block);
            }

            return $block;
        }


        /**
          * Normalize data
          *
          * @param object $block
          *
          * @return object
          */

          protected function normalize(object $block) {

            if (is_string($block->data) && json_decode($block->data) && json_last_error() === 0) {
                $block->data = json_decode($block->data);
            }

            return $block;
        }


        /**
          * Calculate Hash
          *
          * @param object $block
          *
          * @return string
          */

        protected function calculate(object $block) {

            if (is_array($block->data) || is_object($block->data)) {
                $block->data = json_encode($block->data);
            }

            return hash('sha256', $block->index . $block->timestamp . $block->data . $block->proof . @$block->previous);
        }
    }
