<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\web\UploadedFile;
use yii\web\ServerErrorHttpException;
use yii\web\NotFoundHttpException;

class UploadController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        /*$behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];*/
        $behaviors['corsFilter']= [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                // restrict access to
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['POST', 'PUT'],
                // Allow only POST and PUT methods
                'Access-Control-Request-Headers' => ['*'],
                // Allow only headers 'X-Wsse'
                //'Access-Control-Allow-Credentials' => true,
                // Allow OPTIONS caching
                //'Access-Control-Max-Age' => 3600,
                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                //'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
            ],

        ];
        return $behaviors;
    }
    
    
    /**
     * Create new passion profile.
     *
     * @return \yii\db\ActiveRecordInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     */
    public function actionIndex()
    {
        $model = new \app\models\UploadForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->video = UploadedFile::getInstanceByName('video');
        if ($model->upload()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $response->getHeaders()->set('Location', $model->name);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException(Yii::t('app/error', 'Failed to create the passion profile for unknown reason.'));
        }
        return $model;
    }
}
