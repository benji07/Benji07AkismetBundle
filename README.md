#BenjiAkismetBundle

Bundle to use akismet api in Symfony2

## Configuration


    [yml]
    benji_akismet
      key: your key
      blog: your site homepage
      
      
## Usage


    [php]
    $data = array(
        'user_ip' => $comment->getUserIp(),
        'user_agent' => $comment->getUserAgent(),
        'referrer' => $comment->getReferrer(),
        'comment_type' => 'comment',
        'comment_author' => $comment->getUsername(),
        'comment_author_email' => $comment->getEmail(),
        'comment_content' => $comment->getContent()
    );
    
    // check if $comment is a spam
    $this['akismet']->isSpam($data);
    
    // submit a spam
    $this['akismet']->submitSpam($data);
    
    // submit a ham
    $this['akismet']->submitHam($data);