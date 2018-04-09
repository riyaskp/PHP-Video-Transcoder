<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $video;
    public $extension;
    public $name;
    public $bucket;
    public $notify_url;

    public function rules()
    {
        return [
            [['name', 'bucket', 'notify_url'], 'safe'],
            [['video'], 'file', 'skipOnEmpty' => false, 'extensions' => 'mp4, m4a, m4v, f4v, f4a, m4b, m4r, f4b, mov, 3gp, 3gp2, 3g2, 3gpp, 3gpp2, ogg, oga, ogv, ogx, wmv, wma, webm, flv, avi, mkv'],
        ];
    }
    
    public function fields() {
        return ['name', 'bucket'];
    }


    public function upload()
    {
        if ($this->validate()) {
            if (!$this->name) {
                $this->name=$this->video->baseName;
            }
            $this->extension=$this->video->extension;
            if (!file_exists(\Yii::getAlias('@webroot').'/uploads')) {
                mkdir(\Yii::getAlias('@webroot').'/uploads', 0777, true);
            }
            if (!file_exists(\Yii::getAlias('@webroot').'/results')) {
                mkdir(\Yii::getAlias('@webroot').'/results', 0777, true);
            }
            $this->video->saveAs(\Yii::getAlias('@webroot').'/uploads/' . $this->name. '.' . $this->extension);
            //sending feed rabbitmq
            $producer = \Yii::$app->rabbitmq->getProducer('convertVideo');
            $msg = json_encode(['name' => $model->name, 'bucket' => $model->bucket, 'notify_url' => $model->notify_url, 'extension' => $model->extension]);
            $producer->publish($msg, 'ConvertVideo', 'convertVideoMp4');
            return true;
        } else {
            return false;
        }
    }
}