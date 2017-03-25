


<?php
    require('Pusher.php');
    
    $options = array(
                     'encrypted' => true
                     );
    $pusher = new Pusher(
                         '4d991b9f12f2dc2a7da0',
                         '4d9c251bd17204485290',
                         '309570',
                         $options
                         );
    
    $data['message'] = 'hello world';
    $pusher->trigger('my-channel', 'my-event', $data);
    ?>
