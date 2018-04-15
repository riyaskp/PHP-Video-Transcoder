VideoTranscoder PHP Yii2, ffmpeg, rabbitmq, AWS s3(optional)

VideoTranscoder convert video to mp4 format and generate a thumbnail.

You neeed to install ffmpeg and rabbitmq.


CURL STRUCTURE
-------------------

    curl -X POST \
      http://YOUR_IP/upload \
      -H 'cache-control: no-cache' \
      -H 'content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW' \
      -F name=THE-FILE-NAME \
      -F notify_url=THE-POST-HOOK-URL(Optional) \
      -F bucket=true(Optional to upload to s3) \
      -F video=@sample.mkv

CONFIGURE AWS S3
-------------------
Add your S3 detail in config/params.php

    's3key' => '**********',
    's3secret' => '*********',
    's3region' => 'us-east-2',
    's3bucket' => '*********'


CONFIGURE RabbitMQ
-------------------
RabbitMQ detail in config/rabbitmq.php

    'connections' => [
        [
            'host' => '192.168.*.***',
            'port' => '5672',
            'user' => 'admin',
            'password' => '*******',
            'vhost' => '/',
            'heartbeat' => 0,
        ],
    ],


INSTALLATION
------------

Docker
------

run 
    composer install
    
    docker-compose up --build

once the docker is build, connect to docker container by

    docker-compose exec app /bin/bash

In docker container
    cd /var/www/html/transcoder
    supervisord -c supervisord.conf

The curl url is localhost:5080


Manual
------

Install ffmpeg, rabbitmq

Clone this repository

run 
    composer install
