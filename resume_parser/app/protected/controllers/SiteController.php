<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	protected function beforeAction($action)
	{
		Yii::getLogger()->autoFlush = 1;
		Yii::getLogger()->autoDump = true;
		ini_get('safe_mode') or set_time_limit(0);
		return true;
	}
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
	public function filters(){
		return array(
			'accessControl + list',
		);
	}
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('list'),
				'users'=>array('admin'),
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	public function actionParse() {
		$results = new AvitoSearchResults;
		echo 'Начали парсинг<br />';
		$results->get_results();
		$results->results_to_db();
		
	}
	public function actionList()
	{
		if (!$pagesize=Yii::app()->input->get('pagesize')) {
			$pagesize = 20;
		}
		$all=Yii::app()->input->get('all');
		$criteria = array();
		if (!$all) {
			$criteria['condition'] = 'ISNULL(called)';
		}
		$dataProvider = new CActiveDataProvider('Resumes', array(
			'criteria'=> $criteria,
			'pagination' => array(
				'pageSize' => $pagesize,
			),
		));
		// $model = new Resumes;
		// $criteria=new CDbCriteria;
		// $count=Resumes::model()->count($criteria);
		// $pages=new CPagination($count);
		// $pages->pageSize=$pagesize;
		// $pages->applyLimit($criteria);
		// $models=Resumes::model()->findAll($criteria);
		$this->render('list', array(
			// 'models' => $models,
			// 'pages' => $pages,
			'all' => $all,
			'dataProvider' => $dataProvider
		));
		
	}
	public function actionCall() {
		$id = Yii::app()->input->get('id');
		$resume=Resumes::model()->findByPk($id);	
		$binds = array();
		if ($resume->email) {
			$binds[':email'] = ($resume->email?"%{$resume->email}%":'');
		}
		if ($resume->phone) {
			$binds[':phone'] = ($resume->phone?"%{$resume->phone}%":'');
		}
		if (!empty($binds)) {
			Resumes::model()->updateAll(
			array('called'=>($resume->called?'NULL':1)),
			($resume->phone?'phone LIKE :phone':'').($resume->phone && $resume->email?' OR ':'').($resume->email?'email LIKE :email':''),
			$binds);
		}
		echo 1;
	}
}