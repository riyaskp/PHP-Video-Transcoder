<?php

namespace app\components\rabbitmq;

use mikemadisonweb\rabbitmq\components\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Yii;

class ConvertVideo1 implements ConsumerInterface
{
    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        $post = json_decode($msg->body);
        if (isset($post)) {
            $filename = $post->name . '.mp4';
            $source = Yii::getAlias('@app') . '/web/uploads/' . $post->name. '.' . $post->extension;
            $destination = Yii::getAlias('@app') . '/web/results/';
            exec('ffmpeg -y -i '.$source.' -f mp4 -vcodec libx264 -preset fast -profile:v main -acodec aac ' . $destination . $filename . ' -hide_banner', $result, $return);
            if ($return===0) {
                exec("ffmpeg -y -i " . $destination . $filename . " -vframes 1 -ss `ffmpeg -i " . $destination . $filename . " 2>&1 | grep Duration | awk '{print $2}' | tr -d , | awk -F ':' '{print ($3+$2*60+$1*3600)/2}'` -s 240x160 " . $destination . $post->name . "_thumb.png");
                if ($post->bucket) {
                    $s3 = Yii::$app->get('s3');
                    //$s3->defaultBucket=$post->bucket;
                    $s3->commands()->upload($filename, $destination . $filename)->withContentType(\yii\helpers\FileHelper::getMimeType($destination . $filename))->execute();
                    $s3->commands()->upload($post->name . '_thumb.png', $destination .$post->name . '_thumb.png')->withContentType(\yii\helpers\FileHelper::getMimeType($destination . $post->name . '_thumb.png'))->execute();
                    
                    unlink($source);
                    unlink($destination . $filename);
                    unlink($destination . $post->name . '_thumb.png');
                }
                
                if ($post->notify_url) {
                    $ch = curl_init($post->notify_url);
                    
                    if ($post->bucket) {
                        $payload = json_encode(['name' => $post->name, 'thumbnail' => 'https://s3.'.$s3->region.'.amazonaws.com/'.$s3->defaultBucket.'/' . $post->name . '_thumb.png', 'video' => 'https://s3.'.$s3->region.'.amazonaws.com/' . $filename]);
                    } else{
                        $payload = json_encode(['name' => $post->name, 'thumbnail' => $destination . $post->name . '_thumb.png', 'video' => $destination . $filename]);
                        unlink($source);
                    }
                    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    $result = curl_exec($ch);
                    curl_close($ch);
                }
                
                return ConsumerInterface::MSG_ACK;
            }
        }
    }
}
