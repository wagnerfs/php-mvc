<h1>Fatal error</h1>
<p>Uncaught exception: '<?php echo get_class($this->exception) ?>'</p>
<p>Message: '<?php echo $this->exception->getMessage() ?>'</p>
<p>Stack trace:<pre><?php echo $this->exception->getTraceAsString() ?></pre></p>
<p>Thrown in '<?php echo $this->exception->getFile() ?>' on line <?php echo $this->exception->getLine() ?></p>