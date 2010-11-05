#AkismetBundle

Bundle to use akismet api in Symfony2

## Configuration


    [yml]
    akismet.config:
      key: ~
      
      
## Usage


    [php]
    // check if $comment is a spam
    $this['akismet']->isSpam($comment);
    
    // submit a spam
    $this['akismet']->submitSpam($comment);
    
    // submit a ham
    $this['akismet']->submitHam($comment);